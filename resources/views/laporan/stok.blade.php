@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

<style>
    /* =========================
       PRINT TEMPLATE
    ========================= */

    @media print {

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        /* Sembunyikan elemen navigasi */
        aside,
        nav,
        header,
        [role="navigation"],
        .print\:hidden,
        .no-print {
            display: none !important;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #000 !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }

        main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }

        .card-base {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        /* =========================
           KOP APOTEK
        ========================= */

        .print-kop {
            display: flex !important;
            position: relative;
            align-items: center;
            justify-content: center;

            min-height: 82px;
            padding-bottom: 10px;
            margin-bottom: 15px;

            border-bottom: 2px solid #222;
        }

        .print-kop-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);

            width: 65px;
            height: 65px;

            object-fit: contain;
            object-position: center;
        }

        .print-kop-info {
            width: 100%;
            text-align: center;
        }

        .print-kop-info h1 {
            margin: 0 0 4px;

            font-size: 18px;
            line-height: 1.2;
            font-weight: 700;

            text-transform: uppercase;
            color: #111;
        }

        .print-kop-info p {
            margin: 2px 0;

            font-size: 10px;
            line-height: 1.3;

            color: #333;
        }

        /* =========================
           JUDUL LAPORAN
        ========================= */

        .print-report-title {
            display: block !important;

            text-align: center;
            margin: 8px 0 15px;
        }

        .print-report-title h2 {
            margin: 0;

            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
        }

        .print-report-title p {
            margin: 4px 0 0;

            font-size: 9px;
            color: #555;
        }

        /* =========================
           SECTION LAPORAN
        ========================= */

        .print-section {
            margin-bottom: 18px !important;
            page-break-inside: auto;
        }

        .print-section-header {
            display: flex !important;
            justify-content: space-between;
            align-items: flex-end;

            margin-bottom: 7px;
        }

        .print-section-header h3 {
            margin: 0;

            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .print-section-header p {
            margin: 2px 0 0;

            font-size: 8px;
            color: #555;
        }

        .print-section-count {
            font-size: 8px;
            color: #555;
            white-space: nowrap;
        }

        /* =========================
           TABEL PRINT
        ========================= */

        .print-table-wrapper {
            width: 100% !important;
            overflow: visible !important;
        }

        .print-table {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;

            table-layout: fixed !important;
            border-collapse: collapse !important;

            font-size: 8px !important;
        }

        .print-table th {
            padding: 6px 4px !important;

            background: #2563eb !important;
            color: #fff !important;

            border: 1px solid #1d4ed8 !important;

            font-size: 7px !important;
            font-weight: 700 !important;

            text-align: center !important;
            vertical-align: middle !important;

            white-space: normal !important;
        }

        .print-table td {
            padding: 5px 4px !important;

            border: 1px solid #bbb !important;

            font-size: 8px !important;

            vertical-align: middle !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
        }

        .print-table tbody tr {
            page-break-inside: avoid !important;
        }

        .print-table thead {
            display: table-header-group !important;
        }

        .print-table tfoot {
            display: table-row-group !important;
        }

        /* Kolom */
        .print-table .col-no {
            width: 4%;
        }

        .print-table .col-barang {
            width: 14%;
        }

        .print-table .col-kategori {
            width: 11%;
        }

        .print-table .col-batch {
            width: 8%;
        }

        .print-table .col-rak {
            width: 7%;
        }

        .print-table .col-expired {
            width: 9%;
        }

        .print-table .col-status-expired {
            width: 11%;
        }

        .print-table .col-number {
            width: 7%;
        }

        .print-table .col-status-stok {
            width: 8%;
        }

        /* Warna status tetap terlihat saat print */
        .print-table .badge-danger {
            color: #b91c1c !important;
            background: #fee2e2 !important;
            border: 1px solid #fecaca !important;
        }

        .print-table .badge-warning,
        .print-table .badge-orange {
            color: #92400e !important;
            background: #fef3c7 !important;
            border: 1px solid #fde68a !important;
        }

        .print-table .badge-success {
            color: #166534 !important;
            background: #dcfce7 !important;
            border: 1px solid #bbf7d0 !important;
        }

        .print-table .badge-secondary {
            color: #374151 !important;
            background: #f3f4f6 !important;
            border: 1px solid #d1d5db !important;
        }

        /* =========================
           TOTAL
        ========================= */

        .print-table tfoot td {
            padding: 7px 4px !important;
            font-weight: 700 !important;
            border-top: 2px solid #222 !important;
        }
        .print-hide {
            display: none !important;
        }
    }

    /* Jangan tampil di layar biasa */
    .print-only {
        display: none;
    }

    @media print {
        .print-only {
            display: block !important;
        }
    }

    /* =========================
    TANDA TANGAN
    ========================= */
    .print-signature {
        margin-top: 45px;
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .print-signature-box {
        text-align: center;
        font-size: 10px;
        color: #111;
    }

    .print-signature-box p {
        margin: 3px 0;
    }

    .print-signature-space {
        height: 65px;
    }

    .print-signature-name {
        font-weight: 700;
        text-decoration: underline;
    }
</style>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Monitoring Stok & Kadaluarsa</h1>

        <p class="text-caption mt-1">
            Analisis ketersediaan stok obat serta deteksi dini batch kadaluarsa.
        </p>
    </div>

    <div class="flex gap-2 shrink-0">
        <a
            href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'excel'])) }}"
            class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5"
        >
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>

        <button
            onclick="window.print()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.stok') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="nama" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Nama Barang
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        value="{{ request('nama') }}"
                        placeholder="Cari nama barang..."
                        class="form-input pr-8"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label for="kategori_id" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Kategori
                </label>
                <select
                    name="kategori_id"
                    id="kategori_id"
                    class="form-input"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            @selected(request('kategori_id') == $kategori->id)
                        >
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_expired" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Kadaluarsa
                </label>
                <select
                    name="status_expired"
                    id="status_expired"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="kadaluarsa" @selected(request('status_expired') === 'kadaluarsa')>
                        Kadaluarsa
                    </option>
                    <option value="1_bulan" @selected(request('status_expired') === '1_bulan')>
                        ≤ 1 Bulan
                    </option>
                    <option value="3_bulan" @selected(request('status_expired') === '3_bulan')>
                        ≤ 3 Bulan
                    </option>
                    <option value="normal" @selected(request('status_expired') === 'normal')>
                        Normal
                    </option>
                    <option value="tidak_ada" @selected(request('status_expired') === 'tidak_ada')>
                        Tidak Ada Tanggal
                    </option>
                </select>
            </div>

            <div>
                <label for="status_stok" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Stok
                </label>
                <select
                    name="status_stok"
                    id="status_stok"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="habis" @selected(request('status_stok') === 'habis')>
                        Habis
                    </option>
                    <option value="menipis" @selected(request('status_stok') === 'menipis')>
                        Menipis
                    </option>
                    <option value="aman" @selected(request('status_stok') === 'aman')>
                        Aman
                    </option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['nama', 'barang', 'kategori_id', 'supplier_id', 'batch', 'no_batch', 'status_expired', 'status_stok', 'tanggal', 'dari', 'sampai']))
                <a
                    href="{{ route('laporan.stok') }}"
                    class="btn-secondary py-1.5 px-4 flex items-center justify-center"
                >
                    Reset
                </a>
            @endif

            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>

        </div>
    </form>
