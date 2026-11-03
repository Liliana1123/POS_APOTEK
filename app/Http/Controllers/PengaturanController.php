<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\InfoApotek;
use App\Models\PengaturanPpn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless(
            $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan') || $user->hasPermission('sistem.pengaturan-pajak'),
            403,
            'Anda tidak memiliki akses ke Pengaturan Apotek.'
        );

        $apotek = InfoApotek::singleton();
        $ppnAktif = (bool) ($apotek->ppn_aktif ?? true);
        $ppnAktifConfig = PengaturanPpn::getActiveForDate();
        $riwayatPpn = PengaturanPpn::with(['createdBy', 'updatedBy'])
            ->orderBy('tanggal_mulai', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $canManagePengaturan = $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan:kelola');
        $canViewPpn = $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan-pajak');
        $canManagePpn = $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan-pajak:kelola');

        return view('pengaturan.index', compact(
            'apotek',
            'ppnAktif',
            'ppnAktifConfig',
            'riwayatPpn',
            'canManagePengaturan',
            'canViewPpn',
            'canManagePpn'
        ));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        abort_unless(
            $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan:kelola'),
            403,
            'Anda tidak memiliki izin mengelola Pengaturan Apotek.'
        );

        $apotek = InfoApotek::singleton();

        $data = $request->validate([
            'nama_apotek' => 'required|string|max:255',
            'nama_pemilik' => 'nullable|string|max:255',
            'alamat_jalan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'no_izin_sia' => 'nullable|string|max:100',
            'nama_apoteker_pj' => 'nullable|string|max:255',
            'no_sipa' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
            'background_type' => 'nullable|in:default,custom',
            'background_opacity' => 'nullable|integer|min:5|max:100',
            'background_image' => 'nullable|image|max:5120',
        ]);

        $canManageBackground = $user->isAdmin() || $user->isSuperAdmin();

        if ($canManageBackground) {
            if ($request->boolean('reset_background_default')) {
                if ($apotek->background_image) {
                    Storage::disk('public')->delete($apotek->background_image);
                }
                $data['background_image'] = null;
                $data['background_type'] = 'custom';
            }

            if ($request->filled('background_type')) {
                $data['background_type'] = $request->input('background_type');
            }
            if ($request->filled('background_opacity')) {
                $data['background_opacity'] = (int) $request->input('background_opacity');
            }
            if ($request->hasFile('background_image')) {
                if ($apotek->background_image) {
                    Storage::disk('public')->delete($apotek->background_image);
                }
                $data['background_image'] = $request->file('background_image')->store('info-apotek', 'public');
                $data['background_type'] = 'custom';
            }
        } else {
            unset($data['background_type'], $data['background_opacity'], $data['background_image']);
        }

        if ($request->hasFile('logo')) {
            if ($apotek->logo) {
                Storage::disk('public')->delete($apotek->logo);
            }
            $data['logo'] = $request->file('logo')->store('info-apotek', 'public');
        }

        $apotek->update($data);
        Cache::forget('info_apotek');

        ActivityLog::log(
            'Update Pengaturan Apotek',
            "Nama: {$apotek->nama_apotek}",
            ActivityLog::CATEGORY_SISTEM
        );

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Toggle status aktif/nonaktif PPN global apotek.
     */
    public function togglePpnStatus(Request $request)
    {
        $user = $request->user();
        abort_unless(
            $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan-pajak:kelola'),
            403,
            'Anda tidak memiliki izin mengelola status PPN.'
        );

        $status = $request->boolean('ppn_aktif');
        $apotek = InfoApotek::singleton();
        $apotek->update(['ppn_aktif' => $status]);
        Cache::forget('info_apotek');

        $statusText = $status ? 'AKTIF' : 'NONAKTIF';
        ActivityLog::log(
            'Ubah Status PPN',
            "Status PPN apotek diubah menjadi: {$statusText}",
            ActivityLog::CATEGORY_SISTEM
        );

        return back()->with('success', "Status PPN berhasil diubah menjadi: {$statusText}.");
    }

    /**
     * Simpan konfigurasi tarif PPN baru.
     */
    public function storePpn(Request $request)
    {
        $user = $request->user();
        abort_unless(
            $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan-pajak:kelola'),
            403,
            'Anda tidak memiliki izin menambahkan tarif PPN.'
        );

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'tarif_persen' => 'required|numeric|min:0|max:100',
            'metode_perhitungan' => 'required|in:standar,dpp_nilai_lain',
            'persentase_dpp' => 'required|numeric|min:0.0001|max:100',
            'tipe_perlakuan_harga' => 'required|in:eksklusif,inklusif',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:500',
            'auto_tutup_sebelumnya' => 'nullable|boolean',
        ]);

        $tanggalMulai = $data['tanggal_mulai'];
        $tanggalAkhir = $data['tanggal_akhir'] ?? null;

        // Jika opsi auto tutup tarif sebelumnya dicentang:
        // Set tanggal_akhir tarif aktif lama yang open-ended atau tumpang tindih ke H-1
        if ($request->boolean('auto_tutup_sebelumnya')) {
            $hMinus1 = Carbon::parse($tanggalMulai)->subDay()->toDateString();
            PengaturanPpn::where('is_aktif', true)
                ->where('tanggal_mulai', '<', $tanggalMulai)
                ->where(function ($q) use ($tanggalMulai) {
                    $q->whereNull('tanggal_akhir')
                        ->orWhere('tanggal_akhir', '>=', $tanggalMulai);
                })
                ->update(['tanggal_akhir' => $hMinus1]);
        }

        // Cek konflik periode berlaku
        $conflict = PengaturanPpn::findConflictPeriod($tanggalMulai, $tanggalAkhir);
        if ($conflict) {
            return back()->withErrors([
                'tanggal_mulai' => "Periode tanggal bertabrakan dengan konfigurasi aktif '{$conflict->nama}' ({$conflict->periode_teks}). Silakan atur tanggal akhir konfigurasi lama atau centang opsi penutupan otomatis tarif sebelumnya."
            ])->withInput();
        }

        $data['is_aktif'] = true;
        $data['created_by'] = $user->id;

        $ppn = PengaturanPpn::create($data);

        ActivityLog::log(
            'Tambah Tarif PPN',
            "Nama: {$ppn->nama}, Tarif: {$ppn->tarif_persen}%, Metode: {$ppn->metode_perhitungan}, Masa Berlaku: {$ppn->periode_teks}",
            ActivityLog::CATEGORY_SISTEM
        );

        return back()->with('success', "Konfigurasi PPN '{$ppn->nama}' berhasil ditambahkan.");
    }

    /**
     * Update konfigurasi tarif PPN (edit tanggal, keterangan, atau status aktif).
     */
    public function updatePpn(Request $request, PengaturanPpn $ppn)
    {
        $user = $request->user();
        abort_unless(
            $user->isSuperAdmin() || $user->hasPermission('sistem.pengaturan-pajak:kelola'),
            403,
            'Anda tidak memiliki izin mengedit konfigurasi PPN.'
        );

        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'tarif_persen' => 'required|numeric|min:0|max:100',
            'metode_perhitungan' => 'required|in:standar,dpp_nilai_lain',
            'persentase_dpp' => 'required|numeric|min:0.0001|max:100',
            'tipe_perlakuan_harga' => 'required|in:eksklusif,inklusif',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_aktif' => 'nullable|boolean',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $data['is_aktif'] = $request->boolean('is_aktif');
        $data['updated_by'] = $user->id;

        // Jika tetap aktif, cek konflik periode (kecuali dengan dirinya sendiri)
        if ($data['is_aktif']) {
            $conflict = PengaturanPpn::findConflictPeriod($data['tanggal_mulai'], $data['tanggal_akhir'] ?? null, $ppn->id);
            if ($conflict) {
                return back()->withErrors([
                    'tanggal_mulai' => "Periode tanggal bertabrakan dengan konfigurasi aktif '{$conflict->nama}' ({$conflict->periode_teks})."
                ])->withInput();
            }
        }

        $ppn->update($data);

        ActivityLog::log(
            'Update Tarif PPN',
            "ID: {$ppn->id}, Nama: {$ppn->nama}, Tarif: {$ppn->tarif_persen}%, Status: " . ($ppn->is_aktif ? 'Aktif' : 'Nonaktif'),
            ActivityLog::CATEGORY_SISTEM
        );

        return back()->with('success', "Konfigurasi PPN '{$ppn->nama}' berhasil diperbarui.");
    }
}
