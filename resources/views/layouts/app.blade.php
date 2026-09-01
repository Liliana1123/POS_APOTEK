<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'POS Apotek')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen flex overflow-x-hidden">
    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 w-60 bg-white border-r flex flex-col justify-between z-50 transform -translate-x-full lg:translate-x-0 lg:static lg:flex transition-transform duration-200 ease-in-out print:hidden">
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Sidebar Header -->
            <div class="px-5 py-4 flex justify-between items-center">
                <div class="font-bold text-base text-blue-600 tracking-wider flex items-center gap-2">
                    <span>APOTEK KITA</span>
                </div>
                <button id="close-sidebar" class="lg:hidden text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>



                <!-- Group: Master Data -->
                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-3 pb-1 px-3">Data Master</div>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('barang.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('barang.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Barang / Produk</span>
                    </a>
                    <a href="{{ route('kategori.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('kategori.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('satuan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('satuan.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Satuan</span>
                    </a>
                    <a href="{{ route('pabrik.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('pabrik.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span>Pabrik</span>
                    </a>
                    <a href="{{ route('supplier.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('supplier.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Supplier</span>
                    </a>
                    <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('pelanggan.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Pelanggan / Member</span>
                    </a>
                @endif
                <!-- Group: Operational -->
                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-3 pb-1 px-3">Transaksi</div>
                <a href="{{ route('penjualan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('penjualan.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Penjualan (Kasir)</span>
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('penerimaan.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('penerimaan.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path>
                        </svg>
                        <span>Penerimaan barang</span>
                    </a>
                    <a href="{{ route('rusak.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('rusak.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Barang rusak</span>
                    </a>
                @endif
                
                <!-- Group: Laporan (Admin Only) -->
                @if (auth()->user()->isAdmin())
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-3 pb-1 px-3">Laporan</div>
                    <a href="{{ route('laporan.stok') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.stok') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Laporan stok</span>
                    </a>
                    <a href="{{ route('laporan.penerimaan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.penerimaan') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Laporan penerimaan</span>
                    </a>
                    <a href="{{ route('laporan.penjualan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.penjualan') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Laporan penjualan</span>
                    </a>
                    <a href="{{ route('laporan.rusak') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.rusak') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Laporan barang rusak</span>
                    </a>
                    <a href="{{ route('laporan.laba-rugi') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.laba-rugi') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Laporan laba-rugi</span>
                    </a>
                    <a href="{{ route('laporan.diskon') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('laporan.diskon') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Laporan diskon</span>
                    </a>
                @endif

                <!-- Group: Promo & Log (Admin Only) -->
                @if (auth()->user()->isAdmin())
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider pt-3 pb-1 px-3">Promo & Log</div>
                    <a href="{{ route('custom-discount.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('custom-discount.*') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0h-4m0 0v13m0 0h6m-6 0H6"></path>
                        </svg>
                        <span>Custom Discount</span>
                    </a>
                    <a href="{{ route('activity-log') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ request()->routeIs('activity-log') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-600 pl-2' : 'text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Log Aktivitas</span>
                    </a>
                @endif

            </nav>
        </div>

        <!-- Sidebar Profile Card (Bottom) -->
        <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center shrink-0 text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <span class="block text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</span>
                    <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider truncate">{{ auth()->user()->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <span class="font-bold text-sm text-gray-800">@yield('title', 'Dashboard')</span>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="text-gray-600 font-medium hidden md:inline">
                    Login sebagai: <span class="text-[10px] font-bold text-blue-700 px-2.5 py-0.5 ml-1 uppercase tracking-wider">{{ auth()->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline md:hidden">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Logout</button>
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
        <span class="text-green-500 font-bold">✓</span>
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
        <span class="text-red-500 font-bold">✕</span>
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
                                <button type="button" id="confirm-x" class="text-gray-400 hover:text-gray-600 font-bold text-base">&times;</button>
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