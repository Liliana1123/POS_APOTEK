<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Penerimaan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $penerimaans = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('nama')->get();

        return view('penerimaan.index', compact('penerimaans', 'suppliers'));
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
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:100|unique:penerimaans,no_faktur',
            'lunas' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.no_batch' => 'required|string|max:100',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.expired_date' => 'required|date|after_or_equal:tanggal',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data, $request) {
            $penerimaan = Penerimaan::create([
                'user_id' => $request->user()->id,
                'supplier_id' => $data['supplier_id'],
                'tanggal' => $data['tanggal'],
                'no_faktur' => $data['no_faktur'],
                'lunas' => $request->boolean('lunas'),
            ]);

            foreach ($data['items'] as $item) {
                DetailPenerimaan::create([
                    'penerimaan_id' => $penerimaan->id,
                    'barang_id' => $item['barang_id'],
                    'no_batch' => $item['no_batch'],
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => $item['harga_jual'],
                    'expired_date' => $item['expired_date'],
                    'jumlah' => $item['jumlah'],
                    'stok' => $item['jumlah'],
                    'aktif' => true,
                ]);
            }
        });

        return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil disimpan.');
    }

    public function show(Penerimaan $penerimaan)
    {
        $penerimaan->load(['user', 'supplier', 'detail.barang']);

        return view('penerimaan.show', compact('penerimaan'));
    }

    public function destroy(Penerimaan $penerimaan)
    {
        $sudahDipakai = $penerimaan->detail()
            ->whereHas('detailPenjualan')
            ->exists();

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
