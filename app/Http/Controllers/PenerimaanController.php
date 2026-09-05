<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
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
        $query = Penerimaan::with(['user', 'supplier']);

        if ($request->filled('cari')) {
            $query->where('no_faktur', 'like', '%' . $request->cari . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
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
            'no_faktur' => 'required|string|max:100|unique:penerimaans,no_faktur',
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal',
            'pembayaran_pertama' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.no_batch' => 'required|string|max:100',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.harga_jual' => 'required|numeric|min:0|gte:items.*.harga_beli',
            'items.*.expired_date' => 'required|date|after_or_equal:tanggal',
            'items.*.no_rak' => 'required|string|max:50',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $data['supplier_id'] = (int) $data['supplier_id'];
        $supplier = Supplier::findOrFail($data['supplier_id']);
        $totalFaktur = collect($data['items'])->sum(fn ($item) => (float) $item['harga_beli'] * (int) $item['jumlah']);
        $pembayaranPertama = (float) ($data['pembayaran_pertama'] ?? 0);
        
        $kombinasiBatch = collect($data['items'])
        ->map(fn ($item) => $item['barang_id'] . '|' . $item['no_batch'])
        ->duplicates();
        if ($kombinasiBatch->isNotEmpty()) {
            throw ValidationException::withMessages(['items' => 'Barang dan nomor batch yang sama tidak boleh dimasukkan lebih dari satu kali dalam satu faktur.',
        ]);}

        if ($pembayaranPertama > $totalFaktur) {
            throw ValidationException::withMessages(['pembayaran_pertama' => 'Pembayaran pertama tidak boleh melebihi total faktur.']);
        }
        if ($pembayaranPertama > 0 && empty($data['jatuh_tempo']) && $pembayaranPertama < $totalFaktur) {
            throw ValidationException::withMessages(['jatuh_tempo' => 'Jatuh tempo wajib diisi jika pembayaran belum lunas.']);
        }

        DB::transaction(function () use ($data, $request, $supplier, $pembayaranPertama) {
            $penerimaan = Penerimaan::create([
                'user_id' => $request->user()->id,
                'supplier_id' => $data['supplier_id'],
                'telepon_supplier' => $supplier->telepon,
                'keterangan' => $data['keterangan'] ?? null,
                'tanggal' => $data['tanggal'],
                'no_faktur' => $data['no_faktur'],
                'lunas' => $pembayaranPertama >= collect($data['items'])->sum(fn ($item) => (float) $item['harga_beli'] * (int) $item['jumlah']),
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $item['barang_id'],
                    'no_batch' => $item['no_batch'],
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => $item['harga_jual'],
                    'expired_date' => $item['expired_date'],
                    'no_rak' => $item['no_rak'],
                    'jumlah' => $item['jumlah'],
                    'stok' => $item['jumlah'],
                    'aktif' => true,
                ]);
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
        });

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil disimpan.');
    }

    public function show(Penerimaan $penerimaan)
    {
        $penerimaan->load(['user', 'supplier', 'detail.barang.pabrik', 'detail.barang.satuan', 'pembayaran.user']);

        return view('penerimaan.show', compact('penerimaan'));
    }

    public function edit(Penerimaan $penerimaan)
    {
        $penerimaan->load([
            'supplier',
            'detail.barang.pabrik',
            'detail.barang.satuan',
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
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'keterangan' => 'nullable|string',
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:100|unique:penerimaans,no_faktur,' . $penerimaan->id,
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal',

            'items' => 'required|array|min:1',
            'items.*.detail_id' => 'nullable|integer',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.no_batch' => 'required|string|max:100',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.harga_jual' => 'required|numeric|min:0|gte:items.*.harga_beli',
            'items.*.expired_date' => 'required|date|after_or_equal:tanggal',
            'items.*.no_rak' => 'required|string|max:50',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $kombinasiBatch = collect($data['items'])
            ->map(fn ($item) => $item['barang_id'] . '|' . $item['no_batch'])
            ->duplicates();

        if ($kombinasiBatch->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Barang dan nomor batch yang sama tidak boleh dimasukkan lebih dari satu kali dalam satu faktur.',
            ]);
        }

        DB::transaction(function () use ($data, $penerimaan) {
            $penerimaan->update([
                'supplier_id' => $data['supplier_id'],
                'telepon_supplier' => Supplier::findOrFail($data['supplier_id'])->telepon,
                'keterangan' => $data['keterangan'] ?? null,
                'tanggal' => $data['tanggal'],
                'no_faktur' => $data['no_faktur'],
                'jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            ]);

            $existingDetails = $penerimaan->detail()
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $submittedDetailIds = collect($data['items'])
                ->pluck('detail_id')
                ->filter()
                ->map(fn ($id) => (int) $id);

            foreach ($data['items'] as $item) {
                $detailId = isset($item['detail_id'])
                    ? (int) $item['detail_id']
                    : null;

                    if ($detailId && $existingDetails->has($detailId)) {
                        $detail = $existingDetails->get($detailId);

                        $sudahDipakai = $detail->detailPenjualan()->exists()
                            || $detail->rusak()->exists();

                        if ($sudahDipakai) {
                            throw ValidationException::withMessages([
                                'items' => 'Detail barang yang sudah digunakan untuk penjualan atau barang rusak tidak boleh diedit.',
                            ]);
                        }
                    }

                if ($detailId && !$existingDetails->has($detailId)) {
                    throw ValidationException::withMessages([
                        'items' => 'Detail penerimaan tidak valid.',
                    ]);
                }

                if ($detailId) {
                    $detail = $existingDetails->get($detailId);

                    $detail->update([
                        'barang_id' => $item['barang_id'],
                        'no_batch' => $item['no_batch'],
                        'harga_beli' => $item['harga_beli'],
                        'harga_jual' => $item['harga_jual'],
                        'expired_date' => $item['expired_date'],
                        'no_rak' => $item['no_rak'],
                        'jumlah' => $item['jumlah'],
                        'stok' => $item['jumlah'],
                        'aktif' => true,
                    ]);
                } else {
                    $penerimaan->detail()->create([
                        'barang_id' => $item['barang_id'],
                        'no_batch' => $item['no_batch'],
                        'harga_beli' => $item['harga_beli'],
                        'harga_jual' => $item['harga_jual'],
                        'expired_date' => $item['expired_date'],
                        'no_rak' => $item['no_rak'],
                        'jumlah' => $item['jumlah'],
                        'stok' => $item['jumlah'],
                        'aktif' => true,
                    ]);
                }
            }

            $existingDetails
                ->except($submittedDetailIds->all())
                ->each(function ($detail) {
                    $detail->delete();
                });

            $totalFaktur = (float) $penerimaan->detail()
                ->sum(DB::raw('harga_beli * jumlah'));

            $totalDibayar = $penerimaan->totalDibayar();

            if ($totalDibayar > $totalFaktur) {
                throw ValidationException::withMessages([
                    'items' => 'Perubahan tidak dapat disimpan karena total pembayaran sudah melebihi total faktur baru.',
                ]);
            }

            $penerimaan->update([
                'lunas' => $totalDibayar >= $totalFaktur,
            ]);
        });

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

        $totalFaktur = $penerimaan->totalFaktur();
        $totalDibayar = $penerimaan->totalDibayar();
        $sisa = max(0, $totalFaktur - $totalDibayar);
        if ((float) $data['jumlah'] > $sisa) {
            throw ValidationException::withMessages(['jumlah' => 'Pembayaran tidak boleh melebihi sisa tagihan.']);
        }

        DB::transaction(function () use ($data, $request, $penerimaan, $totalFaktur, $totalDibayar) {
            PembayaranPenerimaan::create([
                'penerimaan_id' => $penerimaan->id,
                'user_id' => $request->user()->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);
            $penerimaan->update(['lunas' => ($totalDibayar + (float) $data['jumlah']) >= $totalFaktur]);
        });

        return back()->with('success', 'Pembayaran penerimaan berhasil disimpan.');
    }

    public function print(Penerimaan $penerimaan)
    {
        $penerimaan->load(['user', 'supplier', 'detail.barang', 'pembayaran.user']);

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
