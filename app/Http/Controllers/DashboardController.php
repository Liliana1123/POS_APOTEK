<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\DiscountUsage;
use App\Models\CustomDiscount;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Models\DetailPenerimaan;
use App\Models\Penerimaan;
use App\Models\PembayaranPenerimaan;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->startOfDay();
        $periode = $request->input('periode', 'bulan_ini');

        // 1. Tentukan rentang tanggal periode terpilih & periode komparasi
        switch ($periode) {
            case 'hari_ini':
                $dari = now()->startOfDay()->format('Y-m-d');
                $sampai = now()->endOfDay()->format('Y-m-d');
                $prevDari = now()->subDay()->startOfDay()->format('Y-m-d');
                $prevSampai = now()->subDay()->endOfDay()->format('Y-m-d');
                $periodeLabel = 'Hari Ini';
                break;
            case 'minggu_ini':
                $dari = now()->startOfWeek()->format('Y-m-d');
                $sampai = now()->endOfWeek()->format('Y-m-d');
                $prevDari = now()->subWeek()->startOfWeek()->format('Y-m-d');
                $prevSampai = now()->subWeek()->endOfWeek()->format('Y-m-d');
                $periodeLabel = 'Minggu Ini';
                break;
            case 'tahun_ini':
                $dari = now()->startOfYear()->format('Y-m-d');
                $sampai = now()->endOfYear()->format('Y-m-d');
                $prevDari = now()->subYear()->startOfYear()->format('Y-m-d');
                $prevSampai = now()->subYear()->endOfYear()->format('Y-m-d');
                $periodeLabel = 'Tahun Ini';
                break;
            case 'custom':
                $dari = $request->input('dari', now()->startOfMonth()->format('Y-m-d'));
                $sampai = $request->input('sampai', now()->endOfMonth()->format('Y-m-d'));
                $daysDiff = Carbon::parse($dari)->diffInDays(Carbon::parse($sampai)) + 1;
                $prevDari = Carbon::parse($dari)->subDays($daysDiff)->format('Y-m-d');
                $prevSampai = Carbon::parse($dari)->subDay()->format('Y-m-d');
                $periodeLabel = 'Custom';
                break;
            case 'bulan_ini':
            default:
                $periode = 'bulan_ini';
                $dari = now()->startOfMonth()->format('Y-m-d');
                $sampai = now()->endOfMonth()->format('Y-m-d');
                $prevDari = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $prevSampai = now()->subMonth()->endOfMonth()->format('Y-m-d');
                $periodeLabel = 'Bulan Ini';
                break;
        }

        $lastUpdated = now()->locale('id')->isoFormat('D MMMM Y HH:mm');

        // 2. KPI KEUANGAN UTAMA (Berdasarkan Periode)
        $omzetKotor = (float) DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('COALESCE(SUM(dp.harga_jual * dp.jumlah), 0) as total')
            ->value('total');

        $prevOmzetKotor = (float) DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->whereBetween('p.tanggal', [$prevDari, $prevSampai])
            ->selectRaw('COALESCE(SUM(dp.harga_jual * dp.jumlah), 0) as total')
            ->value('total');

        $totalDiskon = (float) DiscountUsage::whereHas('penjualan', function ($q) use ($dari, $sampai) {
            $q->whereBetween('tanggal', [$dari, $sampai]);
        })->sum('nominal');

        $prevTotalDiskon = (float) DiscountUsage::whereHas('penjualan', function ($q) use ($prevDari, $prevSampai) {
            $q->whereBetween('tanggal', [$prevDari, $prevSampai]);
        })->sum('nominal');

        $omzetBersih = max(0, $omzetKotor - $totalDiskon);
        $prevOmzetBersih = max(0, $prevOmzetKotor - $prevTotalDiskon);

        // Gross Profit periode saat ini
        $grossProfit = (float) DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('
                COALESCE(
                    SUM(dp.subtotal) - SUM(dr.harga_beli * dp.jumlah),
                    0
                ) as total
            ')
            ->value('total');

        // Gross Profit periode sebelumnya
        $prevGrossProfit = (float) DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->whereBetween('p.tanggal', [$prevDari, $prevSampai])
            ->selectRaw('
                COALESCE(
                    SUM(dp.subtotal) - SUM(dr.harga_beli * dp.jumlah),
                    0
                ) as total
            ')
            ->value('total');

$deltaGrossProfit = $prevGrossProfit > 0
    ? round((($grossProfit - $prevGrossProfit) / $prevGrossProfit) * 100, 1)
    : null;

        $totalMember = Pelanggan::where('is_member', true)->count();
        $newMembersInPeriod = Pelanggan::where('is_member', true)
            ->whereBetween('member_since', [$dari, $sampai])
            ->count();
        $prevNewMembers = Pelanggan::where('is_member', true)
            ->whereBetween('member_since', [$prevDari, $prevSampai])
            ->count();

        $deltaOmzetKotor = $prevOmzetKotor > 0 ? round((($omzetKotor - $prevOmzetKotor) / $prevOmzetKotor) * 100, 1) : null;
        $deltaTotalDiskon = $prevTotalDiskon > 0 ? round((($totalDiskon - $prevTotalDiskon) / $prevTotalDiskon) * 100, 1) : null;
        $deltaOmzetBersih = $prevOmzetBersih > 0 ? round((($omzetBersih - $prevOmzetBersih) / $prevOmzetBersih) * 100, 1) : null;
        $deltaMember = $prevNewMembers > 0 ? round((($newMembersInPeriod - $prevNewMembers) / $prevNewMembers) * 100, 1) : null;

        // 3. FINANCIAL CONTROL (Posisi Outstanding Terkini)
        // A. Hutang Supplier (Penerimaan belum lunas)
        $unpaidPenerimaans = Penerimaan::with(['detail', 'pembayaran'])
            ->where('lunas', false)
            ->get();

        $totalHutangSupplier = 0;
        $hutangOverdue = 0;
        $hutangDueSoon = 0;
        $hutangNotDue = 0;
        $countHutangOverdue = 0;
        $countHutangDueSoon = 0;
        $countHutangNotDue = 0;

        foreach ($unpaidPenerimaans as $p) {
            $sisa = $p->sisaTagihan();
            if ($sisa <= 0) {
                continue;
            }
            $totalHutangSupplier += $sisa;

            if ($p->jatuh_tempo) {
                if ($p->jatuh_tempo->lt($today)) {
                    $hutangOverdue += $sisa;
                    $countHutangOverdue++;
                } elseif ($p->jatuh_tempo->lte($today->copy()->addDays(7))) {
                    $hutangDueSoon += $sisa;
                    $countHutangDueSoon++;
                } else {
                    $hutangNotDue += $sisa;
                    $countHutangNotDue++;
                }
            } else {
                $hutangNotDue += $sisa;
                $countHutangNotDue++;
            }
        }

        // B. Piutang Member
        $totalPiutangMember = (float) Pelanggan::where('is_member', true)->sum('saldo_piutang');
        $memberBerpiutangCount = Pelanggan::where('is_member', true)->where('saldo_piutang', '>', 0)->count();

        // Breakdown umur piutang dari transaksi kasir piutang jika ada
        $unpaidPiutangPenjualans = Penjualan::with('pembayaranPiutang')
            ->where('metode_pembayaran', 'piutang')
            ->get()
            ->map(function ($pj) {
                $dibayar = (float) $pj->pembayaranPiutang->sum('jumlah');
                $sisa = max(0, (float) $pj->total - $dibayar);
                return [
                    'sisa' => $sisa,
                    'tanggal' => $pj->tanggal,
                ];
            })
            ->filter(fn ($pj) => $pj['sisa'] > 0);

        $piutangOverdue = 0;
        $piutangDueSoon = 0;
        $piutangNotDue = 0;
        $countPiutangOverdue = 0;
        $countPiutangDueSoon = 0;
        $countPiutangNotDue = 0;

        foreach ($unpaidPiutangPenjualans as $item) {
            // Standar termin piutang apotek 30 hari dari tanggal transaksi
            $dueDate = $item['tanggal'] ? Carbon::parse($item['tanggal'])->addDays(30) : null;
            if ($dueDate) {
                if ($dueDate->lt($today)) {
                    $piutangOverdue += $item['sisa'];
                    $countPiutangOverdue++;
                } elseif ($dueDate->lte($today->copy()->addDays(7))) {
                    $piutangDueSoon += $item['sisa'];
                    $countPiutangDueSoon++;
                } else {
                    $piutangNotDue += $item['sisa'];
                    $countPiutangNotDue++;
                }
            } else {
                $piutangNotDue += $item['sisa'];
                $countPiutangNotDue++;
            }
        }

        // 4. GRAFIK PENJUALAN & LABA (Time-series data untuk Chart.js)
        $carbonDari = Carbon::parse($dari);
        $carbonSampai = Carbon::parse($sampai);
        $diffDays = $carbonDari->diffInDays($carbonSampai);

        $dailySales = DB::table('penjualans as p')
            ->join('detail_penjualans as dp', 'p.id', '=', 'dp.penjualan_id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('
                DATE(p.tanggal) as date_val,
                SUM(dp.harga_jual * dp.jumlah) as gross_sales,
                SUM(dp.diskon) as line_discount,
                SUM(dp.subtotal) as net_sales,
                SUM(dr.harga_beli * dp.jumlah) as total_hpp
            ')
            ->groupBy(DB::raw('DATE(p.tanggal)'))
            ->get()
            ->keyBy('date_val');

        $dailyDiscounts = DB::table('discount_usages as du')
            ->join('penjualans as p', 'du.penjualan_id', '=', 'p.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->selectRaw('DATE(p.tanggal) as date_val, SUM(du.nominal) as total_discount')
            ->groupBy(DB::raw('DATE(p.tanggal)'))
            ->get()
            ->keyBy('date_val');

        $chartLabels = [];
        $chartGross = [];
        $chartNet = [];
        $chartProfit = [];

        if ($diffDays <= 31) {
            for ($d = $carbonDari->copy(); $d->lte($carbonSampai); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                $chartLabels[] = $d->format('d M');
                $row = $dailySales->get($dateKey);
                $gross = $row ? (float) $row->gross_sales : 0;
                $disc = isset($dailyDiscounts[$dateKey]) ? (float) $dailyDiscounts[$dateKey]->total_discount : ($row ? (float) $row->line_discount : 0);
                $net = max(0, $gross - $disc);
                $hpp = $row ? (float) $row->total_hpp : 0;
                $profit = max(0, $net - $hpp);

                $chartGross[] = $gross;
                $chartNet[] = $net;
                $chartProfit[] = $profit;
            }
        } else {
            $monthlySales = DB::table('penjualans as p')
                ->join('detail_penjualans as dp', 'p.id', '=', 'dp.penjualan_id')
                ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
                ->whereBetween('p.tanggal', [$dari, $sampai])
                ->selectRaw('
                    DATE_FORMAT(p.tanggal, "%Y-%m") as month_val,
                    SUM(dp.harga_jual * dp.jumlah) as gross_sales,
                    SUM(dp.diskon) as line_discount,
                    SUM(dp.subtotal) as net_sales,
                    SUM(dr.harga_beli * dp.jumlah) as total_hpp
                ')
                ->groupBy(DB::raw('DATE_FORMAT(p.tanggal, "%Y-%m")'))
                ->get()
                ->keyBy('month_val');

            $monthlyDiscounts = DB::table('discount_usages as du')
                ->join('penjualans as p', 'du.penjualan_id', '=', 'p.id')
                ->whereBetween('p.tanggal', [$dari, $sampai])
                ->selectRaw('DATE_FORMAT(p.tanggal, "%Y-%m") as month_val, SUM(du.nominal) as total_discount')
                ->groupBy(DB::raw('DATE_FORMAT(p.tanggal, "%Y-%m")'))
                ->get()
                ->keyBy('month_val');

            for ($d = $carbonDari->copy()->startOfMonth(); $d->lte($carbonSampai); $d->addMonth()) {
                $monthKey = $d->format('Y-m');
                $chartLabels[] = $d->format('M Y');
                $row = $monthlySales->get($monthKey);
                $gross = $row ? (float) $row->gross_sales : 0;
                $disc = isset($monthlyDiscounts[$monthKey]) ? (float) $monthlyDiscounts[$monthKey]->total_discount : ($row ? (float) $row->line_discount : 0);
                $net = max(0, $gross - $disc);
                $hpp = $row ? (float) $row->total_hpp : 0;
                $profit = max(0, $net - $hpp);

                $chartGross[] = $gross;
                $chartNet[] = $net;
                $chartProfit[] = $profit;
            }
        }

        // 5. INVENTORY CONTROL CENTER (Posisi Stok Aktual Terkini)
        $totalStok = (int) DetailPenerimaan::where('aktif', true)->sum('stok');
        $nilaiPersediaan = (float) DetailPenerimaan::where('aktif', true)
            ->selectRaw('COALESCE(SUM(stok * harga_beli), 0) as total')
            ->value('total');

        // Evaluasi kondisi stok per barang (Single source of truth dengan Laporan Stok)
        $barangsStok = Barang::with('kategori')
            ->withSum(['detailPenerimaan' => function ($query) {
                $query->where('aktif', true);
            }], 'stok')
            ->where('aktif', true)
            ->get();

        $totalSku = $barangsStok->count();
        $stokHabisCount = 0;
        $stokMenipisCount = 0;
        $stokAmanCount = 0;
        $barangHarusDibeliList = collect();

        foreach ($barangsStok as $b) {
            $stok = $b->stokTotal();
            $min = (int) $b->stok_minimum;

            if ($stok == 0) {
                $stokHabisCount++;
                $barangHarusDibeliList->push([
                    'barang' => $b,
                    'stok' => $stok,
                    'stok_minimum' => $min,
                    'kekurangan' => max(1, $min),
                    'status' => 'HABIS',
                ]);
            } elseif ($stok <= $min) {
                $stokMenipisCount++;
                $barangHarusDibeliList->push([
                    'barang' => $b,
                    'stok' => $stok,
                    'stok_minimum' => $min,
                    'kekurangan' => max(0, $min - $stok),
                    'status' => 'SEGERA BELI',
                ]);
            } else {
                $stokAmanCount++;
            }
        }

        // 6. BARANG YANG HARUS DIBELI (Maksimal 10 item)
        $barangHarusDibeli = $barangHarusDibeliList->sortBy([
            ['stok', 'asc'],
            ['kekurangan', 'desc'],
        ])->take(10)->values();

        // 7. EXPIRY HEALTH (Sesuai klasifikasi Laporan Stok)
        $today = now()->startOfDay();
        $oneMonth = $today->copy()->addMonth();
        $threeMonths = $today->copy()->addMonths(3);

        $baseBatchQuery = DetailPenerimaan::where('aktif', true)->where('stok', '>', 0);
        $totalBatchesCount = (clone $baseBatchQuery)->count();

        $expiryKadaluarsa = (clone $baseBatchQuery)->whereDate('expired_date', '<=', $today)->count();
        $expiryCritical = (clone $baseBatchQuery)->whereDate('expired_date', '>', $today)->whereDate('expired_date', '<=', $oneMonth)->count();
        $expiryWarning = (clone $baseBatchQuery)->whereDate('expired_date', '>', $oneMonth)->whereDate('expired_date', '<=', $threeMonths)->count();
        $expiryAman = (clone $baseBatchQuery)->where(function ($q) use ($threeMonths) {
            $q->whereDate('expired_date', '>', $threeMonths)
              ->orWhereNull('expired_date');
        })->count();

        // 8. ACTION CENTER (Alerts Dinamis dengan Link Cepat)
        $actionCenter = [
            'critical' => [
                'count' => $stokHabisCount,
                'title' => 'CRITICAL',
                'label' => 'Barang stok habis',
                'route' => route('laporan.stok', ['status_stok' => 'habis']),
            ],
            'attention' => [
                'count' => $stokMenipisCount,
                'title' => 'ATTENTION',
                'label' => 'Barang stok menipis',
                'route' => route('laporan.stok', ['status_stok' => 'menipis']),
            ],
            'monitoring' => [
                'count' => $expiryCritical,
                'title' => 'MONITORING',
                'label' => 'Mendekati expired (< 30 hari)',
                'route' => route('laporan.stok', ['status_expired' => '1_bulan']),
            ],
            'overdue' => [
                'count' => $countHutangOverdue,
                'title' => 'OVERDUE',
                'label' => 'Hutang supplier jatuh tempo',
                'route' => route('penerimaan.index', ['status_pembayaran' => 'belum_lunas']),
            ],
            'piutang' => [
                'count' => $memberBerpiutangCount,
                'title' => 'PIUTANG',
                'label' => 'Member memiliki piutang',
                'route' => route('pelanggan.index', ['status_piutang' => 'belum_lunas']),
            ],
        ];

        // 9. 10 OBAT TERLARIS (Sesuai Periode)
        $topSellingMedicines = DB::table('detail_penjualans as dp')
            ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
            ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
            ->join('barangs as b', 'dr.barang_id', '=', 'b.id')
            ->leftJoin('kategoris as k', 'b.kategori_id', '=', 'k.id')
            ->whereBetween('p.tanggal', [$dari, $sampai])
            ->select(
                'b.id',
                'b.nama',
                'k.nama as kategori',
                DB::raw('SUM(dp.jumlah) as total_terjual'),
                DB::raw('SUM(dp.subtotal) as total_omzet')
            )
            ->groupBy('b.id', 'b.nama', 'k.nama')
            ->orderByDesc('total_terjual')
            ->orderBy('b.nama')
            ->limit(10)
            ->get();

        // 10. 10 OBAT PALING SEDIKIT TERJUAL (Sesuai Periode, mencakup barang yang belum terjual = 0)
        $leastSellingMedicines = DB::table('barangs as b')
            ->leftJoin('kategoris as k', 'b.kategori_id', '=', 'k.id')
            ->leftJoin('detail_penerimaans as dr', 'b.id', '=', 'dr.barang_id')
            ->leftJoin('detail_penjualans as dp', function ($join) use ($dari, $sampai) {
                $join->on('dr.id', '=', 'dp.detail_penerimaan_id')
                    ->whereExists(function ($query) use ($dari, $sampai) {
                        $query->select(DB::raw(1))
                            ->from('penjualans as p')
                            ->whereColumn('p.id', 'dp.penjualan_id')
                            ->whereBetween('p.tanggal', [$dari, $sampai]);
                    });
            })
            ->where('b.aktif', true)
            ->select(
                'b.id',
                'b.nama',
                'k.nama as kategori',
                DB::raw('COALESCE(SUM(dp.jumlah), 0) as total_terjual'),
                DB::raw('COALESCE(SUM(dp.subtotal), 0) as total_omzet')
            )
            ->groupBy('b.id', 'b.nama', 'k.nama')
            ->orderBy('total_terjual')
            ->orderBy('b.nama')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'periode',
            'periodeLabel',
            'dari',
            'sampai',
            'lastUpdated',
            'omzetKotor',
            'totalDiskon',
            'omzetBersih',
            'grossProfit',
            'deltaGrossProfit',
            'totalMember',
            'newMembersInPeriod',
            'deltaOmzetKotor',
            'deltaTotalDiskon',
            'deltaOmzetBersih',
            'deltaMember',
            'totalHutangSupplier',
            'hutangOverdue',
            'hutangDueSoon',
            'hutangNotDue',
            'countHutangOverdue',
            'countHutangDueSoon',
            'countHutangNotDue',
            'totalPiutangMember',
            'piutangOverdue',
            'piutangDueSoon',
            'piutangNotDue',
            'countPiutangOverdue',
            'countPiutangDueSoon',
            'countPiutangNotDue',
            'memberBerpiutangCount',
            'chartLabels',
            'chartGross',
            'chartNet',
            'chartProfit',
            'totalSku',
            'totalStok',
            'nilaiPersediaan',
            'stokHabisCount',
            'stokMenipisCount',
            'stokAmanCount',
            'expiryKadaluarsa',
            'expiryCritical',
            'expiryWarning',
            'expiryAman',
            'totalBatchesCount',
            'actionCenter',
            'barangHarusDibeli',
            'topSellingMedicines',
            'leastSellingMedicines'
        ));
    }

    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('created_at', [$request->dari . ' 00:00:00', $request->sampai . ' 23:59:59']);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function($q) use ($cari) {
                $q->where('action', 'like', '%' . $cari . '%')
                  ->orWhereHas('user', function($qu) use ($cari) {
                      $qu->where('name', 'like', '%' . $cari . '%');
                  });
            });
        }

        $logs = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        if ($request->query('export') === 'csv') {
            $allLogs = $query->orderByDesc('created_at')->get();
            $headers = ['Tanggal', 'User', 'Role', 'Aksi / Aktivitas'];
            $callback = function() use ($headers, $allLogs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);
                foreach ($allLogs as $log) {
                    fputcsv($file, [
                        $log->created_at->format('d M Y H:i'),
                        $log->user->name ?? 'System',
                        $log->user->role ?? '',
                        $log->action
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=laporan-activity-log-" . now()->format('Ymd') . ".csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ]);
        }

        $dari = $request->input('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->input('sampai', now()->endOfMonth()->format('Y-m-d'));

        return view('activity_log.index', compact('logs', 'dari', 'sampai'));
    }
}
