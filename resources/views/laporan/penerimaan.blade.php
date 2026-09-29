@extends('layouts.app')
@section('title', 'Laporan Penerimaan')

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
       PRINT TEMPLATE (A4 LANDSCAPE)
    ========================= */
    @media print {
        @page {
            size: A4 landscape;
            margin: 4mm 10mm 10mm 10mm;
        }

        /* Sembunyikan elemen navigasi, filter, dan tombol web */
        #sidebar-backdrop,
        #toast-success,
        aside,
        nav,
        header,
        [role="navigation"],
        .print\:hidden,
        .no-print,
        .card-base {
            display: none !important;
        }

        /* Reset html & body agar multi-page print aktif dan tidak terpotong 1 halaman */
        html,
        body {
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            background: #fff !important;
            color: #000 !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }

        /* Override wrapper layouts/app yang menggunakan h-screen overflow-y-auto */
        div.flex-1.flex.flex-col,
        .h-screen,
        .overflow-hidden,
        .overflow-y-auto,
        .overflow-x-hidden,
        .overflow-x-auto {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            display: block !important;
            position: static !important;
            flex: none !important;
            padding: 0 !important;
            margin: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        main {
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow: visible !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
            width: 100% !important;
            max-width: none !important;
            display: block !important;
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
            display: flex !important;
            position: relative;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            margin-top: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 5px;
            margin-bottom: 6px;
            border-bottom: 1.5px solid #222;
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
        }

        .print-kop-info h1 {
            margin: 0 0 2px;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
        }

        .print-kop-info p {
            margin: 1px 0;
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
           TABEL PRINT
        ========================= */
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
            font-size: 7.5px !important;
            font-weight: 700 !important;
            text-align: center !important;
            vertical-align: middle !important;
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

        .print-table tfoot td {
            padding: 6px 4px !important;
            font-weight: 700 !important;
            border-top: 2px solid #222 !important;
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

    /* Penyelarasan Vertikal Web */
    .table-penerimaan th,
    .table-penerimaan td {
        vertical-align: middle;
    }

    /* =========================
       TANDA TANGAN (HANYA CETAK)
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
            margin-top: 35px;
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
            height: 60px !important;
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
            Laporan Penerimaan Barang Masuk
        </h2>
        <p>
            Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
            @if(request('supplier_id') && ($supplierAktif = $suppliers->firstWhere('id', request('supplier_id'))))
                &nbsp;•&nbsp; Supplier: {{ $supplierAktif->nama }}
            @endif
            @if(request('status_pembayaran'))
                &nbsp;•&nbsp; Status: {{ request('status_pembayaran') === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
            @endif
            &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>
    </div>

</div>

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Penerimaan Barang</h1>
        <p class="text-caption mt-1">Ringkasan barang masuk dan akumulasi nilai pembelian dari supplier.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.penerimaan', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.penerimaan') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    No. Faktur
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="no_faktur"
                        value="{{ request('no_faktur') }}"
                        placeholder="Cari no. faktur..."
                        class="form-input pr-8 font-mono"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Supplier
                </label>
                <select name="supplier_id" class="form-input">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>
                            {{ $s->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Nama Barang
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="nama_barang"
                        value="{{ request('nama_barang') }}"
                        placeholder="Cari nama barang..."
                        class="form-input pr-8"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Pembayaran
                </label>
                <select name="status_pembayaran" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="lunas" @selected(request('status_pembayaran') === 'lunas')>
                        Lunas
                    </option>
                    <option value="belum_lunas" @selected(request('status_pembayaran') === 'belum_lunas')>
                        Belum Lunas
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Penerimaan
                </label>

                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="dari"
                        value="{{ $dari }}"
                        class="form-input min-w-0"
                    >

                    <span class="text-sm text-gray-400">-</span>

                    <input
                        type="date"
                        name="sampai"
                        value="{{ $sampai }}"
                        class="form-input min-w-0"
                    >
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai', 'status_pembayaran']))
                <a
                    href="{{ route('laporan.penerimaan') }}"
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

<!-- Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-penerimaan print-table w-full min-w-[54rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-[4%] min-w-[2.5rem] text-center !px-1.5 py-3">No</th>
                    <th scope="col" class="w-[14%] min-w-[7.5rem] text-center !px-2 py-3 whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="w-[11.5%] min-w-[6.25rem] !px-2.5 py-3 break-words">No. Faktur</th>
                    <th scope="col" class="w-[13%] min-w-[6.5rem] !px-2.5 py-3 break-words">Supplier</th>
                    <th scope="col" class="w-[26%] min-w-[11.5rem] !px-3 py-3 break-words">Nama Barang</th>
                    <th scope="col" class="text-right w-[7%] min-w-[3.75rem] !px-2 py-3 whitespace-nowrap">Jumlah</th>
                    <th scope="col" class="text-right w-[11%] min-w-[6rem] !px-2.5 py-3 whitespace-nowrap">Harga Beli</th>
                    <th scope="col" class="text-right w-[13%] min-w-[6.75rem] !px-2.5 py-3 whitespace-nowrap">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num text-center text-xs text-gray-500 !px-1.5 py-3">{{ $index + 1 }}</td>
                        <td class="text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">{{ $item->penerimaan->tanggal->translatedFormat('d F Y') }}</td>
                        <td class="font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->penerimaan->no_faktur }}</td>
                        <td class="text-gray-600 font-medium text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800 text-right text-xs !px-2 py-3 whitespace-nowrap">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="table-num text-gray-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-800 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai', 'status_pembayaran']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai', 'status_pembayaran']))
                                        Tidak ada data penerimaan barang masuk yang cocok dengan filter kriteria Anda.
                                    @else
                                        Tidak ada data penerimaan barang masuk yang terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="7" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600">Total Nilai Penerimaan:</td>
                    <td class="table-num !px-2.5 py-3.5 text-right text-emerald-700 font-mono text-sm font-bold whitespace-nowrap">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
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
                    SIPA: {{ $apotek->no_sipa }}
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
@endsection
