<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppFollowUpHistory;
use App\Models\WhatsAppLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
        $statusFollowUp = $request->input('status_follow_up');
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

        // Terapkan filter status pengiriman pada tabel data jika dipilih
        if (!empty($status) && $status !== 'semua') {
            $baseQuery->where('status', $status);
        }

        // Terapkan filter status follow-up pada tabel data jika dipilih
        if (!empty($statusFollowUp) && $statusFollowUp !== 'semua') {
            if ($statusFollowUp === WhatsAppLog::STATUS_FOLLOW_UP_BELUM) {
                $baseQuery->where(function ($q) {
                    $q->where('status_follow_up', WhatsAppLog::STATUS_FOLLOW_UP_BELUM)
                      ->orWhereNull('status_follow_up');
                });
            } else {
                $baseQuery->where('status_follow_up', $statusFollowUp);
            }
        }

        $logs = $baseQuery->with(['user', 'pelanggan', 'followUpUser'])
            ->orderBy('waktu_kirim', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $statusFollowUpOptions = WhatsAppLog::getStatusFollowUpOptions();

        return view('whatsapp-monitor.index', compact(
            'logs',
            'stats',
            'search',
            'status',
            'statusFollowUp',
            'statusFollowUpOptions',
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
        $log = WhatsAppLog::with(['user', 'pelanggan', 'penjualan', 'followUpUser', 'followUpHistories.user'])->findOrFail($id);

        $waktuKirimFormatted = $log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-';
        $waktuResponFormatted = $log->waktu_respon ? $log->waktu_respon->translatedFormat('d M Y, H:i') : ($log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-');
        $waktuFollowUpFormatted = $log->waktu_follow_up ? $log->waktu_follow_up->translatedFormat('d M Y, H:i') : '-';

        $senderName = $log->user?->name ?? '-';
        $senderRole = $log->user?->role ? ucwords(str_replace('_', ' ', $log->user->role)) : '-';

        $followUpUserName = $log->followUpUser?->name ?? '-';

        $histories = $log->followUpHistories->map(function (WhatsAppFollowUpHistory $history) {
            return [
                'id' => $history->id,
                'status_sebelum' => $history->status_sebelum,
                'status_sebelum_label' => $history->status_sebelum_label,
                'status_sesudah' => $history->status_sesudah,
                'status_sesudah_label' => $history->status_sesudah_label,
                'status_sesudah_badge_class' => $history->status_sesudah_badge_class,
                'catatan' => $history->catatan ?: '-',
                'waktu' => $history->created_at ? $history->created_at->translatedFormat('d M Y, H:i') : '-',
                'admin_nama' => $history->user?->name ?? '-',
            ];
        });

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
                'status_follow_up' => $log->status_follow_up ?? WhatsAppLog::STATUS_FOLLOW_UP_BELUM,
                'status_follow_up_label' => $log->status_follow_up_label,
                'status_follow_up_badge_class' => $log->status_follow_up_badge_class,
                'catatan_follow_up' => $log->catatan_follow_up ?: '-',
                'waktu_follow_up' => $waktuFollowUpFormatted,
                'follow_up_user_nama' => $followUpUserName,
                'follow_up_histories' => $histories,
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

    /**
     * Mengambil data khusus follow-up dan riwayatnya untuk modal follow-up.
     */
    public function getFollowUp(int $id): JsonResponse
    {
        $log = WhatsAppLog::with(['pelanggan', 'user', 'followUpUser', 'followUpHistories.user'])->findOrFail($id);

        $histories = $log->followUpHistories->map(function (WhatsAppFollowUpHistory $history) {
            return [
                'id' => $history->id,
                'status_sebelum' => $history->status_sebelum,
                'status_sebelum_label' => $history->status_sebelum_label,
                'status_sesudah' => $history->status_sesudah,
                'status_sesudah_label' => $history->status_sesudah_label,
                'status_sesudah_badge_class' => $history->status_sesudah_badge_class,
                'catatan' => $history->catatan ?: '-',
                'waktu' => $history->created_at ? $history->created_at->translatedFormat('d M Y, H:i') : '-',
                'admin_nama' => $history->user?->name ?? '-',
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $log->id,
                'nama_member' => $log->nama_member,
                'nomor_whatsapp' => $log->nomor_whatsapp,
                'kategori_label' => $log->kategori_label,
                'isi_pesan' => $log->isi_pesan,
                'waktu_kirim' => $log->waktu_kirim ? $log->waktu_kirim->translatedFormat('d M Y, H:i') : '-',
                'status_pengiriman' => $log->status,
                'status_pengiriman_label' => $log->status_label,
                'status_pengiriman_badge_class' => $log->status_badge_class,
                'status_follow_up' => $log->status_follow_up ?? WhatsAppLog::STATUS_FOLLOW_UP_BELUM,
                'status_follow_up_label' => $log->status_follow_up_label,
                'status_follow_up_badge_class' => $log->status_follow_up_badge_class,
                'catatan_follow_up' => $log->catatan_follow_up ?: '',
                'waktu_follow_up' => $log->waktu_follow_up ? $log->waktu_follow_up->translatedFormat('d M Y, H:i') : '-',
                'follow_up_user_nama' => $log->followUpUser?->name ?? '-',
                'options' => WhatsAppLog::getStatusFollowUpOptions(),
                'histories' => $histories,
            ],
        ]);
    }

    /**
     * Memperbarui status follow-up dan mencatat riwayat perubahannya.
     */
    public function updateFollowUp(Request $request, int $id): JsonResponse
    {
        $validStatuses = array_keys(WhatsAppLog::getStatusFollowUpOptions());

        $validated = $request->validate([
            'status_follow_up' => ['required', 'string', Rule::in($validStatuses)],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'status_follow_up.required' => 'Pilih salah satu status follow-up.',
            'status_follow_up.in' => 'Status follow-up yang dipilih tidak valid.',
            'catatan.max' => 'Catatan follow-up maksimal 1000 karakter.',
        ]);

        return DB::transaction(function () use ($id, $validated) {
            $log = WhatsAppLog::lockForUpdate()->findOrFail($id);

            $statusSebelum = $log->status_follow_up ?? WhatsAppLog::STATUS_FOLLOW_UP_BELUM;
            $statusBaru = $validated['status_follow_up'];
            $catatan = trim((string) ($validated['catatan'] ?? ''));

            // 1. Catat riwayat baru (tidak pernah menimpa riwayat lama)
            $history = WhatsAppFollowUpHistory::create([
                'whatsapp_log_id' => $log->id,
                'user_id' => auth()->id(),
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $statusBaru,
                'catatan' => $catatan !== '' ? $catatan : null,
            ]);

            // 2. Perbarui status follow-up terkini pada log pengiriman
            $log->update([
                'status_follow_up' => $statusBaru,
                'catatan_follow_up' => $catatan !== '' ? $catatan : null,
                'waktu_follow_up' => now(),
                'follow_up_user_id' => auth()->id(),
            ]);

            $log->load(['user', 'followUpUser']);

            $adminName = auth()->user()?->name ?? '-';

            return response()->json([
                'status' => true,
                'message' => 'Status follow-up berhasil diperbarui.',
                'data' => [
                    'log_id' => $log->id,
                    'status_follow_up' => $log->status_follow_up,
                    'status_follow_up_label' => $log->status_follow_up_label,
                    'status_follow_up_badge_class' => $log->status_follow_up_badge_class,
                    'catatan_follow_up' => $log->catatan_follow_up ?: '-',
                    'waktu_follow_up' => $log->waktu_follow_up ? $log->waktu_follow_up->translatedFormat('d M Y, H:i') : '-',
                    'follow_up_user_nama' => $log->followUpUser?->name ?? $adminName,
                    'new_history' => [
                        'id' => $history->id,
                        'status_sebelum' => $history->status_sebelum,
                        'status_sebelum_label' => $history->status_sebelum_label,
                        'status_sesudah' => $history->status_sesudah,
                        'status_sesudah_label' => $history->status_sesudah_label,
                        'status_sesudah_badge_class' => $history->status_sesudah_badge_class,
                        'catatan' => $history->catatan ?: '-',
                        'waktu' => $history->created_at ? $history->created_at->translatedFormat('d M Y, H:i') : '-',
                        'admin_nama' => $adminName,
                    ],
                ],
            ]);
        });
    }
}
