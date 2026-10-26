@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')


@php
    // 1. KPI Cards (6 Kartu)
    $canKpiOmzetKotor   = auth()->user()->hasPermission('dashboard.kpi-omzet-kotor');
    $canKpiTotalDiskon  = auth()->user()->hasPermission('dashboard.kpi-total-diskon');
    $canKpiOmzetBersih  = auth()->user()->hasPermission('dashboard.kpi-omzet-bersih');
    $canKpiTotalMember  = auth()->user()->hasPermission('dashboard.kpi-total-member');
    $canKpiHutang       = auth()->user()->hasPermission('dashboard.kpi-hutang-supplier');
    $canKpiPiutang      = auth()->user()->hasPermission('dashboard.kpi-piutang-member');

    // 2. Grafik & Analisis
    $canGrafikOmzet     = auth()->user()->hasPermission('dashboard.grafik-omzet');
    $canGrafikProfit    = auth()->user()->hasPermission('dashboard.grafik-profit');
    $canKondisiBisnis   = auth()->user()->hasPermission('dashboard.kondisi-bisnis');
    $canTombolKasir     = auth()->user()->hasPermission('dashboard.tombol-buka-kasir') && auth()->user()->canManage('transaksi.penjualan');
    $hasAlerts          = !empty($actionCenter);
    $showActionCol      = $hasAlerts || $canTombolKasir;

    // 3. Stok & Inventory
    $canPemantauanStok  = auth()->user()->hasPermission('dashboard.pemantauan-stok');
    $canNilaiPersediaan = auth()->user()->hasPermission('dashboard.nilai-persediaan');
    $canBarangBeli      = auth()->user()->hasPermission('dashboard.barang-harus-dibeli');

    // 4. Obat & Karyawan
    $canObatTerlaris    = auth()->user()->hasPermission('dashboard.obat-terlaris');
    $canObatTerendah    = auth()->user()->hasPermission('dashboard.obat-terendah');
    $canOmzetObat       = auth()->user()->hasPermission('dashboard.omzet-obat');
    $canKinerja         = auth()->user()->hasPermission('dashboard.kinerja-karyawan');
    $canDokterResep     = auth()->user()->hasPermission('dashboard.dokter-resep');
@endphp

