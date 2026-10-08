<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\DetailPesananPenerimaan;
use App\Models\RiwayatPenerimaan;
use App\Models\Penerimaan;
use App\Models\PembayaranPenerimaan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenerimaanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penerimaan::with([
            'user',
            'supplier',
            'detailPesanan.riwayatPenerimaan',
        ]);

        if ($request->filled('cari')) {
            $query->where('no_faktur', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status_pembayaran')) {
            if ($request->status_pembayaran === 'lunas') {
                $query->where('lunas', true);
            }

            if ($request->status_pembayaran === 'belum_lunas') {
                $query->where('lunas', false);
            }
        }

        if ($request->filled('status_penerimaan')) {

        // BELUM LENGKAP
        if ($request->status_penerimaan === 'belum_lengkap') {
            $query->whereHas('detailPesanan', function ($q) {
                $q->whereRaw("
                    jumlah_dipesan >
                    (
                        SELECT COALESCE(SUM(
                            CASE
                                WHEN jenis = 'penerimaan' THEN jumlah
                                WHEN jenis = 'pembatalan' THEN jumlah
                                ELSE 0
                            END
                        ), 0)
                        FROM riwayat_penerimaans
                        WHERE detail_pesanan_penerimaan_id = detail_pesanan_penerimaans.id
                    )
                ");
            });
        }

        // LENGKAP
        if ($request->status_penerimaan === 'lengkap') {
            $query
                ->whereDoesntHave('detailPesanan', function ($q) {
                    $q->whereRaw("
                        jumlah_dipesan >
                        (
                            SELECT COALESCE(SUM(
                                CASE
                                    WHEN jenis = 'penerimaan' THEN jumlah
                                    WHEN jenis = 'pembatalan' THEN jumlah
                                    ELSE 0
                                END
                            ), 0)
                            FROM riwayat_penerimaans
                            WHERE detail_pesanan_penerimaan_id = detail_pesanan_penerimaans.id
                        )
                    ");
                })
                ->whereHas('detailPesanan');
        }
    }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $perPage = $request->input('per_page', 15);

        $penerimaans = $query->withSum('pembayaran', 'jumlah')
            ->with(['detail', 'detailPesanan.riwayatPenerimaan'])
            ->orderByDesc('tanggal')->paginate($perPage)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get();
        $barangs = Barang::with(['pabrik', 'satuan'])->where('aktif', true)->orderBy('nama')->get();

        return view('penerimaan.index', compact('penerimaans', 'suppliers', 'barangs'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $barangs = Barang::where('aktif', true)->orderBy('nama')->get();

        return view('penerimaan.create', compact('suppliers', 'barangs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            
            'supplier_id' => 'required|exists:suppliers,id',
            'telepon_supplier' => 'nullable|string|max:30',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
            'tanggal_faktur' => 'required|date',
            'no_faktur' => 'required|string|max:100|unique:penerimaans,no_faktur',
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal',
            'pembayaran_pertama' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.no_batch' => 'required|string|max:100',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.expired_date' => 'required|date',
            'items.*.no_rak' => 'required|string|max:50',
            'items.*.jumlah_dipesan' => 'required|integer|min:1',
            'items.*.jumlah_diterima' => 'required|integer|min:0',
        ]);

        $itemErrors = [];
        foreach ($data['items'] as $index => $item) {
            if ((int) $item['jumlah_diterima'] > (int) $item['jumlah_dipesan']) {
                $itemErrors["items.$index.jumlah_diterima"] = [
                    'Jumlah diterima tidak boleh melebihi jumlah dipesan.',
                ];
            }
            if (!empty($item['expired_date']) && !empty($data['tanggal'])) {
                if ($item['expired_date'] < $data['tanggal']) {
                    $itemErrors["items.$index.expired_date"] = [
                        'Tanggal expired tidak boleh sebelum tanggal penerimaan.',
                    ];
                }
            }
            if (isset($item['harga_jual']) && isset($item['harga_beli'])) {
                if ((float) $item['harga_jual'] < (float) $item['harga_beli']) {
                    $itemErrors["items.$index.harga_jual"] = [
                        'Harga jual tidak boleh lebih kecil dari harga beli.',
                    ];
                }
            }
        }
        if (!empty($itemErrors)) {
            throw ValidationException::withMessages($itemErrors);
        }

        $data['supplier_id'] = (int) $data['supplier_id'];
        $supplier = Supplier::findOrFail($data['supplier_id']);
        $totalFaktur = collect($data['items'])->sum(fn ($item) => (float) $item['harga_beli'] * (int) $item['jumlah_diterima']);
        $ppn = $totalFaktur * 0.11;
        $totalTagihan = $totalFaktur + $ppn;
        $pembayaranPertama = (float) ($data['pembayaran_pertama'] ?? 0);
        
        $kombinasiBatch = collect($data['items'])
        ->map(fn ($item) => $item['barang_id'] . '|' . $item['no_batch'])
        ->duplicates();
        if ($kombinasiBatch->isNotEmpty()) {
            throw ValidationException::withMessages(['items' => 'Barang dan nomor batch yang sama tidak boleh dimasukkan lebih dari satu kali dalam satu faktur.',
        ]);}

        if ($pembayaranPertama > $totalTagihan) {
            throw ValidationException::withMessages(['pembayaran_pertama' => 'Pembayaran pertama tidak boleh melebihi total tagihan.']);
        }
        if ($pembayaranPertama < $totalTagihan && empty($data['jatuh_tempo'])) {throw ValidationException::withMessages([
            'jatuh_tempo' => 'Jatuh tempo wajib diisi jika pembayaran belum lunas.',
        ]);
    }

        DB::transaction(function () use ($data, $request, $supplier, $pembayaranPertama, $totalTagihan, $ppn) {
            
            $penerimaan = Penerimaan::create([
                'user_id' => $request->user()->id,
                'supplier_id' => $data['supplier_id'],
                'telepon_supplier' => $supplier->telepon,
                'keterangan' => $data['keterangan'] ?? null,
                'tanggal' => $data['tanggal'],
                'tanggal_faktur' => $data['tanggal_faktur'],
                'no_faktur' => $data['no_faktur'],
                'ppn' => $ppn,
                'lunas' => $pembayaranPertama >= $totalTagihan,
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            ]);

            // Akumulasi target pesanan per barang agar mendukung barang sama beda batch dalam 1 faktur
            $pesananPerBarang = [];
            foreach ($data['items'] as $item) {
                $bId = (int) $item['barang_id'];
                $pesananPerBarang[$bId] = ($pesananPerBarang[$bId] ?? 0) + (int) $item['jumlah_dipesan'];
            }

            $detailPesananMap = [];
            foreach ($pesananPerBarang as $bId => $totalDipesan) {
                $detailPesananMap[$bId] = DetailPesananPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $bId,
                    'jumlah_dipesan' => $totalDipesan,
                ]);
            }

            foreach ($data['items'] as $item) {
                $detailPenerimaan = DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $item['barang_id'],
                    'no_batch' => $item['no_batch'],
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => $item['harga_jual'],
                    'expired_date' => $item['expired_date'],
                    'no_rak' => $item['no_rak'],
                    'jumlah' => $item['jumlah_diterima'],
                    'stok' => $item['jumlah_diterima'],
                    'aktif' => true,
                ]);

                $detailPesanan = $detailPesananMap[(int) $item['barang_id']];

                if ((int) $item['jumlah_diterima'] > 0) {
                    RiwayatPenerimaan::create([
                        'penerimaan_id' => $penerimaan->id,
                        'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                        'detail_penerimaan_id' => $detailPenerimaan->id,
                        'jenis' => 'penerimaan',
                        'jumlah' => $item['jumlah_diterima'],
                        'tanggal' => $data['tanggal'],
                        'keterangan' => 'Penerimaan awal',
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            if ($pembayaranPertama > 0) {
                
                PembayaranPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'user_id' => $request->user()->id,
                    'tanggal_bayar' => $data['tanggal'],
                    'jumlah' => $pembayaranPertama,
                    'keterangan' => 'Pembayaran pertama',
                ]);
            }

            \App\Models\ActivityLog::log(
                'Penerimaan Barang',
                "Faktur: {$penerimaan->no_faktur}, Supplier: {$supplier->nama}, Total: Rp " . number_format($totalTagihan, 2),
                \App\Models\ActivityLog::CATEGORY_INVENTARIS
            );
        });

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil disimpan.');
    }

    public function show(Penerimaan $penerimaan)
    {
        $penerimaan->load([
            'user',
            'supplier',
            'detail.barang.pabrik',
            'detail.barang.satuan',
            'detailPesanan.barang.satuan',
            'detailPesanan.barang.pabrik',
            'detailPesanan.riwayatPenerimaan.detailPenerimaan',
            'detailPesanan.riwayatPenerimaan.user',
            'riwayatPenerimaan.detailPenerimaan',
            'riwayatPenerimaan.user',
            'pembayaran.user'
        ]);

        return view('penerimaan.show', compact('penerimaan'));
    }

    public function edit(Penerimaan $penerimaan)
    {
        if (!$penerimaan->canBeEdited()) {
            $pesan = $penerimaan->alasanTidakBisaDiedit() ?? 'Penerimaan tidak dapat diedit karena sudah memiliki transaksi lanjutan.';
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => $pesan], 403);
            }
            abort(403, $pesan);
        }

        $penerimaan->load([
            'supplier',
            'detail.barang.pabrik',
            'detail.barang.satuan',
            'detail.detailPenjualan',
            'detail.rusak',
            'detailPesanan',
            'riwayatPenerimaan',
            'pembayaran',
        ]);

        $suppliers = Supplier::orderBy('nama')->get();

        $barangs = Barang::with(['pabrik', 'satuan'])
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();

        return view('penerimaan.edit', compact(
            'penerimaan',
            'suppliers',
            'barangs'
        ));
    }

    public function update(Request $request, Penerimaan $penerimaan)
    {
        if (!$penerimaan->canBeEdited()) {
            $pesan = $penerimaan->alasanTidakBisaDiedit() ?? 'Penerimaan tidak dapat diedit karena sudah memiliki transaksi lanjutan.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $pesan,
                    'errors' => ['faktur' => [$pesan]],
                ], 403);
            }
            abort(403, $pesan);
        }

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
            'tanggal_faktur' => 'required|date',
            'no_faktur' => [
                'required',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('penerimaans', 'no_faktur')->ignore($penerimaan->id)->whereNull('deleted_at'),
            ],
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal',
            'pembayaran_pertama' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.detail_id' => 'nullable|integer',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.no_batch' => 'required|string|max:100',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.expired_date' => 'required|date',
            'items.*.no_rak' => 'required|string|max:50',
            'items.*.jumlah_dipesan' => 'required|integer|min:0',
            'items.*.jumlah_diterima' => 'required|integer|min:0',
        ]);

        $itemErrors = [];
        foreach ($data['items'] as $index => $item) {
            if (!empty($item['expired_date']) && !empty($data['tanggal'])) {
                if ($item['expired_date'] < $data['tanggal']) {
                    $itemErrors["items.$index.expired_date"] = [
                        'Tanggal expired tidak boleh sebelum tanggal penerimaan.',
                    ];
                }
            }
            if (isset($item['harga_jual']) && isset($item['harga_beli'])) {
                if ((float) $item['harga_jual'] < (float) $item['harga_beli']) {
                    $itemErrors["items.$index.harga_jual"] = [
                        'Harga jual tidak boleh lebih kecil dari harga beli.',
                    ];
                }
            }
        }
        if (!empty($itemErrors)) {
            throw ValidationException::withMessages($itemErrors);
        }

        // Total jumlah diterima per barang tidak boleh melebihi total dipesan per barang
        $dipesanPerBarang = [];
        $diterimaPerBarang = [];
        foreach ($data['items'] as $item) {
            $bId = (int) $item['barang_id'];
            $dipesanPerBarang[$bId] = ($dipesanPerBarang[$bId] ?? 0) + (int) $item['jumlah_dipesan'];
            $diterimaPerBarang[$bId] = ($diterimaPerBarang[$bId] ?? 0) + (int) $item['jumlah_diterima'];
        }

        foreach ($diterimaPerBarang as $bId => $totalDiterima) {
            $totalDipesan = $dipesanPerBarang[$bId] ?? 0;
            if ($totalDiterima > $totalDipesan) {
                $barangNama = Barang::find($bId)?->nama ?? 'Barang';
                throw ValidationException::withMessages([
                    'items' => "Total jumlah diterima untuk [{$barangNama}] ({$totalDiterima}) tidak boleh melebihi total jumlah dipesan ({$totalDipesan}).",
                ]);
            }
        }

        $kombinasiBatch = collect($data['items'])
            ->map(fn ($item) => $item['barang_id'] . '|' . $item['no_batch'])
            ->duplicates();

        if ($kombinasiBatch->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Barang dan nomor batch yang sama tidak boleh dimasukkan lebih dari satu kali dalam satu faktur.',
            ]);
        }

        $totalFaktur = collect($data['items'])->sum(fn ($item) => (float) $item['harga_beli'] * (int) $item['jumlah_diterima']);
        $ppn = round($totalFaktur * 0.11, 2);
        $totalTagihan = $totalFaktur + $ppn;
        $pembayaranPertama = (float) ($data['pembayaran_pertama'] ?? 0);

        if ($pembayaranPertama > $totalTagihan) {
            throw ValidationException::withMessages([
                'pembayaran_pertama' => 'Pembayaran pertama tidak boleh melebihi total tagihan.',
            ]);
        }

        if ($pembayaranPertama < $totalTagihan && empty($data['jatuh_tempo'])) {
            throw ValidationException::withMessages([
                'jatuh_tempo' => 'Jatuh tempo wajib diisi jika pembayaran belum lunas.',
            ]);
        }

        DB::transaction(function () use ($data, $request, $penerimaan, $dipesanPerBarang, $totalTagihan, $ppn, $pembayaranPertama) {
            $supplier = Supplier::findOrFail($data['supplier_id']);

            $penerimaan->update([
                'supplier_id' => $data['supplier_id'],
                'telepon_supplier' => $supplier->telepon,
                'keterangan' => $data['keterangan'] ?? null,
                'tanggal' => $data['tanggal'],
                'tanggal_faktur' => $data['tanggal_faktur'],
                'no_faktur' => $data['no_faktur'],
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
                'ppn' => $ppn,
                'lunas' => $pembayaranPertama >= $totalTagihan,
            ]);

            // Sinkronisasi pembayaran pertama/utama
            $pembayaranPertamaRecord = $penerimaan->pembayaran()
                ->where(function ($q) {
                    $q->where('keterangan', 'Pembayaran pertama')
                        ->orWhereNull('keterangan');
                })
                ->first();

            if ($pembayaranPertama > 0) {
                if ($pembayaranPertamaRecord) {
                    $pembayaranPertamaRecord->update([
                        'jumlah' => $pembayaranPertama,
                        'tanggal_bayar' => $data['tanggal'],
                        'keterangan' => 'Pembayaran pertama',
                    ]);
                } else {
                    PembayaranPenerimaan::create([
                        'penerimaan_id' => $penerimaan->id,
                        'user_id' => $request->user()?->id ?? $penerimaan->user_id ?? auth()->id() ?? 1,
                        'tanggal_bayar' => $data['tanggal'],
                        'jumlah' => $pembayaranPertama,
                        'keterangan' => 'Pembayaran pertama',
                    ]);
                }
            } else {
                if ($pembayaranPertamaRecord) {
                    $pembayaranPertamaRecord->delete();
                }
            }

            $existingDetails = $penerimaan->detail()
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $submittedDetailIds = collect($data['items'])
                ->pluck('detail_id')
                ->filter()
                ->map(fn ($id) => (int) $id);

            // 1. Tangani baris yang dihapus oleh user
            $detailsToDelete = $existingDetails->except($submittedDetailIds->all());
            foreach ($detailsToDelete as $detail) {
                $sudahDipakai = $detail->detailPenjualan()->exists() || $detail->rusak()->exists();
                if ($sudahDipakai) {
                    $barangNama = $detail->barang->nama ?? 'Obat';
                    throw ValidationException::withMessages([
                        'items' => "Barang [{$barangNama}] sudah digunakan untuk transaksi penjualan atau tercatat rusak sehingga tidak boleh dihapus.",
                    ]);
                }

                // Hapus riwayat penerimaan awal terkait
                $penerimaan->riwayatPenerimaan()
                    ->where('detail_penerimaan_id', $detail->id)
                    ->delete();

                // Set stok ke 0 dan hapus detail
                $detail->update(['stok' => 0, 'aktif' => false]);
                $detail->delete();
            }

            // 2. Sinkronisasi DetailPesananPenerimaan per barang
            $detailPesananMap = [];
            foreach ($dipesanPerBarang as $bId => $totalDipesan) {
                $dp = DetailPesananPenerimaan::firstOrNew([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $bId,
                ]);
                $dp->jumlah_dipesan = $totalDipesan;
                $dp->save();
                $detailPesananMap[$bId] = $dp;
            }

            // Hapus pesanan penerimaan untuk barang yang sudah tidak ada di items
            DetailPesananPenerimaan::where('penerimaan_id', $penerimaan->id)
                ->whereNotIn('barang_id', array_keys($dipesanPerBarang))
                ->delete();

            // 3. Update atau create item detail
            foreach ($data['items'] as $item) {
                $detailId = isset($item['detail_id']) ? (int) $item['detail_id'] : null;

                if ($detailId && $existingDetails->has($detailId)) {
                    $detail = $existingDetails->get($detailId);
                    $sudahDipakai = $detail->detailPenjualan()->exists() || $detail->rusak()->exists();
                    $terpakai = (int) $detail->jumlah - (int) $detail->stok;

                    if ($sudahDipakai || $terpakai > 0) {
                        $isBarangChanged = (int) $item['barang_id'] !== (int) $detail->barang_id;
                        $isBatchChanged = (string) $item['no_batch'] !== (string) $detail->no_batch;

                        if ($isBarangChanged || $isBatchChanged) {
                            $barangNama = $detail->barang->nama ?? 'Obat';
                            throw ValidationException::withMessages([
                                'items' => "Barang [{$barangNama}] dengan nomor batch [{$detail->no_batch}] sudah digunakan dalam transaksi sehingga jenis barang dan nomor batch tidak boleh diubah.",
                            ]);
                        }

                        if ((int) $item['jumlah_diterima'] < $terpakai) {
                            $barangNama = $detail->barang->nama ?? 'Obat';
                            throw ValidationException::withMessages([
                                'items' => "Jumlah diterima untuk [{$barangNama}] tidak boleh lebih kecil dari jumlah yang sudah terjual/terpakai ({$terpakai}).",
                            ]);
                        }

                        $newStok = (int) $item['jumlah_diterima'] - $terpakai;
                    } else {
                        // Belum pernah dipakai sama sekali, stok mengikuti jumlah diterima baru
                        $newStok = (int) $item['jumlah_diterima'];
                    }

                    $detail->update([
                        'barang_id' => $item['barang_id'],
                        'no_batch' => $item['no_batch'],
                        'harga_beli' => $item['harga_beli'],
                        'harga_jual' => $item['harga_jual'],
                        'expired_date' => $item['expired_date'],
                        'no_rak' => $item['no_rak'],
                        'jumlah' => $item['jumlah_diterima'],
                        'stok' => $newStok,
                        'aktif' => true,
                    ]);

                    $detailPesanan = $detailPesananMap[(int) $item['barang_id']];

                    // Update atau buat RiwayatPenerimaan awal
                    $riwayat = RiwayatPenerimaan::where('penerimaan_id', $penerimaan->id)
                        ->where('detail_penerimaan_id', $detail->id)
                        ->first();

                    if ($riwayat) {
                        if ((int) $item['jumlah_diterima'] > 0) {
                            $riwayat->update([
                                'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                                'jumlah' => $item['jumlah_diterima'],
                                'tanggal' => $data['tanggal'],
                                'keterangan' => 'Penerimaan awal',
                            ]);
                        } else {
                            $riwayat->delete();
                        }
                    } elseif ((int) $item['jumlah_diterima'] > 0) {
                        RiwayatPenerimaan::create([
                            'penerimaan_id' => $penerimaan->id,
                            'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                            'detail_penerimaan_id' => $detail->id,
                            'jenis' => 'penerimaan',
                            'jumlah' => $item['jumlah_diterima'],
                            'tanggal' => $data['tanggal'],
                            'keterangan' => 'Penerimaan awal',
                            'user_id' => $request->user()?->id ?? $penerimaan->user_id ?? auth()->id() ?? 1,
                        ]);
                    }

                } elseif ($detailId && !$existingDetails->has($detailId)) {
                    throw ValidationException::withMessages([
                        'items' => 'Detail penerimaan tidak valid.',
                    ]);
                } else {
                    // Item baru ditambahkan saat edit
                    $detailPenerimaan = $penerimaan->detail()->create([
                        'barang_id' => $item['barang_id'],
                        'no_batch' => $item['no_batch'],
                        'harga_beli' => $item['harga_beli'],
                        'harga_jual' => $item['harga_jual'],
                        'expired_date' => $item['expired_date'],
                        'no_rak' => $item['no_rak'],
                        'jumlah' => $item['jumlah_diterima'],
                        'stok' => $item['jumlah_diterima'],
                        'aktif' => true,
                    ]);

                    $detailPesanan = $detailPesananMap[(int) $item['barang_id']];

                    if ((int) $item['jumlah_diterima'] > 0) {
                        RiwayatPenerimaan::create([
                            'penerimaan_id' => $penerimaan->id,
                            'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                            'detail_penerimaan_id' => $detailPenerimaan->id,
                            'jenis' => 'penerimaan',
                            'jumlah' => $item['jumlah_diterima'],
                            'tanggal' => $data['tanggal'],
                            'keterangan' => 'Penerimaan awal',
                            'user_id' => $request->user()?->id ?? $penerimaan->user_id ?? auth()->id() ?? 1,
                        ]);
                    }
                }
            }

            // Hitung ulang total faktur, PPN, dan tagihan
            $totalFaktur = (float) $penerimaan->detail()->sum(DB::raw('harga_beli * jumlah'));
            $ppn = round($totalFaktur * 0.11, 2);
            $totalTagihan = $totalFaktur + $ppn;
            $totalDibayar = $penerimaan->totalDibayar();

            if ($totalDibayar < $totalTagihan && empty($data['jatuh_tempo'])) {
                throw ValidationException::withMessages([
                    'jatuh_tempo' => 'Jatuh tempo wajib diisi jika pembayaran belum lunas.',
                ]);
            }

            $penerimaan->update([
                'ppn' => $ppn,
                'lunas' => $totalDibayar >= $totalTagihan,
            ]);

            \App\Models\ActivityLog::log(
                'Edit Penerimaan Barang',
                "Faktur: {$penerimaan->no_faktur}, Supplier: {$supplier->nama}, Total: Rp " . number_format($totalTagihan, 2),
                \App\Models\ActivityLog::CATEGORY_INVENTARIS
            );
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('penerimaan.index')
            ->with('success', 'Penerimaan berhasil diperbarui.');
    }


    public function paymentForm(Penerimaan $penerimaan)
    {
        $penerimaan->load(['supplier', 'pembayaran.user']);

        return view('penerimaan.payment', compact('penerimaan'));
    }

    public function paymentStore(Request $request, Penerimaan $penerimaan)
    {
        $data = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string',
        ]);

        $totalTagihan = $penerimaan->totalTagihan();
        $totalDibayar = $penerimaan->totalDibayar();
        $sisa = max(0, $totalTagihan - $totalDibayar);
        if ((float) $data['jumlah'] > $sisa) {
            throw ValidationException::withMessages(['jumlah' => 'Pembayaran tidak boleh melebihi sisa tagihan.']);
        }

        DB::transaction(function () use ($data, $request, $penerimaan, $totalTagihan, $totalDibayar) {
            PembayaranPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'user_id' => $request->user()->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
            $penerimaan->update(['lunas' => ($totalDibayar + (float) $data['jumlah']) >= $totalTagihan]);
        });

        return back()->with('success', 'Pembayaran penerimaan berhasil disimpan.');
    }

    public function susulanForm(Penerimaan $penerimaan)
    {
        abort_unless(
            $penerimaan->statusPenerimaan() === 'BELUM LENGKAP',
            404
        );

        $penerimaan->load([
            'supplier',
            'user',
            'detailPesanan.barang.satuan',
            'detailPesanan.barang.pabrik',
            'detailPesanan.riwayatPenerimaan',
            'detail.barang',
        ]);

        $detailPesanan = $penerimaan->detailPesanan
            ->filter(fn ($detail) => $detail->kekurangan() > 0)
            ->values();

        return view('penerimaan.susulan', compact(
            'penerimaan',
            'detailPesanan'
        ));
    }

    public function susulanStore(Request $request, Penerimaan $penerimaan)
    {
        abort_unless(
            $penerimaan->statusPenerimaan() === 'BELUM LENGKAP',
            404
        );

        $data = $request->validate([
            'tanggal_terima' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'jumlah_susulan' => ['required', 'array'],
            'jumlah_susulan.*' => ['required', 'integer', 'min:0'],

            'jumlah_pembatalan' => ['required', 'array'],
            'jumlah_pembatalan.*' => ['required', 'integer', 'min:0'],

            'detail' => ['nullable', 'array'],
            'detail.*.no_batch' => ['nullable', 'string', 'max:100'],
            'detail.*.expired_date' => ['nullable', 'date'],
            'detail.*.harga_beli' => ['nullable', 'numeric', 'min:0'],
            'detail.*.harga_jual' => ['nullable', 'numeric', 'min:0'],
            'detail.*.no_rak' => ['nullable', 'string', 'max:50'],


        ]);

        $jumlahSusulanTotal = collect($data['jumlah_susulan'])
            ->map(fn ($jumlah) => (int) $jumlah)
            ->sum();

        $jumlahPembatalanTotal = collect($data['jumlah_pembatalan'])
            ->map(fn ($jumlah) => (int) $jumlah)
            ->sum();

        if ($jumlahSusulanTotal + $jumlahPembatalanTotal <= 0) {
            throw ValidationException::withMessages([
                'jumlah_susulan' => 'Minimal satu barang harus memiliki jumlah susulan atau pembatalan lebih dari 0.',
            ]);
        }

        foreach ($data['jumlah_susulan'] as $detailPesananId => $jumlahSusulan) {
            $jumlahPembatalan = (int) ($data['jumlah_pembatalan'][$detailPesananId] ?? 0);

            $detail = $penerimaan->detailPesanan
                ->firstWhere('id', $detailPesananId);

            if (!$detail) {
                abort(422, 'Detail barang tidak ditemukan dalam penerimaan ini.');
            }

            if (
                (int) $jumlahSusulan + $jumlahPembatalan
                > $detail->kekurangan()
            ) {
                abort(
                    422,
                    "Jumlah susulan dan pembatalan untuk {$detail->barang->nama} melebihi kekurangan."
                );
            }
        }

        $penerimaan->load('detailPesanan');
        foreach ($request->jumlah_susulan as $detailPesananId => $jumlahSusulan) {
            $detail = $penerimaan->detailPesanan
                ->firstWhere('id', $detailPesananId);

            if (!$detail) {
                abort(422, 'Detail barang tidak ditemukan dalam penerimaan ini.');
            }

            if ((int) $jumlahSusulan > $detail->kekurangan()) {
                abort(
                    422,
                    "Jumlah susulan untuk {$detail->barang->nama} melebihi kekurangan."
                );
            }
        }

        foreach ($data['jumlah_susulan'] as $detailPesananId => $jumlahSusulan) {
            if ((int) $jumlahSusulan <= 0) {
                continue;
            }

            $detailData = $data['detail'][$detailPesananId] ?? [];

            if (
                empty($detailData['no_batch']) ||
                empty($detailData['expired_date']) ||
                $detailData['harga_beli'] === null ||
                $detailData['harga_jual'] === null ||
                empty($detailData['no_rak'])
            ) {
                throw ValidationException::withMessages([
                    'detail' => 'Data batch, expired date, harga beli, harga jual, dan no. rak wajib diisi untuk barang yang menerima susulan.',
                ]);
            }

            if (
                (float) $detailData['harga_jual'] < (float) $detailData['harga_beli']
            ) {
                throw ValidationException::withMessages([
                    'detail' => 'Harga jual tidak boleh lebih kecil dari harga beli.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $penerimaan, $data) {

            $penerimaan->load('detailPesanan');

            foreach ($data['jumlah_susulan'] as $detailPesananId => $jumlahSusulan) {

                $jumlahSusulan = (int) $jumlahSusulan;

                if ($jumlahSusulan <= 0) {
                    continue;
                }

                $detailPesanan = $penerimaan->detailPesanan
                    ->firstWhere('id', $detailPesananId);

                if (!$detailPesanan) {
                    throw ValidationException::withMessages([
                        'jumlah_susulan' => 'Detail barang tidak ditemukan dalam penerimaan ini.',
                    ]);
                }

                $detailData = $data['detail'][$detailPesananId] ?? null;

                if (!$detailData) {
                    throw ValidationException::withMessages([
                        'detail' => 'Data detail penerimaan susulan tidak lengkap.',
                    ]);
                }

                $detailPenerimaan = DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $detailPesanan->barang_id,
                    'no_batch' => $detailData['no_batch'],
                    'harga_beli' => $detailData['harga_beli'],
                    'harga_jual' => $detailData['harga_jual'],
                    'expired_date' => $detailData['expired_date'],
                    'no_rak' => $detailData['no_rak'],
                    'jumlah' => $jumlahSusulan,
                    'stok' => $jumlahSusulan,
                    'aktif' => true,
                ]);

                RiwayatPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                    'detail_penerimaan_id' => $detailPenerimaan->id,
                    'jenis' => 'penerimaan',
                    'jumlah' => $jumlahSusulan,
                    'tanggal' => $data['tanggal_terima'],
                    'keterangan' => $data['keterangan'] ?: 'Penerimaan susulan',
                    'user_id' => $request->user()->id,
                ]);
            }

            foreach ($data['jumlah_pembatalan'] as $detailPesananId => $jumlahPembatalan) {

                $jumlahPembatalan = (int) $jumlahPembatalan;

                if ($jumlahPembatalan <= 0) {
                    continue;
                }

                $detailPesanan = $penerimaan->detailPesanan
                    ->firstWhere('id', $detailPesananId);

                if (!$detailPesanan) {
                    throw ValidationException::withMessages([
                        'jumlah_pembatalan' => 'Detail barang tidak ditemukan dalam penerimaan ini.',
                    ]);
                }

                RiwayatPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'detail_pesanan_penerimaan_id' => $detailPesanan->id,
                    'detail_penerimaan_id' => null,
                    'jenis' => 'pembatalan',
                    'jumlah' => $jumlahPembatalan,
                    'tanggal' => $data['tanggal_terima'],
                    'keterangan' => $data['keterangan'] ?: 'Pembatalan kekurangan',
                    'user_id' => $request->user()->id,
                ]);
            }

            // Hitung ulang total faktur (DPP) fisik setelah barang susulan masuk
            $totalFakturBaru = (float) $penerimaan->detail()->sum(DB::raw('harga_beli * jumlah'));
            $ppnBaru = $totalFakturBaru * 0.11;
            $totalTagihanBaru = $totalFakturBaru + $ppnBaru;
            $totalDibayar = $penerimaan->totalDibayar();

            $penerimaan->update([
                'ppn' => $ppnBaru,
                'lunas' => $totalDibayar >= $totalTagihanBaru,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Penerimaan susulan berhasil disimpan.',
        ]);
    }

    public function print(Penerimaan $penerimaan)
    {
        $penerimaan->load([
            'user',
            'supplier',
            'detail.barang.satuan',
            'detail.barang.pabrik',
            'detailPesanan.barang.satuan',
            'detailPesanan.barang.pabrik',
            'detailPesanan.riwayatPenerimaan',
            'riwayatPenerimaan.detailPesanan.barang',
            'riwayatPenerimaan.detailPenerimaan.barang',
            'riwayatPenerimaan.user',
            'pembayaran.user',
        ]);

        return view('penerimaan.print', compact('penerimaan'));
    }

    public function destroy(Penerimaan $penerimaan)
    {
        if ($penerimaan->sisaTagihan() > 0) {
            return back()->with('error', 'Penerimaan ini tidak bisa dihapus karena masih memiliki sisa tagihan.');
        }
        $sudahDipakai = $penerimaan->detail()
            ->where(function ($query) {
                $query->whereHas('detailPenjualan')->orWhereHas('rusak');
            })->exists();

        if ($sudahDipakai) {
            return back()->with('error', 'Penerimaan ini tidak bisa dihapus karena sudah ada barang yang terjual dari batch ini.');
        }

        DB::transaction(function () use ($penerimaan) {
            $penerimaan->detail()->delete();
            $penerimaan->delete();
        });

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan berhasil dihapus.');
    }
}
