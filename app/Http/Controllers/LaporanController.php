<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\DetailPenerimaan;
use App\Models\DetailPenjualan;
use App\Models\Rusak;
use App\Models\Beban;
use App\Models\Penjualan;
use App\Models\Penerimaan;
use App\Models\Pelanggan;
use App\Models\ActivityLog;
use App\Services\DashboardCacheService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class LaporanController extends Controller
{
    // Laporan stok: total stok per barang + alert stok menipis & mendekati expired
    public function stok(Request $request)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $barangs = collect(); // unused, kept for view compatibility

        $stokPerBatch = DetailPenerimaan::with(['barang.kategori', 'penerimaan.supplier'])
            ->withSum('detailPenjualan as stok_terjual', 'jumlah')
            ->withSum('rusak as stok_rusak', 'jumlah')
            ->when($request->status_stok !== 'habis', function ($query) {
                $query->where('aktif', true)
                    ->where('stok', '>', 0);
            })
            ->when($request->filled('nama'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama . '%');
                });
            })
            ->when($request->filled('barang'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->barang . '%');
                });
            })
            ->when($request->filled('kategori_id'), function ($query) use ($request) {
                $query->whereHas('barang', function ($q) use ($request) {
                    $q->where('kategori_id', $request->kategori_id);
                });
            })
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->whereHas('penerimaan', function ($q) use ($request) {
                    $q->where('supplier_id', $request->supplier_id);
                });
            })
            ->when($request->filled('batch'), function ($query) use ($request) {
                $query->where('no_batch', 'like', '%' . $request->batch . '%');
            })
            ->when($request->filled('no_batch'), function ($query) use ($request) {
                $query->where('no_batch', 'like', '%' . $request->no_batch . '%');
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('expired_date', $request->tanggal);
            })
            ->when($request->filled('dari'), function ($query) use ($request) {
                $query->whereDate('expired_date', '>=', $request->dari);
            })
            ->when($request->filled('sampai'), function ($query) use ($request) {
                $query->whereDate('expired_date', '<=', $request->sampai);
            })
            ->when($request->filled('status_stok'), function ($query) use ($request) {

                if ($request->status_stok === 'habis') {
                    $query->where('stok', 0);
                }

                if ($request->status_stok === 'menipis') {
                    $query->where('stok', '>', 0)
                        ->whereHas('barang', function ($q) {
                            $q->whereColumn('detail_penerimaans.stok', '<=', 'stok_minimum');
                        });
                }

                if ($request->status_stok === 'aman') {
                    $query->whereHas('barang', function ($q) {
                        $q->whereColumn('detail_penerimaans.stok', '>', 'stok_minimum');
                    });
                }
            })

            ->when($request->filled('status_expired'), function ($query) use ($request) {
                $today = now()->startOfDay();

                if ($request->status_expired === 'kadaluarsa') {
                    $query->whereDate('expired_date', '<=', $today);
                }

                if ($request->status_expired === '1_bulan') {
                    $query->whereDate('expired_date', '>', $today)
                        ->whereDate('expired_date', '<', $today->copy()->addMonth());
                }

                if ($request->status_expired === '3_bulan') {
                    $query->whereDate('expired_date', '>=', $today->copy()->addMonth())
                        ->whereDate('expired_date', '<', $today->copy()->addMonths(3));
                }

                if ($request->status_expired === 'normal') {
                    $query->whereDate('expired_date', '>=', $today->copy()->addMonths(3));
                }

                if ($request->status_expired === 'tidak_ada') {
                    $query->whereNull('expired_date');
                }
            })

            ->orderBy(
            Barang::select('nama')
                ->whereColumn('barangs.id', 'detail_penerimaans.barang_id')
            )
            ->orderBy('expired_date')
            ->get();

        // Ringkasan metrik stok sinkron dari data transaksi riil
        $totalStokAwal = $stokPerBatch->sum('jumlah');
        $totalStokTerjual = $stokPerBatch->sum(fn ($i) => (int) ($i->stok_terjual ?? 0));
        $totalStokRusak = $stokPerBatch->sum(fn ($i) => (int) ($i->stok_rusak ?? 0));
        $totalSisaStok = $totalStokAwal - $totalStokTerjual - $totalStokRusak;

        // Batch mendekati expired (<= 90 hari dari hari ini termasuk yang sudah kadaluarsa) - otomatis & tidak terpengaruh filter
        $mendekatiExpired = DetailPenerimaan::with(['barang.kategori'])
            ->withSum('detailPenjualan as stok_terjual', 'jumlah')
            ->withSum('rusak as stok_rusak', 'jumlah')
            ->where('aktif', true)
            ->where('stok', '>', 0)
            ->mendekatiExpired(90)
            ->orderBy('expired_date', 'asc')
            ->get();

        if (in_array($request->query('export'), ['excel', 'xlsx', 'csv'])) {
            $headers = [
                'No',
                'Barang',
                'Kategori',
                'No. Batch',
                'No. Rak',
                'Expired',
                'Status Expired',
                'Stok Awal',
                'Stok Terjual',
                'Stok Rusak',
                'Sisa Stok',
                'Status Stok',
            ];

            $today = now()->startOfDay();
            $data = [];
            foreach ($stokPerBatch as $index => $item) {
                $expiredDate = $item->expired_date
                    ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                    : null;

                $stokAwal = (int) $item->jumlah;
                $stokTerjual = (int) ($item->stok_terjual ?? 0);
                $stokRusak = (int) ($item->stok_rusak ?? 0);
                $sisaStok = $stokAwal - $stokTerjual - $stokRusak;
                $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);

                if (!$expiredDate) {
                    $statusExpired = 'Tidak Ada Tanggal';
                } elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today)) {
                    $statusExpired = 'Kadaluarsa';
                } elseif ($expiredDate->lt($today->copy()->addMonth())) {
                    $statusExpired = '< 1 Bulan';
                } elseif ($expiredDate->lt($today->copy()->addMonths(3))) {
                    $statusExpired = '< 3 Bulan';
                } else {
                    $statusExpired = 'Normal';
                }

                if ($sisaStok <= 0) {
                    $statusStok = 'Habis';
                } elseif ($sisaStok <= $stokMinimum) {
                    $statusStok = 'Menipis';
                } else {
                    $statusStok = 'Aman';
                }

                $data[] = [
                    $index + 1,
                    $item->barang->nama ?? '—',
                    $item->barang->kategori->nama ?? '—',
                    $item->no_batch ?? '—',
                    $item->no_rak ?? '—',
                    $expiredDate ? $expiredDate->format('d M Y') : '—',
                    $statusExpired,
                    $stokAwal,
                    $stokTerjual,
                    $stokRusak,
                    $sisaStok,
                    $statusStok,
                ];
            }

            $totalRow = [
                '',
                'Total',
                '',
                '',
                '',
                '',
                '',
                $totalStokAwal,
                $totalStokTerjual,
                $totalStokRusak,
                $totalSisaStok,
                '',
            ];

            return $this->exportXlsx('monitoring_stok_per_batch.xlsx', $headers, $data, $totalRow);
        }

        return view('laporan.stok', compact(
            'barangs',
            'stokPerBatch',
            'mendekatiExpired',
            'kategoris',
            'totalStokAwal',
            'totalStokTerjual',
            'totalStokRusak',
            'totalSisaStok'
        ));
    }

    // Laporan penerimaan barang dalam rentang tanggal
    public function penerimaan(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = DetailPenerimaan::with(['barang', 'penerimaan.supplier']);

        $query->whereHas('penerimaan', function ($q) use ($dari, $sampai) {
            if ($dari && $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            } elseif ($dari) {
                $q->where('tanggal', '>=', $dari);
            } elseif ($sampai) {
                $q->where('tanggal', '<=', $sampai);
            }
        });

        if ($request->filled('no_faktur')) {
            $query->whereHas('penerimaan', function ($q) use ($request) {
                $q->where('no_faktur', 'like', '%' . $request->no_faktur . '%');
            });
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('penerimaan', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->filled('nama_barang')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama_barang . '%');
            });
        }

        if ($request->filled('status_pembayaran')) {
            $status = $request->status_pembayaran;
            if ($status === 'lunas' || $status === '1') {
                $query->whereHas('penerimaan', function ($q) {
                    $q->where('lunas', true);
                });
            } elseif ($status === 'belum_lunas' || $status === '0') {
                $query->whereHas('penerimaan', function ($q) {
                    $q->where('lunas', false);
                });
            }
        }

        $items = $query->orderByDesc('created_at')->get();

        $totalNilai = $items->sum(fn ($i) => $i->harga_beli * $i->jumlah);

        if (in_array($request->query('export'), ['excel', 'xlsx', 'csv'])) {
            $headers = ['No', 'Tanggal', 'No. Faktur', 'Supplier', 'Nama Barang', 'Jumlah', 'Harga Beli', 'Total Nilai'];
            $data = [];
            foreach ($items as $index => $item) {
                $data[] = [
                    $index + 1,
                    $item->penerimaan->tanggal ? $item->penerimaan->tanggal->format('d M Y') : '—',
                    $item->penerimaan->no_faktur ?? '—',
                    $item->penerimaan->supplier->nama ?? '—',
                    $item->barang->nama ?? '—',
                    $item->jumlah,
                    $item->harga_beli,
                    $item->harga_beli * $item->jumlah
                ];
            }

            $totalRow = [
                '',
                'Total',
                '',
                '',
                '',
                $items->sum('jumlah'),
                '',
                $totalNilai,
            ];

            return $this->exportXlsx('laporan_penerimaan_barang.xlsx', $headers, $data, $totalRow);
        }

        $suppliers = Supplier::orderBy('nama')->get();

        return view('laporan.penerimaan', compact('items', 'totalNilai', 'dari', 'sampai', 'suppliers'));
    }

    // Laporan penjualan barang dalam rentang tanggal
    public function penjualan(Request $request)
{
    [$dari, $sampai] = $this->rentangTanggal($request);

    $query = \App\Models\Penjualan::with([
        'pelanggan',
        'detail.detailPenerimaan.barang.satuan',
        'user',
        'pembayaranPiutang',
    ])->whereBetween('tanggal', [$dari, $sampai]);

    // =========================================================
    // FILTER METODE PEMBAYARAN
    // =========================================================
    if ($request->filled('metode_pembayaran')) {
        $query->where(
            'metode_pembayaran',
            $request->input('metode_pembayaran')
        );
    }

    // =========================================================
    // FILTER PELANGGAN / MEMBER
    // =========================================================
    if ($request->filled('pelanggan')) {
        $pelanggan = $request->input('pelanggan');

        if ($pelanggan === 'pelanggan_umum') {
            $query->where(function ($q) {
                $q->whereNull('pelanggan_id')
                    ->orWhereHas('pelanggan', function ($q2) {
                        $q2->where('is_member', false);
                    });
            });
        }

        if ($pelanggan === 'pelanggan_tetap') {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('is_member', true)
                    ->where(
                        'status_member',
                        'Member Pelanggan Tetap'
                    );
            });
        }

        if ($pelanggan === 'keluarga_nakes') {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('is_member', true)
                    ->where(
                        'status_member',
                        'Member Keluarga Nakes'
                    );
            });
        }

        if ($pelanggan === 'member_only') {
            $query->whereHas('pelanggan', function ($q) {
                $q->where('is_member', true)
                    ->where(
                        'status_member',
                        'Member Only'
                    );
            });
        }
    }

    // =========================================================
    // FILTER JENIS TRANSAKSI
    // =========================================================
    if ($request->filled('jenis_transaksi')) {
        $query->where(
            'jenis_transaksi',
            $request->input('jenis_transaksi')
        );
    }

    // =========================================================
    // FILTER STATUS PIUTANG
    // =========================================================
    if ($request->filled('status_piutang')) {

        // Status piutang hanya berlaku untuk transaksi piutang
        $query->where('metode_pembayaran', 'piutang');

        $statusPiutang = $request->input('status_piutang');

        if ($statusPiutang === 'lunas') {

            $query->whereRaw('
                penjualans.total <= (
                    SELECT COALESCE(SUM(pp.jumlah), 0)
                    FROM pembayaran_piutangs pp
                    WHERE pp.penjualan_id = penjualans.id
                )
            ');

        } elseif ($statusPiutang === 'belum_lunas') {

            $query->whereRaw('
                penjualans.total > (
                    SELECT COALESCE(SUM(pp.jumlah), 0)
                    FROM pembayaran_piutangs pp
                    WHERE pp.penjualan_id = penjualans.id
                )
            ');

        } elseif ($statusPiutang === 'terlambat') {

            $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->whereRaw('
                    penjualans.total > (
                        SELECT COALESCE(SUM(pp.jumlah), 0)
                        FROM pembayaran_piutangs pp
                        WHERE pp.penjualan_id = penjualans.id
                    )
                ');
        }
    }

    // =========================================================
    // SORTING & AMBIL DATA
    // =========================================================
    $sort = $request->input('sort', 'tanggal');
    $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

    $allowedSorts = ['tanggal', 'no_faktur', 'invoice'];
    if (!in_array($sort, $allowedSorts, true)) {
        $sort = 'tanggal';
    }

    if ($sort === 'invoice' || $sort === 'no_faktur') {
        $query->orderBy('no_faktur', $direction)
            ->orderBy('tanggal', $direction);
    } else {
        $query->orderBy('tanggal', $direction)
            ->orderBy('no_faktur', $direction);
    }

    $penjualans = $query->get();

    // =========================================================
    // RINGKASAN
    // =========================================================
    $jumlahTransaksi = $penjualans->count();

    $totalDiskon = $penjualans->sum(function ($p) {
        return $p->detail->sum('diskon');
    });

    $totalPenjualanBersih = $penjualans->sum('total');

    $omzet = $totalPenjualanBersih + $totalDiskon;

    $transaksiMember = $penjualans->filter(function ($p) {
        return $p->pelanggan
            && $p->pelanggan->is_member;
    })->count();

    $transaksiNonMember =
        $jumlahTransaksi - $transaksiMember;

    // =========================================================
    // EXPORT EXCEL (XLSX)
    // =========================================================
    if (in_array($request->query('export'), ['excel', 'xlsx', 'csv'])) {

        $headers = [
            'Tanggal',
            'No. Invoice',
            'Nama Pelanggan',
            'Jenis Pelanggan',
            'Kasir',
            'Jenis Transaksi',
            'Metode Pembayaran',
            'Status Piutang',
            'Total Kotor',
            'Total Transaksi',
        ];

        $data = [];
        $totalKotor = 0;
        $totalTransaksi = 0;

        foreach ($penjualans as $p) {

            $diskon = $p->detail->sum('diskon');
            $kotor   = $p->total + $diskon;
            $totalKotor      += $kotor;
            $totalTransaksi  += $p->total;

            // Jenis pelanggan
            if ($p->pelanggan && $p->pelanggan->is_member) {
                $jenisPelanggan = $p->pelanggan->status_member ?: 'Member Pelanggan Tetap';
            } else {
                $jenisPelanggan = 'Pelanggan Umum';
            }

            // Jenis transaksi
            $jenisTransaksi = match($p->jenis_transaksi) {
                'resep'     => 'Resep',
                'non_resep' => 'Non Resep',
                default     => '—',
            };

            // Metode pembayaran
            $metode = match(strtolower((string) $p->metode_pembayaran)) {
                'cash'    => 'Cash',
                'qris'    => 'QRIS',
                'debit'   => 'Debit',
                'piutang' => 'Piutang',
                default   => ucfirst((string) ($p->metode_pembayaran ?? '—')),
            };

            // Status piutang
            if ($p->metode_pembayaran !== 'piutang') {
                $statusPiutang = '—';
            } else {
                $totalDibayar = $p->pembayaranPiutang->sum('jumlah');
                $sisa = max(0, $p->total - $totalDibayar);
                if ($sisa <= 0) {
                    $statusPiutang = 'Lunas';
                } elseif ($p->due_date && $p->due_date->isPast()) {
                    $statusPiutang = 'Terlambat';
                } else {
                    $statusPiutang = 'Belum Lunas';
                }
            }

            $data[] = [
                $p->tanggal ? $p->tanggal->format('d M Y') : '—',
                $p->no_faktur,
                $p->pelanggan->nama ?? 'Umum',
                $jenisPelanggan,
                $p->user->name ?? 'Admin',
                $jenisTransaksi,
                $metode,
                $statusPiutang,
                $kotor,
                $p->total,
            ];
        }

        $totalRow = [
            '',
            'Total',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalKotor,
            $totalTransaksi,
        ];

        return $this->exportXlsx(
            'laporan_penjualan_' . $dari . '_' . $sampai . '.xlsx',
            $headers,
            $data,
            $totalRow
        );
    }

    return view('laporan.penjualan', compact(
        'penjualans',
        'jumlahTransaksi',
        'omzet',
        'totalDiskon',
        'totalPenjualanBersih',
        'transaksiMember',
        'transaksiNonMember',
        'dari',
        'sampai',
        'sort',
        'direction'
    ));
}

    // Laporan barang rusak dalam rentang tanggal
    public function rusak(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = Rusak::with('detailPenerimaan.barang');

        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $query->where('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $query->where('tanggal', '<=', $sampai);
        }

        if ($request->filled('nama_barang')) {
            $query->whereHas('detailPenerimaan.barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama_barang . '%');
            });
        }

        if ($request->filled('barang')) {
            $query->whereHas('detailPenerimaan.barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->barang . '%');
            });
        }

        if ($request->filled('cari')) {
            $query->whereHas('detailPenerimaan.barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->filled('no_batch')) {
            $query->whereHas('detailPenerimaan', function ($q) use ($request) {
                $q->where('no_batch', 'like', '%' . $request->no_batch . '%');
            });
        }

        if ($request->filled('batch')) {
            $query->whereHas('detailPenerimaan', function ($q) use ($request) {
                $q->where('no_batch', 'like', '%' . $request->batch . '%');
            });
        }

        if ($request->filled('jenis')) {
            $query->where('keterangan', 'like', '%' . $request->jenis . '%');
        }

        if ($request->filled('keterangan')) {
            $query->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }

        $items = $query->orderByDesc('tanggal')->get();

        $totalKerugian = $items->sum(fn ($r) => $r->jumlah * ($r->detailPenerimaan->harga_beli ?? 0));

        if (in_array($request->query('export'), ['excel', 'xlsx', 'csv'])) {
            $headers = ['No', 'Tanggal Lapor', 'Nama Barang', 'No. Batch', 'Jumlah', 'Total Kerugian', 'Keterangan'];
            $data = [];
            foreach ($items as $index => $item) {
                $data[] = [
                    $index + 1,
                    $item->tanggal ? $item->tanggal->format('d M Y') : '—',
                    $item->detailPenerimaan->barang->nama ?? '—',
                    $item->detailPenerimaan->no_batch ?? '—',
                    $item->jumlah,
                    $item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0),
                    $item->keterangan ?? '-'
                ];
            }

            $totalRow = [
                '',
                'Total',
                '',
                '',
                $items->sum('jumlah'),
                $totalKerugian,
                '',
            ];

            return $this->exportXlsx('laporan_barang_rusak_kadaluwarsa.xlsx', $headers, $data, $totalRow);
        }

        return view('laporan.rusak', compact('items', 'totalKerugian', 'dari', 'sampai'));
    }

    // Laporan laba-rugi: Redesign komprehensif (Omzet, Diskon, Penjualan Bersih, HPP Perpetual, Laba Kotor, Beban Operasional, Laba Bersih, Piutang & Utang)
    public function labaRugi(Request $request)
    {
        // 1. Filter rentang tanggal
        $periode = $request->input('periode', 'bulan_ini');
        if ($request->filled('dari') || $request->filled('sampai')) {
            $dari = $request->input('dari', now()->startOfMonth()->format('Y-m-d'));
            $sampai = $request->input('sampai', now()->endOfMonth()->format('Y-m-d'));
            $periode = 'custom';
        } else {
            switch ($periode) {
                case 'hari_ini':
                    $dari = now()->startOfDay()->format('Y-m-d');
                    $sampai = now()->endOfDay()->format('Y-m-d');
                    break;
                case 'minggu_ini':
                    $dari = now()->startOfWeek()->format('Y-m-d');
                    $sampai = now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'bulan_lalu':
                    $dari = now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $sampai = now()->subMonth()->endOfMonth()->format('Y-m-d');
                    break;
                case 'tahun_ini':
                    $dari = now()->startOfYear()->format('Y-m-d');
                    $sampai = now()->endOfYear()->format('Y-m-d');
                    break;
                case 'semua':
                    $minTanggal = Penjualan::min('tanggal') ?? now()->startOfYear()->format('Y-m-d');
                    $dari = Carbon::parse($minTanggal)->format('Y-m-d');
                    $sampai = now()->endOfDay()->format('Y-m-d');
                    break;
                case 'bulan_ini':
                default:
                    $periode = 'bulan_ini';
                    $dari = now()->startOfMonth()->format('Y-m-d');
                    $sampai = now()->endOfMonth()->format('Y-m-d');
                    break;
            }
        }

        // 2. Hitung metrik Penjualan & HPP Perpetual (berdasarkan relasi detail penjualan & penerimaan)
        $salesData = DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('
                COALESCE(SUM(dp.harga_jual * dp.jumlah), 0) as omzet_kotor,
                COALESCE(SUM(dp.diskon), 0) as total_diskon,
                COALESCE(SUM(dp.subtotal), 0) as penjualan_bersih,
                COALESCE(SUM(dr.harga_beli * dp.jumlah), 0) as total_hpp,
                COUNT(DISTINCT p.id) as jumlah_transaksi,
                COALESCE(SUM(dp.jumlah), 0) as total_item_terjual
            ')
            ->first();

        $omzetKotor = (float) ($salesData->omzet_kotor ?? 0);
        $totalDiskon = (float) ($salesData->total_diskon ?? 0);
        $penjualanBersih = max(0, $omzetKotor - $totalDiskon);
        $hpp = (float) ($salesData->total_hpp ?? 0);
        $labaKotor = $penjualanBersih - $hpp;
        $jumlahTransaksi = (int) ($salesData->jumlah_transaksi ?? 0);
        $totalItemTerjual = (int) ($salesData->total_item_terjual ?? 0);

        // 3. Beban Operasional dalam rentang tanggal
        $totalBeban = (float) Beban::whereBetween('tanggal', [$dari, $sampai])->sum('nominal');
        $labaBersih = $labaKotor - $totalBeban;

        $bebanPerKategori = Beban::whereBetween('tanggal', [$dari, $sampai])
            ->selectRaw('kategori, SUM(nominal) as total, COUNT(*) as jumlah_transaksi')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        $daftarBeban = Beban::with('user')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        // 4. Financial Control: Piutang Member & Hutang Supplier (sumber data riil, selaras dengan Dashboard)
        $today = now()->startOfDay();

        // Hutang Supplier (Penerimaan belum lunas)
        $unpaidPenerimaans = Penerimaan::with(['detail', 'pembayaran'])
            ->where('lunas', false)
            ->get();

        $totalHutangSupplier = 0;
        $hutangOverdue = 0;
        $countHutangBelumLunas = 0;

        foreach ($unpaidPenerimaans as $p) {
            $sisa = $p->sisaTagihan();
            if ($sisa <= 0) {
                continue;
            }
            $totalHutangSupplier += $sisa;
            $countHutangBelumLunas++;
            if ($p->jatuh_tempo && $p->jatuh_tempo->lt($today)) {
                $hutangOverdue += $sisa;
            }
        }

        // Piutang Member
        $totalPiutangMember = (float) DB::table('pelanggans')->where('is_member', true)->sum('saldo_piutang');
        $memberBerpiutangCount = (int) DB::table('pelanggans')->where('is_member', true)->where('saldo_piutang', '>', 0)->count();

        $unpaidPiutangPenjualans = Penjualan::with('pembayaranPiutang')
            ->where('metode_pembayaran', 'piutang')
            ->get()
            ->map(function ($pj) {
                $dibayar = (float) $pj->pembayaranPiutang->sum('jumlah');
                $sisa = max(0, (float) $pj->total - $dibayar);
                return [
                    'sisa' => $sisa,
                    'tanggal' => $pj->tanggal,
                    'due_date' => $pj->due_date,
                ];
            })
            ->filter(fn ($pj) => $pj['sisa'] > 0);

        $piutangOverdue = 0;
        $countPiutangBelumLunas = $unpaidPiutangPenjualans->count();

        foreach ($unpaidPiutangPenjualans as $item) {
            $dueDate = $item['due_date'] ? Carbon::parse($item['due_date']) : ($item['tanggal'] ? Carbon::parse($item['tanggal'])->addDays(30) : null);
            if ($dueDate && $dueDate->lt($today)) {
                $piutangOverdue += $item['sisa'];
            }
        }

        // 5. Margin persentase
        $marginKotorPersen = $penjualanBersih > 0 ? round(($labaKotor / $penjualanBersih) * 100, 1) : 0;
        $marginBersihPersen = $penjualanBersih > 0 ? round(($labaBersih / $penjualanBersih) * 100, 1) : 0;
        $hppPersen = $penjualanBersih > 0 ? round(($hpp / $penjualanBersih) * 100, 1) : 0;
        $diskonPersen = $omzetKotor > 0 ? round(($totalDiskon / $omzetKotor) * 100, 1) : 0;
        $bebanPersen = $penjualanBersih > 0 ? round(($totalBeban / $penjualanBersih) * 100, 1) : 0;

        // 6. Rincian Tren Harian Penjualan & Laba
        $rincianHarian = DB::table('penjualans as p')
            ->join('detail_penjualans as dp', 'p.id', '=', 'dp.penjualan_id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('
                DATE(p.tanggal) as tanggal,
                SUM(dp.harga_jual * dp.jumlah) as omzet_kotor,
                SUM(dp.diskon) as total_diskon,
                SUM(dp.subtotal) as penjualan_bersih,
                SUM(dr.harga_beli * dp.jumlah) as hpp,
                SUM(dp.subtotal) - SUM(dr.harga_beli * dp.jumlah) as laba_kotor,
                COUNT(DISTINCT p.id) as transaksi_count
            ')
            ->groupBy(DB::raw('DATE(p.tanggal)'))
            ->orderBy('tanggal', 'desc')
            ->get();

        $bebanHarian = Beban::whereBetween('tanggal', [$dari, $sampai])
            ->selectRaw('DATE(tanggal) as tanggal, SUM(nominal) as total_beban')
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->pluck('total_beban', 'tanggal');

        // 7. Ekspor Excel (.xlsx) menggunakan OpenSpout
        if (in_array($request->query('export'), ['excel', 'xlsx'])) {
            $filename = 'laporan_laba_rugi_' . $dari . '_' . $sampai . '.xlsx';
            $headers = ['Komponen Laporan', 'Keterangan', 'Persentase', 'Nominal (Rp)'];
            $data = [
                ['I. PENDAPATAN PENJUALAN', '', '', ''],
                ['  Omzet Kotor (Gross Sales)', 'Total nilai penjualan sebelum potongan diskon', ($omzetKotor > 0 ? '100.0%' : '0%'), $omzetKotor],
                ['  Total Diskon', 'Potongan diskon member dan promosi khusus', ($omzetKotor > 0 ? $diskonPersen . '%' : '0%'), -$totalDiskon],
                ['PENJUALAN BERSIH (NET SALES)', 'Omzet Kotor dikurangi Total Diskon', '100.0%', $penjualanBersih],
                ['', '', '', ''],
                ['II. HARGA POKOK PENJUALAN (HPP)', '', '', ''],
                ['  HPP Barang Terjual (Perpetual)', 'Total akumulasi harga beli batch barang terjual', ($penjualanBersih > 0 ? $hppPersen . '%' : '0%'), -$hpp],
                ['', '', '', ''],
                ['III. LABA KOTOR (GROSS PROFIT)', 'Penjualan Bersih dikurangi HPP', ($penjualanBersih > 0 ? $marginKotorPersen . '%' : '0%'), $labaKotor],
                ['', '', '', ''],
                ['IV. BEBAN OPERASIONAL', '', '', ''],
            ];

            foreach ($bebanPerKategori as $bpk) {
                $pct = $penjualanBersih > 0 ? round(($bpk->total / $penjualanBersih) * 100, 1) . '%' : '0%';
                $data[] = [
                    '  Beban ' . $bpk->kategori,
                    $bpk->jumlah_transaksi . ' transaksi pengeluaran tercatat',
                    $pct,
                    -$bpk->total
                ];
            }
            if ($bebanPerKategori->isEmpty()) {
                $data[] = ['  Beban Operasional', 'Belum ada catatan beban operasional pada periode ini', '0%', 0];
            }

            $data[] = ['TOTAL BEBAN OPERASIONAL', 'Akumulasi beban operasional periode ini', ($penjualanBersih > 0 ? $bebanPersen . '%' : '0%'), -$totalBeban];
            $data[] = ['', '', '', ''];
            $data[] = ['V. LABA BERSIH OPERASIONAL (NET PROFIT)', 'Laba Kotor dikurangi Total Beban Operasional', ($penjualanBersih > 0 ? $marginBersihPersen . '%' : '0%'), $labaBersih];
            $data[] = ['', '', '', ''];
            $data[] = ['VI. POSISI KEUANGAN OUTSTANDING (TERKINI)', '', '', ''];
            $data[] = ['  Total Piutang Member', $memberBerpiutangCount . ' member memiliki tagihan aktif', '', $totalPiutangMember];
            $data[] = ['  Total Utang Supplier', $countHutangBelumLunas . ' penerimaan barang belum lunas', '', $totalHutangSupplier];

            $totalRow = [
                'LABA BERSIH AKHIR PERIODE',
                'Periode: ' . $dari . ' s/d ' . $sampai,
                ($penjualanBersih > 0 ? $marginBersihPersen . '%' : '0%'),
                $labaBersih
            ];

            return $this->exportXlsx($filename, $headers, $data, $totalRow);
        }

        return view('laporan.laba_rugi', compact(
            'dari',
            'sampai',
            'periode',
            'omzetKotor',
            'totalDiskon',
            'penjualanBersih',
            'hpp',
            'labaKotor',
            'totalBeban',
            'labaBersih',
            'marginKotorPersen',
            'marginBersihPersen',
            'hppPersen',
            'diskonPersen',
            'bebanPersen',
            'jumlahTransaksi',
            'totalItemTerjual',
            'totalPiutangMember',
            'memberBerpiutangCount',
            'piutangOverdue',
            'countPiutangBelumLunas',
            'totalHutangSupplier',
            'hutangOverdue',
            'countHutangBelumLunas',
            'bebanPerKategori',
            'daftarBeban',
            'rincianHarian',
            'bebanHarian'
        ));
    }

    // Simpan beban operasional baru
    public function storeBeban(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:1',
        ], [
            'tanggal.required' => 'Tanggal pengeluaran wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'kategori.required' => 'Kategori pengeluaran wajib dipilih/diisi.',
            'nominal.required' => 'Nominal pengeluaran wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka numerik valid.',
            'nominal.min' => 'Nominal pengeluaran minimal Rp 1.',
        ]);

        $beban = Beban::create([
            'user_id' => $request->user()?->id,
            'tanggal' => $validated['tanggal'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'] ?? null,
            'nominal' => $validated['nominal'],
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log(
                'Tambah Beban Operasional',
                "Kategori: {$beban->kategori}, Nominal: Rp " . number_format($beban->nominal, 0, ',', '.') . ($beban->keterangan ? " ({$beban->keterangan})" : ''),
                ActivityLog::CATEGORY_KEUANGAN
            );
        }

        DashboardCacheService::clear();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beban operasional sebesar Rp ' . number_format($beban->nominal, 0, ',', '.') . ' berhasil disimpan.',
                'data' => $beban,
            ]);
        }

        return redirect()->back()->with('success', 'Beban operasional sebesar Rp ' . number_format($beban->nominal, 0, ',', '.') . ' berhasil ditambahkan.');
    }

    // Hapus data beban operasional
    public function destroyBeban(Request $request, Beban $beban)
    {
        $nominal = $beban->nominal;
        $kategori = $beban->kategori;
        $beban->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log(
                'Hapus Beban Operasional',
                "Hapus beban kategori: {$kategori}, Nominal: Rp " . number_format($nominal, 0, ',', '.'),
                ActivityLog::CATEGORY_KEUANGAN
            );
        }

        DashboardCacheService::clear();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Beban operasional berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Beban operasional berhasil dihapus.');
    }

    // Laporan penggunaan diskon (Fase 3)
    public function diskon(Request $request)
    {
        [$dari, $sampai] = $this->rentangTanggal($request);

        $query = \App\Models\DiscountUsage::with(['penjualan.pelanggan', 'customDiscount'])
            ->whereHas('penjualan', function ($q) use ($dari, $sampai) {
                $q->whereBetween('tanggal', [$dari, $sampai]);
            });

        // Filter: jenis (member / custom)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter: promo_id
        if ($request->filled('promo_id')) {
            $query->where('custom_discount_id', $request->promo_id);
        }

        // Filter: status pelanggan (member / umum)
        if ($request->filled('status_pelanggan')) {
            if ($request->status_pelanggan === 'member') {
                $query->whereHas('penjualan.pelanggan', function ($q) {
                    $q->where('is_member', true);
                });
            } else if ($request->status_pelanggan === 'umum') {
                $query->where(function ($q) {
                    $q->whereHas('penjualan.pelanggan', function ($q2) {
                        $q2->where('is_member', false);
                    })->orWhereNull('penjualan.pelanggan_id');
                });
            }
        }

        $usages = $query->orderByDesc('created_at')->get();
        $totalNominal = $usages->sum('nominal');

        // Untuk dropdown filter promo
        $promos = \App\Models\CustomDiscount::orderBy('nama')->get();

        if ($request->query('export') === 'csv') {
            $headers = ['Tanggal', 'No. Faktur', 'Barang', 'Pelanggan', 'Jenis Diskon', 'Nama Promo', 'Persentase', 'Nominal'];
            $data = [];
            foreach ($usages as $usage) {
                $data[] = [
                    $usage->created_at->format('d M Y H:i'),
                    $usage->penjualan->no_faktur ?? '',
                    $usage->barang_nama,
                    $usage->penjualan->pelanggan->nama ?? 'Umum',
                    $usage->jenis,
                    $usage->custom_discount_nama ?? '-',
                    $usage->persentase,
                    $usage->nominal
                ];
            }
            return $this->exportCsv('laporan-diskon-' . $dari . '-' . $sampai . '.csv', $headers, $data);
        }

        return view('laporan.diskon', compact('usages', 'totalNominal', 'promos', 'dari', 'sampai'));
    }

    // Helper: export data ke format Excel (.xlsx) menggunakan OpenSpout
    private function exportXlsx(string $filename, array $headers, array $data, ?array $totalRow = null)
    {
        return response()->streamDownload(function () use ($headers, $data, $totalRow) {
            $writer = new Writer();
            $writer->openToFile('php://output');

            $headerStyle = (new Style())
                ->setFontBold()
                ->setFontColor('FFFFFF')
                ->setBackgroundColor('1E40AF');

            $writer->addRow(Row::fromValues($headers, $headerStyle));

            foreach ($data as $row) {
                $writer->addRow(Row::fromValues($row));
            }

            if ($totalRow) {
                $totalStyle = (new Style())
                    ->setFontBold()
                    ->setBackgroundColor('F3F4F6');
                $writer->addRow(Row::fromValues($totalRow, $totalStyle));
            }

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Helper: export data ke format CSV native PHP stream
    private function exportCsv(string $filename, array $headers, array $data)
    {
        $callback = function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    // Helper: ambil rentang tanggal dari query string, default bulan berjalan
    private function rentangTanggal(Request $request): array
    {
        $dari = $request->filled('dari') ? $request->dari : now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->filled('sampai') ? $request->sampai : now()->endOfMonth()->format('Y-m-d');

        return [$dari, $sampai];
    }
}
