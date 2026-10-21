<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuickSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([
                'medicines' => [],
                'sales' => [],
                'pages' => [],
            ]);
        }

        $user = $request->user() ?? auth()->user();
        if (! $user) {
            return response()->json([
                'medicines' => [],
                'sales' => [],
                'pages' => [],
            ]);
        }
        $medicines = Barang::with('kategori')
            ->withSum(['detailPenerimaan' => fn ($query) => $query->where('aktif', true)], 'stok')
            ->where('aktif', true)
            ->where(function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_apotek', 'like', "%{$q}%")
                    ->orWhere('kode_kfa', 'like', "%{$q}%")
                    ->orWhere('merk', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($b) use ($user) {
                $stok = (int) ($b->detail_penerimaan_sum_stok ?? 0);
                return [
                    'id' => $b->id,
                    'nama' => $b->nama,
                    'kategori' => $b->kategori->nama ?? 'Tanpa Kategori',
                    'stok' => $stok,
                    'harga_jual' => (float) $b->harga_jual,
                    'url' => $user->hasPermission('master.barang') ? route('barang.edit', $b->id) : route('penjualan.create'),
                    'can_sell' => $stok > 0 && $user->canManage('transaksi.penjualan'),
                    'sell_url' => route('penjualan.create'),
                ];
            });

        // 2. Cari Transaksi Penjualan / Faktur
        $sales = collect();
        if ($user->hasPermission('transaksi.penjualan') || $user->hasPermission('laporan.penjualan')) {
            $sales = Penjualan::with('pelanggan')
                ->where('no_faktur', 'like', "%{$q}%")
                ->orWhereHas('pelanggan', fn ($qp) => $qp->where('nama', 'like', "%{$q}%"))
                ->latest('tanggal')
                ->limit(4)
                ->get()
                ->map(function ($pj) {
                    return [
                        'id' => $pj->id,
                        'no_faktur' => $pj->no_faktur,
                        'tanggal' => $pj->tanggal ? \Carbon\Carbon::parse($pj->tanggal)->format('d M Y') : '-',
                        'pelanggan' => $pj->pelanggan->nama ?? 'Umum',
                        'total' => (float) $pj->total,
                        'url' => route('penjualan.index', ['cari' => $pj->no_faktur]),
                    ];
                });
        }

        // 3. Navigasi Cepat Menu / Halaman
        $allPages = [
            ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home', 'perm' => null],
            ['title' => 'Kasir (Penjualan Baru)', 'url' => route('penjualan.create'), 'icon' => 'calculator', 'perm' => 'transaksi.penjualan'],
            ['title' => 'Riwayat Penjualan', 'url' => route('penjualan.index'), 'icon' => 'shopping-cart', 'perm' => 'transaksi.penjualan'],
            ['title' => 'Barang & Produk Obat', 'url' => route('barang.index'), 'icon' => 'cube', 'perm' => 'master.barang'],
            ['title' => 'Kategori Obat', 'url' => route('kategori.index'), 'icon' => 'tag', 'perm' => 'master.kategori'],
            ['title' => 'Penerimaan Barang', 'url' => route('penerimaan.index'), 'icon' => 'truck', 'perm' => 'transaksi.penerimaan'],
            ['title' => 'Barang Rusak / Kadaluwarsa', 'url' => route('rusak.index'), 'icon' => 'trash', 'perm' => 'transaksi.rusak'],
            ['title' => 'Data Pelanggan & Member', 'url' => route('pelanggan.index'), 'icon' => 'users', 'perm' => 'master.pelanggan'],
            ['title' => 'Laporan Stok & Kadaluwarsa', 'url' => route('laporan.stok'), 'icon' => 'archive-box', 'perm' => 'laporan.stok'],
            ['title' => 'Laporan Penjualan', 'url' => route('laporan.penjualan'), 'icon' => 'chart-bar', 'perm' => 'laporan.penjualan'],
            ['title' => 'Laporan Laba Rugi', 'url' => route('laporan.laba-rugi'), 'icon' => 'banknotes', 'perm' => 'laporan.laba-rugi'],
            ['title' => 'Kelola User', 'url' => route('user.index'), 'icon' => 'user-group', 'perm' => 'sistem.kelola-user'],
            ['title' => 'Pengaturan Apotek', 'url' => route('pengaturan.index'), 'icon' => 'cog-6-tooth', 'perm' => 'sistem.pengaturan'],
            ['title' => 'Log Aktivitas', 'url' => route('activity-log'), 'icon' => 'shield-check', 'perm' => 'sistem.activity-log'],
        ];

        $matchedPages = collect($allPages)
            ->filter(function ($item) use ($user, $q) {
                if ($item['perm'] && ! $user->hasPermission($item['perm'])) {
                    return false;
                }
                return str_contains(strtolower($item['title']), strtolower($q));
            })
            ->take(3)
            ->values();

        return response()->json([
            'medicines' => $medicines,
            'sales' => $sales,
            'pages' => $matchedPages,
        ]);
    }
}
