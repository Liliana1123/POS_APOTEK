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
            margin: 0;
        }

        /* Sembunyikan elemen navigasi, backdrop, filter, dan header web */
        #sidebar-backdrop,
        #toast-success,
        #toast-error,
        aside,
        #app-sidebar,
        nav,
        header,
        [role="navigation"],
        .print\:hidden,
        .no-print,
        .no-print-expired,
        .page-header-web,
        .filter-card-web,
        .card-base:not(.print-table-card),
        button,
        .btn-primary,
        .btn-secondary {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            min-height: 0 !important;
            max-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: hidden !important;
            position: absolute !important;
            top: -9999px !important;
            left: -9999px !important;
        }

        /* Reset html & body: nolkan margin browser agar kop surat menempel rapi di atas */
        html,
        body {
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
            position: static !important;
            margin: 0 !important;
            padding: 5mm 10mm 10mm 10mm !important;
            background: #fff !important;
            color: #000 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Override wrapper layouts/app yang menggunakan h-screen overflow-y-auto */
        body > div.flex-1,
        div.flex-1.flex.flex-col,
        main {
            position: static !important;
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            float: none !important;
            flex: none !important;
        }

        .table-custom-container,
        .print-table-wrapper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
        }

        /* =========================
           KOP APOTEK
        ========================= */

        .print-kop {
            display: block !important;
            position: relative;
            text-align: center;
            min-height: 50px;
            margin: 0 0 6px 0 !important;
            padding: 0 0 5px 0 !important;
            border-bottom: 1.5px solid #222;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .print-kop-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            object-fit: contain;
            object-position: center;
        }

        .print-kop-info {
            width: 100%;
            text-align: center;
            box-sizing: border-box;
            padding: 0 54px;
        }

        .print-kop-info h1 {
            margin: 0 0 2px !important;
            padding: 0 !important;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
        }

        .print-kop-info p {
            margin: 1px 0 !important;
            padding: 0 !important;
            font-size: 8.5px;
            line-height: 1.25;
            color: #333;
        }

        /* =========================
           JUDUL LAPORAN
        ========================= */

        .print-report-title {
            display: block !important;

            text-align: center;
            margin: 4px 0 8px;
        }

        .print-report-title h2 {
            margin: 0;

            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
        }

        .print-report-title p {
            margin: 2px 0 0;

            font-size: 8px;
            color: #555;
        }

        /* =========================
           SECTION LAPORAN
        ========================= */

        .print-section {
            margin-bottom: 10px !important;
            margin-top: 0 !important;
            page-break-inside: auto;
        }

        .print-section-header {
            display: flex !important;
            justify-content: space-between;
            align-items: flex-end;

            margin-bottom: 4px;
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
            break-inside: avoid !important;
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
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
    }

   /* =========================
    TANDA TANGAN
    HANYA MUNCUL SAAT CETAK
    ========================= */

    .print-signature {
        display: none !important;
    }

    @media print {
        .no-break {
            -webkit-column-break-inside: avoid;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .print-signature {
            display: block !important;
            margin-top: 40px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .print-signature-grid {
            display: flex !important;
            justify-content: space-between !important;
            width: 100% !important;
            padding: 0 40px !important;
            box-sizing: border-box !important;
        }

        .print-signature-box {
            width: 250px;
            text-align: center;
            font-size: 10px;
            color: #111;
        }

        .print-signature-title {
            margin: 0 !important;
            font-size: 10px !important;
            font-weight: normal !important;
        }

        .print-signature-role {
            margin: 2px 0 0 !important;
            font-size: 10px !important;
            font-weight: 700 !important;
        }

        .print-signature-space {
            height: 65px !important;
        }

        .print-signature-name {
            margin: 0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-decoration: underline !important;
        }

        .print-signature-sipa {
            margin: 3px 0 0 !important;
            font-size: 9px !important;
            color: #333 !important;
        }
    }
</style>

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP & JUDUL LAPORAN) --}}
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
            Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>

    </div>

