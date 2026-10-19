<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppMonitorController extends Controller
{
    /**
     * Menampilkan halaman WhatsApp Monitor dengan filter dan statistik.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $kategori = $request->input('kategori');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Base query untuk tabel & statistik
        $baseQuery = WhatsAppLog::query();

        // Filter rentang tanggal
        if (!empty($tanggalAwal)) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $tanggalAwal)->startOfDay();
            } catch (\Throwable) {
                try {
                    $startDate = Carbon::parse($tanggalAwal)->startOfDay();
                } catch (\Throwable) {
                    $startDate = null;
                }
            }
            if ($startDate) {
                $baseQuery->where('waktu_kirim', '>=', $startDate);
            }
        }

        if (!empty($tanggalAkhir)) {
            try {
                $endDate = Carbon::createFromFormat('Y-m-d', $tanggalAkhir)->endOfDay();
            } catch (\Throwable) {
                try {
                    $endDate = Carbon::parse($tanggalAkhir)->endOfDay();
                } catch (\Throwable) {
                    $endDate = null;
                }
            }
            if ($endDate) {
                $baseQuery->where('waktu_kirim', '<=', $endDate);
            }
        }

        // Filter pencarian nama member, no whatsapp, atau isi pesan
        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama_member', 'like', "%{$search}%")
                  ->orWhere('nomor_whatsapp', 'like', "%{$search}%")
                  ->orWhere('isi_pesan', 'like', "%{$search}%");
            });
        }

        // Filter jenis pesan
        if (!empty($kategori) && $kategori !== 'semua') {
            $baseQuery->where('kategori', $kategori);
        }

        // Hitung statistik ringkasan kartu KPI (berdasarkan kriteria aktif sebelum filter status spesifik)
        $statsQuery = clone $baseQuery;
        $totalAll = $statsQuery->count();
        $totalTerkirim = (clone $statsQuery)->where('status', 'terkirim')->count();
        $totalMenunggu = (clone $statsQuery)->where('status', 'menunggu')->count();
        $totalGagal = (clone $statsQuery)->where('status', 'gagal')->count();
        $totalDibatalkan = (clone $statsQuery)->where('status', 'dibatalkan')->count();

        $pctTerkirim = $totalAll > 0 ? round(($totalTerkirim / $totalAll) * 100) : 0;
        $pctMenunggu = $totalAll > 0 ? round(($totalMenunggu / $totalAll) * 100) : 0;
        $pctGagal = $totalAll > 0 ? round(($totalGagal / $totalAll) * 100) : 0;
        $pctDibatalkan = $totalAll > 0 ? round(($totalDibatalkan / $totalAll) * 100) : 0;

        $stats = [
            'total' => $totalAll,
            'terkirim' => $totalTerkirim,
            'terkirim_pct' => $pctTerkirim,
            'menunggu' => $totalMenunggu,
            'menunggu_pct' => $pctMenunggu,
            'gagal' => $totalGagal,
            'gagal_pct' => $pctGagal,
            'dibatalkan' => $totalDibatalkan,
            'dibatalkan_pct' => $pctDibatalkan,
        ];

        // Terapkan filter status pada tabel data jika dipilih
        if (!empty($status) && $status !== 'semua') {
            $baseQuery->where('status', $status);
        }

        $logs = $baseQuery->with(['user', 'pelanggan'])
            ->orderBy('waktu_kirim', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('whatsapp-monitor.index', compact(
            'logs',
            'stats',
            'search',
            'status',
            'kategori',
            'tanggalAwal',
            'tanggalAkhir',
            'perPage'
        ));
    }

    /**
     * Mengambil detail log pengiriman WhatsApp untuk popup modal.
     */
    public function show(int $id): JsonResponse
    {
        $log = WhatsAppLog::with(['user', 'pelanggan', 'penjualan'])->findOrFail($id);

        $waktuKirimFormatted = $log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-';
        $waktuResponFormatted = $log->waktu_respon ? $log->waktu_respon->translatedFormat('d M Y, H:i') : ($log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-');

        $senderName = $log->user?->name ?? 'Sistem / Otomatis';
        $senderRole = $log->user?->role ? ucwords(str_replace('_', ' ', $log->user->role)) : '-';

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $log->id,
                'nama_member' => $log->nama_member,
                'nomor_whatsapp' => $log->nomor_whatsapp,
                'waktu_kirim' => $waktuKirimFormatted,
                'waktu_respon' => $waktuResponFormatted,
                'kategori' => $log->kategori,
                'kategori_label' => $log->kategori_label,
                'nama_template' => $log->nama_template ?: '-',
                'isi_pesan' => $log->isi_pesan,
                'status' => $log->status,
                'status_label' => $log->status_label,
                'status_badge_class' => $log->status_badge_class,
                'keterangan' => $log->keterangan ?: ($log->status === 'terkirim' ? 'Pesan berhasil dikirim ke nomor WhatsApp.' : '-'),
                'faktur_info' => $log->faktur_info ?: '-',
                'total_tagihan' => $log->total_tagihan,
                'total_tagihan_formatted' => $log->total_tagihan ? ('Rp ' . number_format($log->total_tagihan, 0, ',', '.')) : '-',
                'pengirim_nama' => $senderName,
                'pengirim_role' => $senderRole,
                'metadata' => $log->metadata,
            ],
        ]);
    }
}
