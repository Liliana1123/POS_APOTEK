@extends('layouts.app')
@section('title', 'Laporan Penerimaan')

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
{{-- TEMPLATE CETAK (KOP & JUDUL LAPORAN) --}}
{{-- ========================================================= --}}
<div class="print-only">
    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="Laporan Penerimaan Barang Masuk">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        @if(request('supplier_id') && ($supplierAktif = $suppliers->firstWhere('id', request('supplier_id'))))
            &nbsp;•&nbsp; Supplier: {{ $supplierAktif->nama }}
        @endif
        @if(request('status_pembayaran'))
            &nbsp;•&nbsp; Status: {{ request('status_pembayaran') === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
        @endif
        @if(request('search'))
            &nbsp;•&nbsp; Pencarian: {{ request('search') }}
        @endif
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>
</div>

<x-page-header
    title="Laporan Penerimaan Barang"
    subtitle="Ringkasan barang masuk dan akumulasi nilai pembelian dari supplier."
    class="print:hidden">
    <x-print-actions />
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.penerimaan')"
    :reset-url="route('laporan.penerimaan')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
    :has-reset="request()->anyFilled(['search', 'supplier_id', 'dari', 'sampai', 'status_pembayaran'])"
    :live-search="true">
    <div class="relative">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari faktur / barang..."
            class="form-input pr-8"
            aria-label="Cari Faktur atau Barang"
            title="Cari No. Faktur atau Nama Barang"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <div class="flex items-center gap-1.5">
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

    <select name="supplier_id" class="form-input" aria-label="Supplier" title="Supplier">
        <option value="">Supplier: Semua</option>
        @foreach ($suppliers as $s)
            <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>
                {{ $s->nama }}
            </option>
        @endforeach
    </select>

    <select name="status_pembayaran" class="form-input" aria-label="Status Pembayaran" title="Status Pembayaran">
        <option value="">Pembayaran: Semua</option>
        <option value="lunas" @selected(request('status_pembayaran') === 'lunas')>Lunas</option>
        <option value="belum_lunas" @selected(request('status_pembayaran') === 'belum_lunas')>Belum Lunas</option>
    </select>
</x-card-filter>

<div class="filter-alert-container mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 {{ $dateError ? '' : 'hidden' }}" role="alert">
    {{ $dateError }}
</div>

<!-- Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-penerimaan print-table w-full min-w-[54rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="col-penerimaan-no w-[4%] min-w-[2.5rem] text-center !px-1.5 py-3">No</th>
                    <th scope="col" class="col-penerimaan-tanggal w-[14%] min-w-[7.5rem] text-center !px-2 py-3 whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="col-penerimaan-faktur w-[11.5%] min-w-[6.25rem] !px-2.5 py-3 break-words">No. Faktur</th>
                    <th scope="col" class="col-penerimaan-supplier w-[13%] min-w-[6.5rem] !px-2.5 py-3 break-words">Supplier</th>
                    <th scope="col" class="col-penerimaan-barang w-[26%] min-w-[11.5rem] !px-3 py-3 break-words">Nama Barang</th>
                    <th scope="col" class="col-penerimaan-qty text-right w-[7%] min-w-[3.75rem] !px-2 py-3 whitespace-nowrap">Jumlah</th>
                    <th scope="col" class="col-penerimaan-harga text-right w-[11%] min-w-[6rem] !px-2.5 py-3 whitespace-nowrap">Harga Beli</th>
                    <th scope="col" class="col-penerimaan-total text-right w-[13%] min-w-[6.75rem] !px-2.5 py-3 whitespace-nowrap">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="col-penerimaan-no table-num text-center text-xs text-gray-500 !px-1.5 py-3">{{ ($items->firstItem() ?? 1) + $index }}</td>
                        <td class="col-penerimaan-tanggal text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">@tanggalId($item->penerimaan->tanggal, 'd F Y')</td>
                        <td class="col-penerimaan-faktur font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->penerimaan->no_faktur }}</td>
                        <td class="col-penerimaan-supplier text-gray-600 font-medium text-xs !px-2.5 py-3 break-words leading-snug">{{ $item->penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="col-penerimaan-barang font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="col-penerimaan-qty table-num font-bold text-gray-800 text-right text-xs !px-2 py-3 whitespace-nowrap">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="col-penerimaan-harga table-num text-gray-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">@rupiah($item->harga_beli)</td>
                        <td class="col-penerimaan-total table-num font-bold text-gray-800 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">@rupiah($item->harga_beli * $item->jumlah)</td>
                    </tr>
                @empty
                    <x-empty-state colspan="8" :search-term="request('search')" />
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4 flex flex-col gap-3 print:hidden sm:flex-row sm:items-center sm:justify-between">
        @if ($items->total() > 0)
            <p class="text-xs text-slate-500">
                Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
                dari {{ $items->total() }} data
            </p>
        @endif
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
