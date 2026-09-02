<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query();
        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }
        $satuans = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('satuan.index', compact('satuans'));
    }

    public function create()
    {
        return view('satuan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Satuan::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Satuan berhasil ditambahkan.'], 201);
        }

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $satuan->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Satuan berhasil diperbarui.']);
        }

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->barang()->exists()) {
            return back()->with('error', 'Satuan tidak bisa dihapus karena masih dipakai oleh barang.');
        }

        $satuan->delete();

        return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
