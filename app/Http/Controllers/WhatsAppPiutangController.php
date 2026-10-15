<?php

namespace App\Http\Controllers;

use App\Models\InfoApotek;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class WhatsAppPiutangController extends Controller
{
    /**
     * Menampilkan tagihan yang memenuhi syarat pengingat WhatsApp.
     * Belum melakukan pengiriman pesan.
     */
    public function preview(Request $request, Pelanggan $pelanggan): JsonResponse
    {
        if (
            !$pelanggan->is_member ||
            !$pelanggan->member_aktif
        ) {
            return response()->json([
                'message' => 'Pelanggan bukan member aktif.',
            ], 422);
        }

        $today = Carbon::today();

        
        $tagihan = $pelanggan->penjualan()
            ->where('metode_pembayaran', 'piutang')
            ->with('pembayaranPiutang')
            ->get()
            ->sortBy(function ($penjualan) {
                $dueDate = $penjualan->due_date
                    ?? ($penjualan->tanggal
                        ? $penjualan->tanggal->copy()->addMonth()
                        : null);

                return $dueDate
                    ? $dueDate->format('Y-m-d')
                    : '9999-12-31';
            })

           
            ->map(function ($penjualan) use ($today) {
                $dueDate = $penjualan->due_date
                    ?? ($penjualan->tanggal
                        ? $penjualan->tanggal->copy()->addMonth()
                        : null);

                if (!$dueDate) {
                    return null;
                }


                $totalDibayar = (float) $penjualan
                    ->pembayaranPiutang
                    ->sum('jumlah');

                $totalFaktur = (float) $penjualan->total;
                $sisaPiutang = max(0, $totalFaktur - $totalDibayar);

                if ($sisaPiutang <= 0) {
                    return null;
                }

                $hariMenujuJatuhTempo = (int) $today->diffInDays(
                    $dueDate->copy()->startOfDay(),
                    false
                );

                // Pengingat H-3 sampai jatuh tempo, atau sudah terlambat.
                if ($hariMenujuJatuhTempo > 3) {
                    return null;
                }

                return [
                    'penjualan_id' => $penjualan->id,
                    'no_faktur' => $penjualan->no_faktur,
                    'total_faktur' => $totalFaktur,
                    'total_dibayar' => $totalDibayar,
                    'sisa_piutang' => $sisaPiutang,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'jatuh_tempo' => $dueDate->format('d-m-Y'),
                    'hari_menuju_jatuh_tempo' => $hariMenujuJatuhTempo,
                ];
            })
            ->filter()
            ->values();

        if ($tagihan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada tagihan yang memenuhi syarat pengingat.',
                'tagihan' => [],
            ]);
        }

        $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');

        return response()->json([
            'pelanggan' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
                'telepon' => $pelanggan->telepon,
            ],
            'nama_apotek' => $namaApotek,
            'tagihan' => $tagihan,
            'pengiriman_aktif' => false,
            'message' => 'Pratinjau tagihan berhasil. Pesan WhatsApp belum dikirim.',
        ]);
    }

    /**
     * Mengirim pesan pengingat piutang melalui WhatsApp Gateway.
     */
    public function send(Request $request, Pelanggan $pelanggan, WhatsAppService $whatsAppService): JsonResponse
    {
        // 1. Validasi input faktur
        $validated = $request->validate([
            'penjualan_id' => ['required', 'integer'],
        ], [
            'penjualan_id.required' => 'Faktur tagihan harus dipilih.',
            'penjualan_id.integer' => 'ID faktur tidak valid.',
        ]);

        // 2. Pastikan pelanggan adalah member aktif
        if (!$pelanggan->is_member || !$pelanggan->member_aktif) {
            return response()->json([
                'status' => false,
                'message' => 'Pelanggan bukan member aktif.',
            ], 422);
        }

        // 3. Pastikan nomor telepon member tersedia di database
        if (empty(trim((string) $pelanggan->telepon))) {
            return response()->json([
                'status' => false,
                'message' => 'Nomor WhatsApp pelanggan belum terdaftar pada data master.',
            ], 422);
        }

        // 4. Cari faktur dan pastikan benar-benar milik pelanggan ini
        $penjualan = Penjualan::where('id', $validated['penjualan_id'])
            ->where('pelanggan_id', $pelanggan->id)
            ->first();

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Faktur tidak ditemukan atau bukan milik pelanggan ini.',
            ], 404);
        }

        // 5. Pastikan metode pembayaran piutang
        if ($penjualan->metode_pembayaran !== 'piutang') {
            return response()->json([
                'status' => false,
                'message' => 'Transaksi ini bukan transaksi piutang.',
            ], 422);
        }

        // 6. Hitung ulang total dibayar dan sisa piutang langsung dari database
        $totalDibayar = (float) $penjualan->pembayaranPiutang()->sum('jumlah');
        $totalFaktur = (float) $penjualan->total;
        $sisaPiutang = max(0, $totalFaktur - $totalDibayar);

        if ($sisaPiutang <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan untuk faktur ini sudah lunas.',
            ], 422);
        }

        // 7. Periksa jatuh tempo & syarat pengingat (H-3 atau sudah jatuh tempo)
        $dueDate = $penjualan->due_date
            ?? ($penjualan->tanggal ? $penjualan->tanggal->copy()->addMonth() : null);

        if (!$dueDate) {
            return response()->json([
                'status' => false,
                'message' => 'Tanggal jatuh tempo faktur tidak valid.',
            ], 422);
        }

        $today = Carbon::today();
        $hariMenujuJatuhTempo = (int) $today->diffInDays($dueDate->copy()->startOfDay(), false);

        if ($hariMenujuJatuhTempo > 3) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan ini belum memenuhi batas waktu pengingat (minimal H-3 atau sudah lewat jatuh tempo).',
            ], 422);
        }

        // 8. Perlindungan dari pengiriman ganda akibat klik berulang (Lock 10 detik)
        $lockKey = "wa_send_lock_{$pelanggan->id}_{$penjualan->id}";
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'status' => false,
                'message' => 'Pengiriman pesan untuk faktur ini sedang diproses. Mohon tunggu beberapa saat.',
            ], 429);
        }

        try {
            // 9. Bentuk pesan pengingat resmi dari server
            $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');
            $sisaFormatted = 'Rp ' . number_format($sisaPiutang, 0, ',', '.');
            $jatuhTempoFormatted = $dueDate->format('d-m-Y');
            $noFaktur = $penjualan->no_faktur ?: ('#' . $penjualan->id);

            $pesan = "Yth. Bapak/Ibu {$pelanggan->nama},\n\n"
                . "Kami dari {$namaApotek} ingin mengingatkan bahwa masih terdapat piutang sebesar {$sisaFormatted} dengan tanggal jatuh tempo {$jatuhTempoFormatted}.\n\n"
                . "Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku."
                . "Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi."
                . "Terima kasih atas perhatian dan kerja samanya.\n\n"
                . "Salam,\n\n\n"
                . "{$namaApotek}";

            // 10. Kirim melalui WhatsAppService
            $result = $whatsAppService->sendMessage($pelanggan->telepon, $pesan);

            if (!$result['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $result['msg'] ?? 'Gagal mengirim pesan WhatsApp.',
                ], 502);
            }

            return response()->json([
                'status' => true,
                'message' => $result['msg'] ?? 'Pesan WhatsApp berhasil dikirim.',
                'data' => [
                    'pelanggan_id' => $pelanggan->id,
                    'penjualan_id' => $penjualan->id,
                    'no_faktur' => $noFaktur,
                    'sisa_piutang' => $sisaPiutang,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pengiriman WhatsApp.',
            ], 500);
        }
    }
}