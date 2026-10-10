<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Apotek')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $apotek = \Illuminate\Support\Facades\Cache::remember(
            'info_apotek',
            now()->addHours(6),
            fn () => \App\Models\InfoApotek::first()
        );
    @endphp
    <link rel="icon" href="{{ $apotek?->logo ? asset('storage/' . $apotek->logo) : asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            aside#app-sidebar,
            #sidebar-backdrop,
            nav,
            footer,
            .app-global-footer {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                min-height: 0 !important;
                max-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                position: absolute !important;
                top: -9999px !important;
                left: -9999px !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 h-screen overflow-hidden flex">
    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden transition-opacity" aria-hidden="true"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar" class="group fixed top-0 left-0 h-screen w-72 max-w-[85vw] bg-blue-700 border-r border-blue-800 flex flex-col justify-between z-50 shrink-0 self-start transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:w-24 lg:hover:w-64 transition-all duration-200 ease-in-out print:hidden">
        <!-- Pinned Sidebar Header -->
        <div class="sidebar-header shrink-0 px-5 py-5 flex justify-between items-center">
            @php
                $abbr = ($apotek?->nama_apotek ?? '')
                    ? collect(explode(' ', $apotek->nama_apotek))
                        ->take(2)
                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                        ->implode('')
                    : 'AK';
            @endphp
            <div class="font-bold text-base text-white tracking-wider flex items-center gap-3 sidebar-header-logo-wrapper">
                <div class="w-[3.5rem] h-[3.5rem] rounded-lg bg-white p-2 shrink-0 flex items-center justify-center shadow-md">
                    @if($apotek?->logo)
                        <img src="{{ asset('storage/' . $apotek->logo) }}"
                             alt="{{ $apotek?->nama_apotek }}"
                             class="w-full h-full object-contain object-center"
                             aria-hidden="true">
                    @else
                        <span class="text-blue-700 font-extrabold text-base">{{ $abbr }}</span>
                    @endif
                </div>
                <span class="sidebar-nav-label truncate whitespace-nowrap font-bold text-base tracking-wide uppercase">
                    {{ $apotek?->nama_apotek ?? 'APOTEK KITA' }}
                </span>
            </div>
            <button id="close-sidebar" class="lg:hidden text-blue-200 hover:text-white focus:outline-none" aria-label="Tutup menu">
                <x-heroicon-o-x-mark class="w-5 h-5" aria-hidden="true" />
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 min-h-0 overflow-y-auto sidebar-scroll-area flex flex-col">
            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-1.5">
                <x-nav-link
                    route="dashboard"
                    icon="home"
                    icon-type="s"
                    label="Dashboard"
                    variant="top"
                />

                @php
                    $u = auth()->user();
                    if (!$u) {
                        $u = new class {
                            public $name = '';
                            public $role = '';
                            public function hasPermission($name) { return false; }
                            public function isSuperAdmin() { return false; }
                        };
                    }
                    $masterActive = request()->routeIs('barang.*') || request()->routeIs('kategori.*')
                        || request()->routeIs('satuan.*') || request()->routeIs('pabrik.*')
                        || request()->routeIs('supplier.*') || request()->routeIs('pelanggan.*')
                        || request()->routeIs('user.*') || request()->routeIs('pengaturan.*')
                        || request()->routeIs('permission.*');
                    $adaMaster = $u->hasPermission('master.barang') || $u->hasPermission('master.kategori')
                        || $u->hasPermission('master.satuan') || $u->hasPermission('master.pabrik')
                        || $u->hasPermission('master.supplier') || $u->hasPermission('master.pelanggan')
                        || $u->hasPermission('sistem.kelola-user') || $u->hasPermission('sistem.pengaturan')
                        || $u->isSuperAdmin();
                @endphp

                <!-- Group: Master Data -->
                @if ($adaMaster)
                    <x-sidebar-group name="master" icon="cube" icon-type="s" label="Data Master" :has-active="$masterActive">
                        @if ($u->hasPermission('master.barang'))
                            <x-nav-link route="barang.index" pattern="barang.*" icon="archive-box" label="Barang / Produk" />
                        @endif
                        @if ($u->hasPermission('master.kategori'))
                            <x-nav-link route="kategori.index" pattern="kategori.*" icon="percent-badge" label="Kategori" />
                        @endif
                        @if ($u->hasPermission('master.satuan'))
                            <x-nav-link route="satuan.index" pattern="satuan.*" icon="scale" label="Satuan" />
                        @endif
                        @if ($u->hasPermission('master.pabrik'))
                            <x-nav-link route="pabrik.index" pattern="pabrik.*" icon="building-storefront" label="Pabrik" />
                        @endif
                        @if ($u->hasPermission('master.supplier'))
                            <x-nav-link route="supplier.index" pattern="supplier.*" icon="building-office-2" label="Supplier" />
                        @endif
                        @if ($u->hasPermission('master.pelanggan'))
                            <x-nav-link route="pelanggan.index" pattern="pelanggan.*" icon="user" label="Pelanggan / Member" />
                        @endif
                        @if ($u->hasPermission('sistem.kelola-user'))
                            <x-nav-link route="user.index" pattern="user.*" icon="users" label="Kelola User" />
                        @endif
                        @if ($u->hasPermission('sistem.pengaturan'))
                            <x-nav-link route="pengaturan.index" pattern="pengaturan.*" icon="cog-6-tooth" label="Pengaturan Apotek" />
                        @endif
                    </x-sidebar-group>
                @endif

                <!-- Group: Transaksi -->
                @php
                    $transaksiActive = request()->routeIs('penjualan.*') || request()->routeIs('penerimaan.*')
                        || request()->routeIs('rusak.*');
                @endphp
                <x-sidebar-group name="transaksi" icon="banknotes" icon-type="s" label="Transaksi" :has-active="$transaksiActive">
                    @if ($u->hasPermission('transaksi.penjualan'))
                        <x-nav-link route="penjualan.index" pattern="penjualan.*" icon="shopping-cart" label="Penjualan (Kasir)" />
                    @endif
                    @if ($u->hasPermission('transaksi.penerimaan'))
                        <x-nav-link route="penerimaan.index" pattern="penerimaan.*" icon="truck" label="Penerimaan barang" />
                    @endif
                    @if ($u->hasPermission('transaksi.rusak'))
                        <x-nav-link route="rusak.index" pattern="rusak.*" icon="exclamation-triangle" label="Barang rusak" />
                    @endif
                </x-sidebar-group>

                <!-- Group: Laporan -->
                @if ($u->hasPermission('laporan.stok') || $u->hasPermission('laporan.penerimaan')
                    || $u->hasPermission('laporan.penjualan') || $u->hasPermission('laporan.rusak')
                    || $u->hasPermission('laporan.laba-rugi') || $u->hasPermission('laporan.diskon'))
                    <x-sidebar-group name="laporan" icon="chart-bar" icon-type="s" label="Laporan" :has-active="request()->routeIs('laporan.*')">
                        @if ($u->hasPermission('laporan.stok'))
                            <x-nav-link route="laporan.stok" icon="document-chart-bar" label="Laporan stok" />
                        @endif
                        @if ($u->hasPermission('laporan.penerimaan'))
                            <x-nav-link route="laporan.penerimaan" icon="document-duplicate" label="Laporan penerimaan" />
                        @endif
                        @if ($u->hasPermission('laporan.penjualan'))
                            <x-nav-link route="laporan.penjualan" icon="currency-dollar" label="Laporan penjualan" />
                        @endif
                        @if ($u->hasPermission('laporan.rusak'))
                            <x-nav-link route="laporan.rusak" icon="no-symbol" label="Laporan barang rusak" />
                        @endif
                        @if ($u->hasPermission('laporan.laba-rugi'))
                            <x-nav-link route="laporan.laba-rugi" icon="chart-pie" label="Laporan laba-rugi" />
                        @endif
                        @if ($u->hasPermission('laporan.diskon'))
                            <x-nav-link route="laporan.diskon" icon="ticket" label="Laporan diskon" />
                        @endif
                    </x-sidebar-group>
                @endif

                <!-- Group: Promo & Log -->
                @if ($u->hasPermission('master.custom-discount') || $u->hasPermission('sistem.activity-log'))
                    @php
                        $promoActive = request()->routeIs('custom-discount.*') || request()->routeIs('activity-log');
                    @endphp
                    <x-sidebar-group name="promo" icon="gift" icon-type="s" label="Promo & Log" :has-active="$promoActive">
                        @if ($u->hasPermission('master.custom-discount'))
                            <x-nav-link route="custom-discount.index" pattern="custom-discount.*" icon="tag" label="Custom Discount" />
                        @endif
                        @if ($u->hasPermission('sistem.activity-log'))
                            <x-nav-link route="activity-log" icon="book-open" label="Log Aktivitas" />
                        @endif
                    </x-sidebar-group>
                @endif

                @if ($u->isSuperAdmin())
                    <x-sidebar-group name="izin" icon="shield-check" icon-type="s" label="Izin Akses" :has-active="request()->routeIs('permission.*')">
                        <x-nav-link route="permission.index" pattern="permission.*" icon="adjustments-horizontal" label="Izin Akses" />
                    </x-sidebar-group>
                @endif

            </nav>
        </div>

        <!-- Sidebar Profile Card (Bottom) -->
        <div class="sidebar-footer shrink-0 px-6 py-4 border-t border-blue-600 bg-blue-800 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-full bg-white text-blue-700 font-bold flex items-center justify-center shrink-0 text-xs shadow-md" aria-hidden="true">
                    {{ $u ? strtoupper(substr($u->name, 0, 2)) : '' }}
                </div>
                <div class="sidebar-footer-info min-w-0 overflow-hidden whitespace-nowrap">
                    <span class="block text-xs font-semibold text-white truncate">{{ $u?->name }}</span>
                    <span class="block text-[9px] font-bold text-blue-200 uppercase tracking-wider truncate">{{ $u?->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-footer-logout shrink-0 overflow-hidden">
                @csrf
                <button type="submit" class="text-blue-200 hover:text-red-400 transition-colors p-1" title="Logout" aria-label="Logout">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" aria-hidden="true" />
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto overflow-x-hidden">
        <!-- Top Navbar (Sticky) -->
        <nav class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-5 py-3 flex justify-between items-center print:hidden shadow-xs">
            <div class="flex items-center gap-3">
                <button id="mobile-sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5" aria-hidden="true" />
                </button>
                <span class="font-bold text-sm text-gray-800 uppercase tracking-wide">@yield('title', 'Dashboard')</span>
            </div>
            
            <div class="flex items-center gap-3 md:gap-4 text-xs">
                <!-- Real-time Clock Widget -->
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-lg text-slate-700 shadow-2xs font-medium">
                    <x-heroicon-o-clock class="w-4 h-4 text-blue-600 shrink-0" />
                    <span id="realtime-clock" class="font-mono text-[11px] sm:text-xs tracking-tight">--:--:--</span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="inline md:hidden">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1" aria-label="Logout">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" aria-hidden="true" />
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Wrapper -->
        <main class="flex-1 p-6 w-full pb-8">
            @yield('content')
        </main>

        <!-- Global Reusable Footer -->
        @include('layouts.footer')
    </div>

    <!-- Session Flash Toast Notifications -->
    @if (session('success'))
    <div id="toast-success" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800" role="status" aria-live="polite">
        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 shrink-0" aria-hidden="true" />
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('toast-success').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm" aria-label="Tutup notifikasi">&times;</button>
    </div>
    @endif

    @if (session('error'))
    <div id="toast-error" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800" role="alert" aria-live="assertive">
        <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 shrink-0" aria-hidden="true" />
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('toast-error').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm" aria-label="Tutup notifikasi">&times;</button>
    </div>
    @endif

</body>

</html>
