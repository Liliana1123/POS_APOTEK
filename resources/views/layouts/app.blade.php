<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Apotek')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex overflow-x-hidden">
    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar" class="group fixed top-0 left-0 h-screen w-60 bg-blue-700 border-r border-blue-800 flex flex-col justify-between z-50 self-start transform -translate-x-full lg:translate-x-0 lg:sticky lg:flex lg:w-20 transition-all duration-200 ease-in-out print:hidden">
        <script>
            (function () {
                var sidebar = document.getElementById('app-sidebar');
                var timer;
                sidebar.addEventListener('mouseenter', function () {
                    clearTimeout(timer);
                    sidebar.classList.add('sidebar-expanded');
                });
                sidebar.addEventListener('mouseleave', function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        sidebar.classList.remove('sidebar-expanded');
                    }, 150);
                });
            })();
        </script>
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Sidebar Header -->
            <div class="sidebar-header px-5 py-4 flex justify-between items-center">
                <div class="font-bold text-base text-white tracking-wider flex items-center gap-2 overflow-hidden">
                    <span class="hidden lg:inline lg:group-hover:hidden shrink-0">AK</span>
                    <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">APOTEK KITA</span>
                </div>
                <button id="close-sidebar" class="lg:hidden text-blue-200 hover:text-white focus:outline-none" aria-label="Close menu">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5">
                <a href="{{ route('dashboard') }}" title="Dashboard" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('dashboard') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                    <span class="flex items-center justify-center shrink-0">
                        <x-heroicon-s-home class="w-5 h-5" />
                    </span>
                    <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap font-bold uppercase tracking-wider">Dashboard</span>
                </a>

                <!-- Group: Master Data -->
                <div class="sidebar-group" data-group="master">
                    <button type="button" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider pt-3 pb-1 px-3 hover:text-white transition-colors">
                        <span class="flex items-center gap-2.5 min-w-0">
