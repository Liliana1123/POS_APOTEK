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

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP & JUDUL LAPORAN) --}}
{{-- ========================================================= --}}
<div class="print-only">
    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="Laporan Kerugian Barang Rusak">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        @if(request('nama_barang'))
            &nbsp;•&nbsp; Barang: {{ request('nama_barang') }}
        @endif
        @if(request('no_batch'))
            &nbsp;•&nbsp; Batch: {{ request('no_batch') }}
        @endif
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>
</div>

<x-page-header
    title="Laporan Barang Rusak"
    subtitle="Analisis barang rusak dan kerugian finansial berdasarkan harga beli batch."
    class="print:hidden">
    <x-print-actions />
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.rusak')"
    :reset-url="route('laporan.rusak')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-8"
    action-cols="sm:col-span-2 md:col-span-2"
    :has-reset="request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan'])"
    :live-search="true">
    <div class="relative sm:col-span-2 md:col-span-3">
        <input
            type="text"
            name="cari"
            value="{{ request('cari', request('nama_barang', request('no_batch'))) }}"
            placeholder="Cari nama barang atau no. batch..."
            class="form-input pr-8"
            aria-label="Cari barang atau no. batch"
            title="Cari barang atau no. batch"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <div class="flex items-center gap-1.5 sm:col-span-2 md:col-span-3">
        <input
            type="date"
            name="dari"
            value="{{ old('dari', $dari) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="inline-flex items-center text-gray-400 shrink-0">
            <x-heroicon-m-minus class="w-3.5 h-3.5" />
        </span>
        <input
            type="date"
            name="sampai"
            value="{{ old('sampai', $sampai) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>
</x-card-filter>

<div class="filter-alert-container mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 {{ $dateError ? '' : 'hidden' }}" role="alert">
    {{ $dateError }}
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
                        <td class="table-num text-center text-xs text-gray-500 !px-1.5 py-3">{{ ($items->firstItem() ?? 1) + $index }}</td>
                        <td class="text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">@tanggalId($item->tanggal, 'd F Y')</td>
                        <td class="font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">{{ $item->detailPenerimaan->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600 text-center text-xs !px-2 py-3 whitespace-nowrap">{{ $item->detailPenerimaan->no_batch ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800 text-center text-xs !px-2 py-3 whitespace-nowrap">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-red-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">@rupiah($item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0))</td>
                        <td class="text-gray-600 text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4 print:hidden">
        {{ $items->links('vendor.pagination.apotek') }}
    </div>
</div>

{{-- ========================================================= --}}
{{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
{{-- ========================================================= --}}
<x-print-signature :apotek="$apotek" />

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
