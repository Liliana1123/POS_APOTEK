@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    }
</style>

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp


{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP, JUDUL, TABEL PER BARANG) --}}
{{-- ========================================================= --}}
<div class="print-only">

    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="LAPORAN HASIL PENJUALAN">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }}
        {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        @if(request('metode_pembayaran')) &nbsp;•&nbsp; Metode: {{ ucfirst(request('metode_pembayaran')) }} @endif
        @if(request('jenis_transaksi')) &nbsp;•&nbsp; Jenis: {{ str_replace('_', ' ', ucfirst(request('jenis_transaksi'))) }} @endif
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>

    {{-- TABEL CETAK: 1 baris = 1 transaksi penjualan sesuai 10 kolom wajib --}}
    <table class="table-custom table-penjualan print-table w-full">
        <thead class="table-custom-header">
            <tr>
                <th scope="col" class="col-penjualan-tanggal w-[9%] text-center !px-2 py-3 whitespace-nowrap">Tanggal</th>
                <th scope="col" class="col-penjualan-faktur w-[13%] text-center !px-2 py-3 whitespace-nowrap">No. Invoice</th>
                <th scope="col" class="col-penjualan-pelanggan w-[14%] !px-2.5 py-3 break-words">Nama Pelanggan</th>
                <th scope="col" class="col-penjualan-jenis-pelanggan w-[12%] text-center !px-2 py-3 break-words">Jenis Pelanggan</th>
                <th scope="col" class="col-penjualan-kasir w-[9%] text-center !px-2 py-3 whitespace-nowrap">Kasir</th>
                <th scope="col" class="col-penjualan-jenis w-[9%] text-center !px-2 py-3 whitespace-nowrap">Jenis Transaksi</th>
                <th scope="col" class="col-penjualan-metode w-[9%] text-center !px-2 py-3 whitespace-nowrap">Metode Pembayaran</th>
                <th scope="col" class="col-penjualan-status-piutang w-[8%] text-center !px-2 py-3 whitespace-nowrap">Status Piutang</th>
                <th scope="col" class="col-penjualan-kotor text-right w-[8.5%] !px-2.5 py-3 whitespace-nowrap">Total Kotor</th>
                <th scope="col" class="col-penjualan-total text-right w-[8.5%] !px-2.5 py-3 whitespace-nowrap">Total Transaksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
            @forelse($penjualans as $index => $penjualan)
                @php
                    $diskonFaktur = $penjualan->detail->sum('diskon');
                    $totalKotor = $penjualan->total + $diskonFaktur;

                    $jenisPelanggan = 'Pelanggan Umum';
                    if ($penjualan->pelanggan && $penjualan->pelanggan->is_member) {
                        $jenisPelanggan = $penjualan->pelanggan->status_member ?: 'Member Pelanggan Tetap';
                    }

                    $jenisTransaksi = match($penjualan->jenis_transaksi) {
                        'resep' => 'Resep',
                        'non_resep' => 'Non Resep',
                        default => '—'
                    };

                    $metodeLabel = match(strtolower((string)$penjualan->metode_pembayaran)) {
                        'cash' => 'Cash',
                        'qris' => 'QRIS',
                        'debit' => 'Debit',
                        'piutang' => 'Piutang',
                        default => ucfirst((string)($penjualan->metode_pembayaran ?? '—')),
                    };

                    $statusPiutangText = '—';
                    if ($penjualan->metode_pembayaran === 'piutang') {
                        $totalDibayar = $penjualan->pembayaranPiutang->sum('jumlah');
                        $sisaPiutang = max(0, $penjualan->total - $totalDibayar);
                        if ($sisaPiutang <= 0) {
                            $statusPiutangText = 'Lunas';
                        } elseif ($penjualan->due_date && $penjualan->due_date->isPast()) {
                            $statusPiutangText = 'Terlambat';
                        } else {
                            $statusPiutangText = 'Belum Lunas';
                        }
                    }
                @endphp
                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    {{-- 1. Tanggal --}}
                    <td class="col-penjualan-tanggal text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">
                        @tanggalId($penjualan->tanggal)
                    </td>

                    {{-- 2. No. Invoice --}}
                    <td class="col-penjualan-faktur text-center font-semibold text-gray-800 font-mono text-xs !px-2 py-3 whitespace-nowrap">
                        {{ $penjualan->no_faktur }}
                    </td>

                    {{-- 3. Nama Pelanggan --}}
                    <td class="col-penjualan-pelanggan font-medium text-gray-800 text-xs !px-2.5 py-3 break-words leading-snug">
                        {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                    </td>

                    {{-- 4. Jenis Pelanggan --}}
                    <td class="col-penjualan-jenis-pelanggan text-gray-600 font-medium text-xs !px-2 py-3 break-words leading-snug text-center">
                        {{ $jenisPelanggan }}
                    </td>

                    {{-- 5. Kasir --}}
                    <td class="col-penjualan-kasir text-gray-600 font-medium text-xs !px-2 py-3 whitespace-nowrap text-center">
                        {{ $penjualan->user->name ?? 'Admin' }}
                    </td>

                    {{-- 6. Jenis Transaksi --}}
                    <td class="col-penjualan-jenis text-gray-600 font-medium text-xs !px-2 py-3 whitespace-nowrap text-center">
                        {{ $jenisTransaksi }}
                    </td>

                    {{-- 7. Metode Pembayaran --}}
                    <td class="col-penjualan-metode text-gray-600 font-medium text-xs !px-2 py-3 whitespace-nowrap text-center">
                        {{ $metodeLabel }}
                    </td>

                    {{-- 8. Status Piutang --}}
                    <td class="col-penjualan-status-piutang font-medium text-xs !px-2 py-3 whitespace-nowrap text-center {{ $statusPiutangText === 'Terlambat' ? 'text-red-600 font-semibold' : ($statusPiutangText === 'Lunas' ? 'text-emerald-600 font-semibold' : 'text-gray-600') }}">
                        {{ $statusPiutangText }}
                    </td>

                    {{-- 9. Total Kotor --}}
                    <td class="col-penjualan-kotor table-num text-gray-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">
                        Rp {{ number_format($totalKotor, 0, ',', '.') }}
                    </td>

                    {{-- 10. Total Transaksi --}}
                    <td class="col-penjualan-total table-num font-bold text-gray-800 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">
                        Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">Transaksi Tidak Ditemukan</div>
                            <div class="empty-state-desc">Tidak ada data transaksi penjualan pada periode yang dipilih.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
            <tr>
                <td colspan="8" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600 font-semibold">Total Akumulasi:</td>
                <td class="table-num !px-2.5 py-3.5 text-right text-gray-800 font-mono text-xs font-bold whitespace-nowrap">Rp {{ number_format($omzet, 0, ',', '.') }}</td>
                <td class="table-num !px-2.5 py-3.5 text-right text-emerald-700 font-mono text-xs font-bold whitespace-nowrap">Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ========================================================= --}}
    {{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
    {{-- ========================================================= --}}
    <x-print-signature :apotek="$apotek" />

</div>

<!-- Page Header (layar) -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
                <div>
                    <h1>Laporan Penjualan</h1>
                    <p class="text-caption mt-1">Analisis ringkasan transaksi dan perolehan keuntungan penjualan.</p>
                </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-2 shrink-0">
        {{-- Ekspor Excel --}}
        <a
            id="export-excel-btn"
            href="{{ route('laporan.penjualan', array_merge(request()->query(), ['export' => 'excel', 'sort' => request('sort', $sort ?? 'tanggal'), 'direction' => request('direction', $direction ?? 'asc')])) }}"
            class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5"
        >
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>

        {{-- Cetak Laporan --}}
        <button
            type="button"
            onclick="printReport()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>

    </div>

</div>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.penjualan')"
    :reset-url="route('laporan.penjualan')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
    :has-reset="request()->anyFilled(['dari', 'sampai', 'metode_pembayaran', 'pelanggan', 'jenis_transaksi', 'status_piutang'])"
    :live-search="true">
    <input type="hidden" name="sort" id="filter-sort-input" value="{{ request('sort', $sort ?? 'tanggal') }}">
    <input type="hidden" name="direction" id="filter-direction-input" value="{{ request('direction', $direction ?? 'asc') }}">

    <div class="flex items-center gap-1.5">
        <input
            type="date"
            id="dari"
            name="dari"
            value="{{ $dari }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="text-sm text-gray-400">-</span>
        <input
            type="date"
            id="sampai"
            name="sampai"
            value="{{ $sampai }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>

    <select
        id="metode_pembayaran"
        name="metode_pembayaran"
        class="form-input"
        aria-label="Metode Pembayaran"
        title="Metode Pembayaran"
    >
        <option value="">Metode: Semua</option>
        <option value="cash" @selected(request('metode_pembayaran') === 'cash')>Cash</option>
        <option value="qris" @selected(request('metode_pembayaran') === 'qris')>QRIS</option>
        <option value="debit" @selected(request('metode_pembayaran') === 'debit')>Debit</option>
        <option value="piutang" @selected(request('metode_pembayaran') === 'piutang')>Piutang</option>
    </select>

    <select
        id="pelanggan"
        name="pelanggan"
        class="form-input"
        aria-label="Pelanggan/Member"
        title="Pelanggan/Member"
    >
        <option value="">Pelanggan: Semua</option>
        <option value="pelanggan_umum" @selected(request('pelanggan') === 'pelanggan_umum')>Pelanggan Umum</option>
        <option value="pelanggan_tetap" @selected(request('pelanggan') === 'pelanggan_tetap')>Member Pelanggan Tetap</option>
        <option value="keluarga_nakes" @selected(request('pelanggan') === 'keluarga_nakes')>Member Keluarga Nakes</option>
        <option value="member_only" @selected(request('pelanggan') === 'member_only')>Member Only</option>
    </select>

    <select
        id="jenis_transaksi"
        name="jenis_transaksi"
        class="form-input"
        aria-label="Jenis Transaksi"
        title="Jenis Transaksi"
    >
        <option value="">Transaksi: Semua</option>
        <option value="non_resep" @selected(request('jenis_transaksi') === 'non_resep')>Non Resep</option>
        <option value="resep" @selected(request('jenis_transaksi') === 'resep')>Resep</option>
    </select>

    <select
        id="status_piutang"
        name="status_piutang"
        class="form-input"
        aria-label="Status Piutang"
        title="Status Piutang"
    >
        <option value="">Piutang: Semua</option>
        <option value="lunas" @selected(request('status_piutang') === 'lunas')>Lunas</option>
        <option value="belum_lunas" @selected(request('status_piutang') === 'belum_lunas')>Belum Lunas</option>
        <option value="terlambat" @selected(request('status_piutang') === 'terlambat')>Terlambat</option>
    </select>
</x-card-filter>

<!-- Summary Cards (layar) -->
<div class="laporan-summary-cards grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-3.5 mb-4 print:hidden">

    {{-- CARD 1: Ringkasan Transaksi --}}
    <div class="card-base p-3 sm:p-3.5 flex flex-col justify-between">

        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="flex h-7 w-7 sm:h-7.5 sm:w-7.5 items-center justify-center rounded-lg bg-blue-50 text-blue-600 shrink-0">
                <x-heroicon-o-clipboard-document-list class="h-4 w-4" />
            </div>

            <h2 class="text-xs sm:text-sm font-bold text-slate-800">
                Ringkasan Transaksi
            </h2>
        </div>

        <div class="grid grid-cols-3 gap-1.5 sm:gap-2 text-center items-center py-0.5">

            {{-- Jumlah Transaksi --}}
            <div class="px-1">
                <span class="block text-[11px] sm:text-xs text-slate-500 font-medium leading-tight">
                    Jumlah Transaksi
                </span>

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight" style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;">
                    {{ $jumlahTransaksi }} X
                </strong>
            </div>

            {{-- Member --}}
            <div class="px-1">
                <span class="block text-[11px] sm:text-xs text-slate-500 font-medium leading-tight">
                    Transaksi Member
                </span>

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight" style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;">
                    {{ $transaksiMember }} X
                </strong>
            </div>

            {{-- Umum --}}
            <div class="px-1">
                <span class="block text-[11px] sm:text-xs text-slate-500 font-medium leading-tight">
                    Transaksi Umum
                </span>

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight" style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;">
                    {{ $transaksiNonMember }} X
                </strong>
            </div>

        </div>

    </div>

    {{-- CARD 2: Perolehan Keuntungan --}}
    <div class="card-base p-3 sm:p-3.5 flex flex-col justify-between">

       <div class="flex items-center justify-center gap-2 mb-2">
            <div class="flex h-7 w-7 sm:h-7.5 sm:w-7.5 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                <x-heroicon-o-circle-stack class="h-4 w-4" />
            </div>

            <h2 class="text-xs sm:text-sm font-bold text-slate-800">
                Perolehan Keuntungan
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-2.5 items-stretch text-center">

            {{-- Omzet Kotor --}}
            <div
                class="w-full rounded-lg bg-orange-50/90 border border-orange-200/90 px-2.5 py-1.5 sm:py-2 flex flex-col justify-center"
                style="background-color: #fff7ed; border: 1px solid #fed7aa;"
            >
                <span class="block text-[11px] sm:text-xs font-semibold text-orange-800 leading-tight" style="color: #9a3412;">
                    Omzet Kotor
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-orange-600 tracking-tight" style="color: #ea580c;">
                    Rp {{ number_format($omzet, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Total Potongan Diskon --}}
            <div
                class="w-full rounded-lg bg-purple-50/90 border border-purple-200/90 px-2.5 py-1.5 sm:py-2 flex flex-col justify-center"
                style="background-color: #faf5ff; border: 1px solid #e9d5ff;"
            >
                <span class="block text-[11px] sm:text-xs font-semibold text-purple-800 leading-tight" style="color: #6b21a8;">
                    Total Potongan Diskon
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-purple-600 tracking-tight" style="color: #9333ea;">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Penjualan Bersih (Net) --}}
            <div
                class="w-full rounded-lg bg-emerald-50/80 border border-emerald-100/90 px-2.5 py-1.5 sm:py-2 flex flex-col justify-center"
                style="background-color: #ecfdf5; border: 1px solid #a7f3d0;"
            >
                <span class="block text-[11px] sm:text-xs font-semibold text-emerald-800 leading-tight" style="color: #065f46;">
                    Penjualan Bersih (Net)
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-emerald-600 tracking-tight" style="color: #059669;">
                    Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                </strong>
            </div>

        </div>

    </div>

</div>

<!-- Data Table Card (Layar) -->
<div id="tabel-laporan-layar" class="table-custom-container print:hidden">
    <div class="overflow-x-auto">
        <table class="table-custom table-penjualan w-full min-w-[76rem]">

            {{-- Table Header --}}
            <thead class="table-custom-header">
            <tr>

                <th scope="col" class="w-[14%] min-w-[8rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    <button
                        type="button"
                        data-sort="invoice"
                        id="btn-sort-invoice"
                        class="inline-flex items-center justify-center gap-1.5 w-full text-[10px] !font-bold uppercase tracking-wider text-white hover:text-white/80 transition-colors cursor-pointer group focus:outline-none"
                        title="Urutkan No. Invoice"
                    >
                        <span>No. Invoice</span>
                        <span id="icon-sort-invoice" class="sort-icon-container inline-flex items-center text-white/70 group-hover:text-white">
                            <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                        </span>
                    </button>
                </th>

                <th scope="col" class="w-[11%] min-w-[8rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    <button
                        type="button"
                        data-sort="tanggal"
                        id="btn-sort-tanggal"
                        class="inline-flex items-center justify-center gap-1.5 w-full text-[10px] !font-bold uppercase tracking-wider text-white hover:text-white/80 transition-colors cursor-pointer group focus:outline-none"
                        title="Urutkan Tanggal"
                    >
                        <span>Tanggal</span>
                        <span id="icon-sort-tanggal" class="sort-icon-container inline-flex items-center text-white/70 group-hover:text-white">
                            <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                        </span>
                    </button>
                </th>

                <th scope="col" class="w-[18%] min-w-[12rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    Nama Pelanggan
                </th>

                <th scope="col" class="w-[14%] min-w-[8rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    Kasir
                </th>

                <th scope="col" class="w-[11%] min-w-[8.5rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    Jenis Transaksi
                </th>

                <th scope="col" class="w-[10%] min-w-[9.5rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    Metode Pembayaran
                </th>

                <th scope="col" class="w-[9%] min-w-[8rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                    Status Piutang
                </th>

                <th scope="col" class="w-[12%] min-w-[8rem] !text-left !px-3.5 py-3.5 whitespace-nowrap">
                    Total Kotor
                </th>

                <th scope="col" class="w-[12%] min-w-[8.5rem] !text-left !px-3.5 py-3.5 whitespace-nowrap">
                    Total Transaksi
                </th>

            </tr>
        </thead>

            {{-- Table Body --}}
            <tbody id="table-laporan-body" class="table-custom-body">

                @forelse ($penjualans as $index => $penjualan)

                    @php
                        $diskonFaktur = $penjualan->detail->sum('diskon');
                    @endphp

                    <tr
                        class="penjualan-row {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}"
                        data-row-index="{{ $index }}"
                        data-invoice="{{ $penjualan->no_faktur }}"
                        data-tanggal="{{ $penjualan->tanggal ? $penjualan->tanggal->format('Y-m-d') : '' }}"
                    >
                        {{-- No. Invoice --}}
                        <td class="align-middle text-center font-semibold text-gray-800 font-mono text-xs !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            <a
                                href="{{ route('penjualan.show', $penjualan) . '?from=laporan_penjualan&' . http_build_query(request()->query()) }}"
                                class="font-semibold text-blue-600 hover:text-blue-800 hover:underline print:text-gray-800"
                            >
                                {{ $penjualan->no_faktur }}
                            </a>
                        </td>

                        {{-- Tanggal --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            @tanggalId($penjualan->tanggal)
                        </td>

                        {{-- Pelanggan --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            <div class="flex flex-col items-center gap-1 min-w-0">

                                {{-- Nama Pelanggan --}}
                                <span class="font-semibold text-gray-800 text-xs truncate">
                                    {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                                </span>

                                {{-- Status Pelanggan --}}
                                @if($penjualan->pelanggan && $penjualan->pelanggan->is_member)

                                    @php
                                        $statusMember = trim((string) $penjualan->pelanggan->status_member);

                                        $memberVariant = match($statusMember) {
                                            'Member Keluarga Nakes' => 'info',
                                            'Member Only' => 'warning',
                                            default => 'success',
                                        };
                                    @endphp

                                    <x-badge :variant="$memberVariant" class="whitespace-nowrap w-fit">
                                        {{ $statusMember ?: 'Member Pelanggan Tetap' }}
                                    </x-badge>

                                @else

                                    <x-badge variant="secondary" class="whitespace-nowrap w-fit">
                                        Pelanggan Umum
                                    </x-badge>

                                @endif

                            </div>
                        </td>

                        {{-- Kasir --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            {{ $penjualan->user->name ?? 'Admin' }}
                        </td>

                        {{-- Jenis Transaksi --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            <span class="font-medium text-gray-700">
                                @if($penjualan->jenis_transaksi === 'resep')
                                    Resep
                                @elseif($penjualan->jenis_transaksi === 'non_resep')
                                    Non Resep
                                @else
                                    —
                                @endif
                            </span>
                        </td>

                        {{-- Metode Pembayaran --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            <span class="font-medium text-gray-700">
                                @switch($penjualan->metode_pembayaran)
                                    @case('cash')
                                        Cash
                                        @break

                                    @case('qris')
                                        QRIS
                                        @break

                                    @case('debit')
                                        Debit
                                        @break

                                    @case('piutang')
                                        Piutang
                                        @break

                                    @default
                                        —
                                @endswitch
                            </span>
                        </td>

                        {{-- Status Piutang --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">

                            @if($penjualan->metode_pembayaran !== 'piutang')

                                <span class="text-gray-400">—</span>

                            @else

                                @php
                                    $totalDibayar = $penjualan->pembayaranPiutang->sum('jumlah');
                                    $sisaPiutang = max(0, $penjualan->total - $totalDibayar);

                                    if ($sisaPiutang <= 0) {
                                        $statusPiutang = 'Lunas';
                                    } elseif (
                                        $penjualan->due_date &&
                                        $penjualan->due_date->isPast()
                                    ) {
                                        $statusPiutang = 'Terlambat';
                                    } else {
                                        $statusPiutang = 'Belum Lunas';
                                    }
                                @endphp

                                @if($statusPiutang === 'Lunas')
                                    <span class="font-semibold text-emerald-600">
                                        Lunas
                                    </span>
                                @elseif($statusPiutang === 'Terlambat')
                                    <span class="font-semibold text-red-600">
                                        Terlambat
                                    </span>
                                @else
                                    <span class="font-semibold text-amber-600">
                                        Belum Lunas
                                    </span>
                                @endif

                            @endif

                        </td>

                        {{-- Total Kotor --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            Rp {{ number_format($penjualan->total + $diskonFaktur, 0, ',', '.') }}
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    Transaksi Tidak Ditemukan
                                </div>

                                <div class="empty-state-desc">
                                    Tidak ada data transaksi pada filter yang dipilih.
                                </div>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

            {{-- Table Footer: Total Akumulasi --}}
            @if($penjualans->count() > 0)
                <tfoot>
                    <tr class="bg-gray-50/75 border-t border-gray-200 font-bold text-xs align-middle">
                        <td colspan="7" class="!px-3.5 py-3.5 text-right uppercase tracking-wider text-gray-600 font-semibold">
                            Total Akumulasi
                        </td>

                        <td class="table-num !px-3.5 py-3.5 !text-left text-gray-800 font-mono text-sm font-bold whitespace-nowrap">
                            Rp {{ number_format($omzet, 0, ',', '.') }}
                        </td>

                        <td class="table-num !px-3.5 py-3.5 !text-left text-blue-700 font-mono text-sm font-bold whitespace-nowrap">
                            Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            @endif

        </table>
    </div>

    {{-- Pagination & Informasi Data --}}
    @if($penjualans->count() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100 bg-white px-4 sm:px-5 py-3.5 print:hidden">

            <div class="text-xs text-slate-500">
                Menampilkan
                <span id="page-item-count" class="font-semibold text-slate-700">
                    {{ min(15, $penjualans->count()) }}
                </span>
                dari
                <span class="font-semibold text-slate-700">
                    {{ $penjualans->count() }}
                </span>
                data
            </div>

            <div id="pagination-controls" class="flex items-center gap-1">
                {{-- Di-render dinamis oleh JavaScript --}}
            </div>

        </div>
    @endif

</div>

<script>
function initPenjualanTablePagination() {
    const tableBody = document.getElementById('table-laporan-body');
    let rows = Array.from(document.querySelectorAll('.penjualan-row'));
    const totalRows = rows.length;
    const pageSize = 15; // 15 baris per halaman
    const totalPages = Math.ceil(totalRows / pageSize);
    let currentPage = 1;

    const paginationContainer = document.getElementById('pagination-controls');
    const pageItemCountEl = document.getElementById('page-item-count');

    if (!tableBody || totalRows === 0) {
        if (paginationContainer) paginationContainer.innerHTML = '';
        return;
    }

    // Sorting state (default tanggal ascending)
    const urlParams = new URLSearchParams(window.location.search);
    let currentSort = urlParams.get('sort') || @json($sort ?? 'tanggal');
    let currentDirection = (urlParams.get('direction') || @json($direction ?? 'asc')).toLowerCase();

    if (!['invoice', 'no_faktur', 'tanggal'].includes(currentSort)) {
        currentSort = 'tanggal';
    }
    if (currentSort === 'no_faktur') {
        currentSort = 'invoice';
    }
    if (!['asc', 'desc'].includes(currentDirection)) {
        currentDirection = 'asc';
    }

    const iconUpDown = '<svg class="h-3.5 w-3.5 text-white/60 group-hover:text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.25 9.24a.75.75 0 011.1.02L10 15.148l2.7-2.888a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.05-1.04z" clip-rule="evenodd" /></svg>';
    const iconUp = '<svg class="h-3.5 w-3.5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z" clip-rule="evenodd" /></svg>';
    const iconDown = '<svg class="h-3.5 w-3.5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>';

    function sortRows(sortKey, direction) {
        if (!tableBody || rows.length === 0) return;

        rows.sort((a, b) => {
            if (sortKey === 'invoice') {
                const valA = a.dataset.invoice || '';
                const valB = b.dataset.invoice || '';
                const cmp = valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' });
                if (cmp !== 0) {
                    return direction === 'asc' ? cmp : -cmp;
                }
                const dateA = a.dataset.tanggal || '';
                const dateB = b.dataset.tanggal || '';
                return dateA.localeCompare(dateB);
            } else {
                // tanggal
                const dateA = a.dataset.tanggal || '';
                const dateB = b.dataset.tanggal || '';
                const dateCmp = dateA.localeCompare(dateB);
                if (dateCmp !== 0) {
                    return direction === 'asc' ? dateCmp : -dateCmp;
                }
                const invA = a.dataset.invoice || '';
                const invB = b.dataset.invoice || '';
                return invA.localeCompare(invB, undefined, { numeric: true, sensitivity: 'base' });
            }
        });

        rows.forEach((row, idx) => {
            tableBody.appendChild(row);
            row.classList.remove('bg-white', 'bg-gray-200');
            row.classList.add(idx % 2 === 0 ? 'bg-white' : 'bg-gray-200');
        });
    }

    function updateSortVisuals() {
        const iconInvoice = document.getElementById('icon-sort-invoice');
        const iconTanggal = document.getElementById('icon-sort-tanggal');

        if (iconInvoice) {
            if (currentSort === 'invoice') {
                iconInvoice.innerHTML = currentDirection === 'asc' ? iconUp : iconDown;
            } else {
                iconInvoice.innerHTML = iconUpDown;
            }
        }

        if (iconTanggal) {
            if (currentSort === 'tanggal') {
                iconTanggal.innerHTML = currentDirection === 'asc' ? iconUp : iconDown;
            } else {
                iconTanggal.innerHTML = iconUpDown;
            }
        }

        const exportBtn = document.getElementById('export-excel-btn') || document.querySelector('a[href*="export=excel"]');
        if (exportBtn) {
            try {
                const url = new URL(exportBtn.href, window.location.origin);
                url.searchParams.set('sort', currentSort === 'invoice' ? 'no_faktur' : 'tanggal');
                url.searchParams.set('direction', currentDirection);
                exportBtn.href = url.pathname + url.search;
            } catch (e) {
                // ignore
            }
        }

        const hiddenSort = document.getElementById('filter-sort-input');
        const hiddenDir = document.getElementById('filter-direction-input');
        if (hiddenSort) hiddenSort.value = currentSort === 'invoice' ? 'no_faktur' : 'tanggal';
        if (hiddenDir) hiddenDir.value = currentDirection;

        try {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', currentSort === 'invoice' ? 'no_faktur' : 'tanggal');
            currentUrl.searchParams.set('direction', currentDirection);
            window.history.replaceState({}, '', currentUrl.toString());
        } catch (e) {
            // ignore
        }
    }

    const btnInvoice = document.getElementById('btn-sort-invoice');
    if (btnInvoice) {
        btnInvoice.onclick = function () {
            if (currentSort === 'invoice') {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = 'invoice';
                currentDirection = 'asc';
            }
            sortRows(currentSort, currentDirection);
            updateSortVisuals();
            renderPage(1);
        };
    }

    const btnTanggal = document.getElementById('btn-sort-tanggal');
    if (btnTanggal) {
        btnTanggal.onclick = function () {
            if (currentSort === 'tanggal') {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = 'tanggal';
                currentDirection = 'asc';
            }
            sortRows(currentSort, currentDirection);
            updateSortVisuals();
            renderPage(1);
        };
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const end = start + pageSize;

        rows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const visibleCount = Math.min(end, totalRows) - start;
        if (pageItemCountEl) {
            pageItemCountEl.textContent = visibleCount;
        }

        renderPaginationButtons();
    }

    function renderPaginationButtons() {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
            currentPage === 1
                ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
        }`;
        prevBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.08 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) renderPage(currentPage - 1);
        };
        paginationContainer.appendChild(prevBtn);

        for (let p = 1; p <= totalPages; p++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
                p === currentPage
                    ? 'bg-blue-600 text-white border-blue-600 shadow-2xs font-bold'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
            }`;
            pageBtn.textContent = p;
            pageBtn.onclick = () => renderPage(p);
            paginationContainer.appendChild(pageBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
            currentPage === totalPages
                ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
        }`;
        nextBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) renderPage(currentPage + 1);
        };
        paginationContainer.appendChild(nextBtn);
    }

    sortRows(currentSort, currentDirection);
    updateSortVisuals();
    renderPage(1);
}

document.addEventListener('DOMContentLoaded', initPenjualanTablePagination);
document.addEventListener('live-filter:updated', initPenjualanTablePagination);
</script>

<script>
    function printReport() {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
        setTimeout(function () {
            window.print();
        }, 50);
    }

    window.addEventListener('beforeprint', function () {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
    });
</script>
@endsection

