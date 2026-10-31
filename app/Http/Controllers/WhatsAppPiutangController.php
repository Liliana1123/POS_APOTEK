<?php

namespace App\Http\Controllers;

use App\Models\InfoApotek;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\WhatsAppLog;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppPiutangController extends Controller
{
    /**
     * Menampilkan data member dan tagihan yang memenuhi syarat pengingat WhatsApp.
     * Belum melakukan pengiriman pesan.
     */
    public function preview(Request $request, Pelanggan $pelanggan): JsonResponse
    {
        if (
            !$pelanggan->is_member ||
            !$pelanggan->member_aktif
        ) {
            return response()->json([
                'status' => false,
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

                return [
                    'penjualan_id' => $penjualan->id,
                    'no_faktur' => $penjualan->no_faktur ?: ('#' . $penjualan->id),
                    'total_faktur' => $totalFaktur,
                    'total_dibayar' => $totalDibayar,
                    'sisa_piutang' => $sisaPiutang,
                    'sisa_piutang_formatted' => 'Rp ' . number_format($sisaPiutang, 0, ',', '.'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'jatuh_tempo' => $dueDate->format('d-m-Y'),
                    'hari_menuju_jatuh_tempo' => $hariMenujuJatuhTempo,
                ];
            })
            ->filter()
            ->values();

        $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');
        $rawPhone = trim((string) $pelanggan->telepon);
        $formattedPhone = app(WhatsAppService::class)->formatPhoneNumber($rawPhone);
        $totalPiutang = (float) $tagihan->sum('sisa_piutang');

        return response()->json([
            'status' => true,
            'pelanggan' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
                'telepon' => $pelanggan->telepon,
                'telepon_formatted' => $formattedPhone,
                'telepon_valid' => !empty($formattedPhone),
                'is_member' => (bool) $pelanggan->is_member,
                'member_aktif' => (bool) $pelanggan->member_aktif,
            ],
            'nama_apotek' => $namaApotek,
            'tagihan' => $tagihan,
            'has_piutang' => $tagihan->isNotEmpty(),
            'total_piutang' => $totalPiutang,
            'total_piutang_formatted' => 'Rp ' . number_format($totalPiutang, 0, ',', '.'),
            'total_faktur_belum_lunas' => $tagihan->count(),
            'pengiriman_aktif' => false,
            'message' => $tagihan->isEmpty()
                ? 'Member tidak memiliki tagihan piutang aktif yang belum lunas.'
                : 'Pratinjau tagihan berhasil. Pesan WhatsApp belum dikirim.',
        ]);
    }

    /**
     * Mengirim pesan WhatsApp perorangan (mendukung 6 kategori pesan dan pilihan faktur tunggal / semua faktur).
     */
    public function send(Request $request, Pelanggan $pelanggan, WhatsAppService $whatsAppService): JsonResponse
    {
        // 1. Validasi input
        $validated = $request->validate([
            'kategori' => ['nullable', 'string', 'in:pengingat_piutang,promosi_diskon,ucapan_apresiasi,informasi_produk,informasi_pengumuman,pesan_custom'],
            'penjualan_id' => ['nullable'],
            'pesan' => ['nullable', 'string'],
        ]);

        $kategori = $validated['kategori'] ?? 'pengingat_piutang';

        // 2. Pastikan pelanggan adalah member aktif
        if (!$pelanggan->is_member || !$pelanggan->member_aktif) {
            return response()->json([
                'status' => false,
                'message' => 'Pelanggan bukan member aktif.',
            ], 422);
        }

        // 3. Pastikan nomor telepon member tersedia & valid
        $rawPhone = trim((string) $pelanggan->telepon);
        if ($rawPhone === '') {
            return response()->json([
                'status' => false,
                'message' => 'Nomor WhatsApp member belum terdaftar pada data master. Silakan lengkapi nomor telepon terlebih dahulu.',
            ], 422);
        }

        $formattedPhone = $whatsAppService->formatPhoneNumber($rawPhone);
        if (!$formattedPhone) {
            return response()->json([
                'status' => false,
                'message' => 'Format nomor WhatsApp member tidak valid. Pastikan nomor diawali dengan 08 atau 62.',
            ], 422);
        }

        $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');

        // 4. Logika Khusus Kategori Pengingat Piutang
        $dataTagihan = [];
        if ($kategori === 'pengingat_piutang') {
            $penjualanIdInput = $request->input('penjualan_id');

            if (empty($penjualanIdInput)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Faktur tagihan harus dipilih untuk pengingat piutang.',
                ], 422);
            }

            if ($penjualanIdInput === 'all') {
                // Pilihan: "Semua faktur belum lunas"
                $unpaidInvoices = $pelanggan->penjualan()
                    ->where('metode_pembayaran', 'piutang')
                    ->with('pembayaranPiutang')
                    ->get()
                    ->map(function ($p) {
                        $totalDibayar = (float) $p->pembayaranPiutang->sum('jumlah');
                        $sisa = max(0, (float) $p->total - $totalDibayar);
                        $dueDate = $p->due_date ?? ($p->tanggal ? $p->tanggal->copy()->addMonth() : null);
                        return [
                            'penjualan' => $p,
                            'sisa' => $sisa,
                            'no_faktur' => $p->no_faktur ?: ('#' . $p->id),
                            'due_date' => $dueDate,
                        ];
                    })
                    ->filter(fn($item) => $item['sisa'] > 0)
                    ->values();

                if ($unpaidInvoices->isEmpty()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Member ini tidak memiliki sisa piutang aktif atau seluruh tagihan telah lunas.',
                    ], 422);
                }

                $totalSisa = (float) $unpaidInvoices->sum('sisa');
                $countFaktur = $unpaidInvoices->count();
                $fakturList = $unpaidInvoices->pluck('no_faktur')->join(', ');

                $dataTagihan = [
                    'mode' => 'all',
                    'count' => $countFaktur,
                    'total_sisa' => $totalSisa,
                    'sisa_formatted' => 'Rp ' . number_format($totalSisa, 0, ',', '.'),
                    'no_faktur' => "Semua faktur belum lunas ({$countFaktur} faktur: {$fakturList})",
                    'jatuh_tempo' => 'Bervariasi',
                ];
            } else {
                // Pilihan: Faktur spesifik
                $penjualan = Penjualan::where('id', (int) $penjualanIdInput)
                    ->where('pelanggan_id', $pelanggan->id)
                    ->first();

                if (!$penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Faktur tidak ditemukan atau bukan milik member ini.',
                    ], 404);
                }

                if ($penjualan->metode_pembayaran !== 'piutang') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Transaksi ini bukan transaksi piutang.',
                    ], 422);
                }

                $totalDibayar = (float) $penjualan->pembayaranPiutang()->sum('jumlah');
                $totalFaktur = (float) $penjualan->total;
                $sisaPiutang = max(0, $totalFaktur - $totalDibayar);

                if ($sisaPiutang <= 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Tagihan untuk faktur ini sudah lunas.',
                    ], 422);
                }

                $dueDate = $penjualan->due_date
                    ?? ($penjualan->tanggal ? $penjualan->tanggal->copy()->addMonth() : null);

                $dataTagihan = [
                    'mode' => 'single',
                    'penjualan_id' => $penjualan->id,
                    'sisa' => $sisaPiutang,
                    'sisa_formatted' => 'Rp ' . number_format($sisaPiutang, 0, ',', '.'),
                    'no_faktur' => $penjualan->no_faktur ?: ('#' . $penjualan->id),
                    'jatuh_tempo' => $dueDate ? $dueDate->format('d-m-Y') : '-',
                ];
            }
        }

        // 5. Perlindungan dari pengiriman ganda akibat klik cepat (Lock 10 detik)
        $lockKey = "wa_send_lock_pelanggan_{$pelanggan->id}";
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'status' => false,
                'message' => 'Pengiriman pesan untuk pelanggan ini sedang diproses. Mohon tunggu beberapa saat.',
            ], 429);
        }

        try {
            // 6. Susun pesan
            $customPesan = trim((string) $request->input('pesan', ''));

            if ($customPesan === '') {
                if ($kategori === 'pengingat_piutang') {
                    if (($dataTagihan['mode'] ?? '') === 'all') {
                        $customPesan = "Yth. Bapak/Ibu {nama_member},\n\n"
                            . "Kami dari {nama_apotek} ingin menginformasikan bahwa terdapat {$dataTagihan['count']} tagihan faktur belum lunas dengan total sisa piutang sebesar {total_piutang}.\n\n"
                            . "Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n"
                            . "Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n"
                            . "Terima kasih atas perhatian dan kerja samanya.\n\n"
                            . "Salam,\n{nama_apotek}";
                    } else {
                        $customPesan = "Yth. Bapak/Ibu {nama_member},\n\n"
                            . "Kami dari {nama_apotek} ingin mengingatkan bahwa masih terdapat piutang sebesar {total_piutang} pada faktur {nomor_faktur} dengan tanggal jatuh tempo {tanggal_jatuh_tempo}.\n\n"
                            . "Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n"
                            . "Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n"
                            . "Terima kasih atas perhatian dan kerja samanya.\n\n"
                            . "Salam,\n{nama_apotek}";
                    }
                } else {
                    $customPesan = "Halo {nama_member},\n\nTerima kasih telah menjadi bagian dari member setia {nama_apotek}.\n\nSalam sehat selalu!";
                }
            }

            // Ganti placeholder dengan data aktual
            $replacements = [
                '{nama_member}' => $pelanggan->nama,
                '{nama_apotek}' => $namaApotek,
                '{total_piutang}' => $kategori === 'pengingat_piutang' ? ($dataTagihan['sisa_formatted'] ?? '-') : '-',
                '{nomor_faktur}' => $kategori === 'pengingat_piutang' ? ($dataTagihan['no_faktur'] ?? '-') : '-',
                '{tanggal_jatuh_tempo}' => $kategori === 'pengingat_piutang' ? ($dataTagihan['jatuh_tempo'] ?? '-') : '-',
            ];

            $pesanFinal = strtr($customPesan, $replacements);

            // 7. Kirim melalui WhatsAppService
            $waktuKirim = now();
            $result = $whatsAppService->sendMessage($formattedPhone, $pesanFinal);
            $waktuRespon = now();

            $status = $result['status'] ? 'terkirim' : 'gagal';
            $keterangan = $result['msg'] ?? ($result['status'] ? 'Pesan berhasil dikirim ke nomor WhatsApp.' : 'Gagal mengirim pesan WhatsApp.');

            try {
                WhatsAppLog::create([
                    'pelanggan_id' => $pelanggan->id,
                    'user_id' => auth()->id(),
                    'penjualan_id' => ($dataTagihan['mode'] ?? '') === 'single' ? ($dataTagihan['penjualan_id'] ?? null) : null,
                    'nama_member' => $pelanggan->nama,
                    'nomor_whatsapp' => $formattedPhone,
                    'kategori' => $kategori,
                    'nama_template' => $request->input('nama_template') ?: null,
                    'isi_pesan' => $pesanFinal,
                    'status' => $status,
                    'waktu_kirim' => $waktuKirim,
                    'waktu_respon' => $waktuRespon,
                    'keterangan' => $keterangan,
                    'faktur_info' => $kategori === 'pengingat_piutang' ? ($dataTagihan['no_faktur'] ?? null) : null,
                    'total_tagihan' => $kategori === 'pengingat_piutang' ? ($dataTagihan['total_sisa'] ?? ($dataTagihan['sisa'] ?? null)) : null,
                    'metadata' => [
                        'mode' => $dataTagihan['mode'] ?? 'single',
                        'provider_response' => $result,
                    ],
                ]);
            } catch (\Throwable $logEx) {
                Log::error('Failed recording WhatsAppLog: ' . $logEx->getMessage());
            }

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
                    'kategori' => $kategori,
                    'telepon' => $formattedPhone,
                    'data_tagihan' => $dataTagihan,
                ],
            ]);
        } catch (\Throwable $e) {
            try {
                WhatsAppLog::create([
                    'pelanggan_id' => $pelanggan->id,
                    'user_id' => auth()->id(),
                    'penjualan_id' => ($dataTagihan['mode'] ?? '') === 'single' ? ($dataTagihan['penjualan_id'] ?? null) : null,
                    'nama_member' => $pelanggan->nama,
                    'nomor_whatsapp' => $formattedPhone ?: $pelanggan->telepon,
                    'kategori' => $kategori,
                    'nama_template' => $request->input('nama_template') ?: null,
                    'isi_pesan' => $pesanFinal ?? ($customPesan ?? ''),
                    'status' => 'gagal',
                    'waktu_kirim' => $waktuKirim ?? now(),
                    'waktu_respon' => now(),
                    'keterangan' => 'Kesalahan sistem: ' . $e->getMessage(),
                    'faktur_info' => $kategori === 'pengingat_piutang' ? ($dataTagihan['no_faktur'] ?? null) : null,
                    'total_tagihan' => $kategori === 'pengingat_piutang' ? ($dataTagihan['total_sisa'] ?? ($dataTagihan['sisa'] ?? null)) : null,
                    'metadata' => ['exception' => $e->getMessage()],
                ]);
            } catch (\Throwable $logEx) {}

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem saat mengirim pesan: ' . $e->getMessage(),
            ], 500);
        } finally {
            $lock->release();
        }
    }

    /**
     * Memvalidasi dan menyiapkan pratinjau daftar member untuk pengiriman massal multi-kategori.
     */
    public function bulkPreview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pelanggan_ids' => ['required', 'array', 'min:1'],
            'pelanggan_ids.*' => ['integer'],
            'kategori' => ['nullable', 'string'],
            'pesan_template' => ['nullable', 'string'],
        ], [
            'pelanggan_ids.required' => 'Pilih minimal satu member untuk pengiriman.',
            'pelanggan_ids.array' => 'Data pilihan member tidak valid.',
        ]);

        $pelangganIds = array_values(array_unique($validated['pelanggan_ids']));
        $kategori = $validated['kategori'] ?? 'pengingat_piutang';
        $pesanTemplate = $validated['pesan_template'] ?? null;

        // Ambil data pelanggan dengan relasi penjualan piutang
        $pelanggans = Pelanggan::whereIn('id', $pelangganIds)
            ->where('is_member', true)
            ->where('member_aktif', true)
            ->with(['penjualan' => function ($q) {
                $q->where('metode_pembayaran', 'piutang')
                  ->with('pembayaranPiutang');
            }])
            ->get();

        $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');
        $whatsAppService = app(WhatsAppService::class);

        $penerima = [];
        $dikeluarkan = [];

        foreach ($pelangganIds as $id) {
            $pelanggan = $pelanggans->firstWhere('id', $id);

            if (!$pelanggan) {
                $dikeluarkan[] = [
                    'id' => $id,
                    'nama' => 'ID #' . $id,
                    'alasan' => 'Data member tidak ditemukan atau bukan member aktif.',
                ];
                continue;
            }

            // Validasi format nomor telepon melalui WhatsAppService
            $teleponFormatted = $whatsAppService->formatPhoneNumber($pelanggan->telepon);
            if (!$teleponFormatted) {
                $dikeluarkan[] = [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'alasan' => empty(trim((string) $pelanggan->telepon))
                        ? 'Nomor WhatsApp belum terdaftar pada data member.'
                        : 'Format nomor WhatsApp tidak valid (harus diawali 08/628 dan 9-14 digit).',
                ];
                continue;
            }

            if ($kategori === 'pengingat_piutang') {
                $pengingat = $pelanggan->pengingat_piutang;

                if (!$pengingat || ($pengingat['total_sisa'] ?? 0) <= 0) {
                    $alasan = 'Tidak memiliki sisa piutang aktif.';
                    if (($pelanggan->saldo_piutang ?? 0) <= 0) {
                        $alasan = 'Tagihan piutang sudah lunas.';
                    }

                    $dikeluarkan[] = [
                        'id' => $pelanggan->id,
                        'nama' => $pelanggan->nama,
                        'alasan' => $alasan,
                    ];
                    continue;
                }

                $sisaFormatted = 'Rp ' . number_format($pengingat['total_sisa'], 0, ',', '.');
                $jatuhTempoFormatted = $pengingat['jatuh_tempo_pesan'];
                $noFaktur = '#' . $pengingat['primary_penjualan_id'];

                if ($pesanTemplate && trim($pesanTemplate) !== '') {
                    $pesan = strtr($pesanTemplate, [
                        '{nama_member}' => $pelanggan->nama,
                        '{nomor_faktur}' => $noFaktur,
                        '{total_piutang}' => $sisaFormatted,
                        '{tanggal_jatuh_tempo}' => $jatuhTempoFormatted,
                        '{nama_apotek}' => $namaApotek,
                    ]);
                } else {
                    $pesan = "Yth. Bapak/Ibu {$pelanggan->nama},\n\n"
                        . "Kami dari {$namaApotek} ingin mengingatkan bahwa masih terdapat piutang sebesar {$sisaFormatted} dengan tanggal jatuh tempo {$jatuhTempoFormatted}.\n\n"
                        . "Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n"
                        . "Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n"
                        . "Terima kasih atas perhatian dan kerja samanya.\n\n"
                        . "Salam,\n"
                        . "{$namaApotek}";
                }

                $penerima[] = [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'telepon' => $pelanggan->telepon,
                    'sisa_piutang' => $pengingat['total_sisa'],
                    'sisa_piutang_formatted' => $sisaFormatted,
                    'jatuh_tempo' => $pengingat['jatuh_tempo_formatted'],
                    'jatuh_tempo_raw' => $jatuhTempoFormatted,
                    'pesan' => $pesan,
                    'primary_penjualan_id' => $pengingat['primary_penjualan_id'],
                ];
            } else {
                // Kategori non-piutang: Promosi, Ucapan, Informasi Produk, Pengumuman, Pesan Custom
                $pengingat = $pelanggan->pengingat_piutang;
                $sisaPiutang = $pengingat['total_sisa'] ?? 0;
                $sisaFormatted = $sisaPiutang > 0 ? ('Rp ' . number_format($sisaPiutang, 0, ',', '.')) : 'Rp 0';
                $jatuhTempoFormatted = $pengingat['jatuh_tempo_pesan'] ?? '-';

                if ($pesanTemplate && trim($pesanTemplate) !== '') {
                    $pesan = strtr($pesanTemplate, [
                        '{nama_member}' => $pelanggan->nama,
                        '{nomor_faktur}' => $pengingat ? ('#' . $pengingat['primary_penjualan_id']) : '-',
                        '{total_piutang}' => $sisaFormatted,
                        '{tanggal_jatuh_tempo}' => $jatuhTempoFormatted,
                        '{nama_apotek}' => $namaApotek,
                    ]);
                } else {
                    $pesan = "Halo Bapak/Ibu {$pelanggan->nama},\n\nTerima kasih telah menjadi member setia di {$namaApotek}.\n\nSalam sehat,\n{$namaApotek}";
                }

                $penerima[] = [
                    'id' => $pelanggan->id,
                    'nama' => $pelanggan->nama,
                    'telepon' => $pelanggan->telepon,
                    'sisa_piutang' => $sisaPiutang,
                    'sisa_piutang_formatted' => $sisaFormatted,
                    'jatuh_tempo' => $pengingat['jatuh_tempo_formatted'] ?? '-',
                    'jatuh_tempo_raw' => $jatuhTempoFormatted,
                    'pesan' => $pesan,
                    'primary_penjualan_id' => $pengingat['primary_penjualan_id'] ?? null,
                ];
            }
        }

        $totalPiutang = array_sum(array_column($penerima, 'sisa_piutang'));

        return response()->json([
            'status' => count($penerima) > 0,
            'kategori' => $kategori,
            'nama_apotek' => $namaApotek,
            'total_dipilih' => count($pelangganIds),
            'total_penerima' => count($penerima),
            'total_dikeluarkan' => count($dikeluarkan),
            'total_piutang' => $totalPiutang,
            'total_piutang_formatted' => 'Rp ' . number_format($totalPiutang, 0, ',', '.'),
            'penerima' => $penerima,
            'dikeluarkan' => $dikeluarkan,
        ]);
    }

    /**
     * Mengirim pesan WhatsApp massal untuk satu member terpilih (sekuensial).
     */
    public function sendBulkItem(Request $request, Pelanggan $pelanggan, WhatsAppService $whatsAppService): JsonResponse
    {
        // 1. Pastikan pelanggan adalah member aktif
        if (!$pelanggan->is_member || !$pelanggan->member_aktif) {
            return response()->json([
                'status' => false,
                'message' => 'Pelanggan bukan member aktif.',
            ], 422);
        }

        $kategori = $request->input('kategori', 'pengingat_piutang');
        $customPesan = trim((string) $request->input('pesan', ''));

        // 2. Khusus pengingat piutang: cek kelayakan dan hitung ulang piutang dari database
        if ($kategori === 'pengingat_piutang') {
            $pengingat = $pelanggan->pengingat_piutang;

            if (!$pengingat || ($pengingat['total_sisa'] ?? 0) <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member sudah tidak memiliki tagihan piutang aktif atau tagihan telah lunas.',
                ], 422);
            }
        }

        // 3. Format dan periksa nomor WhatsApp
        $teleponFormatted = $whatsAppService->formatPhoneNumber($pelanggan->telepon);
        if (!$teleponFormatted) {
            return response()->json([
                'status' => false,
                'message' => 'Format nomor WhatsApp pelanggan tidak valid.',
            ], 422);
        }

        // 4. Lock idempotensi selama 10 detik untuk mencegah double send
        $lockKey = "wa_bulk_send_lock_{$pelanggan->id}";
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return response()->json([
                'status' => false,
                'message' => 'Pengiriman pesan untuk member ini sedang diproses. Mohon tunggu beberapa saat.',
            ], 429);
        }

        try {
            $namaApotek = InfoApotek::first()?->nama_apotek ?: (config('app.name') ?: 'Apotek');

            if ($customPesan !== '') {
                $pesan = $customPesan;
            } elseif ($kategori === 'pengingat_piutang') {
                $pengingat = $pelanggan->pengingat_piutang;
                $sisaFormatted = 'Rp ' . number_format($pengingat['total_sisa'], 0, ',', '.');
                $jatuhTempoFormatted = $pengingat['jatuh_tempo_pesan'];

                $pesan = "Yth. Bapak/Ibu {$pelanggan->nama},\n\n"
                    . "Kami dari {$namaApotek} ingin mengingatkan bahwa masih terdapat piutang sebesar {$sisaFormatted} dengan tanggal jatuh tempo {$jatuhTempoFormatted}.\n\n"
                    . "Mohon kesediaannya untuk melakukan pembayaran sesuai ketentuan yang berlaku.\n\n"
                    . "Jika pembayaran telah dilakukan, silakan abaikan pesan ini atau hubungi petugas apotek untuk konfirmasi.\n\n"
                    . "Terima kasih atas perhatian dan kerja samanya.\n\n"
                    . "Salam,\n"
                    . "{$namaApotek}";
            } else {
                $pesan = "Halo Bapak/Ibu {$pelanggan->nama},\n\nTerima kasih telah menjadi member setia di {$namaApotek}.\n\nSalam sehat,\n{$namaApotek}";
            }

            $waktuKirim = now();
            $result = $whatsAppService->sendMessage($pelanggan->telepon, $pesan);
            $waktuRespon = now();

            $status = $result['status'] ? 'terkirim' : 'gagal';
            $keterangan = $result['msg'] ?? ($result['status'] ? 'Pesan berhasil dikirim ke nomor WhatsApp.' : 'Gagal mengirim pesan WhatsApp.');

            try {
                $isPiutang = $kategori === 'pengingat_piutang';
                $pengingatData = $isPiutang ? $pelanggan->pengingat_piutang : null;
                $penjualanId = $isPiutang ? ($request->input('primary_penjualan_id') ?: ($pengingatData['primary_penjualan_id'] ?? null)) : null;
                $penjualan = $penjualanId ? Penjualan::find($penjualanId) : null;
                $fakturInfo = $isPiutang ? ($penjualan?->no_faktur ?: ($penjualanId ? '#' . $penjualanId : null)) : null;
                $totalTagihan = $isPiutang ? ($pengingatData['total_sisa'] ?? null) : null;

                WhatsAppLog::create([
                    'pelanggan_id' => $pelanggan->id,
                    'user_id' => auth()->id(),
                    'penjualan_id' => $penjualanId,
                    'nama_member' => $pelanggan->nama,
                    'nomor_whatsapp' => $teleponFormatted,
                    'kategori' => $kategori,
                    'nama_template' => $request->input('nama_template') ?: null,
                    'isi_pesan' => $pesan,
                    'status' => $status,
                    'waktu_kirim' => $waktuKirim,
                    'waktu_respon' => $waktuRespon,
                    'keterangan' => $keterangan,
                    'faktur_info' => $fakturInfo,
                    'total_tagihan' => $totalTagihan,
                    'metadata' => [
                        'bulk' => true,
                        'provider_response' => $result,
                    ],
                ]);
            } catch (\Throwable $logEx) {
                Log::error('Failed recording WhatsAppLog in bulk: ' . $logEx->getMessage());
            }

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
                    'nama' => $pelanggan->nama,
                    'telepon' => $pelanggan->telepon,
                    'kategori' => $kategori,
                ],
            ]);
        } catch (\Throwable $e) {
            try {
                $isPiutang = $kategori === 'pengingat_piutang';
                $pengingatData = $isPiutang ? $pelanggan->pengingat_piutang : null;
                $penjualanId = $isPiutang ? ($request->input('primary_penjualan_id') ?: ($pengingatData['primary_penjualan_id'] ?? null)) : null;
                $penjualan = $penjualanId ? Penjualan::find($penjualanId) : null;
                $fakturInfo = $isPiutang ? ($penjualan?->no_faktur ?: ($penjualanId ? '#' . $penjualanId : null)) : null;
                $totalTagihan = $isPiutang ? ($pengingatData['total_sisa'] ?? null) : null;

                WhatsAppLog::create([
                    'pelanggan_id' => $pelanggan->id,
                    'user_id' => auth()->id(),
                    'penjualan_id' => $penjualanId,
                    'nama_member' => $pelanggan->nama,
                    'nomor_whatsapp' => $teleponFormatted ?? $pelanggan->telepon,
                    'kategori' => $kategori,
                    'nama_template' => $request->input('nama_template') ?: null,
                    'isi_pesan' => $pesan ?? ($customPesan ?? ''),
                    'status' => 'gagal',
                    'waktu_kirim' => $waktuKirim ?? now(),
                    'waktu_respon' => now(),
                    'keterangan' => 'Kesalahan sistem: ' . $e->getMessage(),
                    'faktur_info' => $fakturInfo,
                    'total_tagihan' => $totalTagihan,
                    'metadata' => ['bulk' => true, 'exception' => $e->getMessage()],
                ]);
            } catch (\Throwable $logEx) {}

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pengiriman WhatsApp.',
            ], 500);
        } finally {
            $lock->release();
        }
    }

    /**
     * Mengambil daftar template WhatsApp (bawaan & custom).
     */
    public function getTemplates(Request $request): JsonResponse
    {
        $kategori = $request->input('kategori');
        $query = WhatsAppTemplate::query();

        if ($kategori && $kategori !== 'semua') {
            $query->where('kategori', $kategori);
        }

        $templates = $query->orderBy('is_default', 'desc')->orderBy('id', 'asc')->get();

        return response()->json([
            'status' => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Menyimpan template pesan custom baru.
     */
    public function storeTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'in:pengingat_piutang,promosi_diskon,ucapan_apresiasi,informasi_produk,informasi_pengumuman,pesan_custom'],
            'konten' => ['required', 'string', 'max:3000'],
        ], [
            'nama.required' => 'Nama template wajib diisi.',
            'kategori.required' => 'Kategori template wajib dipilih.',
            'konten.required' => 'Isi pesan template wajib diisi.',
        ]);

        $template = WhatsAppTemplate::create([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'konten' => $validated['konten'],
            'is_default' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Template pesan berhasil disimpan.',
            'template' => $template,
        ]);
    }

    /**
     * Memperbarui template pesan custom.
     */
    public function updateTemplate(Request $request, WhatsAppTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'string', 'in:pengingat_piutang,promosi_diskon,ucapan_apresiasi,informasi_produk,informasi_pengumuman,pesan_custom'],
            'konten' => ['required', 'string', 'max:3000'],
        ], [
            'nama.required' => 'Nama template wajib diisi.',
            'kategori.required' => 'Kategori template wajib dipilih.',
            'konten.required' => 'Isi pesan template wajib diisi.',
        ]);

        $template->update([
            'nama' => $validated['nama'],
            'kategori' => $validated['kategori'],
            'konten' => $validated['konten'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Template pesan berhasil diperbarui.',
            'template' => $template,
        ]);
    }

    /**
     * Menghapus template pesan custom.
     */
    public function destroyTemplate(WhatsAppTemplate $template): JsonResponse
    {
        if ($template->is_default) {
            return response()->json([
                'status' => false,
                'message' => 'Template bawaan sistem tidak dapat dihapus.',
            ], 422);
        }

        $template->delete();

        return response()->json([
            'status' => true,
            'message' => 'Template berhasil dihapus.',
        ]);
    }
}