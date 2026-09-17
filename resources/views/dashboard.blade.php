@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container space-y-7">

    {{-- ============================================================ --}}
    {{-- 1. HEADER EXECUTIVE DENGAN FILTER PERIODE                    --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">👋</span>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Ringkasan kondisi operasional dan keuangan apotek Anda hari ini.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Preset Periode --}}
            <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-1 shadow-2xs">
                <a href="{{ route('dashboard', ['periode' => 'hari_ini']) }}"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $periode === 'hari_ini' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    Hari Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'minggu_ini']) }}"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $periode === 'minggu_ini' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    Minggu Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'bulan_ini']) }}"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $periode === 'bulan_ini' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    Bulan Ini
                </a>
                <a href="{{ route('dashboard', ['periode' => 'tahun_ini']) }}"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $periode === 'tahun_ini' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    Tahun Ini
                </a>
                <button type="button" onclick="toggleCustomDate()" id="btnToggleCustom"
                    class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $periode === 'custom' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    Custom
                </button>
            </div>

            {{-- Form Rentang Tanggal Custom --}}
            <form method="GET" action="{{ route('dashboard') }}" id="customDateContainer" class="{{ $periode === 'custom' ? 'flex' : 'hidden' }} items-center gap-1.5 bg-white p-1 rounded-lg border border-gray-200 shadow-2xs">
                <input type="hidden" name="periode" value="custom">
                <input type="date" name="dari" id="inputDari" value="{{ $dari }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                <span class="text-xs text-gray-400">s/d</span>
                <input type="date" name="sampai" id="inputSampai" value="{{ $sampai }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                <button type="submit" class="btn-primary !py-1 !px-2.5 !text-xs">Terapkan</button>
            </form>

            {{-- Tombol Reset (Aktif jika sedang filter selain default Bulan Ini) --}}
            @if(request()->filled('periode') && request('periode') !== 'bulan_ini' || request()->filled('dari') || request()->filled('sampai'))
                <a href="{{ route('dashboard') }}"
                   class="btn-secondary !py-1 !px-2.5 !text-xs flex items-center gap-1 text-gray-600 hover:text-red-600 hover:border-red-300 transition-colors shadow-2xs"
                   title="Reset filter ke Bulan Ini">
                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5 text-gray-500" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 2. KPI KEUANGAN UTAMA (4 KARTU)                              --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Card 1: Omzet Kotor --}}
        <div class="kpi-card kpi-kotor flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Omzet Kotor</span>
                <div class="text-xl sm:text-2xl font-black text-gray-900 mt-1 tracking-tight">
                    Rp {{ number_format($omzetKotor, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] text-gray-500">Sebelum diskon</span>
                    @if(!is_null($deltaOmzetKotor))
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $deltaOmzetKotor >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $deltaOmzetKotor >= 0 ? '+' : '' }}{{ $deltaOmzetKotor }}%
                        </span>
                    @endif
                </div>
            </div>
            <div class="kpi-icon bg-amber-50 text-amber-600 shadow-2xs">
                <x-heroicon-o-arrow-trending-up class="w-6 h-6" />
            </div>
        </div>

        {{-- Card 2: Total Diskon --}}
        <div class="kpi-card kpi-diskon flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Diskon</span>
                <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-1 tracking-tight">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] text-gray-500">Total diskon transaksi</span>
                    @if(!is_null($deltaTotalDiskon))
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $deltaTotalDiskon <= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $deltaTotalDiskon >= 0 ? '+' : '' }}{{ $deltaTotalDiskon }}%
                        </span>
                    @endif
                </div>
            </div>
            <div class="kpi-icon bg-emerald-50 text-emerald-600 shadow-2xs">
                <x-heroicon-o-tag class="w-6 h-6" />
            </div>
        </div>

        {{-- Card 3: Omzet Bersih --}}
        <div class="kpi-card kpi-bersih flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Omzet Bersih</span>
                <div class="text-xl sm:text-2xl font-black text-blue-600 mt-1 tracking-tight">
                    Rp {{ number_format($omzetBersih, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] text-gray-500">Omzet kotor - total diskon</span>
                    @if(!is_null($deltaOmzetBersih))
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $deltaOmzetBersih >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $deltaOmzetBersih >= 0 ? '+' : '' }}{{ $deltaOmzetBersih }}%
                        </span>
                    @endif
                </div>
            </div>
            <div class="kpi-icon bg-blue-50 text-blue-600 shadow-2xs">
                <x-heroicon-o-banknotes class="w-6 h-6" />
            </div>
        </div>

        {{-- Card 4: Total Member --}}
        <div class="kpi-card kpi-member flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">Total Member</span>
                <div class="text-xl sm:text-2xl font-black text-purple-700 mt-1 tracking-tight">
                    {{ number_format($totalMember) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="text-[11px] text-gray-500">Member terdaftar</span>
                    @if($newMembersInPeriod > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-50 text-purple-700">
                            +{{ $newMembersInPeriod }} baru periode ini
                        </span>
                    @endif
                </div>
            </div>
            <div class="kpi-icon bg-purple-50 text-purple-600 shadow-2xs">
                <x-heroicon-o-user-group class="w-6 h-6" />
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 3. FINANCIAL CONTROL (HUTANG SUPPLIER & PIUTANG MEMBER)       --}}
    {{-- ============================================================ --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span> Financial Control
            </h2>
            <span class="text-xs text-gray-500">Kondisi liabilitas & piutang berjalan</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Panel Hutang Supplier --}}
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between pb-3 border-b border-gray-150">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <x-heroicon-o-building-office-2 class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Hutang Supplier</span>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">
                                Rp {{ number_format($totalHutangSupplier, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('penerimaan.index', ['status_pembayaran' => 'belum_lunas']) }}"
                       class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                        Buka Penerimaan &rarr;
                    </a>
                </div>

                {{-- Breakdown Hutang --}}
                <div class="grid grid-cols-3 gap-3 mt-4 pt-1">
                    <div class="p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wide block">Belum Tempo</span>
                        <span class="text-sm font-bold text-emerald-800 block mt-1">Rp {{ number_format($hutangNotDue, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-emerald-600 block mt-0.5">{{ $countHutangNotDue }} faktur</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-100">
                        <span class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide block">Segera Tempo (H-7)</span>
                        <span class="text-sm font-bold text-amber-800 block mt-1">Rp {{ number_format($hutangDueSoon, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-amber-600 block mt-0.5">{{ $countHutangDueSoon }} faktur</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-rose-50/60 border border-rose-100">
                        <span class="text-[10px] font-semibold text-rose-700 uppercase tracking-wide block">Jatuh Tempo</span>
                        <span class="text-sm font-bold text-rose-800 block mt-1">Rp {{ number_format($hutangOverdue, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-rose-600 block mt-0.5">{{ $countHutangOverdue }} faktur</span>
                    </div>
                </div>
            </div>

            {{-- Panel Piutang Member --}}
            <div class="dashboard-card p-5">
                <div class="flex items-start justify-between pb-3 border-b border-gray-150">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <x-heroicon-o-credit-card class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Piutang Member</span>
                            <div class="text-xl font-bold text-gray-900 mt-0.5">
                                Rp {{ number_format($totalPiutangMember, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('pelanggan.index', ['status_piutang' => 'belum_lunas']) }}"
                       class="text-xs font-semibold text-purple-600 hover:text-purple-800 flex items-center gap-1 transition-colors">
                        Buka Pelanggan &rarr;
                    </a>
                </div>

                {{-- Breakdown Piutang --}}
                <div class="grid grid-cols-3 gap-3 mt-4 pt-1">
                    <div class="p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[10px] font-semibold text-emerald-700 uppercase tracking-wide block">Belum Tempo</span>
                        <span class="text-sm font-bold text-emerald-800 block mt-1">Rp {{ number_format($piutangNotDue, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-emerald-600 block mt-0.5">{{ $countPiutangNotDue }} piutang</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-100">
                        <span class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide block">Segera Tempo</span>
                        <span class="text-sm font-bold text-amber-800 block mt-1">Rp {{ number_format($piutangDueSoon, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-amber-600 block mt-0.5">{{ $countPiutangDueSoon }} piutang</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-rose-50/60 border border-rose-100">
                        <span class="text-[10px] font-semibold text-rose-700 uppercase tracking-wide block">Overdue</span>
                        <span class="text-sm font-bold text-rose-800 block mt-1">Rp {{ number_format($piutangOverdue, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-rose-600 block mt-0.5">{{ $countPiutangOverdue }} piutang</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 4. GRAFIK PENJUALAN (SALES & PROFIT PERFORMANCE)              --}}
    {{-- ============================================================ --}}
    <div class="dashboard-card p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900 tracking-tight">Sales & Profit Performance</h3>
                <p class="text-xs text-gray-500 mt-0.5">Grafik tren transaksi penjualan dan omzet apotek aktual.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-gray-600">Omzet Kotor</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                    <span class="text-gray-600">Omzet Bersih</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-gray-600">Gross Profit</span>
                </span>
            </div>
        </div>

        <div class="relative w-full" style="min-height: 280px; max-height: 340px;">
            <canvas id="salesPerformanceChart"></canvas>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 5. INVENTORY CONTROL CENTER                                  --}}
    {{-- ============================================================ --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Inventory Control Center
            </h2>
            <a href="{{ route('laporan.stok') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                Laporan Stok Lengkap &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Panel Kondisi Stok (TANPA TOTAL SKU) --}}
            <div class="dashboard-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-gray-150">
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Stok Unit Aktif</span>
                            <div class="text-2xl font-black text-gray-900 mt-1">
                                {{ number_format($totalStok) }} <span class="text-sm font-semibold text-gray-500">Unit</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Nilai Persediaan</span>
                            <div class="text-lg font-bold text-blue-700 mt-1">
                                Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wide block mb-2">Kondisi Stok Barang</span>
                        <div class="grid grid-cols-3 gap-2.5">
                            <a href="{{ route('laporan.stok', ['status_stok' => 'habis']) }}"
                               class="p-3 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-100/60 transition-colors flex items-center justify-between group">
                                <div>
                                    <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Habis</span>
                                    <span class="text-xl font-black text-rose-900 block mt-0.5">{{ $stokHabisCount }}</span>
                                </div>
                                <span class="text-xs text-rose-400 group-hover:text-rose-600">&rarr;</span>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}"
                               class="p-3 rounded-lg border border-amber-200 bg-amber-50/50 hover:bg-amber-100/60 transition-colors flex items-center justify-between group">
                                <div>
                                    <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Menipis</span>
                                    <span class="text-xl font-black text-amber-900 block mt-0.5">{{ $stokMenipisCount }}</span>
                                </div>
                                <span class="text-xs text-amber-400 group-hover:text-amber-600">&rarr;</span>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'aman']) }}"
                               class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/60 transition-colors flex items-center justify-between group">
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Aman</span>
                                    <span class="text-xl font-black text-emerald-900 block mt-0.5">{{ $stokAmanCount }}</span>
                                </div>
                                <span class="text-xs text-emerald-400 group-hover:text-emerald-600">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                    <span>*Threshold konsisten dengan Laporan Stok Apotek</span>
                    <span class="font-medium text-gray-700">Total Produk: {{ $stokHabisCount + $stokMenipisCount + $stokAmanCount }}</span>
                </div>
            </div>

            {{-- Panel Expiry Health --}}
            <div class="dashboard-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between pb-3 border-b border-gray-150">
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Expiry Health</span>
                            <div class="text-lg font-bold text-gray-900 mt-0.5">
                                Status Kedaluwarsa Batch
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block">Total Batch Aktif</span>
                            <span class="text-base font-bold text-gray-800">{{ $totalBatchesCount }} Batch</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mt-4">
                        <a href="{{ route('laporan.stok', ['status_expired' => 'normal']) }}"
                           class="p-2.5 rounded-lg border border-emerald-200 bg-emerald-50/40 hover:bg-emerald-50 transition-colors text-center block">
                            <span class="text-[10px] font-bold text-emerald-700 block uppercase">Aman (>90 hr)</span>
                            <span class="text-lg font-black text-emerald-800 block mt-1">{{ $expiryAman }}</span>
                        </a>

                        <a href="{{ route('laporan.stok', ['status_expired' => '3_bulan']) }}"
                           class="p-2.5 rounded-lg border border-amber-200 bg-amber-50/40 hover:bg-amber-50 transition-colors text-center block">
                            <span class="text-[10px] font-bold text-amber-700 block uppercase">Warning (60-90)</span>
                            <span class="text-lg font-black text-amber-800 block mt-1">{{ $expiryWarning }}</span>
                        </a>

                        <a href="{{ route('laporan.stok', ['status_expired' => '1_bulan']) }}"
                           class="p-2.5 rounded-lg border border-orange-200 bg-orange-50/40 hover:bg-orange-50 transition-colors text-center block">
                            <span class="text-[10px] font-bold text-orange-700 block uppercase">Critical (<60)</span>
                            <span class="text-lg font-black text-orange-800 block mt-1">{{ $expiryCritical }}</span>
                        </a>

                        <a href="{{ route('laporan.stok', ['status_expired' => 'kadaluarsa']) }}"
                           class="p-2.5 rounded-lg border border-rose-200 bg-rose-50/40 hover:bg-rose-50 transition-colors text-center block">
                            <span class="text-[10px] font-bold text-rose-700 block uppercase">Kadaluarsa</span>
                            <span class="text-lg font-black text-rose-800 block mt-1">{{ $expiryKadaluarsa }}</span>
                        </a>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-500 flex items-center justify-between">
                    <span>*Klik kategori untuk memfilter langsung di Laporan Stok</span>
                    <span class="font-semibold text-rose-600">
                        {{ $expiryKadaluarsa + $expiryCritical }} Batch perlu tindakan
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 6. ACTION CENTER (ALERT OPERASIONAL REAL-TIME)               --}}
    {{-- ============================================================ --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-600"></span> Action Center
            </h2>
            <span class="text-xs text-gray-500">Pusat monitoring kondisi yang membutuhkan perhatian operasional</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Alert 1: Stok Habis --}}
            <a href="{{ $actionCenter['critical']['route'] }}" class="action-alert-card critical">
                <div class="flex items-center justify-between">
                    <span class="badge-chip bg-rose-100 text-rose-700">Critical</span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-rose-400" />
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl font-black text-rose-800">{{ $actionCenter['critical']['count'] }}</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">{{ $actionCenter['critical']['label'] }}</div>
                </div>
            </a>

            {{-- Alert 2: Stok Menipis --}}
            <a href="{{ $actionCenter['attention']['route'] }}" class="action-alert-card attention">
                <div class="flex items-center justify-between">
                    <span class="badge-chip bg-amber-100 text-amber-700">Attention</span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-amber-400" />
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl font-black text-amber-800">{{ $actionCenter['attention']['count'] }}</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">{{ $actionCenter['attention']['label'] }}</div>
                </div>
            </a>

            {{-- Alert 3: Mendekati Expired --}}
            <a href="{{ $actionCenter['monitoring']['route'] }}" class="action-alert-card monitoring">
                <div class="flex items-center justify-between">
                    <span class="badge-chip bg-sky-100 text-sky-700">Monitoring</span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-sky-400" />
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl font-black text-sky-800">{{ $actionCenter['monitoring']['count'] }}</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">{{ $actionCenter['monitoring']['label'] }}</div>
                </div>
            </a>

            {{-- Alert 4: Hutang Jatuh Tempo --}}
            <a href="{{ $actionCenter['overdue']['route'] }}" class="action-alert-card overdue">
                <div class="flex items-center justify-between">
                    <span class="badge-chip bg-rose-100 text-rose-700">Overdue</span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-rose-400" />
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl font-black text-rose-800">{{ $actionCenter['overdue']['count'] }}</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">{{ $actionCenter['overdue']['label'] }}</div>
                </div>
            </a>

            {{-- Alert 5: Piutang Member --}}
            <a href="{{ $actionCenter['piutang']['route'] }}" class="action-alert-card piutang">
                <div class="flex items-center justify-between">
                    <span class="badge-chip bg-purple-100 text-purple-700">Piutang</span>
                    <x-heroicon-o-chevron-right class="w-4 h-4 text-purple-400" />
                </div>
                <div class="mt-2.5">
                    <div class="text-2xl font-black text-purple-800">{{ $actionCenter['piutang']['count'] }}</div>
                    <div class="text-xs font-semibold text-gray-700 mt-0.5">{{ $actionCenter['piutang']['label'] }}</div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 7. BARANG YANG HARUS DIBELI                                   --}}
    {{-- ============================================================ --}}
    <div class="dashboard-card p-5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-150 mb-3">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shopping-bag class="w-5 h-5 text-amber-600" />
                <div>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Barang yang Harus Dibeli</h3>
                    <p class="text-xs text-gray-500">Daftar obat aktif yang stoknya habis atau di bawah batas minimum.</p>
                </div>
            </div>
            <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                Lihat Selengkapnya &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table-compact w-full text-left">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center">Stok Minimum</th>
                        <th class="text-center">Kekurangan</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangHarusDibeli as $index => $item)
                        <tr>
                            <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                            <td class="font-bold text-gray-900">{{ $item['barang']->nama }}</td>
                            <td class="text-gray-600">{{ $item['barang']->kategori->nama ?? '-' }}</td>
                            <td class="text-center font-bold text-gray-800">{{ $item['stok'] }}</td>
                            <td class="text-center font-medium text-gray-500">{{ $item['stok_minimum'] }}</td>
                            <td class="text-center font-bold text-rose-600">{{ $item['kekurangan'] }}</td>
                            <td class="text-center">
                                @if($item['status'] === 'HABIS')
                                    <span class="badge-chip bg-rose-100 text-rose-700">Habis</span>
                                @else
                                    <span class="badge-chip bg-amber-100 text-amber-700">Segera Beli</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-gray-400">
                                Seluruh stok obat berada dalam kondisi aman di atas batas minimum.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 8. 10 OBAT TERLARIS & 10 OBAT PALING SEDIKIT TERJUAL         --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- 10 Obat Terlaris --}}
        <div class="dashboard-card p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-150 mb-3">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-trophy class="w-5 h-5 text-amber-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900">10 Obat Terlaris</h3>
                        <p class="text-xs text-gray-500">Kuantitas unit terjual pada periode {{ $periodeLabel }}.</p>
                    </div>
                </div>
                <a href="{{ route('laporan.penjualan') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    Laporan Penjualan &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="table-compact w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topSellingMedicines as $index => $medicine)
                            <tr>
                                <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td class="font-bold text-gray-900">{{ $medicine->nama }}</td>
                                <td class="text-gray-600">{{ $medicine->kategori ?? '-' }}</td>
                                <td class="text-center font-bold text-blue-600">{{ number_format($medicine->total_terjual) }} unit</td>
                                <td class="text-right font-semibold text-gray-800">Rp {{ number_format($medicine->total_omzet, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-gray-400">
                                    Belum ada transaksi penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 10 Obat Paling Sedikit Terjual --}}
        <div class="dashboard-card p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-150 mb-3">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-rose-500" />
                    <div>
                        <h3 class="text-base font-bold text-gray-900">10 Obat Paling Sedikit Terjual</h3>
                        <p class="text-xs text-gray-500">Termasuk obat aktif yang belum terjual (0 unit).</p>
                    </div>
                </div>
                <a href="{{ route('laporan.penjualan') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    Laporan Penjualan &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="table-compact w-full text-left">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leastSellingMedicines as $index => $medicine)
                            <tr>
                                <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td class="font-bold text-gray-900">{{ $medicine->nama }}</td>
                                <td class="text-gray-600">{{ $medicine->kategori ?? '-' }}</td>
                                <td class="text-center font-bold {{ $medicine->total_terjual == 0 ? 'text-gray-400' : 'text-amber-600' }}">
                                    {{ number_format($medicine->total_terjual) }} unit
                                </td>
                                <td class="text-right font-semibold text-gray-800">
                                    Rp {{ number_format($medicine->total_omzet, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-gray-400">
                                    Belum ada data obat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 9. AKSES CEPAT KASIR (SATU TOMBOL SAJA)                      --}}
    {{-- ============================================================ --}}
    <div class="dashboard-card p-6 bg-gradient-to-r from-blue-700 to-indigo-800 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                <x-heroicon-o-shopping-cart class="w-7 h-7 text-white" />
            </div>
            <div>
                <h3 class="text-lg font-bold text-white tracking-tight">Akses Cepat Kasir POS</h3>
                <p class="text-xs text-blue-100 mt-0.5">
                    Lakukan transaksi penjualan obat, audit FEFO batch otomatis, dan diskon member dalam satu layar.
                </p>
            </div>
        </div>

        <a href="{{ route('penjualan.create') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white text-blue-700 font-bold text-sm shadow-md hover:bg-blue-50 transition-all transform hover:scale-[1.02] shrink-0">
            <x-heroicon-o-bolt class="w-5 h-5 text-amber-500" />
            BUKA KASIR
        </a>
    </div>

</div>

{{-- Script Chart.js CDN & Interaktivitas --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDate() {
        const container = document.getElementById('customDateContainer');
        const hiddenPeriode = document.getElementById('hiddenCustomPeriode');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            container.classList.add('flex');
            hiddenPeriode.disabled = false;
        } else {
            container.classList.add('hidden');
            container.classList.remove('flex');
            hiddenPeriode.disabled = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesPerformanceChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const dataGross = @json($chartGross);
        const dataNet = @json($chartNet);
        const dataProfit = @json($chartProfit);

        const formatRupiah = (val) => {
            return 'Rp ' + Number(val).toLocaleString('id-ID');
        };

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Omzet Kotor',
                        data: dataGross,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.05)',
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#f59e0b',
                        fill: false,
                    },
                    {
                        label: 'Omzet Bersih',
                        data: dataNet,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.10)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#2563eb',
                        fill: true,
                    },
                    {
                        label: 'Gross Profit',
                        data: dataProfit,
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderWidth: 1.75,
                        borderDash: [4, 4],
                        tension: 0.35,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#10b981',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: {
                            weight: 'bold',
                            size: 12,
                        },
                        bodyFont: {
                            size: 11,
                        },
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 10,
                            },
                            color: '#64748b',
                            maxRotation: 0,
                        }
                    },
                    y: {
                        border: {
                            display: false,
                        },
                        grid: {
                            color: '#f1f5f9',
                        },
                        ticks: {
                            font: {
                                size: 10,
                            },
                            color: '#64748b',
                            callback: function (val) {
                                if (val >= 1000000) {
                                    return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                                } else if (val >= 1000) {
                                    return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
                                }
                                return 'Rp ' + val;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