<span class="flex items-center justify-center shrink-0">
                        <x-heroicon-s-cube class="w-5 h-5" />
                            </span>
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Data Master</span>
                        </span>
                        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 transition-transform duration-200 lg:opacity-0 lg:group-hover:opacity-100" />
                    </button>
                    @if (auth()->user()->isAdmin())
                        <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out">
                            <a href="{{ route('barang.index') }}" title="Barang / Produk" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('barang.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-archive-box class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Barang / Produk</span>
                            </a>
<a href="{{ route('kategori.index') }}" title="Kategori" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('kategori.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
<x-heroicon-o-percent-badge class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Kategori</span>
                            </a>
<a href="{{ route('satuan.index') }}" title="Satuan" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('satuan.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-scale class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Satuan</span>
                            </a>
<a href="{{ route('pabrik.index') }}" title="Pabrik" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('pabrik.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-building-storefront class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Pabrik</span>
                            </a>
<a href="{{ route('supplier.index') }}" title="Supplier" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('supplier.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-building-office-2 class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Supplier</span>
                            </a>
<a href="{{ route('pelanggan.index') }}" title="Pelanggan / Member" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('pelanggan.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-user class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Pelanggan / Member</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Group: Transaksi -->
                <div class="sidebar-group" data-group="transaksi">
                    <button type="button" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider pt-3 pb-1 px-3 hover:text-white transition-colors">
                        <span class="flex items-center gap-2.5 min-w-0">
                            <span class="flex items-center justify-center shrink-0">
                                <x-heroicon-s-banknotes class="w-5 h-5" />
                            </span>
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Transaksi</span>
                        </span>
                        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 transition-transform duration-200 lg:opacity-0 lg:group-hover:opacity-100" />
                    </button>
                    <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out">
                        <a href="{{ route('penjualan.index') }}" title="Penjualan (Kasir)" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('penjualan.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-shopping-cart class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Penjualan (Kasir)</span>
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('penerimaan.index') }}" title="Penerimaan Barang" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('penerimaan.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                                <x-heroicon-o-truck class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Penerimaan barang</span>
                            </a>
                            <a href="{{ route('rusak.index') }}" title="Barang Rusak" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('rusak.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
<x-heroicon-o-exclamation-triangle class="w-4 h-4 shrink-0" />
                                <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Barang rusak</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Group: Laporan (Admin Only) -->
                @if (auth()->user()->isAdmin())
                <div class="sidebar-group" data-group="laporan">
                    <button type="button" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider pt-3 pb-1 px-3 hover:text-white transition-colors">
                        <span class="flex items-center gap-2.5 min-w-0">
                            <span class="flex items-center justify-center shrink-0">
                                <x-heroicon-s-chart-bar class="w-5 h-5" />
                            </span>
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan</span>
                        </span>
                        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 transition-transform duration-200 lg:opacity-0 lg:group-hover:opacity-100" />
                    </button>
                    <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out">
                        <a href="{{ route('laporan.stok') }}" title="Laporan Stok" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.stok') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-document-chart-bar class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan stok</span>
                        </a>
                        <a href="{{ route('laporan.penerimaan') }}" title="Laporan Penerimaan" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.penerimaan') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-document-duplicate class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan penerimaan</span>
                        </a>
                        <a href="{{ route('laporan.penjualan') }}" title="Laporan Penjualan" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.penjualan') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-currency-dollar class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan penjualan</span>
                        </a>
                        <a href="{{ route('laporan.rusak') }}" title="Laporan Barang Rusak" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.rusak') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-no-symbol class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan barang rusak</span>
                        </a>
                        <a href="{{ route('laporan.laba-rugi') }}" title="Laporan Laba Rugi" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.laba-rugi') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-chart-pie class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan laba-rugi</span>
                        </a>
                        <a href="{{ route('laporan.diskon') }}" title="Laporan Diskon" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.diskon') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-ticket class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Laporan diskon</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Group: Promo & Log (Admin Only) -->
                @if (auth()->user()->isAdmin())
                <div class="sidebar-group" data-group="promo">
                    <button type="button" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider pt-3 pb-1 px-3 hover:text-white transition-colors">
                        <span class="flex items-center gap-2.5 min-w-0">
                            <span class="flex items-center justify-center shrink-0">
                                <x-heroicon-s-gift class="w-5 h-5" />
                            </span>
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Promo & Log</span>
                        </span>
                        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 transition-transform duration-200 lg:opacity-0 lg:group-hover:opacity-100" />
                    </button>
                    <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out">
                        <a href="{{ route('custom-discount.index') }}" title="Custom Discount" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('custom-discount.*') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-tag class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Custom Discount</span>
                        </a>
                        <a href="{{ route('activity-log') }}" title="Log Aktivitas" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('activity-log') ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2' : 'text-gray-200 hover:bg-blue-600' }}">
                            <x-heroicon-o-book-open class="w-4 h-4 shrink-0" />
                            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-150 whitespace-nowrap">Log Aktivitas</span>
                        </a>
                    </div>
                </div>
                @endif

            </nav>
        </div>

        <!-- Restore sidebar group state synchronously (blocking) to prevent flicker -->
        <script>
            // Runs during HTML parsing, before first paint. Uses inline style directly
            // (no scrollHeight/CSS dependency), so expanded groups render open with no flicker.
            (function () {
                document.querySelectorAll('.sidebar-group').forEach(function (group) {
                    var name = group.dataset.group;
                    var saved = localStorage.getItem('sidebar-group-' + name);
                    var items = group.querySelector('.sidebar-group-items');
                    if (!items) return;
                    var active = items.querySelector('a[class*="border-white"]');
                    var expand = saved === '1' || !!active;
                    if (expand) {
                        items.style.maxHeight = '999px';
                        items.setAttribute('data-expanded', '1');
                        var arrow = items.previousElementSibling ? items.previousElementSibling.querySelector('.sidebar-group-arrow') : null;
                        if (arrow) arrow.classList.add('rotate-90');
                    }
                });
            })();
        </script>

        <!-- Sidebar Profile Card (Bottom) -->
        <div class="sidebar-footer p-4 border-t border-blue-600 bg-blue-800 flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-full bg-white text-blue-700 font-bold flex items-center justify-center shrink-0 text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0 overflow-hidden whitespace-nowrap lg:max-w-0 lg:opacity-0 lg:group-hover:max-w-[200px] lg:group-hover:opacity-100 transition-all duration-200 ease-in-out">
                    <span class="block text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</span>
                    <span class="block text-[9px] font-bold text-blue-200 uppercase tracking-wider truncate">{{ auth()->user()->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0 overflow-hidden whitespace-nowrap lg:max-w-0 lg:opacity-0 lg:group-hover:max-w-[50px] lg:group-hover:opacity-100 transition-all duration-200 ease-in-out">
                @csrf
                <button type="submit" class="text-blue-200 hover:text-red-400 transition-colors p-1" title="Logout">
                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" />
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Navbar -->
        <nav class="bg-white border-b px-5 py-3.5 flex justify-between items-center print:hidden">
            <div class="flex items-center gap-3">
                <button id="mobile-sidebar-toggle" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none" aria-label="Open menu">
                    <x-heroicon-o-bars-3 class="w-5 h-5" />
                </button>
                <span class="font-bold text-sm text-gray-800">@yield('title', 'Dashboard')</span>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="text-gray-600 font-medium hidden md:inline">
                    Login sebagai: <span class="text-[10px] font-bold text-blue-700 px-2.5 py-0.5 ml-1 uppercase tracking-wider">{{ auth()->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline md:hidden">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold flex items-center gap-1">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" />
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content Wrapper -->
        <main class="flex-1 p-5 max-w-6xl w-full mx-auto pb-12">
            @yield('content')
        </main>
    </div>

    <!-- Session Flash Toast Notifications -->
    @if (session('success'))
    <div id="toast-success" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800">
        <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 shrink-0" />
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('toast-success').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-success');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 3500);
    </script>
    @endif

    @if (session('error'))
    <div id="toast-error" class="fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 z-50 transition-all duration-300 transform translate-y-0 opacity-100 border border-gray-800">
        <x-heroicon-o-x-circle class="w-5 h-5 text-red-500 shrink-0" />
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('toast-error').remove()" class="ml-2 font-bold text-gray-400 hover:text-white text-sm">&times;</button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-error');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    </script>
    @endif

    <!-- Shell Interactions & Global Logic Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('app-sidebar');
            const toggle = document.getElementById('mobile-sidebar-toggle');
            const backdrop = document.getElementById('sidebar-backdrop');
            const closeBtn = document.getElementById('close-sidebar');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) backdrop.classList.remove('hidden');
                });
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            }

            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);

            // Escape key to close sidebar drawer
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });

            // Sidebar Group Accordion Toggle (smooth, class-only anti-flicker)
            document.querySelectorAll('.sidebar-group-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    const group = this.closest('.sidebar-group');
                    const items = group.querySelector('.sidebar-group-items');
                    const arrow = this.querySelector('.sidebar-group-arrow');
                    const groupName = group.dataset.group;
                    
                    const isExpanded = items.getAttribute('data-expanded') === '1';
                    
                    if (isExpanded) {
                        items.style.maxHeight = '0px';
                        items.setAttribute('data-expanded', '0');
                        localStorage.setItem('sidebar-group-' + groupName, '0');
                    } else {
                        items.style.maxHeight = '999px';
                        items.setAttribute('data-expanded', '1');
                        localStorage.setItem('sidebar-group-' + groupName, '1');
                    }
                    arrow.classList.toggle('rotate-90');
                });
            });
        });

        // Delete Form Confirmation Modal Interceptor
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.method.toLowerCase() === 'post' && form.querySelector('input[name="_method"][value="DELETE"]')) {
                if (!form.dataset.confirmed) {
                    e.preventDefault();
                    const modal = document.createElement('div');
                    modal.className = 'modal-backdrop-custom';
                    modal.innerHTML = `
                        <div class="modal-container-custom mx-4">
                            <div class="modal-header-custom">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-red-600">Konfirmasi Hapus</h3>
                                <button type="button" id="confirm-x" class="text-gray-400 hover:text-gray-600 font-bold text-base">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                            <div class="modal-body-custom leading-relaxed">
                                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan dapat memengaruhi integritas relasi data transaksi.
                            </div>
                            <div class="modal-footer-custom">
                                <button type="button" id="confirm-cancel" class="btn-secondary">Batal</button>
                                <button type="button" id="confirm-submit" class="btn-destructive">Hapus</button>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                    
                    const closeModal = () => modal.remove();
                    modal.querySelector('#confirm-cancel').onclick = closeModal;
                    modal.querySelector('#confirm-x').onclick = closeModal;
                    modal.querySelector('#confirm-submit').onclick = function () {
                        form.dataset.confirmed = 'true';
                        closeModal();
                        form.submit();
                    };
                }
            }
        });

        // Universal Form Submission Loading States to prevent double-submit
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.action.includes('logout') || form.dataset.confirmed === 'false') {
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                setTimeout(() => {
                    btn.disabled = true;
                    btn.innerHTML = btn.innerHTML.includes('Simpan') ? 'Menyimpan...' : 'Memproses...';
                }, 10);
            }
        });
    </script>
</body>

</html>