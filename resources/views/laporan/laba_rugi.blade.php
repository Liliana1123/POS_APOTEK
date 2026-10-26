@extends('layouts.app')
@section('title', 'Laporan Laba Rugi')

@section('content')

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );

    $currentPeriode = request('periode', $periode ?? 'bulan_ini');
@endphp

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KHUSUS CETAK PDF / PRINTER) --}}
{{-- ========================================================= --}}
<div class="print-only">

    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="LAPORAN LABA RUGI & FINANSIAL">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }}
        {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>

    {{-- RINGKASAN EKSEKUTIF CETAK --}}
    <div class="print-section mb-4">
        <table class="print-table w-full mb-3">
            <thead>
                <tr>
                    <th class="text-left !px-2 py-1.5">Omzet Kotor</th>
                    <th class="text-left !px-2 py-1.5">Total Diskon</th>
                    <th class="text-left !px-2 py-1.5">Penjualan Bersih</th>
                    <th class="text-left !px-2 py-1.5">HPP (Perpetual)</th>
                    <th class="text-left !px-2 py-1.5">Laba Kotor</th>
                    <th class="text-left !px-2 py-1.5">Beban Operasional</th>
                    <th class="text-left !px-2 py-1.5">Laba Bersih</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right !px-2 py-1.5 font-bold text-gray-800">Rp {{ number_format($omzetKotor, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold text-purple-700">- Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold text-blue-800">Rp {{ number_format($penjualanBersih, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold text-rose-700">- Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold text-teal-800">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold text-orange-700">- Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                    <td class="text-right !px-2 py-1.5 font-bold {{ $labaBersih >= 0 ? 'text-green-800' : 'text-red-700' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TABEL RINCIAN LAPORAN CETAK --}}
    <div class="print-section mb-4">
        <table class="print-table w-full">
            <thead>
                <tr>
                    <th class="text-center !px-2 py-1.5 w-12">No</th>
                    <th class="text-left !px-3 py-1.5">Keterangan</th>
                    <th class="text-right !px-3 py-1.5 w-44">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center !px-2 py-1">1</td>
                    <td class="!px-3 py-1">Omzet Kotor</td>
                    <td class="text-right !px-3 py-1 font-mono font-medium">Rp {{ number_format($omzetKotor, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-center !px-2 py-1">2</td>
                    <td class="!px-3 py-1">(-) Total Diskon</td>
                    <td class="text-right !px-3 py-1 font-mono font-medium text-purple-700">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-blue-50 font-bold">
                    <td class="text-center !px-2 py-1">3</td>
                    <td class="!px-3 py-1 text-blue-900">Penjualan Bersih</td>
                    <td class="text-right !px-3 py-1 font-mono text-blue-900">Rp {{ number_format($penjualanBersih, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-center !px-2 py-1">4</td>
                    <td class="!px-3 py-1">(-) HPP (Harga Pokok Penjualan)</td>
                    <td class="text-right !px-3 py-1 font-mono font-medium text-rose-700">Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-teal-50 font-bold">
                    <td class="text-center !px-2 py-1">5</td>
                    <td class="!px-3 py-1 text-teal-900">Laba Kotor</td>
                    <td class="text-right !px-3 py-1 font-mono text-teal-900">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-center !px-2 py-1">6</td>
                    <td class="!px-3 py-1">(-) Beban Operasional</td>
                    <td class="text-right !px-3 py-1 font-mono font-medium text-orange-700">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                </tr>
                <tr class="bg-emerald-100 font-extrabold text-[9px]">
                    <td class="text-center !px-2 py-1.5">7</td>
                    <td class="!px-3 py-1.5 text-emerald-950 uppercase">Laba Bersih</td>
                    <td class="text-right !px-3 py-1.5 font-mono text-emerald-950">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- POSISI OUTSTANDING PIUTANG & UTANG CETAK --}}
    <div class="print-section mb-6">
        <table class="print-table w-full">
            <thead>
                <tr>
                    <th class="text-left !px-3 py-1.5 w-1/2">Komponen Tagihan</th>
                    <th class="text-left !px-2 py-1.5 w-1/4">Keterangan</th>
                    <th class="text-right !px-3 py-1.5 w-1/4">Sisa Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="!px-3 py-1 font-semibold text-gray-800">Piutang Member</td>
                    <td class="!px-2 py-1 text-gray-600">Tagihan pelanggan yang belum dibayar ({{ $memberBerpiutangCount }} member)</td>
                    <td class="text-right !px-3 py-1 font-mono font-bold text-blue-800">Rp {{ number_format($totalPiutangMember, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="!px-3 py-1 font-semibold text-gray-800">Utang Supplier</td>
                    <td class="!px-2 py-1 text-gray-600">Tagihan pembelian barang yang belum dibayar ({{ $countHutangBelumLunas }} faktur)</td>
                    <td class="text-right !px-3 py-1 font-mono font-bold text-amber-800">Rp {{ number_format($totalHutangSupplier, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>



</div>

{{-- ========================================================= --}}
{{-- TAMPILAN LAYAR APLIKASI (SCREEN UI) --}}
{{-- ========================================================= --}}

{{-- Header Halaman & Filter Controls --}}
<div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-6 print:hidden">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Laporan Laba Rugi</h1>
        <p class="text-xs text-slate-500 mt-0.5">Ringkasan keuangan apotek berdasarkan periode yang dipilih</p>
    </div>

    {{-- Tombol Ekspor/Cetak + Filter Periode (sebelah kanan, vertikal) --}}
    <div class="flex flex-col items-end gap-2">
        {{-- Baris 1: Ekspor Excel & Cetak Laporan --}}
        <div class="flex gap-2 shrink-0">
            {{-- Ekspor Excel --}}
            <a
                id="btn-export-excel"
                href="{{ route('laporan.laba-rugi', array_merge(request()->query(), ['export' => 'excel'])) }}"
                class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5"
            >
                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                Ekspor Excel
            </a>

            {{-- Cetak Laporan --}}
            <button
                type="button"
                id="btn-cetak-laporan"
                onclick="window.print()"
                class="btn-primary py-2 px-4"
            >
                Cetak Laporan
            </button>
        </div>

        {{-- Baris 2: Filter Periode (tepat di bawah tombol ekspor/cetak) --}}
        <x-date-range-filter
            :action="route('laporan.laba-rugi')"
            :periode="$currentPeriode"
            :dari="$dari"
            :sampai="$sampai"
        />
    </div>
</div>

{{-- Flash Notification Alerts --}}
@if(isset($errors) && $errors->any())
    <x-alert type="danger" title="Terjadi kesalahan saat memproses data:" class="print:hidden">
        <ul class="list-disc list-inside space-y-0.5 pl-1">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif

{{-- ========================================================= --}}
{{-- BAGIAN ATAS: 7 KARTU RINGKASAN & RINGKASAN PIUTANG UTANG --}}
{{-- ========================================================= --}}
<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm mb-6 print:hidden">
    <div class="flex flex-col lg:flex-row gap-4 items-stretch">

        {{-- KOLOM KIRI (7 KARTU DALAM 2 BARIS) --}}
        <div class="flex-1 min-w-0 flex flex-col gap-3">

            {{-- BARIS 1: 4 KARTU (Omzet Kotor, Total Diskon, Penjualan Bersih, HPP) --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                
                {{-- 1. Omzet Kotor --}}
                <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                    style="background-color: #ffffff; border: 2px solid #bfdbfe !important;">
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            Omzet Kotor
                        </span>

                        <div class="mt-1 text-xl font-black text-gray-900">
                            Rp {{ number_format($omzetKotor, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 shadow-sm">
                        <x-heroicon-o-chart-bar class="h-6 w-6" />
                    </div>
                </div>

                {{-- 2. Total Diskon --}}
                <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                    style="background-color: #ffffff; border: 2px solid #eabffeff !important;">
                    <div>
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">
                            Total Diskon
                        </span>
                        <div class="text-xl font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm shrink-0">
                        <x-heroicon-o-tag class="w-6 h-6" />
                    </div>
                </div>
                
                {{-- 3. Penjualan Bersih --}}
                <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                    style="background-color: #ffffff; border: 2px solid #bffee1ff !important;">
                    <div>
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">
                            Penjualan Bersih
                        </span>
                        <div class="text-xl font-black text-gray-900 mt-1">
                            Rp {{ number_format($penjualanBersih, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="kpi-icon bg-emerald-50 text-emerald-600 shadow-2xs">
                        <x-heroicon-o-banknotes class="w-6 h-6" />
                    </div>
                </div>
                
                {{-- 4. HPP --}}
                <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                    style="background-color: #ffffff; border: 2px solid #ffedd5 !important;">
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            HPP (Harga Pokok Penjualan)
                        </span>
                        <div class="mt-1 text-xl font-black text-gray-900">
                            Rp {{ number_format($hpp, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600 shadow-sm">
                        <x-heroicon-s-circle-stack class="h-6 w-6" />
                    </div>
                </div>
            </div>

            {{-- BARIS 2: 3 KARTU (Laba Kotor, Total Beban Operasional, Laba Bersih) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-stretch w-full">

            {{-- 5. Laba Kotor --}}
            <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                style="background-color: #ffffff; border: 2px solid #bfdbfe !important;">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Laba Kotor
                    </span>
                    <div class="mt-1 text-xl font-black text-gray-900">
                        Rp {{ number_format($labaKotor, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 shadow-sm">
                    <x-heroicon-o-banknotes class="h-6 w-6" />
                </div>
            </div>

            {{-- 6. Total Beban Operasional --}}
            <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                style="background-color: #ffffff; border: 2px solid #ffb6ccff !important;">
                <div>
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Total Beban Operasional
                    </span>
                    <div class="mt-1 text-xl font-black text-gray-900">
                        Rp {{ number_format($totalBeban, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg shadow-sm"
                    style="background-color: #fff1f5; color: #fd89acff;">
                    <x-heroicon-o-wallet class="h-6 w-6" />
                </div>
            </div>

            {{-- 7. Laba Bersih --}}
            <div class="flex items-center justify-between rounded-xl p-3.5 transition-all hover:shadow-sm"
                style="background-color: #ffffff; border: 2px solid #bffee1ff !important;">
                <div class="flex-1 min-w-0">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        Laba Bersih
                    </span>
                    <div class="mt-1 flex items-baseline gap-2 flex-wrap">
                        <span class="text-xl font-black text-gray-900">
                            Rp {{ number_format($labaBersih, 0, ',', '.') }}
                        </span>

                        {{-- Indikator Persentase Kenaikan / Penurunan Laba Bersih (selaras dengan Dashboard) --}}
                        @if ($labaBersihStatus === 'naik')
                            <span class="text-[10px] font-bold text-emerald-500 inline-flex items-center gap-0.5">
                                <span>↑</span>
                                <span>
                                    @if ($deltaLabaBersih !== null)
                                        {{ number_format(abs($deltaLabaBersih), 1, ',', '.') }}%
                                    @else
                                        Rp {{ number_format(abs($selisihNominalLabaBersih), 0, ',', '.') }}
                                    @endif
                                </span>
                            </span>
                        @elseif ($labaBersihStatus === 'turun')
                            <span class="text-[10px] font-bold text-rose-500 inline-flex items-center gap-0.5">
                                <span>↓</span>
                                <span>
                                    @if ($deltaLabaBersih !== null)
                                        {{ number_format(abs($deltaLabaBersih), 1, ',', '.') }}%
                                    @else
                                        Rp {{ number_format(abs($selisihNominalLabaBersih), 0, ',', '.') }}
                                    @endif
                                </span>
                            </span>
                        @else
                            {{-- Tampilan netral: tidak ada perubahan atau persentase tidak dapat dihitung --}}
                            <span class="text-[10px] font-bold text-gray-400 inline-flex items-center gap-0.5">
                                <span>—</span>
                                @if ($deltaLabaBersih !== null)
                                    <span>0,0%</span>
                                @endif
                            </span>
                        @endif
                    </div>

                    @if ($prevLabaBersih !== null)
                        <div class="mt-0.5 text-[9px] text-gray-400">
                            vs periode lalu: Rp {{ number_format($prevLabaBersih, 0, ',', '.') }}
                            @if ($prevLabaBersih < 0 && $labaBersih > 0)
                                <span class="text-emerald-600 font-semibold">(Berbalik Untung)</span>
                            @elseif ($prevLabaBersih > 0 && $labaBersih < 0)
                                <span class="text-rose-600 font-semibold">(Berbalik Rugi)</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg shadow-sm
                    {{ $labaBersihStatus === 'naik' ? 'bg-emerald-50 text-emerald-600' : ($labaBersihStatus === 'turun' ? 'bg-rose-50 text-rose-500' : 'bg-gray-100 text-gray-400') }}">
                    @if ($labaBersihStatus === 'naik')
                        <x-heroicon-o-arrow-trending-up class="h-6 w-6" />
                    @elseif ($labaBersihStatus === 'turun')
                        <x-heroicon-o-arrow-trending-down class="h-6 w-6" />
                    @else
                        <x-heroicon-o-minus class="h-5 w-5" />
                    @endif
                </div>
            </div>
            </div>

        </div>

        {{-- KOLOM KANAN: RINGKASAN PIUTANG & UTANG --}}
        <div class="w-full lg:w-72 xl:w-80 rounded-xl p-4 flex flex-col justify-between shrink-0"
            style="background-color: #ffffffff; border: 2px solid #d1d1d1ff;">
            <h2 class="text-xs font-bold text-slate-800 mb-3 tracking-wide">Ringkasan Piutang & Utang</h2>

            <div class="space-y-4 my-auto">
                {{-- Piutang Member --}}
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <x-heroicon-s-user class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="block text-xs font-bold text-slate-800">Piutang Member</span>
                        <span class="block text-[10px] text-slate-400 leading-tight">Tagihan pelanggan yang belum dibayar</span>
                        <strong class="block text-sm font-bold text-blue-600 mt-0.5 font-mono tracking-tight">
                            Rp {{ number_format($totalPiutangMember, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                {{-- Utang Supplier --}}
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                        <x-heroicon-s-truck class="w-5 h-5" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="block text-xs font-bold text-slate-800">Utang Supplier</span>
                        <span class="block text-[10px] text-slate-400 leading-tight">Tagihan pembelian barang yang belum dibayar</span>
                        <strong class="block text-sm font-bold text-amber-500 mt-0.5 font-mono tracking-tight">
                            Rp {{ number_format($totalHutangSupplier, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="text-[10px] text-blue-600 pt-2.5 border-t border-slate-200 mt-3 flex justify-between items-center">
                <span>{{ $memberBerpiutangCount }} Member aktif</span>
                <span>{{ $countHutangBelumLunas }} Faktur belum lunas</span>
            </div>
        </div>

    </div>
</div>

{{-- ========================================================= --}}
{{-- BAGIAN BAWAH: RINCIAN LAPORAN LABA RUGI & BEBAN OPERASIONAL --}}
{{-- ========================================================= --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8 print:hidden">

    {{-- KARTU KIRI: RINCIAN LAPORAN LABA RUGI --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
        <div>
            <h2 class="text-sm font-bold text-slate-800 mb-4 tracking-tight">Rincian Laporan Laba Rugi</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-y border-slate-150 text-slate-600 font-semibold">
                            <th class="w-12 text-center py-2.5 px-3">No</th>
                            <th class="text-left py-2.5 px-3">Keterangan</th>
                            <th class="text-right py-2.5 px-4 w-44">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- 1. Omzet Kotor --}}
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="text-center py-2.5 px-3 text-slate-500 font-medium">1</td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">Omzet Kotor</td>
                            <td class="text-right py-2.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($omzetKotor, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 2. Total Diskon --}}
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="text-center py-2.5 px-3 text-slate-500 font-medium">2</td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">(-) Total Diskon</td>
                            <td class="text-right py-2.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 3. Penjualan Bersih --}}
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="text-center py-2.5 px-3 text-blue-600 font-bold">3</td>
                            <td class="py-2.5 px-3 text-blue-600 font-bold">Penjualan Bersih</td>
                            <td class="text-right py-2.5 px-4 font-mono font-bold text-blue-600">
                                Rp {{ number_format($penjualanBersih, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 4. HPP (Harga Pokok Penjualan) --}}
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="text-center py-2.5 px-3 text-slate-500 font-medium">4</td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">(-) HPP (Harga Pokok Penjualan)</td>
                            <td class="text-right py-2.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($hpp, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 5. Laba Kotor --}}
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="text-center py-2.5 px-3 text-blue-600 font-bold">5</td>
                            <td class="py-2.5 px-3 text-blue-600 font-bold">Laba Kotor</td>
                            <td class="text-right py-2.5 px-4 font-mono font-bold text-blue-600">
                                Rp {{ number_format($labaKotor, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 6. Beban Operasional --}}
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="text-center py-2.5 px-3 text-slate-500 font-medium">6</td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">(-) Beban Operasional</td>
                            <td class="text-right py-2.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($totalBeban, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- 7. Laba Bersih --}}
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="text-center py-2.5 px-3 text-blue-600 font-bold">7</td>
                            <td class="py-2.5 px-3 text-blue-600 font-bold">Laba Bersih</td>
                            <td class="text-right py-2.5 px-4 font-mono font-bold text-blue-600">
                                Rp {{ number_format($labaBersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KARTU KANAN: RINGKASAN BEBAN OPERASIONAL --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col justify-between">
        <div>
            {{-- Header Kartu Beban --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Ringkasan Beban Operasional</h2>
                </div>

                <div class="flex items-center gap-2.5">
                    {{-- Tombol + Tambah Beban --}}
                    <button
                        type="button"
                        id="btn-tambah-beban"
                        onclick="openModalTambahBeban()"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-2.5 py-1.5 rounded-lg flex items-center gap-1 shadow-sm transition-colors cursor-pointer"
                    >
                        + Tambah Beban
                    </button>

                    {{-- Tombol Lihat Semua > --}}
                    <button
                        type="button"
                        id="btn-lihat-semua-beban"
                        onclick="openModalSemuaBeban()"
                        class="text-blue-600 hover:text-blue-800 text-xs font-semibold flex items-center gap-0.5 transition-colors cursor-pointer"
                    >
                        <span>Lihat Semua</span>
                        <span class="font-mono">&gt;</span>
                    </button>
                </div>
            </div>

            {{-- Tabel Beban --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-y border-slate-150 text-slate-600 font-semibold">
                            <th class="w-12 text-center py-2.5 px-3">No</th>
                            <th class="text-left py-2.5 px-3">Kategori</th>
                            <th class="text-right py-2.5 px-4 w-44">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bebanPerKategori as $index => $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="text-center py-2.5 px-3 text-slate-500 font-medium">{{ $index + 1 }}</td>
                                <td class="py-2.5 px-3 text-slate-700 font-medium">{{ $item->kategori }}</td>
                                <td class="text-right py-2.5 px-4 font-mono font-medium text-slate-800">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-slate-400">
                                    Belum ada beban operasional yang dicatat pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- Ruang kosong untuk menyamakan posisi dengan Laba Bersih --}}
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td colspan="3" class="py-2.5 px-3 border-0">
                                &nbsp;
                            </td>
                        </tr>
                    @endfor

                    {{-- Baris Total Beban Operasional --}}
                    <tr class="border-t border-slate-200" style="background-color: #f0f7ff;">
                        <td colspan="2" class="py-2.5 px-3 font-bold text-blue-600 text-xs">
                            Total Beban Operasional
                        </td>
                        <td class="py-2.5 px-4 text-right font-bold text-blue-600 font-mono text-xs">
                            Rp {{ number_format($totalBeban, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================= --}}
{{-- MODAL FORM + TAMBAH BEBAN --}}
{{-- ========================================================= --}}
<div id="modal-tambah-beban-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden transition-opacity"></div>

<div id="modal-tambah-beban" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl relative border border-slate-100 overflow-hidden transform transition-all">
        {{-- Header Modal --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/60">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <x-heroicon-o-plus class="w-4 h-4" />
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tambah Beban Operasional</h3>
            </div>
            <button
                type="button"
                onclick="closeModalTambahBeban()"
                class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        {{-- Form Tambah Beban --}}
        <form action="{{ route('laporan.laba-rugi.beban.store') }}" method="POST" id="form-tambah-beban" class="p-5 space-y-4">
            @csrf

            {{-- Tanggal Pengeluaran --}}
            <div>
                <label for="modal-beban-tanggal" class="block text-xs font-semibold text-slate-600 mb-1 font-sans">
                    Tanggal Pengeluaran <span class="text-rose-500">*</span>
                </label>
                <input
                    type="date"
                    id="modal-beban-tanggal"
                    name="tanggal"
                    value="{{ date('Y-m-d') }}"
                    required
                    class="form-input text-xs"
                >
            </div>

            {{-- Kategori Beban --}}
            <div>
                <label for="modal-beban-kategori" class="block text-xs font-semibold text-slate-600 mb-1 font-sans">
                    Kategori Pengeluaran <span class="text-rose-500">*</span>
                </label>
                <select
                    id="modal-beban-kategori"
                    name="kategori"
                    required
                    class="form-input text-xs"
                >
                    <option value="">Pilih Kategori...</option>
                    <option value="Listrik">Listrik</option>
                    <option value="Internet">Internet</option>
                    <option value="Transportasi">Transportasi</option>
                    <option value="ATK">ATK</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Gaji Karyawan">Gaji Karyawan</option>
                    <option value="Sewa Tempat">Sewa Tempat</option>
                    <option value="Operasional Apotek">Operasional Apotek</option>
                    <option value="Pemeliharaan & Alat">Pemeliharaan & Alat</option>
                    <option value="Pajak & Retribusi">Pajak & Retribusi</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>

            {{-- Nominal Pengeluaran --}}
            <div>
                <label for="modal-beban-nominal" class="block text-xs font-semibold text-slate-600 mb-1 font-sans">
                    Nominal Beban (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input
                        type="number"
                        id="modal-beban-nominal"
                        name="nominal"
                        min="1"
                        step="any"
                        placeholder="Contoh: 150000"
                        required
                        class="form-input text-xs pl-9 font-mono font-semibold"
                    >
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="modal-beban-keterangan" class="block text-xs font-semibold text-slate-600 mb-1 font-sans">
                    Keterangan (Opsional)
                </label>
                <textarea
                    id="modal-beban-keterangan"
                    name="keterangan"
                    rows="2"
                    placeholder="Contoh: Tagihan internet bulan Oktober..."
                    class="form-input text-xs"
                ></textarea>
            </div>

            {{-- Footer Tombol --}}
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button
                    type="button"
                    onclick="closeModalTambahBeban()"
                    class="btn-secondary py-2 px-4 text-xs font-semibold"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 text-xs font-semibold rounded-lg shadow-sm flex items-center gap-1.5 transition-colors cursor-pointer"
                >
                    <span>Simpan Beban</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL LIHAT SEMUA BEBAN OPERASIONAL --}}
{{-- ========================================================= --}}
<div id="modal-semua-beban-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden transition-opacity"></div>

<div id="modal-semua-beban" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl relative border border-slate-100 overflow-hidden transform transition-all max-h-[85vh] flex flex-col">
        {{-- Header Modal --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/60 shrink-0">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Daftar Lengkap Beban Operasional</h3>
                <p class="text-xs text-slate-400 mt-0.5">Periode: @tanggalId($dari) s/d @tanggalId($sampai) (Total: <strong>@rupiah($totalBeban)</strong>)</p>
            </div>
            <button
                type="button"
                onclick="closeModalSemuaBeban()"
                class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors"
                aria-label="Tutup"
            >
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        {{-- Isi Daftar Tabel --}}
        <div class="p-5 overflow-y-auto flex-1">
            <div class="border border-slate-100 rounded-xl overflow-hidden">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <th class="text-center w-10 py-2.5 px-2">No</th>
                            <th class="text-center w-28 py-2.5 px-3">Tanggal</th>
                            <th class="text-left w-36 py-2.5 px-3">Kategori</th>
                            <th class="text-left py-2.5 px-3">Keterangan</th>
                            <th class="text-right w-36 py-2.5 px-3">Nominal (Rp)</th>
                            <th class="text-center w-28 py-2.5 px-2">Pencatat</th>
                            <th class="text-center w-14 py-2.5 px-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($daftarBeban as $idx => $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="text-center text-slate-400 py-2.5 px-2">{{ $idx + 1 }}</td>
                                <td class="text-center font-mono text-slate-600 py-2.5 px-3 whitespace-nowrap">
                                    @tanggalId($item->tanggal)
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                        {{ $item->kategori }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-slate-700">
                                    {{ $item->keterangan ?: '—' }}
                                </td>
                                <td class="text-right font-mono font-bold text-orange-600 py-2.5 px-3 whitespace-nowrap">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                                <td class="text-center text-slate-500 py-2.5 px-2 text-[11px]">
                                    {{ $item->user->name ?? 'Admin' }}
                                </td>
                                <td class="text-center py-2.5 px-2">
                                    <form
                                        action="{{ route('laporan.laba-rugi.beban.destroy', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus catatan beban Rp {{ number_format($item->nominal, 0, ',', '.') }}?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                            title="Hapus beban"
                                        >
                                            <x-heroicon-o-trash class="w-4 h-4" />
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">
                                    Belum ada beban operasional yang dicatat pada rentang waktu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500 font-medium">
                Total {{ $daftarBeban->count() }} beban: <strong>Rp {{ number_format($totalBeban, 0, ',', '.') }}</strong>
            </span>
            <button
                type="button"
                onclick="closeModalSemuaBeban()"
                class="btn-secondary py-1.5 px-4 text-xs font-semibold"
            >
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- SCRIPT INTERAKSI MODAL & FILTER --}}
{{-- ========================================================= --}}
{{-- Signature Section for Print PDF --}}
<x-print-signature :apotek="$apotek" />
{{-- End of Signature Section --}}
<script>
// Toggle tampilan form custom date (sama dengan Dashboard)
function toggleCustomDate() {
    var container = document.getElementById('customDateContainer');
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        container.classList.add('flex');
    } else {
        container.classList.add('hidden');
        container.classList.remove('flex');
    }
}

function openModalTambahBeban() {
    document.getElementById('modal-tambah-beban-backdrop').classList.remove('hidden');
    document.getElementById('modal-tambah-beban').classList.remove('hidden');
    setTimeout(function() {
        var nominalInput = document.getElementById('modal-beban-nominal');
        if (nominalInput) nominalInput.focus();
    }, 100);
}

function closeModalTambahBeban() {
    document.getElementById('modal-tambah-beban-backdrop').classList.add('hidden');
    document.getElementById('modal-tambah-beban').classList.add('hidden');
}

function openModalSemuaBeban() {
    document.getElementById('modal-semua-beban-backdrop').classList.remove('hidden');
    document.getElementById('modal-semua-beban').classList.remove('hidden');
}

function closeModalSemuaBeban() {
    document.getElementById('modal-semua-beban-backdrop').classList.add('hidden');
    document.getElementById('modal-semua-beban').classList.add('hidden');
}

// Tutup modal ketika klik luar atau ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModalTambahBeban();
        closeModalSemuaBeban();
    }
});

document.getElementById('modal-tambah-beban-backdrop')?.addEventListener('click', closeModalTambahBeban);
document.getElementById('modal-semua-beban-backdrop')?.addEventListener('click', closeModalSemuaBeban);
</script>

@endsection
