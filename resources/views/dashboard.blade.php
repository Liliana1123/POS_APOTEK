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
                variant="hutang"
                icon="heroicon-o-building-office-2"
                icon-class="bg-sky-50 text-sky-600"
            />
        @endif

        @if ($canKpiPiutang)
            <x-kpi-card
                title="Piutang Member"
                :value="'Rp ' . number_format($totalPiutangMember, 0, ',', '.')"
                subtitle="Total piutang member"
                variant="piutang"
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
            @php
                $validNet = array_values(array_filter($chartNet ?? [], fn($v) => (float)$v > 0));
                $peakVal = !empty($chartNet) ? max($chartNet) : 0;
                $peakIdx = $peakVal > 0 ? array_search($peakVal, $chartNet) : false;
                $peakLabel = ($peakIdx !== false && isset($chartLabels[$peakIdx])) ? $chartLabels[$peakIdx] : null;
                $avgDaily = count($chartNet ?? []) > 0 ? array_sum($chartNet) / count($chartNet) : 0;
            @endphp
            <div class="main-graph-card {{ ($canKondisiBisnis && $showActionCol) ? 'xl:col-span-5' : (($canKondisiBisnis || $showActionCol) ? 'xl:col-span-7' : 'xl:col-span-12') }}">
                <div class="dashboard-card p-4 h-full flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 pb-3 border-b border-gray-100">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Grafik Penjualan dan Omzet</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Tren penjualan dan omzet apotek berdasarkan periode yang dipilih.</p>
                            </div>
                            
                            {{-- Interactive Legend (Netral Tanpa Warna Cerah) --}}
                            <div class="flex items-center gap-1.5 text-xs font-medium flex-wrap" id="salesChartLegend" title="Klik tombol untuk menyembunyikan / menampilkan garis">
                                <button type="button" data-dataset-index="0" class="chart-legend-btn px-2.5 py-1 rounded-md border border-gray-200 bg-white text-gray-700 transition-all hover:bg-gray-100 hover:text-gray-900 cursor-pointer text-xs font-semibold select-none shadow-2xs">
                                    Omzet Kotor
                                </button>
                                <button type="button" data-dataset-index="1" class="chart-legend-btn px-2.5 py-1 rounded-md border border-gray-200 bg-white text-gray-700 transition-all hover:bg-gray-100 hover:text-gray-900 cursor-pointer text-xs font-semibold select-none shadow-2xs">
                                    Omzet Bersih
                                </button>
                                @if ($canGrafikProfit)
                                    <button type="button" data-dataset-index="2" class="chart-legend-btn px-2.5 py-1 rounded-md border border-gray-200 bg-white text-gray-700 transition-all hover:bg-gray-100 hover:text-gray-900 cursor-pointer text-xs font-semibold select-none shadow-2xs">
                                        Gross Profit
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Quick Insights Banner (Netral Tanpa Warna Cerah & Tanpa Emoji) --}}
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @if ($peakVal > 0 && $peakLabel)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-50 text-gray-700 border border-gray-200 shadow-2xs">
                                    <span class="text-gray-500 font-bold uppercase tracking-wider text-[10px]">Puncak:</span>
                                    <span>{{ $peakLabel }} (Rp {{ number_format($peakVal, 0, ',', '.') }})</span>
                                </div>
                            @endif
                            @if ($avgDaily > 0)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-50 text-gray-700 border border-gray-200 shadow-2xs">
                                    <span class="text-gray-500 font-bold uppercase tracking-wider text-[10px]">Rata-rata:</span>
                                    <span>Rp {{ number_format($avgDaily, 0, ',', '.') }}/hari</span>
                                </div>
                            @endif
                            <div class="text-[10.5px] text-gray-400 ml-auto hidden sm:block italic">
                                *Klik tombol untuk filter garis grafik
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full flex-1" style="min-height: 270px; max-height: 330px;">
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

            // Status kesehatan stok & palet warna dinamis
            if ($stokSehatPersen >= 80) {
                $healthStatus = 'Sehat';
                $healthHex = '#10b981';
                $healthHexEnd = '#059669';
                $healthBadgeCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                $healthDotCls = 'bg-emerald-500';
                $healthHeaderIconCls = 'bg-emerald-50 text-emerald-600 border-emerald-100';
            } elseif ($stokSehatPersen >= 60) {
                $healthStatus = 'Perlu Dipantau';
                $healthHex = '#f59e0b';
                $healthHexEnd = '#d97706';
                $healthBadgeCls = 'bg-amber-50 text-amber-700 border-amber-200';
                $healthDotCls = 'bg-amber-500';
                $healthHeaderIconCls = 'bg-amber-50 text-amber-600 border-amber-100';
            } else {
                $healthStatus = 'Perlu Perhatian';
                $healthHex = '#f43f5e';
                $healthHexEnd = '#e11d48';
                $healthBadgeCls = 'bg-rose-50 text-rose-700 border-rose-200';
                $healthDotCls = 'bg-rose-500';
                $healthHeaderIconCls = 'bg-rose-50 text-rose-600 border-rose-100';
            }

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
                    <div class="w-9 h-9 rounded-xl {{ $healthHeaderIconCls }} border flex items-center justify-center shrink-0 shadow-xs">
                        <x-heroicon-o-heart class="w-5 h-5" />
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">
                            Kondisi Bisnis
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Kondisi operasional saat ini
                        </p>
                    </div>
                </div>

                {{-- LIVE BADGE --}}
                <div class="flex items-center gap-1.5 text-[8.5px] font-bold text-gray-600 bg-gray-50 border border-gray-200 px-2.5 py-1 rounded-full uppercase tracking-wider">
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
            <div class="grid grid-cols-1 sm:grid-cols-[135px_1fr] gap-4 items-center">

                {{-- LEFT : HEALTH SCORE --}}
                <div class="flex flex-col items-center justify-center border-b sm:border-b-0 sm:border-r border-gray-100 pb-3 sm:pb-0 sm:pr-4">

                    {{-- SVG Circular Gauge --}}
                    <div class="relative w-[105px] h-[105px] flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 100 100">
                            <defs>
                                <linearGradient id="healthGaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="{{ $healthHex }}" />
                                    <stop offset="100%" stop-color="{{ $healthHexEnd }}" />
                                </linearGradient>
                            </defs>
                            <!-- Background Track -->
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                stroke="#f1f5f9"
                                stroke-width="8"
                                fill="none"
                            />
                            <!-- Progress Ring -->
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                stroke="url(#healthGaugeGrad)"
                                stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="251.327"
                                stroke-dashoffset="{{ round(251.327 * (1 - ($stokSehatPersen / 100)), 2) }}"
                                fill="none"
                                class="transition-all duration-1000 ease-out"
                            />
                        </svg>

                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[25px] font-black text-gray-900 leading-none tracking-tight">
                                {{ $stokSehatPersen }}%
                            </span>
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-wider mt-1">
                                Stok Aman
                            </span>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mt-2.5 px-3 py-1 rounded-full {{ $healthBadgeCls }} text-[10px] font-bold inline-flex items-center gap-1.5 border shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full {{ $healthDotCls }}"></span>
                        <span>{{ $healthStatus }}</span>
                    </div>

                    {{-- Mini Breakdown Stok --}}
                    <div class="mt-3 pt-2.5 border-t border-gray-100 w-full flex items-center justify-between text-[9px] px-1 font-semibold">
                        <div class="text-center" title="Stok Aman">
                            <span class="block font-bold text-emerald-600 text-[11px] leading-tight">{{ number_format($stokAmanCount ?? 0, 0, ',', '.') }}</span>
                            <span class="text-[8px] text-gray-400 uppercase">Aman</span>
                        </div>
                        <div class="h-4 w-px bg-gray-200"></div>
                        <div class="text-center" title="Stok Menipis">
                            <span class="block font-bold text-amber-600 text-[11px] leading-tight">{{ number_format($stokMenipisCount ?? 0, 0, ',', '.') }}</span>
                            <span class="text-[8px] text-gray-400 uppercase">Menipis</span>
                        </div>
                        <div class="h-4 w-px bg-gray-200"></div>
                        <div class="text-center" title="Stok Habis">
                            <span class="block font-bold text-rose-600 text-[11px] leading-tight">{{ number_format($stokHabisCount ?? 0, 0, ',', '.') }}</span>
                            <span class="text-[8px] text-gray-400 uppercase">Habis</span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT : BUSINESS METRICS --}}
                <div class="space-y-1">

                    @if ($canKpiOmzetBersih || $canGrafikOmzet)
                        {{-- Penjualan --}}
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 border border-blue-100/60 flex items-center justify-center shrink-0">
                                <x-heroicon-o-banknotes class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Penjualan
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                <span class="text-[9px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($omzetBersih, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[9px] font-bold flex items-center justify-end gap-0.5
                                {{ ($deltaOmzetBersih ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
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
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100/60 flex items-center justify-center shrink-0">
                                <x-heroicon-o-chart-bar class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Gross Profit
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                <span class="text-[9px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($grossProfitHealth, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[9px] font-bold flex items-center justify-end gap-0.5
                                {{ ($deltaGrossProfit ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                @if (($deltaGrossProfit ?? 0) >= 0)
                                    <x-heroicon-m-arrow-trending-up class="w-3 h-3 shrink-0" />
                                @else
                                    <x-heroicon-m-arrow-trending-down class="w-3 h-3 shrink-0" />
                                @endif
                                <span>{{ $deltaGrossProfit !== null ? number_format(abs($deltaGrossProfit), 1, ',', '.') . '%' : '—' }}</span>
                            </div>
                        </div>

                        {{-- Margin --}}
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 border border-amber-100/60 flex items-center justify-center shrink-0">
                                <span class="text-[11px] font-black leading-none">%</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Margin
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                {{ number_format($marginHealth, 1, ',', '.') }}%
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[10px] font-medium text-gray-300">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canKpiHutang)
                        {{-- Hutang Supplier --}}
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-100/60 flex items-center justify-center shrink-0">
                                <x-heroicon-o-building-office-2 class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Hutang Supplier
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                <span class="text-[9px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($totalHutangSupplier, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[10px] font-medium text-gray-300">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canKpiPiutang)
                        {{-- Piutang Member --}}
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 border border-purple-100/60 flex items-center justify-center shrink-0">
                                <x-heroicon-o-users class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Piutang Member
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                <span class="text-[9px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($totalPiutangMember, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[10px] font-medium text-gray-300">
                                —
                            </div>
                        </div>
                    @endif

                    @if ($canNilaiPersediaan)
                        {{-- Nilai Persediaan --}}
                        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg transition-colors hover:bg-gray-50/80 group">
                            <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 border border-teal-100/60 flex items-center justify-center shrink-0">
                                <x-heroicon-o-cube class="w-3.5 h-3.5" />
                            </div>

                            <div class="flex-1 min-w-0">
                                <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors truncate block">
                                    Nilai Persediaan
                                </span>
                            </div>

                            <div class="text-[11px] font-extrabold text-gray-900 whitespace-nowrap tracking-tight">
                                <span class="text-[9px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($nilaiPersediaan, 0, ',', '.') }}
                            </div>

                            <div class="w-[52px] shrink-0 text-right text-[10px] font-medium text-gray-300">
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
                    @php
                        $activeAlertsCount = collect($actionCenter)->filter(fn($item) => ($item['count'] ?? 0) > 0)->count();
                        $actionConfigs = [
                            'critical' => [
                                'label' => 'Kritis',
                                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'dot' => 'bg-rose-500',
                                'ping' => 'bg-rose-400',
                                'arrow' => 'text-rose-400 group-hover:text-rose-600',
                                'card' => 'border-gray-200 border-l-4 border-l-rose-500 hover:border-l-rose-600 hover:bg-rose-50/30 hover:shadow-md hover:shadow-rose-500/10',
                            ],
                            'attention' => [
                                'label' => 'Perlu Perhatian',
                                'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'dot' => 'bg-amber-500',
                                'ping' => 'bg-amber-400',
                                'arrow' => 'text-amber-400 group-hover:text-amber-600',
                                'card' => 'border-gray-200 border-l-4 border-l-amber-500 hover:border-l-amber-600 hover:bg-amber-50/30 hover:shadow-md hover:shadow-amber-500/10',
                            ],
                            'monitoring' => [
                                'label' => 'Dalam Pemantauan',
                                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'dot' => 'bg-sky-500',
                                'ping' => 'bg-sky-400',
                                'arrow' => 'text-sky-400 group-hover:text-sky-600',
                                'card' => 'border-gray-200 border-l-4 border-l-sky-500 hover:border-l-sky-600 hover:bg-sky-50/30 hover:shadow-md hover:shadow-sky-500/10',
                            ],
                            'overdue' => [
                                'label' => 'Terlambat',
                                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'dot' => 'bg-rose-500',
                                'ping' => 'bg-rose-400',
                                'arrow' => 'text-rose-400 group-hover:text-rose-600',
                                'card' => 'border-gray-200 border-l-4 border-l-rose-500 hover:border-l-rose-600 hover:bg-rose-50/30 hover:shadow-md hover:shadow-rose-500/10',
                            ],
                            'piutang' => [
                                'label' => 'Piutang',
                                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'dot' => 'bg-purple-500',
                                'ping' => 'bg-purple-400',
                                'arrow' => 'text-purple-400 group-hover:text-purple-600',
                                'card' => 'border-gray-200 border-l-4 border-l-purple-500 hover:border-l-purple-600 hover:bg-purple-50/30 hover:shadow-md hover:shadow-purple-500/10',
                            ],
                        ];
                    @endphp

                    <div class="dashboard-card p-4 bg-white">
                        {{-- Header --}}
                        <div class="relative flex items-center justify-between mb-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl {{ $activeAlertsCount > 0 ? 'bg-rose-50 text-rose-600 border border-rose-100/80' : 'bg-emerald-50 text-emerald-600 border border-emerald-100/80' }} flex items-center justify-center shrink-0 shadow-xs">
                                    @if ($activeAlertsCount > 0)
                                        <x-heroicon-o-bell-alert class="w-5 h-5" />
                                    @else
                                        <x-heroicon-o-check-circle class="w-5 h-5" />
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                        Tindak Lanjut
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $activeAlertsCount > 0 ? 'Perlu perhatian operasional' : 'Semua operasional aman' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Pill Header --}}
                            @if ($activeAlertsCount > 0)
                                <div class="flex items-center gap-1.5 text-[8.5px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-rose-500"></span>
                                    </span>
                                    <span>{{ $activeAlertsCount }} Aksi</span>
                                </div>
                            @else
                                <div class="flex items-center gap-1 text-[8.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Terkendali</span>
                                </div>
                            @endif
                        </div>

                        {{-- Alert List --}}
                        <div class="space-y-2">
                            @foreach ($actionCenter as $kunci => $item)
                                @php
                                    $cfg = $actionConfigs[$kunci] ?? [
                                        'label' => ucfirst($kunci),
                                        'badge' => 'bg-gray-50 text-gray-700 border-gray-200',
                                        'dot' => 'bg-gray-500',
                                        'ping' => 'bg-gray-400',
                                        'arrow' => 'text-gray-400 group-hover:text-gray-600',
                                        'card' => 'border-gray-200',
                                    ];
                                    $itemCount = (int) ($item['count'] ?? 0);
                                    $hasCount = $itemCount > 0;
                                @endphp

                                <a href="{{ $item['route'] }}"
                                   class="group block p-2.5 rounded-xl border transition-all duration-200 hover:-translate-y-0.5 {{ $hasCount ? $cfg['card'] . ' bg-white' : 'border-gray-200 bg-gray-50/50 hover:bg-white hover:border-gray-300 hover:shadow-xs' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[9px] font-bold border transition-transform duration-200 group-hover:scale-[1.02] origin-left {{ $hasCount ? $cfg['badge'] : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                                    @if ($hasCount)
                                                        <span class="relative flex h-1.5 w-1.5">
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $cfg['ping'] }}"></span>
                                                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $cfg['dot'] }}"></span>
                                                        </span>
                                                    @else
                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                    @endif
                                                    <span>{{ $item['title'] ?? $cfg['label'] }}</span>
                                                </span>

                                                @if (!$hasCount)
                                                    <span class="text-[8px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded">
                                                        Aman
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-black {{ $hasCount ? 'text-gray-900' : 'text-gray-400' }}">
                                                    {{ number_format($itemCount, 0, ',', '.') }}
                                                </span>
                                                <span class="text-[10px] font-medium {{ $hasCount ? 'text-gray-600 group-hover:text-gray-900' : 'text-gray-400' }} transition-colors truncate">
                                                    {{ $item['label'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="shrink-0 flex items-center">
                                            <x-heroicon-o-chevron-right class="w-4 h-4 {{ $hasCount ? $cfg['arrow'] : 'text-gray-300 group-hover:text-gray-500' }} transition-transform duration-200 group-hover:translate-x-1.5" />
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($canTombolKasir)
                    <a href="{{ route('penjualan.create') }}" class="cashier-card block !p-3.5 text-white font-bold text-xs flex items-center justify-between shadow-md transition-all duration-200 group">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-o-calculator class="w-5 h-5 text-white shrink-0" />
                            <div>
                                <span class="text-xs font-extrabold tracking-wide block text-white">Buka Kasir</span>
                                <span class="text-[9px] text-blue-100 font-medium block">Transaksi penjualan baru</span>
                            </div>
                        </div>
                        <x-heroicon-o-chevron-right class="w-4 h-4 text-white/80 group-hover:text-white group-hover:translate-x-1 transition-all shrink-0" />
                    </a>
                @endif
            </div>
        @endif
    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- 4. INVENTORY + EXPIRY + BARANG YANG HARUS DIBELI             --}}
    {{-- ============================================================ --}}
    @if ($canPemantauanStok || $canBarangBeli)
    <div class="inventory-row grid grid-cols-1 {{ ($canPemantauanStok && $canBarangBeli) ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-5 items-stretch">
        @if ($canPemantauanStok)
        {{-- ============================================================ --}}
        {{-- KOLOM KIRI: INVENTORY CONTROL CENTER + KONDISI STOK + EXPIRY HEALTH --}}
        {{-- ============================================================ --}}
        <div class="dashboard-card inventory-card p-5 flex flex-col justify-between bg-white">
            {{-- Header --}}
            <div class="flex items-center justify-between pb-3.5 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100/80 flex items-center justify-center shrink-0 shadow-xs">
                        <x-heroicon-o-archive-box class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">
                            Pemantauan Stok
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Ringkasan kuantitas & masa simpan obat
                        </p>
                    </div>
                </div>

                <a href="{{ route('laporan.stok') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0">
                    <span>Lihat Stok</span>
                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                </a>
            </div>

            <div class="flex flex-col gap-3 mt-3.5 flex-1 justify-between">
                {{-- 1. Baris Metrik: Total Stok & Nilai Persediaan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    {{-- Total Stok --}}
                    <div class="p-3 rounded-xl border border-gray-200/90 bg-white hover:border-gray-300 hover:shadow-xs transition-all flex items-center gap-3 {{ $canNilaiPersediaan ? '' : 'sm:col-span-2' }}">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100/80 flex items-center justify-center text-emerald-600 shrink-0">
                            <x-heroicon-o-cube class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-semibold text-gray-500 block">Total Stok</span>
                            <div class="text-xl font-black text-gray-900 leading-tight mt-0.5 tracking-tight"
                                 data-countup="true"
                                 data-target="{{ (float) preg_replace('/[^0-9]/', '', (string)$totalStok) }}"
                                 data-suffix=" Unit">
                                {{ number_format($totalStok) }} <span class="text-xs font-bold text-gray-400">Unit</span>
                            </div>
                            <span class="text-[11px] text-gray-400 font-medium">Fisik persediaan</span>
                        </div>
                    </div>

                    @if ($canNilaiPersediaan)
                        {{-- Nilai Persediaan --}}
                        <div class="p-3 rounded-xl border border-gray-200/90 bg-white hover:border-gray-300 hover:shadow-xs transition-all flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100/80 flex items-center justify-center text-amber-600 shrink-0">
                                <x-heroicon-o-circle-stack class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-semibold text-gray-500 block">Nilai Persediaan</span>

                                <div class="text-lg font-black text-gray-900 leading-tight mt-0.5 whitespace-nowrap tracking-tight"
                                     data-countup="true"
                                     data-target="{{ (float) preg_replace('/[^0-9]/', '', (string)$nilaiPersediaan) }}"
                                     data-prefix="Rp ">
                                    <span class="text-xs font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($nilaiPersediaan, 0, ',', '.') }}
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
                <div class="p-3 rounded-xl border border-gray-200/90 bg-white hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Kondisi Stok
                        </span>
                        <span class="text-[11px] text-gray-400 font-medium">{{ number_format($totalKondisiStok) }} Total Item</span>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Doughnut Chart Kondisi Stok with Center Text --}}
                        <div class="relative w-[92px] h-[92px] shrink-0 flex items-center justify-center">
                            <canvas id="kondisiStokChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
                                <span class="text-sm font-black text-gray-900 leading-tight my-0.5">{{ number_format($totalKondisiStok) }}</span>
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">Item</span>
                            </div>
                        </div>

                        {{-- Legend List --}}
                        <div class="flex-1 space-y-1 w-full min-w-0">
                            <a href="{{ route('laporan.stok', ['status_stok' => 'habis']) }}"
                               class="flex items-center justify-between text-xs hover:bg-rose-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-rose-700 transition-colors truncate">Habis</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($stokHabisCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctHabis }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-rose-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}"
                               class="flex items-center justify-between text-xs hover:bg-amber-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-amber-700 transition-colors truncate">Menipis</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($stokMenipisCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctMenipis }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-amber-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_stok' => 'aman']) }}"
                               class="flex items-center justify-between text-xs hover:bg-emerald-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors truncate">Aman</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($stokAmanCount) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctAmanStok }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 3. Expiry Health dengan Doughnut Chart & Legend --}}
                @php
                    $totalB = max(1, $totalBatchesCount);
                    $pctAman = ($totalBatchesCount > 0) ? number_format(($expiryAman / $totalB) * 100, 1, ',', '.') : '0';
                    $pctWarning = ($totalBatchesCount > 0) ? number_format(($expiryWarning / $totalB) * 100, 1, ',', '.') : '0';
                    $pctCritical = ($totalBatchesCount > 0) ? number_format(($expiryCritical / $totalB) * 100, 1, ',', '.') : '0';
                    $pctKadaluarsa = ($totalBatchesCount > 0) ? number_format(($expiryKadaluarsa / $totalB) * 100, 1, ',', '.') : '0';
                @endphp
                <div class="p-3 rounded-xl border border-gray-200/90 bg-white hover:border-gray-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
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
                        <div class="relative w-[92px] h-[92px] shrink-0 flex items-center justify-center">
                            <canvas id="expiryHealthChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
                                <span class="text-sm font-black text-gray-900 leading-tight my-0.5">{{ number_format($totalBatchesCount) }}</span>
                                <span class="text-[8px] text-gray-400 font-bold uppercase tracking-wider">Batch</span>
                            </div>
                        </div>

                        {{-- Legend List dengan Angka dan Persentase --}}
                        <div class="flex-1 space-y-1 w-full min-w-0">
                            <a href="{{ route('laporan.stok', ['status_expired' => 'normal']) }}"
                               class="flex items-center justify-between text-xs hover:bg-emerald-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors truncate">Aman (>90 hari)</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($expiryAman) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctAman }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => '3_bulan']) }}"
                               class="flex items-center justify-between text-xs hover:bg-yellow-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-yellow-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-yellow-700 transition-colors truncate">Peringatan (30–90 hari)</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($expiryWarning) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctWarning }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-yellow-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => '1_bulan']) }}"
                               class="flex items-center justify-between text-xs hover:bg-orange-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-orange-700 transition-colors truncate">Kritis (&lt;30 hari)</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($expiryCritical) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctCritical }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all" />
                                </div>
                            </a>

                            <a href="{{ route('laporan.stok', ['status_expired' => 'kadaluarsa']) }}"
                               class="flex items-center justify-between text-xs hover:bg-rose-50/60 p-1.5 rounded-lg transition-colors group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                                    <span class="font-semibold text-gray-700 group-hover:text-rose-700 transition-colors truncate">Kadaluwarsa</span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-extrabold text-gray-900">{{ number_format($expiryKadaluarsa) }}</span>
                                    <span class="text-gray-400 text-[11px] w-12 text-right font-medium">{{ $pctKadaluarsa }}%</span>
                                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-gray-300 group-hover:text-rose-500 group-hover:translate-x-0.5 transition-all" />
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
        <div class="dashboard-card inventory-card p-5 flex flex-col justify-between bg-white">
            {{-- Header --}}
            <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100/80 flex items-center justify-center shrink-0 shadow-xs">
                        <x-heroicon-o-shopping-bag class="w-5 h-5" />
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                Barang yang Harus Dibeli
                            </h3>
                            @if(count($barangHarusDibeli) > 0)
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-bold uppercase tracking-wider">
                                    {{ count($barangHarusDibeli) }} Perlu Beli
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Item stok habis & di bawah batas minimum
                        </p>
                    </div>
                </div>

                <a href="{{ route('laporan.stok', ['status_stok' => 'menipis']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0">
                    <span>Lihat Selengkapnya</span>
                    <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                </a>
            </div>

            <div class="w-full flex-1 overflow-y-auto max-h-[480px] rounded-xl border border-gray-200/90 shadow-2xs">
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
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                                <td class="text-center font-bold text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="font-bold text-gray-900 wrap-cell leading-snug">
                                    {{ $item['barang']->nama }}
                                </td>
                                <td class="text-gray-500 wrap-cell leading-snug text-xs">
                                    {{ $item['barang']->kategori->nama ?? '-' }}
                                </td>
                                <td class="text-center text-xs font-bold {{ (int)$item['stok'] === 0 ? 'text-rose-600' : 'text-gray-900' }}">
                                    {{ number_format($item['stok']) }}
                                </td>
                                <td class="text-center">
                                    @if($item['status'] === 'HABIS')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Habis
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Segera Beli
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center mb-2 shadow-xs">
                                            <x-heroicon-o-check-circle class="w-6 h-6" />
                                        </div>
                                        <span class="text-xs font-bold text-gray-800">Semua Stok Obat Aman</span>
                                        <p class="text-[10px] text-gray-400 mt-0.5 max-w-[260px]">Seluruh stok obat berada dalam kondisi aman di atas batas minimum persediaan.</p>
                                    </div>
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
    {{-- 8. 10 OBAT TERLARIS & 10 OBAT PALING SEDIKIT TERJUAL         --}}
    {{-- ============================================================ --}}
    @if ($canObatTerlaris || $canObatTerendah)
    <div class="grid grid-cols-1 {{ ($canObatTerlaris && $canObatTerendah) ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-5">
        @if ($canObatTerlaris)
        {{-- 10 Obat Terlaris --}}
        <div class="dashboard-card p-5 bg-white flex flex-col justify-between">
            <div>
                {{-- Header --}}
                <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100/80 flex items-center justify-center shrink-0 shadow-xs">
                            <x-heroicon-o-trophy class="w-5 h-5" />
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                10 Obat Terlaris
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Kuantitas unit terjual pada periode {{ $periodeLabel }}
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('laporan.penjualan') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0">
                        <span>Laporan Penjualan</span>
                        <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200/90 shadow-2xs">
                    <table class="table-compact w-full text-left">
                        <thead>
                            <tr>
                                <th class="w-12 text-center sticky-header">Rank</th>
                                <th class="sticky-header">Nama Obat</th>
                                <th class="sticky-header">Kategori</th>
                                <th class="text-center sticky-header">Terjual</th>
                                @if ($canOmzetObat)
                                    <th class="text-right sticky-header">Omzet</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($topSellingMedicines as $index => $medicine)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                                    <td class="text-center py-2.5">
                                        @if ($index === 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-black text-[11px]">1</span>
                                        @elseif ($index === 1)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px]">2</span>
                                        @elseif ($index === 2)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-50 text-amber-900 font-bold text-[11px] border border-amber-200">3</span>
                                        @else
                                            <span class="font-bold text-gray-400 text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="font-bold text-gray-900 text-xs">{{ $medicine->nama }}</td>
                                    <td>
                                        <span class="text-xs text-gray-500">
                                            {{ $medicine->kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center font-bold text-gray-900 text-xs">
                                        {{ number_format($medicine->total_terjual) }} <span class="text-[10px] text-gray-400 font-normal">unit</span>
                                    </td>
                                    @if ($canOmzetObat)
                                        <td class="text-right font-extrabold text-gray-900 text-xs whitespace-nowrap">
                                            <span class="text-[10px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($medicine->total_omzet, 0, ',', '.') }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canOmzetObat ? 5 : 4 }}" class="py-10 text-center text-xs text-gray-400">
                                        Belum ada transaksi penjualan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if ($canObatTerendah)
        {{-- 10 Obat Paling Sedikit Terjual --}}
        <div class="dashboard-card p-5 bg-white flex flex-col justify-between">
            <div>
                {{-- Header --}}
                <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-100/80 flex items-center justify-center shrink-0 shadow-xs">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                10 Obat Paling Sedikit Terjual
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Termasuk obat aktif yang belum terjual (0 unit)
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('laporan.penjualan') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0">
                        <span>Laporan Penjualan</span>
                        <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200/90 shadow-2xs">
                    <table class="table-compact w-full text-left">
                        <thead>
                            <tr>
                                <th class="w-12 text-center sticky-header">No</th>
                                <th class="sticky-header">Nama Obat</th>
                                <th class="sticky-header">Kategori</th>
                                <th class="text-center sticky-header">Terjual</th>
                                @if ($canOmzetObat)
                                    <th class="text-right sticky-header">Omzet</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($leastSellingMedicines as $index => $medicine)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                                    <td class="text-center font-bold text-gray-400 text-xs py-2.5">{{ $index + 1 }}</td>
                                    <td class="font-bold text-gray-900 text-xs">{{ $medicine->nama }}</td>
                                    <td>
                                        <span class="text-xs text-gray-500">
                                            {{ $medicine->kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center text-xs">
                                        @if((int)$medicine->total_terjual === 0)
                                            <span class="font-bold text-rose-600">0</span> <span class="text-[10px] text-rose-400">unit</span>
                                        @else
                                            <span class="font-bold text-gray-900">{{ number_format($medicine->total_terjual) }}</span> <span class="text-[10px] text-gray-400">unit</span>
                                        @endif
                                    </td>
                                    @if ($canOmzetObat)
                                        <td class="text-right font-extrabold text-gray-900 text-xs whitespace-nowrap">
                                            <span class="text-[10px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($medicine->total_omzet, 0, ',', '.') }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canOmzetObat ? 5 : 4 }}" class="py-10 text-center text-xs text-gray-400">
                                        Belum ada data obat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
            <div class="dashboard-card p-5 bg-white flex flex-col justify-between">
                <div>
                    {{-- Header --}}
                    <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-3.5 gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100/80 flex items-center justify-center shrink-0 shadow-xs">
                                <x-heroicon-o-user-group class="w-5 h-5" />
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Kinerja Karyawan</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Performa transaksi dan total penjualan pada periode {{ $periodeLabel }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($kinerjaKaryawan->count() > 5)
                                <button type="button" id="btn-toggle-kinerja" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">
                                    Lihat Semua ({{ $kinerjaKaryawan->count() }})
                                </button>
                                <span class="text-gray-300">|</span>
                            @endif
                            <a href="{{ route('user.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0">
                                <span>Kelola User</span>
                                <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[420px] overflow-y-auto rounded-xl border border-gray-200/90 shadow-2xs">
                        <table class="table-compact w-full text-left" id="table-kinerja-karyawan">
                            <thead>
                                <tr>
                                    <th class="w-12 text-center sticky-header">Rank</th>
                                    <th class="sticky-header">Nama Karyawan</th>
                                    <th class="text-right sticky-header">Penjualan</th>
                                    <th class="text-center sticky-header">Transaksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
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
                                    <tr class="{{ $isExtra ? 'kinerja-extra-row hidden' : '' }} {{ $isCurrentUser ? 'bg-blue-50/60' : ($index % 2 === 0 ? 'bg-white' : 'bg-gray-200') }}">
                                        <td class="text-center py-2.5">
                                            @if ($index === 0)
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-100 text-indigo-800 font-black text-[11px]">1</span>
                                            @elseif ($index === 1)
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px]">2</span>
                                            @elseif ($index === 2)
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-50 text-indigo-900 font-bold text-[11px] border border-indigo-200">3</span>
                                            @else
                                                <span class="font-bold text-gray-400 text-xs">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gray-900 text-xs">{{ $karyawan->name }}</span>
                                                @if ($isCurrentUser)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-black bg-blue-100 text-blue-700 border border-blue-200">ANDA</span>
                                                @endif
                                                <span class="text-[11px] text-gray-400 capitalize">
                                                    ({{ $karyawan->role }})
                                                </span>
                                                @if (!$karyawan->aktif)
                                                    <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">Nonaktif</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-right font-extrabold text-gray-900 text-xs whitespace-nowrap">
                                            <span class="text-[10px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($penjualan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center font-bold text-gray-900 text-xs">
                                            {{ number_format($karyawan->total_transaksi) }} <span class="text-[10px] text-gray-400 font-normal">trx</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-xs text-gray-400">
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
        <div class="dashboard-card p-5 bg-white flex flex-col justify-between">
            <div>
                {{-- Header --}}
                <div class="flex items-center justify-between pb-3.5 border-b border-gray-100 mb-3.5 gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 border border-purple-100/80 flex items-center justify-center shrink-0 shadow-xs">
                            <x-heroicon-o-user class="w-5 h-5" />
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Dokter Penyumbang Resep Terbanyak</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Berdasarkan total resep yang disumbangkan ke apotek pada bulan ini</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModalDokterResep()" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1 group shrink-0 cursor-pointer">
                            <span>Lihat Semua</span>
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[420px] overflow-y-auto rounded-xl border border-gray-200/90 shadow-2xs">
                    <table class="table-compact w-full text-left" id="table-dokter-resep">
                        <thead>
                            <tr>
                                <th class="w-12 text-center sticky-header">Rank</th>
                                <th class="sticky-header">ID Dokter</th>
                                <th class="sticky-header">Nama Dokter</th>
                                <th class="text-center sticky-header">Total Resep</th>
                                <th class="text-right sticky-header">Total Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($topDokterResep as $index => $dokter)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                                    <td class="text-center py-2.5">
                                        @if ($index === 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-100 text-purple-800 font-black text-[11px]">1</span>
                                        @elseif ($index === 1)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px]">2</span>
                                        @elseif ($index === 2)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-50 text-purple-900 font-bold text-[11px] border border-purple-200">3</span>
                                        @else
                                            <span class="font-bold text-gray-400 text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-mono text-[11px] font-bold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200/80">{{ $dokter->id_dokter }}</span>
                                    </td>
                                    <td>
                                        <div class="font-bold text-gray-900 text-xs leading-snug">{{ $dokter->nama_dokter ?: '—' }}</div>
                                        @if (!empty($dokter->alamat_lembaga))
                                            <div class="text-[10px] text-gray-400 capitalize leading-tight mt-0.5">{{ $dokter->alamat_lembaga }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-gray-900 text-xs">
                                        {{ number_format($dokter->total_resep) }} <span class="text-[10px] text-gray-400 font-normal">resep</span>
                                    </td>
                                    <td class="text-right font-extrabold text-gray-900 text-xs whitespace-nowrap">
                                        <span class="text-[10px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($dokter->total_transaksi, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-xs text-gray-400">
                                        Belum ada riwayat transaksi resep dokter pada bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Note Banner at Bottom --}}
            <div class="mt-3.5 p-2.5 rounded-xl bg-gray-50 border border-gray-200 flex items-center gap-2 text-[11px] text-gray-600">
                <x-heroicon-s-information-circle class="w-4 h-4 text-purple-500 shrink-0" />
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
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/60">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100/80 flex items-center justify-center text-purple-600 shrink-0 shadow-xs">
                    <x-heroicon-o-user class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Dokter Penyumbang Resep Bulan Ini</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar lengkap dokter dengan kontribusi resep terdaftar pada bulan ini.</p>
                </div>
            </div>
            <button type="button" onclick="closeModalDokterResep()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer" title="Tutup modal">
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
                    class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 hover:bg-gray-50/80 focus:bg-white border border-gray-200 rounded-xl focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all outline-hidden text-gray-800 placeholder-gray-400"
                    onkeyup="filterModalDokterTable(this.value)"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </div>
            </div>
        </div>

        <!-- Modal Body Table -->
        <div class="p-4 overflow-y-auto flex-1">
            <div class="overflow-x-auto rounded-xl border border-gray-200/90 shadow-2xs">
                <table class="table-compact w-full text-left" id="table-modal-dokter">
                    <thead>
                        <tr>
                            <th class="w-12 text-center sticky-header">Rank</th>
                            <th class="w-32 sticky-header">ID DOKTER</th>
                            <th class="sticky-header">NAMA DOKTER</th>
                            <th class="text-center w-28 sticky-header">TOTAL RESEP</th>
                            <th class="text-right w-36 sticky-header">TOTAL TRANSAKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-modal-dokter" class="divide-y divide-gray-100">
                        @forelse ($semuaDokterResep as $index => $dokter)
                            <tr class="modal-dokter-row {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                                <td class="text-center py-2.5">
                                    @if ($index === 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-100 text-purple-800 font-black text-[11px]">1</span>
                                    @elseif ($index === 1)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px]">2</span>
                                    @elseif ($index === 2)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-50 text-purple-900 font-bold text-[11px] border border-purple-200">3</span>
                                    @else
                                        <span class="font-bold text-gray-400 text-xs">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-[11px] font-bold text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200/80">{{ $dokter->id_dokter }}</span>
                                </td>
                                <td>
                                    <div class="font-bold text-gray-900 text-xs leading-snug">{{ $dokter->nama_dokter ?: '—' }}</div>
                                    @if (!empty($dokter->alamat_lembaga))
                                        <div class="text-[10px] text-gray-400 capitalize leading-tight mt-0.5">{{ $dokter->alamat_lembaga }}</div>
                                    @endif
                                </td>
                                <td class="text-center font-bold text-gray-900 text-xs">
                                    {{ number_format($dokter->total_resep) }} <span class="text-[10px] text-gray-400 font-normal">resep</span>
                                </td>
                                <td class="text-right font-extrabold text-gray-900 text-xs whitespace-nowrap">
                                    <span class="text-[10px] font-bold text-gray-400 mr-0.5">Rp</span>{{ number_format($dokter->total_transaksi, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-modal-dokter">
                                <td colspan="5" class="py-10 text-center text-xs text-gray-400">
                                    Belum ada data resep dokter pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="no-search-match-dokter" class="hidden">
                            <td colspan="5" class="py-10 text-center text-xs text-gray-400">
                                Tidak ada dokter yang cocok dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-gray-100 bg-slate-50/70 flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Total dokter terdaftar bulan ini: <span class="font-bold text-gray-800">{{ $semuaDokterResep->count() }}</span> dokter
            </div>
            <button type="button" onclick="closeModalDokterResep()" class="text-xs font-bold text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 px-4 py-1.5 rounded-lg transition-colors cursor-pointer shadow-2xs">
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

        const canvasContext = ctx.getContext('2d');

        // Linear Gradient lembut untuk Omzet Bersih (Blue)
        const gradientNet = canvasContext.createLinearGradient(0, 0, 0, 320);
        gradientNet.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
        gradientNet.addColorStop(0.65, 'rgba(37, 99, 235, 0.05)');
        gradientNet.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

        // Linear Gradient lembut untuk Gross Profit (Emerald)
        const gradientProfit = canvasContext.createLinearGradient(0, 0, 0, 320);
        gradientProfit.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
        gradientProfit.addColorStop(0.65, 'rgba(16, 185, 129, 0.04)');
        gradientProfit.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        // Custom Plugin: Garis Pandu Vertikal (Crosshair Guideline on Hover)
        const verticalGuidePlugin = {
            id: 'verticalGuide',
            afterDraw: (chartInstance) => {
                if (chartInstance.tooltip?._active?.length) {
                    const activePoint = chartInstance.tooltip._active[0];
                    const chartCtx = chartInstance.ctx;
                    const x = activePoint.element.x;
                    const topY = chartInstance.scales.y.top;
                    const bottomY = chartInstance.scales.y.bottom;

                    chartCtx.save();
                    chartCtx.beginPath();
                    chartCtx.setLineDash([4, 4]);
                    chartCtx.moveTo(x, topY);
                    chartCtx.lineTo(x, bottomY);
                    chartCtx.lineWidth = 1;
                    chartCtx.strokeStyle = 'rgba(148, 163, 184, 0.65)'; // slate-400
                    chartCtx.stroke();
                    chartCtx.restore();
                }
            }
        };

        const chart = new Chart(ctx, {
            type: 'line',
            plugins: [verticalGuidePlugin],
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Omzet Kotor',
                        data: dataGross,
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.38,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#f59e0b',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2.5,
                        pointHitRadius: 14,
                        fill: false,
                    },
                    {
                        label: 'Omzet Bersih',
                        data: dataNet,
                        borderColor: '#2563eb',
                        backgroundColor: gradientNet,
                        borderWidth: 2.5,
                        tension: 0.38,
                        pointRadius: 0,
                        pointHoverRadius: 6.5,
                        pointHoverBackgroundColor: '#2563eb',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2.5,
                        pointHitRadius: 14,
                        fill: true,
                    },
                    @if ($canGrafikProfit)
                    {
                        label: 'Gross Profit',
                        data: dataProfit,
                        borderColor: '#10b981',
                        backgroundColor: gradientProfit,
                        borderWidth: 2,
                        borderDash: [5, 4],
                        tension: 0.38,
                        pointRadius: 0,
                        pointHoverRadius: 5.5,
                        pointHoverBackgroundColor: '#10b981',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2.5,
                        pointHitRadius: 14,
                        fill: true,
                    }
                    @endif
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
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
                        borderColor: 'rgba(255, 255, 255, 0.14)',
                        borderWidth: 1,
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        footerColor: '#34d399',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: {
                            weight: 'bold',
                            size: 12,
                        },
                        bodyFont: {
                            size: 11,
                        },
                        footerFont: {
                            size: 11,
                            weight: 'bold',
                        },
                        boxPadding: 4,
                        callbacks: {
                            title: function (items) {
                                if (!items || items.length === 0) return '';
                                return 'Tanggal: ' + items[0].label;
                            },
                            label: function (context) {
                                return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                            },
                            footer: function (items) {
                                if (!items || items.length === 0) return '';
                                const idx = items[0].dataIndex;
                                const netVal = Number(dataNet[idx] || 0);
                                const profitVal = Number(dataProfit[idx] || 0);
                                if (netVal > 0 && profitVal > 0) {
                                    const marginPercent = ((profitVal / netVal) * 100).toFixed(1);
                                    return 'Margin Laba: ' + marginPercent + '%';
                                }
                                return '';
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
                                if (val >= 1000000000) {
                                    return 'Rp ' + (val / 1000000000).toFixed(1) + ' M';
                                } else if (val >= 1000000) {
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

        // Interaktivitas Legend: Klik tombol untuk toggle/sembunyikan dataset
        const legendButtons = document.querySelectorAll('#salesChartLegend .chart-legend-btn');
        legendButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.datasetIndex, 10);
                const isVisible = chart.isDatasetVisible(idx);
                chart.setDatasetVisibility(idx, !isVisible);
                chart.update();

                if (isVisible) {
                    this.classList.add('opacity-40', 'line-through', 'grayscale');
                } else {
                    this.classList.remove('opacity-40', 'line-through', 'grayscale');
                }
            });
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
                        backgroundColor: totalKondisi > 0 ? ['#f43f5e', '#f59e0b', '#10b981'] : ['#e2e8f0', '#e2e8f0', '#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
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
                        backgroundColor: totalExpiry > 0 ? ['#10b981', '#f59e0b', '#f97316', '#f43f5e'] : ['#e2e8f0', '#e2e8f0', '#e2e8f0', '#e2e8f0'],
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
