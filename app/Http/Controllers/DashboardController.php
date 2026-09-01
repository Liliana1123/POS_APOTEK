<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\DiscountUsage;
use App\Models\CustomDiscount;
use App\Models\Penjualan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Member
        $totalMembers = Pelanggan::where('is_member', true)->count();
        
        // 2. Member Baru Bulan Ini
        $newMembersThisMonth = Pelanggan::where('is_member', true)
            ->whereMonth('member_since', now()->month)
            ->whereYear('member_since', now()->year)
            ->count();

        // 3. Total Transaksi Member
        $memberTransactionsCount = Penjualan::whereHas('pelanggan', function ($q) {
            $q->where('is_member', true);
        })->count();

        // 4. Omzet Member
        $memberSalesTotal = Penjualan::whereHas('pelanggan', function ($q) {
            $q->where('is_member', true);
        })->sum('total');

        // 5. Total Penghematan Member (Rupiah diskon member)
        $memberSavingsTotal = DiscountUsage::where('jenis', 'member')->sum('nominal');

        // 6. Promo Aktif Hari Ini
        $activePromosCount = CustomDiscount::aktifHariIni()->count();

        // 7. Promo Paling Banyak Digunakan (berdasarkan count audit custom)
        $mostUsedPromo = DiscountUsage::where('jenis', 'custom')
            ->select('custom_discount_nama', DB::raw('count(*) as usage_count'))
            ->groupBy('custom_discount_nama')
            ->orderByDesc('usage_count')
            ->first();

        // 8. Promo dengan Nominal Diskon Terbesar
        $biggestDiscountPromo = DiscountUsage::where('jenis', 'custom')
            ->select('custom_discount_nama', DB::raw('sum(nominal) as total_nominal'))
            ->groupBy('custom_discount_nama')
            ->orderByDesc('total_nominal')
            ->first();

        // Monthly stats for general discount card
        $totalDiscountThisMonth = DiscountUsage::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('nominal');

        $nonMemberSalesTotal = Penjualan::where(function ($q) {
            $q->whereNull('pelanggan_id')
              ->orWhereHas('pelanggan', function ($q2) {
                  $q2->where('is_member', false);
              });
        })->sum('total');

        // Stats Hari Ini (Fase 5)
        $todayTransactionsCount = Penjualan::whereDate('tanggal', now()->format('Y-m-d'))->count();
        $todaySalesTotal = Penjualan::whereDate('tanggal', now()->format('Y-m-d'))->sum('total');
        $todayDiscountTotal = DiscountUsage::whereDate('created_at', now()->format('Y-m-d'))->sum('nominal');

        $topSellingMedicines = DB::table('detail_penjualans as detail_penjualan')
            ->join('detail_penerimaans as detail_penerimaan', 'detail_penjualan.detail_penerimaan_id', '=', 'detail_penerimaan.id')
            ->join('barangs as barang', 'detail_penerimaan.barang_id', '=', 'barang.id')
            ->select('barang.id', 'barang.nama', DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'))
            ->groupBy('barang.id', 'barang.nama')
            ->orderByDesc('total_terjual')
            ->orderBy('barang.nama')
            ->limit(10)
            ->get();

        $leastSellingMedicines = DB::table('barangs as barang')
            ->leftJoin('detail_penerimaans as detail_penerimaan', 'barang.id', '=', 'detail_penerimaan.barang_id')
            ->leftJoin('detail_penjualans as detail_penjualan', 'detail_penerimaan.id', '=', 'detail_penjualan.detail_penerimaan_id')
            ->select('barang.id', 'barang.nama', DB::raw('COALESCE(SUM(detail_penjualan.jumlah), 0) as total_terjual'))
            ->groupBy('barang.id', 'barang.nama')
            ->orderBy('total_terjual')
            ->orderBy('barang.nama')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalMembers',
            'newMembersThisMonth',
            'memberTransactionsCount',
            'memberSalesTotal',
            'memberSavingsTotal',
            'activePromosCount',
            'mostUsedPromo',
            'biggestDiscountPromo',
            'totalDiscountThisMonth',
            'nonMemberSalesTotal',
            'todayTransactionsCount',
            'todaySalesTotal',
            'todayDiscountTotal',
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
