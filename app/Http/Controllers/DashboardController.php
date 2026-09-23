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
use App\Services\DashboardCacheService;
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

        // 2 & 4. PENJUALAN, OMZET, LABA & GRAFIK (Data Time-Series & KPI Terpadu - Cached)
        $salesAndKpi = DashboardCacheService::remember('sales_and_kpi', [$dari, $sampai, $prevDari, $prevSampai], function () use ($dari, $sampai, $prevDari, $prevSampai) {
            $carbonDari = Carbon::parse($dari);
            $carbonSampai = Carbon::parse($sampai);
            $diffDays = $carbonDari->diffInDays($carbonSampai);

            $chartLabels = [];
            $chartGross = [];
            $chartNet = [];
            $chartProfit = [];

            if ($diffDays <= 31) {
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

            // Gunakan hasil kalkulasi time-series untuk omzet & laba periode saat ini (tanpa query ulang)
            $omzetKotor = (float) array_sum($chartGross);
            $totalDiskon = (float) DB::table('discount_usages as du')
                ->join('penjualans as p', 'du.penjualan_id', '=', 'p.id')
                ->whereBetween('p.tanggal', [$dari, $sampai])
                ->sum('du.nominal');
            $omzetBersih = max(0, $omzetKotor - $totalDiskon);

            $grossProfit = (float) DB::table('detail_penjualans as dp')
                ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
                ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
                ->whereBetween('p.tanggal', [$dari, $sampai])
                ->selectRaw('COALESCE(SUM(dp.subtotal) - SUM(dr.harga_beli * dp.jumlah), 0) as total')
                ->value('total');

            // Periode sebelumnya: gabungkan query omzet kotor & gross profit menjadi 1 query
            $prevSales = DB::table('detail_penjualans as dp')
                ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
                ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
                ->whereBetween('p.tanggal', [$prevDari, $prevSampai])
                ->selectRaw('
                    COALESCE(SUM(dp.harga_jual * dp.jumlah), 0) as omzet,
                    COALESCE(SUM(dp.subtotal) - SUM(dr.harga_beli * dp.jumlah), 0) as gross
                ')
                ->first();

            $prevOmzetKotor = (float) ($prevSales->omzet ?? 0);
            $prevGrossProfit = (float) ($prevSales->gross ?? 0);

            $prevTotalDiskon = (float) DB::table('discount_usages as du')
                ->join('penjualans as p', 'du.penjualan_id', '=', 'p.id')
                ->whereBetween('p.tanggal', [$prevDari, $prevSampai])
                ->sum('du.nominal');

            $prevOmzetBersih = max(0, $prevOmzetKotor - $prevTotalDiskon);

            $deltaGrossProfit = $prevGrossProfit > 0
                ? round((($grossProfit - $prevGrossProfit) / $prevGrossProfit) * 100, 1)
                : null;

            $deltaOmzetKotor = $prevOmzetKotor > 0 ? round((($omzetKotor - $prevOmzetKotor) / $prevOmzetKotor) * 100, 1) : null;
            $deltaTotalDiskon = $prevTotalDiskon > 0 ? round((($totalDiskon - $prevTotalDiskon) / $prevTotalDiskon) * 100, 1) : null;
            $deltaOmzetBersih = $prevOmzetBersih > 0 ? round((($omzetBersih - $prevOmzetBersih) / $prevOmzetBersih) * 100, 1) : null;

            return compact(
                'chartLabels', 'chartGross', 'chartNet', 'chartProfit',
                'omzetKotor', 'prevOmzetKotor', 'totalDiskon', 'prevTotalDiskon',
                'omzetBersih', 'prevOmzetBersih', 'grossProfit', 'prevGrossProfit',
                'deltaGrossProfit', 'deltaOmzetKotor', 'deltaTotalDiskon', 'deltaOmzetBersih'
            );
        });

        $chartLabels = $salesAndKpi['chartLabels'];
        $chartGross = $salesAndKpi['chartGross'];
        $chartNet = $salesAndKpi['chartNet'];
        $chartProfit = $salesAndKpi['chartProfit'];
        $omzetKotor = $salesAndKpi['omzetKotor'];
        $prevOmzetKotor = $salesAndKpi['prevOmzetKotor'];
        $totalDiskon = $salesAndKpi['totalDiskon'];
        $prevTotalDiskon = $salesAndKpi['prevTotalDiskon'];
        $omzetBersih = $salesAndKpi['omzetBersih'];
        $prevOmzetBersih = $salesAndKpi['prevOmzetBersih'];
        $grossProfit = $salesAndKpi['grossProfit'];
        $prevGrossProfit = $salesAndKpi['prevGrossProfit'];
        $deltaGrossProfit = $salesAndKpi['deltaGrossProfit'];
        $deltaOmzetKotor = $salesAndKpi['deltaOmzetKotor'];
        $deltaTotalDiskon = $salesAndKpi['deltaTotalDiskon'];
        $deltaOmzetBersih = $salesAndKpi['deltaOmzetBersih'];

        // 3. FINANCIAL CONTROL & MEMBER (Posisi Outstanding Terkini - Cached)
        $financialControl = DashboardCacheService::remember('financial_control', [$today->format('Y-m-d'), $dari, $sampai, $prevDari, $prevSampai], function () use ($today, $dari, $sampai, $prevDari, $prevSampai) {
            // A. Hutang Supplier (Penerimaan belum lunas - eager loaded untuk eliminasi N+1)
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

            // B. Member & Piutang: 5 metrik pelanggan disatukan dalam 1 single query
            $memberStats = DB::table('pelanggans')
                ->where('is_member', true)
                ->selectRaw('
                    COUNT(*) as total_member,
                    COUNT(CASE WHEN member_since BETWEEN ? AND ? THEN 1 END) as new_members,
                    COUNT(CASE WHEN member_since BETWEEN ? AND ? THEN 1 END) as prev_new_members,
                    COALESCE(SUM(saldo_piutang), 0) as total_piutang,
                    COUNT(CASE WHEN saldo_piutang > 0 THEN 1 END) as berpiutang_count
                ', [$dari, $sampai, $prevDari, $prevSampai])
                ->first();

            $totalMember = (int) ($memberStats->total_member ?? 0);
            $newMembersInPeriod = (int) ($memberStats->new_members ?? 0);
            $prevNewMembers = (int) ($memberStats->prev_new_members ?? 0);
            $totalPiutangMember = (float) ($memberStats->total_piutang ?? 0);
            $memberBerpiutangCount = (int) ($memberStats->berpiutang_count ?? 0);

            $deltaMember = $prevNewMembers > 0 ? round((($newMembersInPeriod - $prevNewMembers) / $prevNewMembers) * 100, 1) : null;

            // C. Umur piutang dari transaksi kasir piutang
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

            return compact(
                'totalHutangSupplier', 'hutangOverdue', 'hutangDueSoon', 'hutangNotDue',
                'countHutangOverdue', 'countHutangDueSoon', 'countHutangNotDue',
                'totalMember', 'newMembersInPeriod', 'deltaMember',
                'totalPiutangMember', 'piutangOverdue', 'piutangDueSoon', 'piutangNotDue',
                'countPiutangOverdue', 'countPiutangDueSoon', 'countPiutangNotDue',
                'memberBerpiutangCount'
            );
        });

        $totalHutangSupplier = $financialControl['totalHutangSupplier'];
        $hutangOverdue = $financialControl['hutangOverdue'];
        $hutangDueSoon = $financialControl['hutangDueSoon'];
        $hutangNotDue = $financialControl['hutangNotDue'];
        $countHutangOverdue = $financialControl['countHutangOverdue'];
        $countHutangDueSoon = $financialControl['countHutangDueSoon'];
        $countHutangNotDue = $financialControl['countHutangNotDue'];
        $totalMember = $financialControl['totalMember'];
        $newMembersInPeriod = $financialControl['newMembersInPeriod'];
        $deltaMember = $financialControl['deltaMember'];
        $totalPiutangMember = $financialControl['totalPiutangMember'];
        $piutangOverdue = $financialControl['piutangOverdue'];
        $piutangDueSoon = $financialControl['piutangDueSoon'];
        $piutangNotDue = $financialControl['piutangNotDue'];
        $countPiutangOverdue = $financialControl['countPiutangOverdue'];
        $countPiutangDueSoon = $financialControl['countPiutangDueSoon'];
        $countPiutangNotDue = $financialControl['countPiutangNotDue'];
        $memberBerpiutangCount = $financialControl['memberBerpiutangCount'];

        // 5 & 6. INVENTORY CONTROL CENTER & BARANG YANG HARUS DIBELI (Cached)
        $inv = DashboardCacheService::remember('inventory_data', [], function () {
            $inventoryTotals = DetailPenerimaan::where('aktif', true)
                ->selectRaw('COALESCE(SUM(stok), 0) as total_stok, COALESCE(SUM(stok * harga_beli), 0) as nilai_persediaan')
                ->first();

            $totalStok = (int) ($inventoryTotals->total_stok ?? 0);
            $nilaiPersediaan = (float) ($inventoryTotals->nilai_persediaan ?? 0);

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
                $stok = (int) ($b->detail_penerimaan_sum_stok ?? 0);
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

            $barangHarusDibeli = $barangHarusDibeliList->sortBy([
                ['stok', 'asc'],
                ['kekurangan', 'desc'],
            ])->take(10)->values();

            return compact(
                'totalStok', 'nilaiPersediaan', 'totalSku',
                'stokHabisCount', 'stokMenipisCount', 'stokAmanCount',
                'barangHarusDibeli'
            );
        });

        $totalStok = $inv['totalStok'];
        $nilaiPersediaan = $inv['nilaiPersediaan'];
        $totalSku = $inv['totalSku'];
        $stokHabisCount = $inv['stokHabisCount'];
        $stokMenipisCount = $inv['stokMenipisCount'];
        $stokAmanCount = $inv['stokAmanCount'];
        $barangHarusDibeli = $inv['barangHarusDibeli'];

        // 7. EXPIRY HEALTH (Cached & Single Aggregated Query)
        $expiry = DashboardCacheService::remember('expiry_health', [$today->format('Y-m-d')], function () use ($today) {
            $oneMonth = $today->copy()->addMonth();
            $threeMonths = $today->copy()->addMonths(3);

            $expiryStats = DB::table('detail_penerimaans')
                ->where('aktif', true)
                ->where('stok', '>', 0)
                ->whereNull('deleted_at')
                ->selectRaw('
                    COUNT(*) as total_batches,
                    COUNT(CASE WHEN expired_date <= ? THEN 1 END) as kadaluarsa,
                    COUNT(CASE WHEN expired_date > ? AND expired_date <= ? THEN 1 END) as critical,
                    COUNT(CASE WHEN expired_date > ? AND expired_date <= ? THEN 1 END) as warning,
                    COUNT(CASE WHEN expired_date > ? OR expired_date IS NULL THEN 1 END) as aman
                ', [
                    $today->format('Y-m-d'),
                    $today->format('Y-m-d'), $oneMonth->format('Y-m-d'),
                    $oneMonth->format('Y-m-d'), $threeMonths->format('Y-m-d'),
                    $threeMonths->format('Y-m-d')
                ])
                ->first();

            return [
                'totalBatchesCount' => (int) ($expiryStats->total_batches ?? 0),
                'expiryKadaluarsa'  => (int) ($expiryStats->kadaluarsa ?? 0),
                'expiryCritical'    => (int) ($expiryStats->critical ?? 0),
                'expiryWarning'     => (int) ($expiryStats->warning ?? 0),
                'expiryAman'        => (int) ($expiryStats->aman ?? 0),
            ];
        });

        $totalBatchesCount = $expiry['totalBatchesCount'];
        $expiryKadaluarsa = $expiry['expiryKadaluarsa'];
        $expiryCritical = $expiry['expiryCritical'];
        $expiryWarning = $expiry['expiryWarning'];
        $expiryAman = $expiry['expiryAman'];

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

        // 9 & 10. 10 OBAT TERLARIS & 10 OBAT PALING SEDIKIT TERJUAL (Satu Subquery Bersama - Cached)
        $rankings = DashboardCacheService::remember('medicine_rankings', [$dari, $sampai], function () use ($dari, $sampai) {
            $periodSales = DB::table('detail_penjualans as dp')
                ->join('penjualans as p', 'dp.penjualan_id', '=', 'p.id')
                ->join('detail_penerimaans as dr', 'dp.detail_penerimaan_id', '=', 'dr.id')
                ->whereBetween('p.tanggal', [$dari, $sampai])
                ->select(
                    'dr.barang_id',
                    DB::raw('SUM(dp.jumlah) as total_terjual'),
                    DB::raw('SUM(dp.subtotal) as total_omzet')
                )
                ->groupBy('dr.barang_id');

            $topSellingMedicines = DB::table('barangs as b')
                ->leftJoin('kategoris as k', 'b.kategori_id', '=', 'k.id')
                ->joinSub($periodSales, 's', 'b.id', '=', 's.barang_id')
                ->where('b.aktif', true)
                ->select(
                    'b.id',
                    'b.nama',
                    'k.nama as kategori',
                    's.total_terjual',
                    's.total_omzet'
                )
                ->orderByDesc('s.total_terjual')
                ->orderBy('b.nama')
                ->limit(10)
                ->get();

            $leastSellingMedicines = DB::table('barangs as b')
                ->leftJoin('kategoris as k', 'b.kategori_id', '=', 'k.id')
                ->leftJoinSub($periodSales, 's', 'b.id', '=', 's.barang_id')
                ->where('b.aktif', true)
                ->select(
                    'b.id',
                    'b.nama',
                    'k.nama as kategori',
                    DB::raw('COALESCE(s.total_terjual, 0) as total_terjual'),
                    DB::raw('COALESCE(s.total_omzet, 0) as total_omzet')
                )
                ->orderBy('total_terjual')
                ->orderBy('b.nama')
                ->limit(10)
                ->get();

            return compact('topSellingMedicines', 'leastSellingMedicines');
        });

        $topSellingMedicines = $rankings['topSellingMedicines'];
        $leastSellingMedicines = $rankings['leastSellingMedicines'];

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
        $baseQuery = ActivityLog::query();

        $dari = $request->input('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->input('sampai', now()->endOfMonth()->format('Y-m-d'));

        if ($request->filled('dari') && $request->filled('sampai')) {
            $baseQuery->whereBetween('created_at', [$request->dari . ' 00:00:00', $request->sampai . ' 23:59:59']);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $baseQuery->where(function($q) use ($cari) {
                $q->where('action', 'like', '%' . $cari . '%')
                  ->orWhere('target', 'like', '%' . $cari . '%')
                  ->orWhere('user_name', 'like', '%' . $cari . '%')
                  ->orWhereHas('user', function($qu) use ($cari) {
                      $qu->where('name', 'like', '%' . $cari . '%');
                  });
            });
        }

        if ($request->filled('user_id')) {
            $baseQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('kategori')) {
            $baseQuery->where('kategori', $request->kategori);
        }

        $query = $baseQuery->with('user');

        if ($request->query('export') === 'csv') {
            $allLogs = $query->orderByDesc('created_at')->get();
            $headers = ['Tanggal & Waktu', 'Kategori', 'User', 'Role', 'Tindakan / Aksi', 'Rincian Target'];
            $callback = function() use ($headers, $allLogs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);
                foreach ($allLogs as $log) {
                    fputcsv($file, [
                        $log->created_at->format('d M Y H:i:s'),
                        strtoupper($log->kategori ?? 'SISTEM'),
                        $log->user->name ?? $log->user_name,
                        $log->user->role ?? '-',
                        $log->action,
                        $log->target ?? '-'
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=laporan-activity-log-" . now()->format('Ymd-His') . ".csv",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ]);
        }

        $logs = $query->orderByDesc('created_at')->paginate(25)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        $categories = [
            'penjualan'   => ['label' => 'Penjualan', 'icon' => 'shopping-cart', 'color' => 'blue'],
            'inventaris'  => ['label' => 'Inventaris & Stok', 'icon' => 'archive-box', 'color' => 'indigo'],
            'member'      => ['label' => 'Member & Pelanggan', 'icon' => 'users', 'color' => 'emerald'],
            'promo'       => ['label' => 'Promo & Diskon', 'icon' => 'tag', 'color' => 'amber'],
            'keuangan'    => ['label' => 'Keuangan & Piutang', 'icon' => 'banknotes', 'color' => 'purple'],
            'keamanan'    => ['label' => 'Keamanan & User', 'icon' => 'shield-check', 'color' => 'slate'],
            'sistem'      => ['label' => 'Sistem & Pengaturan', 'icon' => 'cog-6-tooth', 'color' => 'gray'],
        ];

        return view('activity_log.index', compact(
            'logs',
            'dari',
            'sampai',
            'users',
            'categories'
        ));
    }
}
