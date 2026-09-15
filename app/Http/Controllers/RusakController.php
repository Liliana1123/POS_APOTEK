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

    public function show(Rusak $rusak)
    {
        $rusak->load('detailPenerimaan.barang');

        return view('rusak.show', compact('rusak'));
    }

    public function edit(Rusak $rusak)
    {
        $rusak->load('detailPenerimaan.barang');

        return response()->json($rusak);
    }

    public function update(Request $request, Rusak $rusak)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $rusak) {
            $rusak->load('detailPenerimaan');

            $batch = DetailPenerimaan::lockForUpdate()->findOrFail(
                $rusak->detail_penerimaan_id
            );

            $stokSetelahMengembalikanLama = $batch->stok + $rusak->jumlah;

            if ($data['jumlah'] > $stokSetelahMengembalikanLama) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah rusak baru melebihi stok yang tersedia untuk dikoreksi (stok maksimal: ' . $stokSetelahMengembalikanLama . ').',
                ]);
            }

            $batch->stok = $stokSetelahMengembalikanLama - $data['jumlah'];
            $batch->aktif = $batch->stok > 0;
            $batch->save();

            $rusak->update($data);
        });

        return redirect()
            ->route('rusak.index')
            ->with('success', 'Data barang rusak berhasil diperbarui dan stok telah disesuaikan.');
    }

    public function destroy(Rusak $rusak)
    {
        DB::transaction(function () use ($rusak) {
            $batch = DetailPenerimaan::lockForUpdate()->findOrFail(
                $rusak->detail_penerimaan_id
            );

            $batch->stok += $rusak->jumlah;
            $batch->aktif = true;
            $batch->save();

            $rusak->delete();
        });

        return redirect()
            ->route('rusak.index')
            ->with('success', 'Data barang rusak berhasil dihapus dan stok telah dikembalikan.');
    }

    public function print(Rusak $rusak)
    {
        $rusak->load('detailPenerimaan.barang');

        return view('rusak.print', compact('rusak'));
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
