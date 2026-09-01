<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pabrik;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'satuan', 'pabrik']);

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('barcode', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif === '1');
        }

        if ($request->filled('butuh_resep')) {
            $query->where('butuh_resep', $request->butuh_resep === '1');
        }

        $barangs = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('barang.index', compact('barangs', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $satuans = Satuan::orderBy('nama')->get();
        $pabriks = Pabrik::orderBy('nama')->get();

        return view('barang.create', compact('kategoris', 'satuans', 'pabriks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'pabrik_id' => 'required|exists:pabriks,id',
            'barcode' => 'nullable|string|max:100|unique:barangs,barcode',
            'butuh_resep' => 'nullable|boolean',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $data['butuh_resep'] = $request->boolean('butuh_resep');

        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $satuans = Satuan::orderBy('nama')->get();
        $pabriks = Pabrik::orderBy('nama')->get();

        return view('barang.edit', compact('barang', 'kategoris', 'satuans', 'pabriks'));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan_id' => 'required|exists:satuans,id',
            'pabrik_id' => 'required|exists:pabriks,id',
            'barcode' => 'nullable|string|max:100|unique:barangs,barcode,' . $barang->id,
            'butuh_resep' => 'nullable|boolean',
            'stok_minimum' => 'required|integer|min:0',
            'aktif' => 'nullable|boolean',
        ]);

        $data['butuh_resep'] = $request->boolean('butuh_resep');
        $data['aktif'] = $request->boolean('aktif');

        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->detailPenerimaan()->exists()) {
            return back()->with('error', 'Barang tidak bisa dihapus karena punya riwayat penerimaan. Nonaktifkan saja lewat form edit.');
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
