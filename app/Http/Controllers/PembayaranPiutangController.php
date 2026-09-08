<?php

namespace App\Http\Controllers;

use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranPiutangController extends Controller
{

    public function pelanggan(Pelanggan $pelanggan)
{
    $penjualans = $pelanggan->penjualan()
        ->where('metode_pembayaran', 'piutang')
        ->with('pembayaranPiutang')
        ->latest('tanggal')
        ->get()
        ->map(function ($penjualan) {
            $totalDibayar = $penjualan->pembayaranPiutang->sum('jumlah');
            $sisaPiutang = max(0, $penjualan->total - $totalDibayar);

            return [
                'id' => $penjualan->id,
                'no_faktur' => $penjualan->no_faktur,
                'tanggal' => $penjualan->tanggal?->format('Y-m-d'),
                'total' => (float) $penjualan->total,
                'total_dibayar' => (float) $totalDibayar,
                'sisa_piutang' => (float) $sisaPiutang,
            ];
        })
        ->filter(fn ($penjualan) => $penjualan['sisa_piutang'] > 0)
        ->values();

    return response()->json([
        'pelanggan' => [
            'id' => $pelanggan->id,
            'member_id' => $pelanggan->member_id,
            'nama' => $pelanggan->nama,
        ],
        'penjualans' => $penjualans,
    ]);
}
    public function form(Penjualan $penjualan)
    {
        $penjualan->load([
            'pelanggan',
            'pembayaranPiutang' => function ($query) {
                $query->latest('tanggal_bayar');
            },
        ]);

        $totalDibayar = $penjualan->pembayaranPiutang->sum('jumlah');
        $sisaPiutang = max(0, $penjualan->total - $totalDibayar);

        return response()->json([
            'penjualan' => $penjualan,
            'total_dibayar' => $totalDibayar,
            'sisa_piutang' => $sisaPiutang,
            'riwayat' => $penjualan->pembayaranPiutang,
        ]);
    }

    public function store(Request $request, Penjualan $penjualan)
    {
        $data = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah' => 'required|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($data, $penjualan, $request) {
            $penjualan->load('pelanggan');

            if (!$penjualan->pelanggan) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Transaksi ini tidak memiliki pelanggan.',
                ]);
            }

            if (!$penjualan->pelanggan->is_member) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Pembayaran piutang hanya dapat dilakukan untuk member.',
                ]);
            }

            if (!($penjualan->pelanggan->member_aktif ?? false)) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Pembayaran piutang hanya dapat dilakukan untuk member yang aktif.',
                ]);
            }

            if ($penjualan->metode_pembayaran !== 'piutang') {
                throw ValidationException::withMessages([
                    'jumlah' => 'Transaksi ini bukan transaksi piutang.',
                ]);
            }

            $totalDibayar = PembayaranPiutang::where(
                'penjualan_id',
                $penjualan->id
            )->sum('jumlah');

            $sisaPiutang = $penjualan->total - $totalDibayar;

            if ($sisaPiutang <= 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Piutang invoice ini sudah lunas.',
                ]);
            }

            if ((float) $data['jumlah'] > (float) $sisaPiutang) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah pembayaran tidak boleh melebihi sisa piutang.',
                ]);
            }

            PembayaranPiutang::create([
                'penjualan_id' => $penjualan->id,
                'pelanggan_id' => $penjualan->pelanggan_id,
                'user_id' => $request->user()->id,
                'tanggal_bayar' => $data['tanggal_bayar'],
                'jumlah' => $data['jumlah'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            $penjualan->pelanggan->decrement(
                'saldo_piutang',
                $data['jumlah']
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran piutang berhasil disimpan.',
        ]);
    }
}