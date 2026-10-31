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
                $statusPiutangText='Lunas' ;
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
                <x-empty-state colspan="10" title="Transaksi Tidak Ditemukan" description="Tidak ada data transaksi penjualan pada periode yang dipilih." />
                @endforelse
        </tbody>
    </table>

    {{-- ========================================================= --}}
    {{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
    {{-- ========================================================= --}}
    <x-print-signature :apotek="$apotek" />

</div>

<x-page-header
    title="Laporan Penjualan"
    subtitle="Analisis ringkasan transaksi dan perolehan keuntungan penjualan."
    class="print:hidden">
    <x-print-actions excel-id="export-excel-btn" />
</x-page-header>

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
            value="{{ old('dari', $dari) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal">
        <span class="inline-flex items-center text-gray-400 shrink-0">
            <x-heroicon-m-minus class="w-3.5 h-3.5" />
        </span>
        <input
            type="date"
            id="sampai"
            name="sampai"
            value="{{ old('sampai', $sampai) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir">
    </div>

    <select
        id="metode_pembayaran"
        name="metode_pembayaran"
        class="form-input"
        aria-label="Metode Pembayaran"
        title="Metode Pembayaran">
        <option value="">Metode: Semua</option>
        <option value="cash" @selected(request('metode_pembayaran')==='cash' )>Cash</option>
        <option value="qris" @selected(request('metode_pembayaran')==='qris' )>QRIS</option>
        <option value="debit" @selected(request('metode_pembayaran')==='debit' )>Debit</option>
        <option value="piutang" @selected(request('metode_pembayaran')==='piutang' )>Piutang</option>
    </select>

    <select
        id="pelanggan"
        name="pelanggan"
        class="form-input"
        aria-label="Pelanggan/Member"
        title="Pelanggan/Member">
        <option value="">Pelanggan: Semua</option>
        <option value="pelanggan_umum" @selected(request('pelanggan')==='pelanggan_umum' )>Pelanggan Umum</option>
        <option value="pelanggan_tetap" @selected(request('pelanggan')==='pelanggan_tetap' )>Member Pelanggan Tetap</option>
        <option value="keluarga_nakes" @selected(request('pelanggan')==='keluarga_nakes' )>Member Keluarga Nakes</option>
        <option value="member_only" @selected(request('pelanggan')==='member_only' )>Member Only</option>
    </select>

    <select
        id="jenis_transaksi"
        name="jenis_transaksi"
        class="form-input"
        aria-label="Jenis Transaksi"
        title="Jenis Transaksi">
        <option value="">Transaksi: Semua</option>
        <option value="non_resep" @selected(request('jenis_transaksi')==='non_resep' )>Non Resep</option>
        <option value="resep" @selected(request('jenis_transaksi')==='resep' )>Resep</option>
    </select>

    <select
        id="status_piutang"
        name="status_piutang"
        class="form-input"
        aria-label="Status Piutang"
        title="Status Piutang">
        <option value="">Piutang: Semua</option>
        <option value="lunas" @selected(request('status_piutang')==='lunas' )>Lunas</option>
        <option value="belum_lunas" @selected(request('status_piutang')==='belum_lunas' )>Belum Lunas</option>
        <option value="terlambat" @selected(request('status_piutang')==='terlambat' )>Terlambat</option>
    </select>
</x-card-filter>

<div class="filter-alert-container mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 {{ $dateError ? '' : 'hidden' }}" role="alert">
    {{ $dateError }}
</div>

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

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight"
                    style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;"
                    data-countup="true"
                    data-target="{{ (float)$jumlahTransaksi }}"
                    data-suffix=" X">
                    {{ $jumlahTransaksi }} X
                </strong>
            </div>

            {{-- Member --}}
            <div class="px-1">
                <span class="block text-[11px] sm:text-xs text-slate-500 font-medium leading-tight">
                    Transaksi Member
                </span>

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight"
                    style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;"
                    data-countup="true"
                    data-target="{{ (float)$transaksiMember }}"
                    data-suffix=" X">
                    {{ $transaksiMember }} X
                </strong>
            </div>

            {{-- Umum --}}
            <div class="px-1">
                <span class="block text-[11px] sm:text-xs text-slate-500 font-medium leading-tight">
                    Transaksi Umum
                </span>

                <strong class="mt-1 block text-xl sm:text-2xl font-extrabold text-blue-600 tracking-tight"
                    style="font-size: clamp(1.15rem, 1.6vw, 1.4rem); line-height: 1.25;"
                    data-countup="true"
                    data-target="{{ (float)$transaksiNonMember }}"
                    data-suffix=" X">
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
                style="background-color: #fff7ed; border: 1px solid #fed7aa;">
                <span class="block text-[11px] sm:text-xs font-semibold text-orange-800 leading-tight" style="color: #9a3412;">
                    Omzet Kotor
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-orange-600 tracking-tight"
                    style="color: #ea580c;"
                    data-countup="true"
                    data-target="{{ (float)$omzet }}"
                    data-prefix="Rp ">
                    Rp {{ number_format($omzet, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Total Potongan Diskon --}}
            <div
                class="w-full rounded-lg bg-purple-50/90 border border-purple-200/90 px-2.5 py-1.5 sm:py-2 flex flex-col justify-center"
                style="background-color: #faf5ff; border: 1px solid #e9d5ff;">
                <span class="block text-[11px] sm:text-xs font-semibold text-purple-800 leading-tight" style="color: #6b21a8;">
                    Total Potongan Diskon
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-purple-600 tracking-tight"
                    style="color: #9333ea;"
                    data-countup="true"
                    data-target="{{ (float)$totalDiskon }}"
                    data-prefix="Rp ">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Penjualan Bersih (Net) --}}
            <div
                class="w-full rounded-lg bg-emerald-50/80 border border-emerald-100/90 px-2.5 py-1.5 sm:py-2 flex flex-col justify-center"
                style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                <span class="block text-[11px] sm:text-xs font-semibold text-emerald-800 leading-tight" style="color: #065f46;">
                    Penjualan Bersih (Net)
                </span>

                <strong class="mt-0.5 block text-sm sm:text-base lg:text-lg font-bold text-emerald-600 tracking-tight"
                    style="color: #059669;"
                    data-countup="true"
                    data-target="{{ (float)$totalPenjualanBersih }}"
                    data-prefix="Rp ">
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
                        @php
                        $nextInvoiceDirection = ($sort === 'no_faktur' && $direction === 'asc') ? 'desc' : 'asc';
                        @endphp
                        <a
                            href="{{ request()->fullUrlWithQuery(['page' => null, 'sort' => 'no_faktur', 'direction' => $nextInvoiceDirection]) }}"
                            data-sort="invoice"
                            id="btn-sort-invoice"
                            class="inline-flex items-center justify-center gap-1.5 w-full text-[10px] !font-bold uppercase tracking-wider text-white hover:text-white/80 transition-colors cursor-pointer group focus:outline-none"
                            title="Urutkan No. Invoice">
                            <span>No. Invoice</span>
                            <span id="icon-sort-invoice" class="sort-icon-container inline-flex items-center text-white/70 group-hover:text-white">
                                <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                            </span>
                        </a>
                    </th>

                    <th scope="col" class="w-[11%] min-w-[8rem] text-center !px-3.5 py-3.5 whitespace-nowrap">
                        @php
                        $nextDateDirection = ($sort === 'tanggal' && $direction === 'asc') ? 'desc' : 'asc';
                        @endphp
                        <a
                            href="{{ request()->fullUrlWithQuery(['page' => null, 'sort' => 'tanggal', 'direction' => $nextDateDirection]) }}"
                            data-sort="tanggal"
                            id="btn-sort-tanggal"
                            class="inline-flex items-center justify-center gap-1.5 w-full text-[10px] !font-bold uppercase tracking-wider text-white hover:text-white/80 transition-colors cursor-pointer group focus:outline-none"
                            title="Urutkan Tanggal">
                            <span>Tanggal</span>
                            <span id="icon-sort-tanggal" class="sort-icon-container inline-flex items-center text-white/70 group-hover:text-white">
                                <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                            </span>
                        </a>
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
                    data-tanggal="{{ $penjualan->tanggal ? $penjualan->tanggal->format('Y-m-d') : '' }}">
                    {{-- No. Invoice --}}
                    <td class="align-middle text-center font-semibold text-gray-800 font-mono text-xs !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                        <a
                            href="{{ route('penjualan.show', $penjualan) . '?from=laporan_penjualan&' . http_build_query(request()->query()) }}"
                            class="font-semibold text-blue-600 hover:text-blue-800 hover:underline print:text-gray-800">
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
                            'Member Keluarga Nakes' => 'indigo',
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
                            $statusPiutang='Lunas' ;
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
                <x-empty-state colspan="9" title="Transaksi Tidak Ditemukan" description="Tidak ada data transaksi pada filter yang dipilih." />
                @endforelse

            </tbody>

        </table>
    </div>

    {{-- Pagination & Informasi Data --}}
    <div class="pagination-container mt-4 print:hidden">
        {{ $penjualans->links('vendor.pagination.apotek') }}
    </div>

</div>

<script>
    function printReport() {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function(el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
        setTimeout(function() {
            window.print();
        }, 50);
    }

    window.addEventListener('beforeprint', function() {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function(el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
    });
</script>
@endsection