</div>

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK --}}
{{-- ========================================================= --}}

<div class="print-only">

    {{-- KOP APOTEK --}}
    <div class="print-kop">

        {{-- LOGO --}}
        @if($apotek?->logo)
            <img
                src="{{ asset('storage/' . $apotek->logo) }}"
                alt="Logo {{ $apotek->nama_apotek }}"
                class="print-kop-logo"
            >
        @endif

        {{-- INFORMASI APOTEK --}}
        <div class="print-kop-info">

            <h1>
                {{ $apotek?->nama_apotek ?? 'POS Apotek' }}
            </h1>

            @if($apotek?->alamat)
                <p>
                    {{ $apotek->alamat }}
                </p>
            @endif

            @if($apotek?->telepon || $apotek?->email)
                <p>

                    @if($apotek?->telepon)
                        Telp: {{ $apotek->telepon }}
                    @endif

                    @if($apotek?->telepon && $apotek?->email)
                        &nbsp; | &nbsp;
                    @endif

                    @if($apotek?->email)
                        Email: {{ $apotek->email }}
                    @endif

                </p>
            @endif

            @if($apotek?->no_izin_sia || $apotek?->no_sipa)
                <p>

                    @if($apotek?->no_izin_sia)
                        SIA: {{ $apotek->no_izin_sia }}
                    @endif

                    @if($apotek?->no_izin_sia && $apotek?->no_sipa)
                        &nbsp;&nbsp;•&nbsp;&nbsp;
                    @endif

                    @if($apotek?->no_sipa)
                        SIPA: {{ $apotek->no_sipa }}
                    @endif

                </p>
            @endif

        </div>

    </div>


    {{-- JUDUL LAPORAN --}}
    <div class="print-report-title">

        <h2>
            Laporan Ketersediaan Stok & Kadaluarsa
        </h2>

        <p>
            Dicetak pada: {{ now()->format('d M Y H:i') }}
        </p>

    </div>

