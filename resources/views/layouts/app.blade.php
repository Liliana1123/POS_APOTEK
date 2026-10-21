<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <script>
        (function () {
            try {
                if ((localStorage.getItem('sidebar-pinned') === '1' || sessionStorage.getItem('sidebar-keep-expanded') === '1')
                    && window.matchMedia('(min-width: 1024px)').matches) {
                    document.documentElement.classList.add('sidebar-keep-expanded');
                }
            } catch (e) {}
        })();
    </script>
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
            html,
            body {
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                background-color: #ffffff !important;
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body > div,
            main {
                display: block !important;
                position: static !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow: visible !important;
                width: 100% !important;
                flex: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            aside#app-sidebar,
            #sidebar-backdrop,
            nav,
            footer,
            .app-global-footer,
            #mobile-sidebar-toggle,
            .print\:hidden,
            #toast-success,
            #toast-error {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                min-height: 0 !important;
                max-height: 0 !important;
                width: 0 !important;
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

    <aside id="app-sidebar" class="group fixed top-0 left-0 h-screen w-72 max-w-[85vw] bg-blue-700 border-r border-blue-800 flex flex-col justify-between z-50 shrink-0 self-start transform -translate-x-full lg:translate-x-0 lg:static lg:flex lg:w-24 lg:hover:w-72 transition-all duration-200 ease-in-out print:hidden">
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
            <!-- Pin Sidebar Toggle (Desktop) -->
            <button id="pin-sidebar-toggle"
                    type="button"
                    class="hidden lg:flex sidebar-nav-label text-blue-200 hover:text-white p-1.5 rounded-lg hover:bg-blue-600/50 focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:outline-none transition-colors"
                    title="Kunci sidebar tetap terbuka (Pin - Ctrl+B)"
                    aria-label="Kunci sidebar (Ctrl+B)">
                <svg id="pin-icon-unpinned" class="w-4 h-4 transform -rotate-45" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="17" x2="12" y2="22"></line>
                    <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                </svg>
                <svg id="pin-icon-pinned" class="w-4 h-4 hidden" fill="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="17" x2="12" y2="22"></line>
                    <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                </svg>
            </button>
            <button id="close-sidebar" class="lg:hidden text-blue-200 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 rounded-lg p-1 transition-colors" aria-label="Tutup menu">
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
                        || request()->routeIs('whatsapp-monitor.*')
                        || request()->routeIs('user.*') || request()->routeIs('pengaturan.*');
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
                            <x-nav-link route="kategori.index" pattern="kategori.*" icon="squares-2x2" label="Kategori" />
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
                            @php
                                $pelangganActive = request()->routeIs('pelanggan.*') || request()->is('pelanggan*');
                                $waMonitorActive = request()->routeIs('whatsapp-monitor.*') || request()->is('whatsapp-monitor*');
                                $pelangganRowClass = $pelangganActive
                                    ? 'sidebar-nav-active bg-white text-blue-800 font-semibold shadow-xs'
                                    : 'text-blue-100 hover:bg-blue-600/60 hover:text-white';
                            @endphp
                            <div class="sidebar-subgroup" data-subgroup="pelanggan">
                                <div class="flex items-center justify-between rounded-lg transition-colors {{ $pelangganRowClass }}">
                                    <a href="{{ route('pelanggan.index') }}" title="Pelanggan / Member" class="sidebar-nav-link sidebar-subgroup-link sidebar-nav-child flex items-center gap-2.5 px-3 py-1.5 flex-1 min-w-0 transition-colors focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:outline-none {{ $pelangganActive ? 'text-blue-800 font-semibold' : 'text-blue-100 hover:text-white' }}">
                                        <x-dynamic-component component="heroicon-o-user" class="w-4 h-4 shrink-0 opacity-80 group-hover:opacity-100" aria-hidden="true" />
                                        <span class="sidebar-nav-label truncate whitespace-nowrap font-medium text-xs">Pelanggan / Member</span>
                                    </a>
                                    <button type="button" aria-expanded="{{ $waMonitorActive ? 'true' : 'false' }}" class="sidebar-subgroup-toggle relative z-10 p-1.5 mr-1 rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 cursor-pointer {{ $pelangganActive ? 'text-blue-800 hover:bg-blue-100' : 'text-blue-300 hover:text-white hover:bg-blue-500/50' }}" title="Buka/Tutup Submenu WhatsApp Monitor" aria-label="Buka/Tutup Submenu WhatsApp Monitor">
                                        <x-heroicon-o-chevron-right class="sidebar-subgroup-arrow w-3.5 h-3.5 shrink-0 transition-transform duration-200 pointer-events-none" aria-hidden="true" />
                                    </button>
                                </div>
                                <div class="sidebar-subgroup-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out flex flex-col space-y-1 my-1" data-has-active="{{ $waMonitorActive ? 'true' : 'false' }}">
                                    @if ($u->hasPermission('master.pelanggan') || $u->isSuperAdmin())
                                        <x-nav-link route="whatsapp-monitor.index" pattern="whatsapp-monitor.*" icon="chat-bubble-left-right" label="WhatsApp Monitor" />
                                    @endif
                                </div>
                            </div>
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
                    $adaTransaksi = $u->hasPermission('transaksi.penjualan') || $u->hasPermission('transaksi.penerimaan')
                        || $u->hasPermission('transaksi.rusak');
                    $transaksiActive = request()->routeIs('penjualan.*') || request()->routeIs('penerimaan.*')
                        || request()->routeIs('rusak.*');
                @endphp
                @if ($adaTransaksi)
                    <x-sidebar-group name="transaksi" icon="banknotes" icon-type="s" label="Transaksi" :has-active="$transaksiActive">
                        @if ($u->hasPermission('transaksi.penjualan'))
                            <x-nav-link route="penjualan.index" pattern="penjualan.*" icon="shopping-cart" label="Penjualan (Kasir)" />
                        @endif
                        @if ($u->hasPermission('transaksi.penerimaan'))
                            <x-nav-link route="penerimaan.index" pattern="penerimaan.*" icon="truck" label="Penerimaan Barang" />
                        @endif
                        @if ($u->hasPermission('transaksi.rusak'))
                            <x-nav-link route="rusak.index" pattern="rusak.*" icon="exclamation-triangle" label="Barang Rusak" />
                        @endif
                    </x-sidebar-group>
                @endif

                <!-- Group: Laporan -->
                @if ($u->hasPermission('laporan.stok') || $u->hasPermission('laporan.penerimaan')
                    || $u->hasPermission('laporan.penjualan') || $u->hasPermission('laporan.rusak')
                    || $u->hasPermission('laporan.laba-rugi') || $u->hasPermission('laporan.diskon'))
                    <x-sidebar-group name="laporan" icon="chart-bar" icon-type="s" label="Laporan" :has-active="request()->routeIs('laporan.*')">
                        @if ($u->hasPermission('laporan.stok'))
                            <x-nav-link route="laporan.stok" icon="document-chart-bar" label="Laporan Stok" />
                        @endif
                        @if ($u->hasPermission('laporan.penerimaan'))
                            <x-nav-link route="laporan.penerimaan" icon="document-duplicate" label="Laporan Penerimaan" />
                        @endif
                        @if ($u->hasPermission('laporan.penjualan'))
                            <x-nav-link route="laporan.penjualan" icon="currency-dollar" label="Laporan Penjualan" />
                        @endif
                        @if ($u->hasPermission('laporan.rusak'))
                            <x-nav-link route="laporan.rusak" icon="no-symbol" label="Laporan Barang Rusak" />
                        @endif
                        @if ($u->hasPermission('laporan.laba-rugi'))
                            <x-nav-link route="laporan.laba-rugi" icon="chart-pie" label="Laporan Laba Rugi" />
                        @endif
                        @if ($u->hasPermission('laporan.diskon'))
                            <x-nav-link route="laporan.diskon" icon="ticket" label="Laporan Diskon" />
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
                    <x-nav-link
                        route="permission.index"
                        pattern="permission.*"
                        icon="shield-check"
                        icon-type="s"
                        label="Izin Akses"
                        variant="top"
                    />
                @endif

            </nav>
        </div>

        <!-- Sidebar Profile Card (Bottom) -->
        <div class="sidebar-footer shrink-0 px-6 py-4 border-t border-blue-600 bg-blue-800 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0" title="{{ $u?->name }} ({{ ucfirst($u?->role) }})">
                <div class="w-12 h-12 rounded-full bg-white text-blue-700 font-bold flex items-center justify-center shrink-0 text-xs shadow-md cursor-default" aria-label="Profil: {{ $u?->name }}">
                    {{ $u ? strtoupper(substr($u->name, 0, 2)) : '' }}
                </div>
                <div class="sidebar-footer-info min-w-0 overflow-hidden whitespace-nowrap">
                    <span class="block text-xs font-semibold text-white truncate">{{ $u?->name }}</span>
                    <span class="block text-[9px] font-bold text-blue-200 uppercase tracking-wider truncate">{{ $u?->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="sidebar-footer-logout shrink-0 overflow-hidden">
                @csrf
                <button type="submit" class="text-blue-200 hover:text-red-400 transition-colors p-1 rounded-md focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:outline-none" title="Logout" aria-label="Logout">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" aria-hidden="true" />
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto overflow-x-hidden">
        <!-- Top Navbar (Sticky) -->
        <nav class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-gray-200 px-5 py-2.5 flex justify-between items-center print:hidden shadow-xs">
            <div class="flex items-center gap-3 md:gap-5 flex-1 min-w-0 pr-4">
                <button id="mobile-sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Buka menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5" aria-hidden="true" />
                </button>

                <!-- Quick Search Input Trigger (Desktop) -->
                <button type="button"
                        id="global-quick-search-trigger"
                        class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-xs text-gray-500 bg-gray-50 hover:bg-gray-100 hover:text-gray-700 border border-gray-200 rounded-lg transition-all w-52 md:w-72 shadow-2xs group focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        title="Cari obat, transaksi faktur, atau menu">
                    <x-heroicon-o-magnifying-glass class="w-3.5 h-3.5 text-gray-400 group-hover:text-blue-600 transition-colors shrink-0" />
                    <span class="truncate text-gray-400 group-hover:text-gray-600 font-normal">Cari obat, faktur, menu...</span>
                </button>

                <!-- Quick Search Icon (Mobile) -->
                <button type="button"
                        id="mobile-quick-search-trigger"
                        class="sm:hidden p-1.5 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer"
                        title="Pencarian cepat">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </button>
            </div>
            
            <div class="flex items-center gap-3 md:gap-4 text-xs shrink-0">
                <!-- Real-time Clock Widget -->
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-lg text-slate-700 shadow-2xs font-medium">
                    <x-heroicon-o-clock class="w-4 h-4 text-blue-600 shrink-0" />
                    <span id="realtime-clock" class="font-mono text-[11px] sm:text-xs tracking-tight">--:--:--</span>
                </div>
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

    {{-- Global Quick Search Modal (Ctrl + K) --}}
    <x-global-quick-search />

    @stack('scripts')
</body>

</html>
