<?php

namespace App\Http\Controllers;

use App\Models\Pabrik;
use Illuminate\Http\Request;

class PabrikController extends Controller
{
    public function index(Request $request)
    {
        $query = Pabrik::query();
        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }
        $pabriks = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('pabrik.index', compact('pabriks'));
    }

    public function create()
    {
        return view('pabrik.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Pabrik::create($data);

        return redirect()->route('pabrik.index')->with('success', 'Pabrik berhasil ditambahkan.');
    }

    public function edit(Pabrik $pabrik)
    {
        return view('pabrik.edit', compact('pabrik'));
    }

    public function update(Request $request, Pabrik $pabrik)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $pabrik->update($data);

        return redirect()->route('pabrik.index')->with('success', 'Pabrik berhasil diperbarui.');
    }

    public function destroy(Pabrik $pabrik)
    {
        if ($pabrik->barang()->exists()) {
            return back()->with('error', 'Pabrik tidak bisa dihapus karena masih dipakai oleh barang.');
        }

        $pabrik->delete();

        return redirect()->route('pabrik.index')->with('success', 'Pabrik berhasil dihapus.');
    }
}