</div>


<!-- ========================================================= -->
<!-- SECTION 1 : STOK PER BATCH -->
<!-- ========================================================= -->
<div class="mb-8 print-section">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans">
                Stok Per Batch
            </h2>
            <p class="text-caption mt-0.5">
                Rincian mutasi stok per batch: Stok Awal − Stok Terjual − Stok Rusak = Sisa Stok.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $stokPerBatch->count() }}</span> batch
        </div>
    </div>

    <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">

            <table class="table-custom print-table min-w-[72rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-12 text-center">
                            No
                        </th>
                        <th scope="col">
                            Barang
                        </th>
                        <th scope="col">
                            Kategori
                        </th>
                        <th scope="col">
                            No. Batch
                        </th>
                        <th scope="col">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-28">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-24">
                            Stok Awal
                        </th>
                        <th scope="col" class="text-right w-24">
                            Stok Terjual
                        </th>
                        <th scope="col" class="text-right w-24">
                            Stok Rusak
                        </th>
                        <th scope="col" class="text-right w-28">
                            Sisa Stok
                        </th>
                        <th scope="col" class="text-center w-24">
                            Status Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($stokPerBatch as $index => $item)
                        @php
                            $today = now()->startOfDay();

                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;

                            $stokAwal = (int) $item->jumlah;
                            $stokTerjual = (int) ($item->stok_terjual ?? 0);
                            $stokRusak = (int) ($item->stok_rusak ?? 0);
                            $sisaStok = $stokAwal - $stokTerjual - $stokRusak;
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                            <!-- No -->
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <!-- Barang -->
                            <td class="font-medium text-gray-800">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <!-- Kategori -->
                            <td class="text-gray-600">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <!-- Batch -->
                            <td class="font-mono text-gray-600">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <!-- Rak -->
                            <td class="font-mono text-gray-600">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <!-- Expired -->
                            <td class="text-center font-mono">
                                {{ $expiredDate ? $expiredDate->format('d M Y') : '—' }}
                            </td>

                            <!-- Status Expired -->
                            <td class="text-center">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        Kadaluarsa
                                    </span>
                                @elseif ($expiredDate->lte($today->copy()->addMonth()))
                                    <span class="badge-orange">
                                        ≤ 1 Bulan
                                    </span>
                                @elseif ($expiredDate->lte($today->copy()->addMonths(3)))
                                    <span class="badge-warning">
                                        ≤ 3 Bulan
                                    </span>
                                @else
                                    <span class="badge-success">
                                        Normal
                                    </span>
                                @endif
                            </td>

                            <!-- Stok Awal -->
                            <td class="table-num font-medium text-gray-700">
                                {{ number_format($stokAwal, 0, ',', '.') }}
                            </td>

                            <!-- Stok Terjual -->
                            <td class="table-num font-semibold text-indigo-600">
                                {{ number_format($stokTerjual, 0, ',', '.') }}
                            </td>

                            <!-- Stok Rusak -->
                            <td class="table-num font-semibold text-rose-600">
                                {{ number_format($stokRusak, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Saat Ini -->
                            <td class="table-num font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ number_format($sisaStok, 0, ',', '.') }}
                            </td>

                            <!-- Status Stok -->
                            <td class="text-center">
                                @if ($sisaStok <= 0)
                                    <span class="badge-danger">Habis</span>
                                @elseif ($sisaStok <= $stokMinimum)
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Stok Batch Tidak Ditemukan
                                        @else
                                            Stok Batch Kosong
                                        @endif
                                    </div>
                                    <div class="empty-state-desc">
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Tidak ada data batch obat yang cocok dengan filter kriteria Anda.
                                        @else
                                            Belum ada data stok batch obat yang terdaftar di sistem.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($stokPerBatch->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total (Ringkasan Data Tampil):
                            </td>
                            <td class="table-num px-5 py-4 text-right text-gray-800 font-bold">
                                {{ number_format($totalStokAwal, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right text-indigo-700 font-bold">
                                {{ number_format($totalStokTerjual, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right text-rose-700 font-bold">
                                {{ number_format($totalStokRusak, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right {{ $totalSisaStok <= 0 ? 'text-red-600' : 'text-emerald-700' }} font-bold">
                                {{ number_format($totalSisaStok, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- SECTION 2 : BATCH MENDEKATI EXPIRED -->
<!-- ========================================================= -->
<div class="mb-8 print-hide">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans">
                Batch Mendekati Expired (&le; 90 Hari)
            </h2>
            <p class="text-caption mt-0.5">
                Daftar obat dengan sisa stok aktif yang akan kadaluarsa dalam rentang waktu dekat.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $mendekatiExpired->count() }}</span> batch
        </div>
    </div>

   <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">

            <table class="table-custom print-table min-w-[72rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-12 text-center">
                            No
                        </th>
                        <th scope="col">
                            Barang
                        </th>
                        <th scope="col">
                            Kategori
                        </th>
                        <th scope="col">
                            No. Batch
                        </th>
                        <th scope="col">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-28">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-28">
                            Sisa Stok
                        </th>
                        <th scope="col" class="text-center w-24">
                            Status Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($mendekatiExpired as $index => $item)
                        @php
                            $today = now()->startOfDay();
                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;
                            $sisaStokExp = (int) $item->jumlah - (int) ($item->stok_terjual ?? 0) - (int) ($item->stok_rusak ?? 0);
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <td class="text-gray-600">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <td class="text-center font-mono">
                                {{ $expiredDate ? $expiredDate->format('d M Y') : '—' }}
                            </td>

                            <td class="text-center">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        Kadaluarsa
                                    </span>
                                @elseif ($expiredDate->lte($today->copy()->addMonth()))
                                    <span class="badge-orange">
                                        ≤ 1 Bulan
                                    </span>
                                @elseif ($expiredDate->lte($today->copy()->addMonths(3)))
                                    <span class="badge-warning">
                                        ≤ 3 Bulan
                                    </span>
                                @else
                                    <span class="badge-success">
                                        Normal
                                    </span>
                                @endif
                            </td>

                            <td class="table-num font-bold {{ $sisaStokExp <= 0 ? 'text-red-600' : ($sisaStokExp <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ number_format($sisaStokExp, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @if ($sisaStokExp <= 0)
                                    <span class="badge-danger">Habis</span>
                                @elseif ($sisaStokExp <= $stokMinimum)
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        Tidak Ada Batch Mendekati Expired
                                    </div>
                                    <div class="empty-state-desc">
                                        Tidak terdapat batch dengan sisa stok aktif yang akan kadaluarsa dalam 90 hari.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($mendekatiExpired->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total Sisa Stok Mendekati Expired:
                            </td>
                            <td class="table-num px-5 py-4 text-right text-red-600 font-bold">
                                {{ number_format($mendekatiExpired->sum(fn ($i) => (int) $i->jumlah - (int) ($i->stok_terjual ?? 0) - (int) ($i->stok_rusak ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- TANDA TANGAN --}}
{{-- ========================================================= --}}

<div class="print-only print-signature">

    {{-- TANDA TANGAN APOTEKER --}}
    <div class="print-signature-box">
        <p>Mengetahui,</p>
        <p>Apoteker Penanggung Jawab</p>

        <div class="print-signature-space"></div>

        <p class="print-signature-name">
            {{ $apotek?->nama_apoteker_pj ?? '.................................' }}
        </p>

        @if($apotek?->no_sipa)
            <p>
                SIPA: {{ $apotek->no_sipa }}
            </p>
        @endif
    </div>

    {{-- TANDA TANGAN ADMIN --}}
    <div class="print-signature-box">
        <p>Dibuat oleh,</p>
        <p>Admin / Petugas Apotek</p>

        <div class="print-signature-space"></div>

        <p class="print-signature-name">
            {{ auth()->user()->name ?? '.................................' }}
        </p>
    </div>

</div>

@endsection