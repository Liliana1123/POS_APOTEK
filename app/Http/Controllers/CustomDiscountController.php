<?php

namespace App\Http\Controllers;

use App\Models\CustomDiscount;
use App\Models\Kategori;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomDiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomDiscount::query();

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $today = now()->format('Y-m-d');
            if ($status === 'aktif') {
                $query->where('aktif', true)
                      ->where('tanggal_mulai', '<=', $today)
                      ->where('tanggal_selesai', '>=', $today);
            } elseif ($status === 'belum_mulai') {
                $query->where('aktif', true)
                      ->where('tanggal_mulai', '>', $today);
            } elseif ($status === 'berakhir') {
                $query->where('aktif', true)
                      ->where('tanggal_selesai', '<', $today);
            } elseif ($status === 'nonaktif') {
                $query->where('aktif', false);
            }
        }

        $discounts = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('custom_discount.index', compact('discounts'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $barangs = Barang::where('aktif', true)->orderBy('nama')->get();
        return view('custom_discount.create', compact('kategoris', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'persentase' => 'required|integer|min:0|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'nullable|boolean',
            'cakupan' => 'required|in:semua,kategori,barang,kombinasi',
            'kategori_ids' => 'required_if:cakupan,kategori,kombinasi|array',
            'kategori_ids.*' => 'exists:kategoris,id',
            'barang_ids' => 'required_if:cakupan,barang,kombinasi|array',
            'barang_ids.*' => 'exists:barangs,id',
        ]);

        $aktif = $request->boolean('aktif');
        $cakupan = $request->input('cakupan');
        $kategoriIds = $request->input('kategori_ids', []);
        $barangIds = $request->input('barang_ids', []);

        // Validasi Overlap Konflik
        if ($aktif) {
            $conflictResponse = $this->checkOverlapConflicts($request, null);
            if ($conflictResponse) {
                return $conflictResponse;
            }
        }

        DB::transaction(function () use ($request, $aktif, $cakupan, $kategoriIds, $barangIds) {
            $discount = CustomDiscount::create([
                'nama' => $request->input('nama'),
                'persentase' => $request->input('persentase'),
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $request->input('tanggal_selesai'),
                'aktif' => $aktif,
                'cakupan' => $cakupan,
            ]);

            if (in_array($cakupan, ['kategori', 'kombinasi'])) {
                $discount->kategoris()->sync($kategoriIds);
            }
            if (in_array($cakupan, ['barang', 'kombinasi'])) {
                $discount->barangs()->sync($barangIds);
            }

            \App\Models\ActivityLog::log('Create Promo', "Promo: {$discount->nama}, Persentase: {$discount->persentase}%");
        });

        return redirect()->route('custom-discount.index')->with('success', 'Custom discount berhasil ditambahkan.');
    }

    public function edit(CustomDiscount $customDiscount)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $barangs = Barang::where('aktif', true)->orderBy('nama')->get();
        
        $selectedKategoriIds = $customDiscount->kategoris->pluck('id')->toArray();
        $selectedBarangIds = $customDiscount->barangs->pluck('id')->toArray();

        return view('custom_discount.edit', compact('customDiscount', 'kategoris', 'barangs', 'selectedKategoriIds', 'selectedBarangIds'));
    }

    public function update(Request $request, CustomDiscount $customDiscount)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'persentase' => 'required|integer|min:0|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'nullable|boolean',
            'cakupan' => 'required|in:semua,kategori,barang,kombinasi',
            'kategori_ids' => 'required_if:cakupan,kategori,kombinasi|array',
            'kategori_ids.*' => 'exists:kategoris,id',
            'barang_ids' => 'required_if:cakupan,barang,kombinasi|array',
            'barang_ids.*' => 'exists:barangs,id',
        ]);

        $aktif = $request->boolean('aktif');
        $cakupan = $request->input('cakupan');
        $kategoriIds = $request->input('kategori_ids', []);
        $barangIds = $request->input('barang_ids', []);

        // Validasi Overlap Konflik
        if ($aktif) {
            $conflictResponse = $this->checkOverlapConflicts($request, $customDiscount->id);
            if ($conflictResponse) {
                return $conflictResponse;
            }
        }

        DB::transaction(function () use ($customDiscount, $request, $aktif, $cakupan, $kategoriIds, $barangIds) {
            $customDiscount->update([
                'nama' => $request->input('nama'),
                'persentase' => $request->input('persentase'),
                'tanggal_mulai' => $request->input('tanggal_mulai'),
                'tanggal_selesai' => $request->input('tanggal_selesai'),
                'aktif' => $aktif,
                'cakupan' => $cakupan,
            ]);

            if (in_array($cakupan, ['kategori', 'kombinasi'])) {
                $customDiscount->kategoris()->sync($kategoriIds);
            } else {
                $customDiscount->kategoris()->detach();
            }

            if (in_array($cakupan, ['barang', 'kombinasi'])) {
                $customDiscount->barangs()->sync($barangIds);
            } else {
                $customDiscount->barangs()->detach();
            }

            \App\Models\ActivityLog::log('Update Promo', "Promo: {$customDiscount->nama}, Persentase: {$customDiscount->persentase}%");
        });

        return redirect()->route('custom-discount.index')->with('success', 'Custom discount berhasil diperbarui.');
    }

    public function destroy(CustomDiscount $customDiscount)
    {
        DB::transaction(function () use ($customDiscount) {
            $customDiscount->kategoris()->detach();
            $customDiscount->barangs()->detach();
            $customDiscount->delete();
            \App\Models\ActivityLog::log('Delete Promo', "Promo: {$customDiscount->nama}");
        });

        return redirect()->route('custom-discount.index')->with('success', 'Custom discount berhasil dihapus.');
    }

    public function toggle(CustomDiscount $customDiscount)
    {
        $newStatus = !$customDiscount->aktif;

        if ($newStatus) {
            $request = new Request([
                'tanggal_mulai' => $customDiscount->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $customDiscount->tanggal_selesai->format('Y-m-d'),
                'cakupan' => $customDiscount->cakupan,
                'kategori_ids' => $customDiscount->kategoris->pluck('id')->toArray(),
                'barang_ids' => $customDiscount->barangs->pluck('id')->toArray(),
            ]);

            $conflictResponse = $this->checkOverlapConflicts($request, $customDiscount->id);
            if ($conflictResponse) {
                return $conflictResponse;
            }
        }

        $customDiscount->update([
            'aktif' => $newStatus
        ]);

        $actionText = $newStatus ? 'Activate Promo' : 'Deactivate Promo';
        \App\Models\ActivityLog::log('Toggle Promo', "Action: {$actionText}, Promo: {$customDiscount->nama}");

        return back()->with('success', 'Status promo berhasil diubah.');
    }

    private function checkOverlapConflicts(Request $request, $excludeId = null)
    {
        $newTanggalMulai = $request->input('tanggal_mulai');
        $newTanggalSelesai = $request->input('tanggal_selesai');
        $newCakupan = $request->input('cakupan');
        $newCategoryIds = $request->input('kategori_ids', []);
        $newBarangIds = $request->input('barang_ids', []);

        // Dapatkan promo aktif lain yang memiliki irisan tanggal
        $overlappingPromosQuery = CustomDiscount::where('aktif', true)
            ->where('tanggal_mulai', '<=', $newTanggalSelesai)
            ->where('tanggal_selesai', '>=', $newTanggalMulai);

        if ($excludeId) {
            $overlappingPromosQuery->where('id', '!=', $excludeId);
        }

        $overlappingPromos = $overlappingPromosQuery->get();
        $newCoveredIds = $this->getCoveredBarangIds($newCakupan, $newCategoryIds, $newBarangIds);

        foreach ($overlappingPromos as $promo) {
            // Semua Barang vs promo apa pun = konflik
            if ($newCakupan === 'semua' || $promo->cakupan === 'semua') {
                return back()->withErrors(['conflict' => "Konflik jadwal dengan promo '{$promo->nama}' ({$promo->tanggal_mulai->format('Y-m-d')} s/d {$promo->tanggal_selesai->format('Y-m-d')}) karena salah satu mencakup semua barang."])->withInput();
            }

            $existingCategoryIds = $promo->kategoris->pluck('id')->toArray();
            $existingBarangIds = $promo->barangs->pluck('id')->toArray();
            $existingCoveredIds = $this->getCoveredBarangIds($promo->cakupan, $existingCategoryIds, $existingBarangIds);

            $intersect = array_intersect($newCoveredIds, $existingCoveredIds);
            if (!empty($intersect)) {
                $conflictingBarang = Barang::find(reset($intersect));
                $barangName = $conflictingBarang ? $conflictingBarang->nama : 'Beberapa barang';
                return back()->withErrors(['conflict' => "Konflik jadwal dengan promo '{$promo->nama}' ({$promo->tanggal_mulai->format('Y-m-d')} s/d {$promo->tanggal_selesai->format('Y-m-d')}) pada barang '{$barangName}'."])->withInput();
            }
        }

        return null;
    }

    private function getCoveredBarangIds($cakupan, $categoryIds = [], $barangIds = [])
    {
        if ($cakupan === 'semua') {
            return Barang::where('aktif', true)->pluck('id')->toArray();
        }

        $ids = [];
        if ($cakupan === 'kategori' || $cakupan === 'kombinasi') {
            $categoryBarangs = Barang::whereIn('kategori_id', $categoryIds)
                ->where('aktif', true)
                ->pluck('id')
                ->toArray();
            $ids = array_merge($ids, $categoryBarangs);
        }

        if ($cakupan === 'barang' || $cakupan === 'kombinasi') {
            $ids = array_merge($ids, $barangIds);
        }

        return array_unique($ids);
    }
}