<div class="dashboard-container dashboard-reference space-y-5">

    {{-- ============================================================ --}}
    {{-- 1. HEADER EXECUTIVE DENGAN FILTER PERIODE                    --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard</h1>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Ringkasan kondisi operasional dan keuangan apotek Anda hari ini.
            </p>
        </div>

        <x-date-range-filter
            :action="route('dashboard')"
            :periode="$periode"
            :dari="$dari"
            :sampai="$sampai"
        />
    </div>

    {{-- ============================================================ --}}
    {{-- 2. KPI UTAMA (KARTU STATISTIK)                               --}}
    {{-- ============================================================ --}}
    @if ($canKpiOmzetKotor || $canKpiTotalDiskon || $canKpiOmzetBersih || $canKpiTotalMember || $canKpiHutang || $canKpiPiutang)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        @if ($canKpiOmzetKotor)
            <x-kpi-card
                title="Omzet Kotor"
                :value="'Rp ' . number_format($omzetKotor, 0, ',', '.')"
                subtitle="Sebelum diskon"
                variant="kotor"
                icon="heroicon-o-chart-bar"
                icon-class="bg-blue-50 text-blue-600"
                :trend="$deltaOmzetKotor !== null ? ($deltaOmzetKotor >= 0 ? '+' : '') . $deltaOmzetKotor . '%' : null"
            />
        @endif

        @if ($canKpiTotalDiskon)
            <x-kpi-card
                title="Total Diskon"
                :value="'Rp ' . number_format($totalDiskon, 0, ',', '.')"
                subtitle="Total diskon transaksi"
                variant="diskon"
                icon="heroicon-o-tag"
                icon-class="bg-purple-50 text-purple-600"
                :trend="$deltaTotalDiskon !== null ? ($deltaTotalDiskon >= 0 ? '+' : '') . $deltaTotalDiskon . '%' : null"
            />
        @endif

        @if ($canKpiOmzetBersih)
            <x-kpi-card
                title="Omzet Bersih"
                :value="'Rp ' . number_format($omzetBersih, 0, ',', '.')"
                subtitle="Setelah diskon"
                variant="bersih"
                icon="heroicon-o-banknotes"
                icon-class="bg-emerald-50 text-emerald-600"
                :trend="$deltaOmzetBersih !== null ? ($deltaOmzetBersih >= 0 ? '+' : '') . $deltaOmzetBersih . '%' : null"
            />
        @endif

        @if ($canKpiTotalMember)
            <x-kpi-card
                title="Total Member"
                :value="number_format($totalMember)"
                subtitle="Member terdaftar"
                variant="member"
                icon="heroicon-o-user-group"
                icon-class="bg-amber-50 text-amber-600"
                :trend="$deltaMember !== null ? ($deltaMember >= 0 ? '+' : '') . $deltaMember . '%' : null"
            />
        @endif

        @if ($canKpiHutang)
            <x-kpi-card
                title="Piutang Supplier"
                :value="'Rp ' . number_format($totalHutangSupplier, 0, ',', '.')"
                subtitle="Total hutang supplier"
                icon="heroicon-o-building-office-2"
                icon-class="bg-sky-50 text-sky-600"
            />
        @endif

        @if ($canKpiPiutang)
            <x-kpi-card
                title="Piutang Member"
                :value="'Rp ' . number_format($totalPiutangMember, 0, ',', '.')"
                subtitle="Total piutang member"
                icon="heroicon-o-credit-card"
                icon-class="bg-rose-50 text-rose-600"
            />
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 3. GRAFIK + BUSINESS HEALTH + ACTION CENTER                  --}}
    {{-- ============================================================ --}}
    @if ($canGrafikOmzet || $canKondisiBisnis || $showActionCol)
    <div class="main-dashboard-grid grid grid-cols-1 xl:grid-cols-12 gap-3 items-stretch">
        @if ($canGrafikOmzet)
            <div class="main-graph-card {{ ($canKondisiBisnis && $showActionCol) ? 'xl:col-span-5' : (($canKondisiBisnis || $showActionCol) ? 'xl:col-span-7' : 'xl:col-span-12') }}">
                <div class="dashboard-card p-4 h-full">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Grafik Penjualan dan Omzet</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tren penjualan dan omzet apotek berdasarkan periode yang dipilih.</p>
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
                            @if ($canGrafikProfit)
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <span class="text-gray-600">Gross Profit</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="relative w-full" style="min-height: 280px; max-height: 340px;">
                        <canvas id="salesPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        @if ($canKondisiBisnis)
            <div class="main-health-card {{ ($canGrafikOmzet && $showActionCol) ? 'xl:col-span-4' : ((!$canGrafikOmzet && $showActionCol) ? 'xl:col-span-7' : (($canGrafikOmzet && !$showActionCol) ? 'xl:col-span-7' : 'xl:col-span-12')) }}">
        @php
            // =========================
            // BUSINESS HEALTH
            // =========================

            // Total kondisi stok
            $totalStokHealth = max(
                1,
                (int) ($totalKondisiStok ?? (
                    $stokAmanCount + $stokMenipisCount + $stokHabisCount
                ))
            );

            // Persentase stok aman
            $stokSehatPersen = min(
                100,
                max(0, round(($stokAmanCount / $totalStokHealth) * 100))
            );

            // Status kesehatan stok
            $healthStatus = $stokSehatPersen >= 80
                ? 'Sehat'
                : ($stokSehatPersen >= 60 ? 'Perlu Dipantau' : 'Perlu Perhatian');

            // Gross Profit dari data grafik yang sudah dihitung Controller
            $grossProfitHealth = array_sum($chartProfit ?? []);

            // Margin
            $marginHealth = $omzetBersih > 0
                ? ($grossProfitHealth / $omzetBersih) * 100
                : 0;
        @endphp

        <div class="dashboard-card h-full overflow-hidden relative p-4 bg-white">

            {{-- Header --}}
            <div class="relative flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-heroicon-o-heart class="w-5 h-5" />
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900">
                            Kondisi Bisnis
                        </h3>
                        <p class="text-[9px] text-gray-400 mt-0.5">
                            Kondisi operasional saat ini
                        </p>
                    </div>
                </div>

                {{-- LIVE --}}
                <div class="flex items-center gap-1.5 text-[8px] font-extrabold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full uppercase tracking-wide">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    Live
                </div>
            </div>

            {{-- =========================================================
                ISI BUSINESS HEALTH
            ========================================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-[125px_1fr] gap-4">

                {{-- LEFT : HEALTH SCORE --}}
                <div class="flex flex-col items-center justify-center border-r border-gray-100 pr-4">

                    {{-- Circle Percentage --}}
                    <div class="relative w-[105px] h-[105px]">
                        <div
                            class="absolute inset-0 rounded-full"
                            style="
                                background: conic-gradient(
                                    #10b981 {{ $stokSehatPersen }}%,
                                    #e5e7eb 0
                                );
                                transform: rotate(-90deg);
                            "
                        ></div>

                        <div class="absolute inset-[7px] rounded-full bg-white flex flex-col items-center justify-center">
                            <span class="text-[27px] font-black text-gray-900 leading-none">
                                {{ $stokSehatPersen }}%
                            </span>

                            <span class="text-[8px] font-bold text-gray-400 mt-1">
                                STOK AMAN
                            </span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mt-3 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-extrabold">
                        ♥ {{ $healthStatus }}
                    </div>
                </div>


                {{-- RIGHT : BUSINESS METRICS --}}
                <div class="space-y-1.5">

                    @if ($canKpiOmzetBersih || $canGrafikOmzet)
                        {{-- Penjualan --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-banknotes class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Penjualan
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($omzetBersih, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] text-right text-[9px] font-bold flex items-center justify-end gap-0.5
                                {{ ($deltaOmzetBersih ?? 0) >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                @if (($deltaOmzetBersih ?? 0) >= 0)
                                    <x-heroicon-m-arrow-trending-up class="w-3 h-3 shrink-0" />
                                @else
                                    <x-heroicon-m-arrow-trending-down class="w-3 h-3 shrink-0" />
                                @endif
                                <span>{{ $deltaOmzetBersih !== null ? number_format(abs($deltaOmzetBersih), 1, ',', '.') . '%' : '—' }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($canGrafikProfit)
                        {{-- Gross Profit --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-chart-bar class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Gross Profit
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($grossProfitHealth, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] text-right text-[9px] font-bold flex items-center justify-end gap-0.5
                                {{ ($deltaGrossProfit ?? 0) >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                @if (($deltaGrossProfit ?? 0) >= 0)
                                    <x-heroicon-m-arrow-trending-up class="w-3 h-3 shrink-0" />
                                @else
                                    <x-heroicon-m-arrow-trending-down class="w-3 h-3 shrink-0" />
                                @endif
                                <span>{{ $deltaGrossProfit !== null ? number_format(abs($deltaGrossProfit), 1, ',', '.') . '%' : '—' }}</span>
                            </div>
                        </div>

                        {{-- Margin --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                <span class="text-sm font-black">%</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Margin
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                {{ number_format($marginHealth, 1, ',', '.') }}%
                            </div>

                            <div class="w-[42px] text-right text-[9px] font-bold text-emerald-500">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canKpiHutang)
                        {{-- Hutang Supplier --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-building-office-2 class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Hutang Supplier
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($totalHutangSupplier, 0, ',', '.') }}
                            </div>

                            <div class="w-[42px] text-right text-[9px] font-bold text-emerald-500">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canKpiPiutang)
                        {{-- Piutang Member --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-users class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Piutang Member
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($totalPiutangMember, 0, ',', '.') }}
                            </div>

                            <div class="w-[42px] text-right text-[9px] font-bold text-rose-500">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canNilaiPersediaan)
                        {{-- Nilai Persediaan --}}
                        <div class="flex items-center gap-2.5 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                                <x-heroicon-o-cube class="w-4 h-4" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold text-gray-600">
                                    Nilai Persediaan
                                </div>
                            </div>

                            <div class="text-[10px] font-extrabold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}
                            </div>

                            <div class="w-[42px] text-right text-[9px] font-bold text-emerald-500">
                                —
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
        @endif

        @if ($showActionCol)
            <div class="main-action-column {{ ($canGrafikOmzet && $canKondisiBisnis) ? 'xl:col-span-3' : (($canGrafikOmzet || $canKondisiBisnis) ? 'xl:col-span-5' : 'xl:col-span-12') }} space-y-3">
                @if ($hasAlerts)
                    <div class="dashboard-card p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-gray-900">Tindak Lanjut</h3>
                        </div>
                        <div class="space-y-2">
                            @php
                                $acakWarna = [
                                    'critical' => ['bg-rose-100 text-rose-700', 'text-rose-400', 'critical'],
                                    'attention' => ['bg-amber-100 text-amber-700', 'text-amber-400', 'attention'],
                                    'monitoring' => ['bg-sky-100 text-sky-700', 'text-sky-400', 'monitoring'],
                                    'overdue' => ['bg-rose-100 text-rose-700', 'text-rose-400', 'overdue'],
                                    'piutang' => ['bg-purple-100 text-purple-700', 'text-purple-400', 'piutang'],
                                ];
                                $actionCenterLabels = [
                                    'critical' => 'Kritis',
                                    'attention' => 'Perlu Perhatian',
                                    'monitoring' => 'Dalam Pemantauan',
                                    'overdue' => 'Terlambat',
                                    'piutang' => 'Piutang',
                                ];
                            @endphp
                            @foreach ($actionCenter as $kunci => $item)
                                @php [$chip, $ikon, $kelas] = $acakWarna[$kunci]; @endphp
                                <a href="{{ $item['route'] }}" class="action-alert-card {{ $kelas }} block !p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="badge-chip {{ $chip }} inline-flex items-center gap-1.5">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $kunci === 'critical' || $kunci === 'overdue' ? 'bg-rose-400' : ($kunci === 'attention' ? 'bg-amber-400' : ($kunci === 'piutang' ? 'bg-purple-400' : 'bg-sky-400')) }}"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $kunci === 'critical' || $kunci === 'overdue' ? 'bg-rose-500' : ($kunci === 'attention' ? 'bg-amber-500' : ($kunci === 'piutang' ? 'bg-purple-500' : 'bg-sky-500')) }}"></span>
                                                </span>
                                                <span>{{ $actionCenterLabels[$kunci] ?? ($item['title'] ?? ucfirst($kunci)) }}</span>
                                            </span>
                                            <div class="text-xs font-bold text-gray-700 mt-1">{{ $item['count'] }} · {{ $item['label'] }}</div>
                                        </div>
                                        <x-heroicon-o-chevron-right class="w-4 h-4 {{ $ikon }} action-arrow transition-transform duration-200" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($canTombolKasir)
                    <div class="cashier-card dashboard-card p-3 bg-blue-700 text-white">
                        <a href="{{ route('penjualan.create') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-white text-blue-700 font-bold text-xs hover:bg-blue-50 transition-colors">
                            <x-heroicon-o-calculator class="w-4 h-4" />
                            Buka Kasir
                            <x-heroicon-o-chevron-right class="w-4 h-4 ml-auto" />
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 4. INVENTORY + EXPIRY + BARANG YANG HARUS DIBELI             --}}
    {{-- ============================================================ --}}
    {{-- ============================================================ --}}
    {{-- 4. INVENTORY + EXPIRY + BARANG YANG HARUS DIBELI             --}}
    {{-- ============================================================ --}}
    @if ($canPemantauanStok || $canBarangBeli)
    <div class="inventory-row grid grid-cols-1 {{ ($canPemantauanStok && $canBarangBeli) ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-5 items-stretch">
        @if ($canPemantauanStok)
        {{-- ============================================================ --}}
        {{-- KOLOM KIRI: INVENTORY CONTROL CENTER + KONDISI STOK + EXPIRY HEALTH --}}
        {{-- ============================================================ --}}
        <div class="dashboard-card inventory-card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                        <x-heroicon-o-archive-box class="w-4 h-4" />
                    </span>
                    Pemantauan Stok
                </h3>
                <a href="{{ route('laporan.stok') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                    Lihat Stok &rarr;
                </a>
            </div>

            <div class="flex flex-col gap-3 mt-3.5 flex-1 justify-between">
                {{-- 1. Baris Metrik: Total Stok & Nilai Persediaan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    {{-- Total Stok --}}
                    <div class="p-3 rounded-xl border border-gray-200 bg-white shadow-2xs flex items-center gap-3 {{ $canNilaiPersediaan ? '' : 'sm:col-span-2' }}">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shrink-0">
                            <x-heroicon-o-cube class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-gray-500 block">Total Stok</span>
                            <div class="text-xl font-black text-gray-900 leading-tight mt-0.5 tracking-tight"
                                 data-countup="true"
                                 data-target="{{ (float) preg_replace('/[^0-9]/', '', (string)$totalStok) }}"
                                 data-suffix=" Unit">
                                {{ number_format($totalStok) }} <span class="text-xs font-medium text-gray-400">Unit</span>
                            </div>
                            <span class="text-[11px] text-gray-400 font-medium">Fisik persediaan</span>
                        </div>
                    </div>

                    @if ($canNilaiPersediaan)
                        {{-- Nilai Persediaan --}}
                        <div class="p-3 rounded-xl border border-gray-200 bg-white shadow-2xs flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                                <x-heroicon-o-circle-stack class="w-6 h-6" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-semibold text-gray-500 block">Nilai Persediaan</span>

                                <div class="text-lg font-black text-gray-900 leading-tight mt-0.5 whitespace-nowrap tracking-tight"
                                     data-countup="true"
                                     data-target="{{ (float) preg_replace('/[^0-9]/', '', (string)$nilaiPersediaan) }}"
                                     data-prefix="Rp ">
                                    Rp{{ number_format($nilaiPersediaan, 0, ',', '.') }}
                                </div>

                                <span class="text-[11px] text-gray-400 font-medium">Estimasi valuasi</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 2. Kondisi Stok dengan Doughnut Chart & Legend --}}
                @php
                    $totalKondisiStok = $stokHabisCount + $stokMenipisCount + $stokAmanCount;
                    $totalK = max(1, $totalKondisiStok);
                    $pctHabis = ($totalKondisiStok > 0) ? number_format(($stokHabisCount / $totalK) * 100, 1, ',', '.') : '0';
                    $pctMenipis = ($totalKondisiStok > 0) ? number_format(($stokMenipisCount / $totalK) * 100, 1, ',', '.') : '0';
                    $pctAmanStok = ($totalKondisiStok > 0) ? number_format(($stokAmanCount / $totalK) * 100, 1, ',', '.') : '0';
                @endphp
                <div class="p-3 rounded-xl border border-gray-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Kondisi Stok
                        </span>
                        <span class="text-[11px] text-gray-400 font-medium">{{ number_format($totalKondisiStok) }} Total Item</span>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Doughnut Chart Kondisi Stok with Center Text --}}
                        <div class="relative w-[88px] h-[88px] shrink-0 flex items-center justify-center">
                            <canvas id="kondisiStokChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[8px] text-gray-400 font-medium uppercase tracking-wider">Total</span>
                                <span class="text-xs font-black text-gray-900 leading-tight">{{ number_format($totalKondisiStok) }}</span>
                                <span class="text-[8px] text-gray-400 font-medium">Item</span>
                            </div>
                        </div>

                        {{-- Legend List --}}
                        <div class="flex-1 space-y-1 w-full min-w-0">
                            <a href="{{ route('laporan.stok', ['status_stok' => 'habis']) }}"
                               class="flex items-center justify-between text-xs hover:bg-rose-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-rose-700">Habis</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($stokHabisCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctHabis }}%</span>
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}"
                               class="flex items-center justify-between text-xs hover:bg-amber-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-amber-700">Menipis</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($stokMenipisCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctMenipis }}%</span>
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'aman']) }}"
                               class="flex items-center justify-between text-xs hover:bg-emerald-50/50 p-1 py-1.5 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-emerald-700">Aman</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($stokAmanCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctAmanStok }}%</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 3. Expiry Health dengan Doughnut Chart & Legend (Pindah ke bawah Kondisi Stok) --}}
                @php
                    $totalB = max(1, $totalBatchesCount);
                    $pctAman = ($totalBatchesCount > 0) ? number_format(($expiryAman / $totalB) * 100, 1, ',', '.') : '0';
                    $pctWarning = ($totalBatchesCount > 0) ? number_format(($expiryWarning / $totalB) * 100, 1, ',', '.') : '0';
                    $pctCritical = ($totalBatchesCount > 0) ? number_format(($expiryCritical / $totalB) * 100, 1, ',', '.') : '0';
                    $pctKadaluarsa = ($totalBatchesCount > 0) ? number_format(($expiryKadaluarsa / $totalB) * 100, 1, ',', '.') : '0';
                @endphp
                <div class="p-3 rounded-xl border border-gray-200 bg-white shadow-2xs">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Expiry Date
                        </span>
                        <a href="{{ route('laporan.stok') }}" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-0.5">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Donut Chart Expiry Health with Center Text --}}
                        <div class="relative w-[88px] h-[88px] shrink-0 flex items-center justify-center">
                            <canvas id="expiryHealthChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[8px] text-gray-400 font-medium uppercase tracking-wider">Total</span>
                                <span class="text-xs font-black text-gray-900 leading-tight">{{ number_format($totalBatchesCount) }}</span>
                                <span class="text-[8px] text-gray-400 font-medium">Batch</span>
                            </div>
                        </div>

                        {{-- Legend List dengan Angka dan Persentase --}}
                        <div class="flex-1 space-y-1 w-full min-w-0">
                            <a href="{{ route('laporan.stok', ['status_expired' => 'normal']) }}"
                               class="flex items-center justify-between text-xs hover:bg-emerald-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-emerald-700">Aman (>90 hari)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($expiryAman) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctAman }}%</span>
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => '3_bulan']) }}"
                               class="flex items-center justify-between text-xs hover:bg-yellow-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-yellow-700">Peringatan (30–90 hari)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($expiryWarning) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctWarning }}%</span>
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => '1_bulan']) }}"
                               class="flex items-center justify-between text-xs hover:bg-orange-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-orange-700">Kritis (&lt;30 hari)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($expiryCritical) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctCritical }}%</span>
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => 'kadaluarsa']) }}"
                               class="flex items-center justify-between text-xs hover:bg-rose-50/50 p-1 rounded transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                    <span class="font-medium text-gray-700 group-hover:text-rose-700">Kadaluwarsa</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-900">{{ number_format($expiryKadaluarsa) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctKadaluarsa }}%</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if ($canBarangBeli)
        {{-- ============================================================ --}}
        {{-- KOLOM KANAN: BARANG YANG HARUS DIBELI                        --}}
        {{-- ============================================================ --}}
        <div class="dashboard-card inventory-card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-shopping-bag class="w-5 h-5 text-amber-600" />
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            Barang yang Harus Dibeli
                            @if(count($barangHarusDibeli) > 0)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-semibold">{{ count($barangHarusDibeli) }}</span>
                            @endif
                        </h3>
                    </div>
                </div>
                <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat Selengkapnya &rarr;
                </a>
            </div>

            <div class="w-full flex-1 overflow-y-auto max-h-[480px] rounded-lg border border-gray-100 shadow-2xs">
                <table class="table-compact w-full text-left table-fixed">
                    <thead>
                        <tr>
                            <th class="w-[8%] text-center sticky-header">No</th>
                            <th class="w-[36%] sticky-header wrap-cell">Nama Barang</th>
                            <th class="w-[22%] sticky-header wrap-cell">Kategori</th>
                            <th class="w-[17%] text-center sticky-header leading-tight">
                                Stok<br>Saat Ini
                            </th>
                            <th class="w-[17%] text-center sticky-header leading-tight">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($barangHarusDibeli as $index => $item)
                            <tr class="hover:bg-gray-50/75 transition-colors">
                                <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td class="font-bold text-gray-900 wrap-cell leading-snug">
                                    {{ $item['barang']->nama }}
                                </td>
                                <td class="text-gray-600 wrap-cell leading-snug">
                                    {{ $item['barang']->kategori->nama ?? '-' }}
                                </td>
                                <td class="text-center font-bold text-gray-800">{{ $item['stok'] }}</td>
                                <td class="text-center">
                                    @if($item['status'] === 'HABIS')
                                        <span class="badge-chip bg-rose-100 text-rose-700 whitespace-nowrap">Habis</span>
                                    @else
                                        <span class="badge-chip bg-amber-100 text-amber-700 whitespace-nowrap">Segera Beli</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-gray-400">
                                    Seluruh stok obat berada dalam kondisi aman di atas batas minimum.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 5. 10 OBAT TERLARIS & 10 OBAT PALING SEDIKIT TERJUAL         --}}
    {{-- ============================================================ --}}
{{-- 8. 10 OBAT TERLARIS & 10 OBAT PALING SEDIKIT TERJUAL         --}}
    {{-- ============================================================ --}}
    @if ($canObatTerlaris || $canObatTerendah)
    <div class="grid grid-cols-1 {{ ($canObatTerlaris && $canObatTerendah) ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-5">
        @if ($canObatTerlaris)
        {{-- 10 Obat Terlaris --}}
        <div class="dashboard-card p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-trophy class="w-5 h-5 text-amber-500" />
                    </div>

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
                            @if ($canOmzetObat)
                                <th class="text-right">Omzet</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topSellingMedicines as $index => $medicine)
                            <tr>
                                <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                <td class="font-bold text-gray-900">{{ $medicine->nama }}</td>
                                <td class="text-gray-600">{{ $medicine->kategori ?? '-' }}</td>
                                <td class="text-center font-bold text-blue-600">{{ number_format($medicine->total_terjual) }} unit</td>
                                @if ($canOmzetObat)
                                    <td class="text-right font-semibold text-gray-800">Rp {{ number_format($medicine->total_omzet, 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canOmzetObat ? 5 : 4 }}" class="py-6 text-center text-xs text-gray-400">
                                    Belum ada transaksi penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if ($canObatTerendah)
        {{-- 10 Obat Paling Sedikit Terjual --}}
        <div class="dashboard-card p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-rose-500" />
                    </div>

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
                            @if ($canOmzetObat)
                                <th class="text-right">Omzet</th>
                            @endif
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
                                @if ($canOmzetObat)
                                    <td class="text-right font-semibold text-gray-800">
                                        Rp {{ number_format($medicine->total_omzet, 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canOmzetObat ? 5 : 4 }}" class="py-6 text-center text-xs text-gray-400">
                                    Belum ada data obat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 9. KINERJA KARYAWAN & DOKTER PENYUMBANG RESEP               --}}
    {{-- ============================================================ --}}
    @if ($canKinerja || $canDokterResep)
    <div class="grid grid-cols-1 {{ ($canKinerja && $canDokterResep) ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-5 mt-5">
        @if ($canKinerja)
            {{-- 9.1 KINERJA KARYAWAN --}}
            <div class="dashboard-card p-5 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-200 mb-3 gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0">
                                <x-heroicon-o-user-group class="w-5 h-5 text-indigo-600" />
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-gray-900">Kinerja Karyawan</h3>
                                <p class="text-xs text-gray-500">Performa transaksi dan total penjualan karyawan pada periode {{ $periodeLabel }}.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($kinerjaKaryawan->count() > 5)
                                <button type="button" id="btn-toggle-kinerja" class="btn-secondary text-xs py-1 px-3">
                                    Lihat Semua ({{ $kinerjaKaryawan->count() }})
                                </button>
                            @endif
                            <a href="{{ route('user.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                Kelola User &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                        <table class="table-compact w-full text-left" id="table-kinerja-karyawan">
                            <thead>
                                <tr>
                                    <th class="w-12 text-center">NO</th>
                                    <th>NAMA KARYAWAN</th>
                                    <th class="text-right">PENJUALAN</th>
                                    <th class="text-center">TRANSAKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kinerjaKaryawan as $index => $karyawan)
                                    @php
                                        $target = isset($karyawan->target) && $karyawan->target > 0 ? (float) $karyawan->target : null;
                                        $penjualan = (float) $karyawan->total_penjualan;
                                        $persen = $target ? round(($penjualan / $target) * 100, 1) : null;
                                        $barWidth = $persen !== null ? min($persen, 100) : 0;
                                        $barColor = $persen >= 100 ? 'bg-emerald-500' : ($persen >= 75 ? 'bg-blue-600' : ($persen >= 50 ? 'bg-amber-500' : 'bg-rose-500'));
                                        $isCurrentUser = auth()->id() === $karyawan->id;
                                        $isExtra = $index >= 5;
                                    @endphp
                                    <tr class="{{ $isExtra ? 'kinerja-extra-row hidden' : '' }} {{ $isCurrentUser ? 'bg-blue-50/40' : '' }}">
                                        <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900">{{ $karyawan->name }}</span>
                                                @if ($isCurrentUser)
                                                    <span class="badge-info text-[10px] py-0 px-1.5 font-semibold">ANDA</span>
                                                @endif
                                                <span class="text-[11px] text-gray-400 capitalize">({{ $karyawan->role }})</span>
                                                @if (!$karyawan->aktif)
                                                    <span class="badge-danger text-[10px] py-0 px-1">Nonaktif</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-right font-semibold text-gray-800">
                                            Rp {{ number_format($penjualan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center font-bold text-blue-600">
                                            {{ number_format($karyawan->total_transaksi) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-xs text-gray-400">
                                            Belum ada data karyawan terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if ($canDokterResep)
        {{-- 9.2 DOKTER PENYUMBANG RESEP TERBANYAK --}}
        <div class="dashboard-card p-5 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-200 mb-3 gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-user class="w-5 h-5 text-indigo-600" />
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-gray-900">Dokter Penyumbang Resep Terbanyak</h3>
                            <p class="text-xs text-gray-500">Berdasarkan total resep yang disumbangkan ke apotek pada bulan ini.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModalDokterResep()" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 cursor-pointer">
                            Lihat Semua &rarr;
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                    <table class="table-compact w-full text-left" id="table-dokter-resep">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">NO</th>
                                <th>ID DOKTER</th>
                                <th>NAMA DOKTER</th>
                                <th class="text-center">TOTAL RESEP</th>
                                <th class="text-right">TOTAL TRANSAKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topDokterResep as $index => $dokter)
                                <tr>
                                    <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="font-bold text-gray-900 font-mono">{{ $dokter->id_dokter }}</td>
                                    <td>
                                        <div class="font-bold text-gray-900 leading-snug">{{ $dokter->nama_dokter ?: '—' }}</div>
                                        @if (!empty($dokter->alamat_lembaga))
                                            <div class="text-[11px] text-gray-400 capitalize leading-tight">{{ $dokter->alamat_lembaga }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-gray-800">
                                        {{ number_format($dokter->total_resep) }} resep
                                    </td>
                                    <td class="text-right font-semibold text-gray-800">
                                        Rp {{ number_format($dokter->total_transaksi, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-xs text-gray-400">
                                        Belum ada riwayat transaksi resep dokter pada bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Note Banner at Bottom --}}
            <div class="mt-4 p-3 rounded-lg bg-blue-50/70 border border-blue-100 flex items-center gap-2 text-xs text-blue-700">
                <x-heroicon-s-information-circle class="w-4 h-4 text-blue-500 shrink-0" />
                <span>Total resep dihitung berdasarkan riwayat transaksi penjualan yang berasal dari resep dokter.</span>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

{{-- ============================================================ --}}
{{-- MODAL LIHAT SEMUA: DOKTER PENYUMBANG RESEP                   --}}
{{-- ============================================================ --}}
<div id="modal-dokter-resep" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                    <x-heroicon-o-user class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Dokter Penyumbang Resep Bulan Ini</h3>
                    <p class="text-xs text-gray-500">Daftar lengkap dokter dengan kontribusi resep terdaftar pada bulan ini.</p>
                </div>
            </div>
            <button type="button" onclick="closeModalDokterResep()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <!-- Modal Search Input -->
        <div class="p-4 border-b border-gray-100 bg-white">
            <div class="relative">
                <input
                    type="text"
                    id="searchDokterModal"
                    placeholder="Cari berdasarkan ID dokter, nama dokter, atau lembaga..."
                    class="input-base pl-9 text-xs w-full"
                    onkeyup="filterModalDokterTable(this.value)"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </div>
            </div>
        </div>

        <!-- Modal Body Table -->
        <div class="p-4 overflow-y-auto flex-1">
            <table class="table-compact w-full text-left" id="table-modal-dokter">
                <thead>
                    <tr>
                        <th class="w-12 text-center">NO</th>
                        <th class="w-32">ID DOKTER</th>
                        <th>NAMA DOKTER</th>
                        <th class="text-center w-28">TOTAL RESEP</th>
                        <th class="text-right w-36">TOTAL TRANSAKSI</th>
                    </tr>
                </thead>
                <tbody id="tbody-modal-dokter">
                    @forelse ($semuaDokterResep as $index => $dokter)
                        <tr class="modal-dokter-row">
                            <td class="text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                            <td class="font-bold text-gray-900 font-mono">{{ $dokter->id_dokter }}</td>
                            <td>
                                <div class="font-bold text-gray-900 leading-snug">{{ $dokter->nama_dokter ?: '—' }}</div>
                                @if (!empty($dokter->alamat_lembaga))
                                    <div class="text-[11px] text-gray-400 capitalize leading-tight">{{ $dokter->alamat_lembaga }}</div>
                                @endif
                            </td>
                            <td class="text-center font-bold text-gray-800">
                                {{ number_format($dokter->total_resep) }} resep
                            </td>
                            <td class="text-right font-semibold text-gray-800">
                                Rp {{ number_format($dokter->total_transaksi, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-modal-dokter">
                            <td colspan="5" class="py-8 text-center text-xs text-gray-400">
                                Belum ada data resep dokter pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="no-search-match-dokter" class="hidden">
                        <td colspan="5" class="py-8 text-center text-xs text-gray-400">
                            Tidak ada dokter yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-gray-100 bg-slate-50/70 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Total dokter terdaftar bulan ini: <span class="font-bold text-gray-800">{{ $semuaDokterResep->count() }}</span> dokter
            </div>
            <button type="button" onclick="closeModalDokterResep()" class="btn-secondary text-xs py-1.5 px-4 cursor-pointer">
                Tutup
            </button>
        </div>
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
                    @if ($canGrafikProfit)
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
                    @endif
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1100,
                    easing: 'easeOutQuart',
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.94)',
                        borderColor: 'rgba(255, 255, 255, 0.12)',
                        borderWidth: 1,
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: {
                            weight: 'bold',
                            size: 12,
                        },
                        bodyFont: {
                            size: 11,
                        },
                        boxPadding: 4,
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

        // ==========================================
        // DOUGHNUT CHART: KONDISI STOK
        // ==========================================
        const ctxKondisi = document.getElementById('kondisiStokChart');
        if (ctxKondisi) {
            const stokHabis = {{ (int) $stokHabisCount }};
            const stokMenipis = {{ (int) $stokMenipisCount }};
            const stokAman = {{ (int) $stokAmanCount }};
            const totalKondisi = stokHabis + stokMenipis + stokAman;

            new Chart(ctxKondisi, {
                type: 'doughnut',
                data: {
                    labels: ['Habis', 'Menipis', 'Aman'],
                    datasets: [{
                        data: totalKondisi > 0 ? [stokHabis, stokMenipis, stokAman] : [0, 0, 1],
                        backgroundColor: totalKondisi > 0 ? ['#ef4444', '#f59e0b', '#10b981'] : ['#e2e8f0', '#e2e8f0', '#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeOutCubic'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: totalKondisi > 0,
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            borderColor: 'rgba(255, 255, 255, 0.12)',
                            borderWidth: 1,
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 10 },
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const pct = totalKondisi > 0 ? ((val / totalKondisi) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + val + ' item (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ==========================================
        // DOUGHNUT CHART: EXPIRY HEALTH
        // ==========================================
        const ctxExpiry = document.getElementById('expiryHealthChart');
        if (ctxExpiry) {
            const expAman = {{ (int) $expiryAman }};
            const expWarning = {{ (int) $expiryWarning }};
            const expCritical = {{ (int) $expiryCritical }};
            const expKadaluarsa = {{ (int) $expiryKadaluarsa }};
            const totalExpiry = expAman + expWarning + expCritical + expKadaluarsa;

            new Chart(ctxExpiry, {
                type: 'doughnut',
                data: {
                    labels: ['Aman (>90 hari)', 'Peringatan (30–90 hari)', 'Kritis (<30 hari)', 'Kadaluwarsa'],
                    datasets: [{
                        data: totalExpiry > 0 ? [expAman, expWarning, expCritical, expKadaluarsa] : [0, 0, 0, 1],
                        backgroundColor: totalExpiry > 0 ? ['#10b981', '#eab308', '#f97316', '#ef4444'] : ['#e2e8f0', '#e2e8f0', '#e2e8f0', '#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1100,
                        easing: 'easeOutCubic'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: totalExpiry > 0,
                            backgroundColor: 'rgba(15, 23, 42, 0.94)',
                            borderColor: 'rgba(255, 255, 255, 0.12)',
                            borderWidth: 1,
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 10,
                            cornerRadius: 8,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const pct = totalExpiry > 0 ? ((val / totalExpiry) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + val + ' batch (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }


        // ==========================================
        // TOGGLE LIHAT SEMUA: KINERJA KARYAWAN
        // ==========================================
        const btnToggleKinerja = document.getElementById('btn-toggle-kinerja');
        if (btnToggleKinerja) {
            let expanded = false;
            const originalText = btnToggleKinerja.textContent.trim();
            btnToggleKinerja.addEventListener('click', function () {
                expanded = !expanded;
                document.querySelectorAll('.kinerja-extra-row').forEach(row => {
                    row.classList.toggle('hidden', !expanded);
                });
                btnToggleKinerja.textContent = expanded ? 'Sembunyikan' : originalText;
            });
        }

        // ==========================================
        // MODAL LIHAT SEMUA: DOKTER PENYUMBANG RESEP
        // ==========================================
        const modalDokter = document.getElementById('modal-dokter-resep');
        if (modalDokter) {
            modalDokter.addEventListener('click', function(e) {
                if (e.target === modalDokter) closeModalDokterResep();
            });
        }
    });

    function openModalDokterResep() {
        const modal = document.getElementById('modal-dokter-resep');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            const searchInput = document.getElementById('searchDokterModal');
            if (searchInput) {
                searchInput.value = '';
                filterModalDokterTable('');
                setTimeout(() => searchInput.focus(), 100);
            }
        }
    }

    function closeModalDokterResep() {
        const modal = document.getElementById('modal-dokter-resep');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function filterModalDokterTable(keyword) {
        const term = keyword.toLowerCase().trim();
        const rows = document.querySelectorAll('.modal-dokter-row');
        let visibleCount = 0;
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const match = text.includes(term);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        const noMatchRow = document.getElementById('no-search-match-dokter');
        if (noMatchRow) {
            noMatchRow.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModalDokterResep();
    });
</script>
@endsection
