@extends('layouts.app')
@section('title', 'Laporan Barang Rusak')

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
            margin: 0;
        }

        /* Sembunyikan elemen navigasi, filter, dan tombol web */
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
        .page-header-web,
        .filter-card-web,
        .card-base,
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
        display: none !important;
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
    .table-rusak th,
    .table-rusak td {
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
            Laporan Kerugian Barang Rusak & Kadaluarsa
        </h2>
        <p>
            Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
            @if(request('nama_barang'))
                &nbsp;•&nbsp; Barang: {{ request('nama_barang') }}
            @endif
            @if(request('no_batch'))
                &nbsp;•&nbsp; Batch: {{ request('no_batch') }}
            @endif
            &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
        </p>
    </div>

</div>

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Barang Rusak / Kadaluarsa</h1>
        <p class="text-caption mt-1">Analisis barang rusak dan kerugian finansial berdasarkan harga beli batch.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.rusak', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>
        <button onclick="printReport()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.rusak') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
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
                    No. Batch
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="no_batch"
                        value="{{ request('no_batch') }}"
                        placeholder="Cari no. batch..."
                        class="form-input pr-8 font-mono"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Lapor
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
            @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                <a
                    href="{{ route('laporan.rusak') }}"
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
        <table class="table-custom table-rusak print-table w-full min-w-[54rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-[4%] min-w-[2.5rem] text-center !px-1.5 py-3">No</th>
                    <th scope="col" class="w-[14%] min-w-[7.5rem] text-center !px-2 py-3 whitespace-nowrap">Tanggal Lapor</th>
                    <th scope="col" class="w-[24%] min-w-[10rem] !px-3 py-3 break-words">Nama Barang</th>
                    <th scope="col" class="w-[13%] min-w-[6.5rem] text-center !px-2 py-3 whitespace-nowrap">No. Batch</th>
                    <th scope="col" class="w-[7%] min-w-[3.5rem] text-center !px-2 py-3 whitespace-nowrap">Jumlah</th>
                    <th scope="col" class="w-[15%] min-w-[7.5rem] text-right !px-2.5 py-3 whitespace-nowrap">Total Kerugian</th>
                    <th scope="col" class="w-[23%] min-w-[10rem] !px-2.5 py-3 break-words">Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num text-center text-xs text-gray-500 !px-1.5 py-3">{{ $index + 1 }}</td>
                        <td class="text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">{{ $item->tanggal ? $item->tanggal->translatedFormat('d F Y') : '—' }}</td>
                        <td class="font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">{{ $item->detailPenerimaan->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600 text-center text-xs !px-2 py-3 whitespace-nowrap">{{ $item->detailPenerimaan->no_batch ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800 text-center text-xs !px-2 py-3 whitespace-nowrap">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-red-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">Rp {{ number_format($item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0), 0, ',', '.') }}</td>
                        <td class="text-gray-600 text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                                        Barang Rusak Tidak Ditemukan
                                    @else
                                        Barang Rusak Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                                        Tidak ditemukan data pelaporan obat rusak atau kadaluarsa yang cocok dengan filter kriteria Anda.
                                    @else
                                        Tidak ditemukan riwayat pelaporan obat rusak atau kadaluarsa yang terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="5" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600">Total Kerugian Finansial:</td>
                    <td class="table-num !px-2.5 py-3.5 text-right text-red-650 font-mono text-sm font-bold whitespace-nowrap">Rp {{ number_format($totalKerugian, 0, ',', '.') }}</td>
                    <td class="!px-2.5 py-3.5"></td>
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
