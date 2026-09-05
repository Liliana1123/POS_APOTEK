<?php

namespace App\Http\Controllers;

use App\Models\DetailPenerimaan;
use App\Models\Rusak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RusakController extends Controller
{
    public function index(Request $request)
    {
        $query = Rusak::with('detailPenerimaan.barang');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('cari')) {
            $query->whereHas('detailPenerimaan.barang', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%');
            });
        }

        $rusaks = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('rusak.index', compact('rusaks'));
    }

    public function create()
    {
        $batches = DetailPenerimaan::with('barang')
            ->where('aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('expired_date')
            ->get();

        return view('rusak.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'detail_penerimaan_id' => 'required|exists:detail_penerimaans,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $batch = DetailPenerimaan::lockForUpdate()->findOrFail($data['detail_penerimaan_id']);

            if ($data['jumlah'] > $batch->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah rusak melebihi stok batch ini (sisa stok: ' . $batch->stok . ').',
                ]);
            }

            Rusak::create($data);

            $batch->decrement('stok', $data['jumlah']);
            $batch->refresh();
            if ($batch->stok <= 0) {
                $batch->update(['aktif' => false]);
            }
        });

        return redirect()->route('rusak.index')->with('success', 'Barang rusak berhasil dicatat, stok otomatis dikurangi.');
    }
}