</div>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden no-print page-header-web">
    <div>
        <h1>Laporan Monitoring Stok dan Kadaluarsa</h1>

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
            onclick="printReport()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden no-print filter-card-web">
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


<!-- ========================================================= -->
<!-- SECTION 1 : STOK PER BATCH -->
<!-- ========================================================= -->
<div class="mb-8 print-section">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2 print:mb-1">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans print:text-gray-900">
                Stok Per Batch
            </h2>
            <p class="text-caption mt-0.5 print:text-[8px] print:mt-0">
                Rincian mutasi stok per batch: Stok Awal − Stok Terjual − Stok Rusak = Sisa Stok.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500 print:text-[8px]">
            Menampilkan <span class="font-semibold text-gray-700 print:text-gray-900">{{ $stokPerBatch->count() }}</span> batch
        </div>
    </div>

    <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">

            <table class="table-custom print-table w-full min-w-[62rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-10 text-center !px-2">
                            No
                        </th>
                        <th scope="col" class="w-44 max-w-[12rem] !px-2.5">
                            Barang
                        </th>
                        <th scope="col" class="w-28 max-w-[7.5rem] !px-2 whitespace-nowrap">
                            Kategori
                        </th>
                        <th scope="col" class="w-24 text-center !px-2 whitespace-nowrap">
                            No. Batch
                        </th>
                        <th scope="col" class="w-20 text-center !px-2 whitespace-nowrap">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28 !px-2 whitespace-nowrap">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-24 !px-2 whitespace-nowrap">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Awal
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Terjual
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Stok Rusak
                        </th>
                        <th scope="col" class="text-right w-16 !px-2 whitespace-nowrap">
                            Sisa Stok
                        </th>
                        <th scope="col" class="text-center w-20 !px-2 whitespace-nowrap">
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
                            <td class="font-medium text-gray-800 !px-2.5 max-w-[12rem] break-words text-xs leading-snug">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <!-- Kategori -->
                            <td class="text-gray-600 !px-2 text-xs max-w-[7.5rem] truncate" title="{{ $item->barang->kategori->nama ?? '—' }}">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <!-- Batch -->
                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <!-- Rak -->
                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <!-- Expired -->
                            <td class="text-center font-mono !px-2 whitespace-nowrap">
                                {{ $expiredDate ? $expiredDate->translatedFormat('d F Y') : '—' }}
                            </td>

                            <!-- Status Expired -->
                            <td class="text-center !px-2 whitespace-nowrap">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        KADALUARSA
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
                            <td class="table-num font-medium text-gray-700 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokAwal, 0, ',', '.') }}
                            </td>

                            <!-- Stok Terjual -->
                            <td class="table-num font-semibold text-indigo-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokTerjual, 0, ',', '.') }}
                            </td>

                            <!-- Stok Rusak -->
                            <td class="table-num font-semibold text-rose-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokRusak, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Saat Ini -->
                            <td class="table-num font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }} !px-2 text-right whitespace-nowrap">
                                {{ number_format($sisaStok, 0, ',', '.') }}
                            </td>

                            <!-- Status Stok -->
                            <td class="text-center !px-2 whitespace-nowrap">
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
                            <td colspan="7" class="!px-3 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total (Ringkasan Data Tampil):
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-gray-800 font-bold whitespace-nowrap">
                                {{ number_format($totalStokAwal, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-indigo-700 font-bold whitespace-nowrap">
                                {{ number_format($totalStokTerjual, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-rose-700 font-bold whitespace-nowrap">
                                {{ number_format($totalStokRusak, 0, ',', '.') }}
                            </td>
                            <td class="table-num !px-2 py-3 text-right {{ $totalSisaStok <= 0 ? 'text-red-600' : 'text-emerald-700' }} font-bold whitespace-nowrap">
                                {{ number_format($totalSisaStok, 0, ',', '.') }}
                            </td>
                            <td class="!px-2 py-3"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- ========================================================= --}}
{{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
{{-- ========================================================= --}}
<div class="no-break print-signature">
    <div class="print-signature-grid">
        {{-- KIRI: PENANGGUNG JAWAB APOTEK --}}
        <div class="print-signature-box">
            <p class="print-signature-title">Mengetahui,</p>
            <p class="print-signature-role">Penanggung Jawab Apotek</p>

            <div class="print-signature-space"></div>

            <p class="print-signature-name">
                @if (!empty($apotek?->nama_apoteker_pj))
                    {{ $apotek->nama_apoteker_pj }}
                @else
                    (____________________)
                @endif
            </p>

            @if (!empty($apotek?->no_sipa))
                <p class="print-signature-sipa">
                    No. SIPA: {{ $apotek->no_sipa }}
                </p>
            @endif
        </div>

        {{-- KANAN: PEMBUAT LAPORAN --}}
        <div class="print-signature-box">
            <p class="print-signature-title">Dibuat oleh,</p>
            <p class="print-signature-role">Pembuat Laporan</p>

            <div class="print-signature-space"></div>

            <p class="print-signature-name">
                @if (!empty(auth()->user()?->name))
                    {{ auth()->user()->name }}
                @else
                    (____________________)
                @endif
            </p>
        </div>
    </div>
</div>


<!-- ========================================================= -->
<!-- SECTION 2 : BATCH MENDEKATI EXPIRED -->
<!-- ========================================================= -->
<div class="mb-8 no-print print:hidden no-print-expired">
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

            <table class="table-custom print-table w-full min-w-[58rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-10 text-center !px-2">
                            No
                        </th>
                        <th scope="col" class="w-44 max-w-[12rem] !px-2.5">
                            Barang
                        </th>
                        <th scope="col" class="w-28 max-w-[7.5rem] !px-2 whitespace-nowrap">
                            Kategori
                        </th>
                        <th scope="col" class="w-24 text-center !px-2 whitespace-nowrap">
                            No. Batch
                        </th>
                        <th scope="col" class="w-20 text-center !px-2 whitespace-nowrap">
                            No. Rak
                        </th>
                        <th scope="col" class="text-center w-28 !px-2 whitespace-nowrap">
                            Expired
                        </th>
                        <th scope="col" class="text-center w-24 !px-2 whitespace-nowrap">
                            Status Expired
                        </th>
                        <th scope="col" class="text-right w-20 !px-2 whitespace-nowrap">
                            Sisa Stok
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

                            <td class="font-medium text-gray-800 !px-2.5 max-w-[12rem] break-words text-xs leading-snug">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <td class="text-gray-600 !px-2 text-xs max-w-[7.5rem] truncate" title="{{ $item->barang->kategori->nama ?? '—' }}">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <td class="text-center font-mono !px-2 whitespace-nowrap">
                                {{ $expiredDate ? $expiredDate->translatedFormat('d F Y') : '—' }}
                            </td>

                            <td class="text-center !px-2 whitespace-nowrap">
                                @if (!$expiredDate)
                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>
                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))
                                    <span class="badge-danger">
                                        KADALUARSA
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

                            <td class="table-num font-bold {{ $sisaStokExp <= 0 ? 'text-red-600' : ($sisaStokExp <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }} !px-2 text-right whitespace-nowrap">
                                {{ number_format($sisaStokExp, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        Tidak Ada Batch Kadaluarsa / Mendekati Expired
                                    </div>
                                    <div class="empty-state-desc">
                                        Tidak terdapat batch dengan sisa stok aktif yang sudah kadaluarsa atau akan kadaluarsa dalam 90 hari.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($mendekatiExpired->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-right uppercase tracking-wider text-gray-600">
                                Total Sisa Stok Kadaluarsa & Mendekati Expired:
                            </td>
                            <td class="table-num !px-2 py-3 text-right text-red-600 font-bold whitespace-nowrap">
                                {{ number_format($mendekatiExpired->sum(fn ($i) => (int) $i->jumlah - (int) ($i->stok_terjual ?? 0) - (int) ($i->stok_rusak ?? 0)), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

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