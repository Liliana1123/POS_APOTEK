<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('telepon', 'like', '%' . $request->cari . '%')
                  ->orWhere('alamat', 'like', '%' . $request->cari . '%');
            });
        }
        $suppliers = $query->orderBy('id')->paginate(15)->withQueryString();
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Supplier berhasil ditambahkan.'], 201);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Supplier berhasil diperbarui.']);
        }

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->penerimaan()->exists()) {
            return back()->with('error', 'Supplier tidak bisa dihapus karena punya riwayat penerimaan barang.');
        }

        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
