@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<style>
@media print {
    aside, nav, header, [role="navigation"], .print\:hidden, .no-print {
        display: none !important;
    }
    main {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card-base {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    body {
        background: white !important;
        color: black !important;
    }
    .penjualan-row {
        display: table-row !important;
    }
    @page {
        margin: 15mm 10mm 15mm 10mm;
    }
}
</style>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 print:hidden">

    {{-- Judul & Subtitle --}}
    <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100 shadow-2xs shrink-0">
            <x-heroicon-o-document-text class="h-6 w-6" />
        </div>

        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight leading-tight">
                Laporan Penjualan
            </h1>

            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Analisis ringkasan transaksi dan perolehan keuntungan penjualan.
            </p>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex items-center gap-2.5 shrink-0">

        {{-- Export CSV --}}
        <a
            href="{{ route('laporan.penjualan', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-600 bg-white px-3.5 py-2 text-xs sm:text-sm font-semibold text-blue-600 transition-all duration-150 hover:bg-blue-50 shadow-2xs"
        >
            <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
            <span>Export CSV</span>
        </a>

        {{-- Cetak Laporan --}}
        <button
            type="button"
            onclick="window.print()"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2 text-xs sm:text-sm font-semibold text-white transition-all duration-150 hover:bg-blue-700 shadow-2xs cursor-pointer"
        >
            <x-heroicon-o-printer class="h-4 w-4" />
            <span>Cetak Laporan</span>
        </button>

    </div>

</div>

<!-- Filter Card -->
<div class="card-base mb-4 p-3.5 sm:p-4 print:hidden">

    <form
        method="GET"
        action="{{ route('laporan.penjualan') }}"
        class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1.2fr_auto_auto] lg:items-end"
    >

        {{-- Dari Tanggal --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                <x-heroicon-o-calendar-days class="h-4 w-4 text-slate-400" />
                <span>Dari Tanggal</span>
            </label>

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-calendar-days class="h-4 w-4" />
                </div>
                <input
                    type="date"
                    name="dari"
                    value="{{ $dari }}"
                    class="h-[38px] w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-1.5 text-xs sm:text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
            </div>
        </div>

        {{-- Sampai Tanggal --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                <x-heroicon-o-calendar-days class="h-4 w-4 text-slate-400" />
                <span>Sampai Tanggal</span>
            </label>

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-calendar-days class="h-4 w-4" />
                </div>
                <input
                    type="date"
                    name="sampai"
                    value="{{ $sampai }}"
                    class="h-[38px] w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-1.5 text-xs sm:text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                >
            </div>
        </div>

        {{-- Status Pelanggan --}}
        <div>
            <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                <x-heroicon-o-user class="h-4 w-4 text-slate-400" />
                <span>Status Pelanggan</span>
            </label>

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <x-heroicon-o-user class="h-4 w-4" />
                </div>
                <select
                    name="status_pelanggan"
                    class="h-[38px] w-full appearance-none rounded-lg border border-slate-200 bg-white pl-9 pr-8 py-1.5 text-xs sm:text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 cursor-pointer"
                >
                    <option value="">Semua Pelanggan</option>

                    <option
                        value="member"
                        @selected(request('status_pelanggan') === 'member')
                    >
                        Member Only
                    </option>

                    <option
                        value="non-member"
                        @selected(request('status_pelanggan') === 'non-member')
                    >
                        Umum / Non-Member
                    </option>
                </select>

                <x-heroicon-o-chevron-down class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
            </div>
        </div>

        {{-- Tombol Filter --}}
        <button
            type="submit"
            class="inline-flex h-[38px] items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 text-xs sm:text-sm font-semibold text-white transition-all duration-150 hover:bg-blue-700 shadow-2xs cursor-pointer"
        >
            <x-heroicon-o-funnel class="h-4 w-4" />
            <span>Filter</span>
        </button>

        {{-- Tombol Reset --}}
        <a
            href="{{ route('laporan.penjualan') }}"
            class="inline-flex h-[38px] items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-blue-600 transition-all duration-150 hover:bg-slate-50 hover:border-slate-300 shadow-2xs"
        >
            <x-heroicon-o-arrow-path class="h-4 w-4" />
            <span>Reset</span>
        </a>

    </form>

</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Rincian Transaksi Penjualan</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 mb-5 print:mb-4">

    {{-- CARD 1: Ringkasan Transaksi --}}
    <div class="card-base p-4 sm:p-5">

        <div class="flex items-center gap-2.5 mb-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shrink-0">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
            </div>

            <h2 class="text-sm sm:text-base font-bold text-slate-800">
                Ringkasan Transaksi
            </h2>
        </div>

        <div class="grid grid-cols-3 gap-2">

            {{-- Jumlah Transaksi --}}
            <div class="pr-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Jumlah Transaksi
                </span>

                <strong class="mt-2 block text-lg sm:text-xl lg:text-2xl font-bold text-blue-600 tracking-tight">
                    {{ $jumlahTransaksi }} kali
                </strong>
            </div>

            {{-- Member --}}
            <div class="px-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Transaksi Member
                </span>

                <strong class="mt-2 block text-lg sm:text-xl lg:text-2xl font-bold text-blue-600 tracking-tight">
                    {{ $transaksiMember }} kali
                </strong>
            </div>

            {{-- Umum --}}
            <div class="pl-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Transaksi Umum
                </span>

                <strong class="mt-2 block text-lg sm:text-xl lg:text-2xl font-bold text-blue-600 tracking-tight">
                    {{ $transaksiNonMember }} kali
                </strong>
            </div>

        </div>

    </div>

    {{-- CARD 2: Perolehan Keuntungan --}}
    <div class="card-base p-4 sm:p-5">

        <div class="flex items-center gap-2.5 mb-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                <x-heroicon-o-circle-stack class="h-5 w-5" />
            </div>

            <h2 class="text-sm sm:text-base font-bold text-slate-800">
                Perolehan Keuntungan
            </h2>
        </div>

        <div class="grid grid-cols-3 gap-2 items-center">

            {{-- Omzet Kotor --}}
            <div class="pr-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Omzet Kotor
                </span>

                <strong class="mt-2 block text-lg sm:text-xl lg:text-2xl font-bold text-blue-600 tracking-tight">
                    Rp {{ number_format($omzet, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Total Potongan Diskon --}}
            <div class="px-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Total Potongan Diskon
                </span>

                <strong class="mt-2 block text-lg sm:text-xl lg:text-2xl font-bold text-blue-600 tracking-tight">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Penjualan Bersih (Net) --}}
            <div class="rounded-xl bg-emerald-50/80 border border-emerald-100/90 px-3.5 py-2.5 sm:py-3">
                <span class="block text-xs font-semibold text-emerald-800">
                    Penjualan Bersih (Net)
                </span>

                <strong class="mt-1 block text-lg sm:text-xl lg:text-2xl font-bold text-emerald-600 tracking-tight">
                    Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                </strong>
            </div>

        </div>

    </div>

</div>

<!-- Data Table Card -->
<div class="card-base overflow-hidden p-0 mb-4">

    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px]">

            {{-- Table Header --}}
            <thead>
                <tr class="bg-blue-600 text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider">

                    <th class="py-3.5 px-4 text-center w-24">
                        Aksi
                    </th>

                    <th class="py-3.5 px-4 text-left">
                        <div class="inline-flex items-center gap-1 cursor-default">
                            <span>No. Invoice</span>
                            <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                        </div>
                    </th>

                    <th class="py-3.5 px-4 text-left">
                        <div class="inline-flex items-center gap-1 cursor-default">
                            <span>Tanggal</span>
                            <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                        </div>
                    </th>

                    <th class="py-3.5 px-4 text-left">
                        Pelanggan
                    </th>

                    <th class="py-3.5 px-4 text-left">
                        Kasir
                    </th>

                    <th class="py-3.5 px-4 text-right">
                        Total Kotor
                    </th>

                    <th class="py-3.5 px-4 text-right">
                        Total Transaksi
                    </th>

                </tr>
            </thead>

            {{-- Table Body --}}
            <tbody id="table-laporan-body" class="divide-y divide-slate-100 text-xs sm:text-sm">

                @forelse ($penjualans as $index => $penjualan)

                    @php
                        $diskonFaktur = $penjualan->detail->sum('diskon');
                    @endphp

                    <tr class="penjualan-row transition-colors duration-150 hover:bg-blue-50/40" data-row-index="{{ $index }}">

                        {{-- Aksi --}}
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">

                                {{-- Detail --}}
                                <a
                                    href="{{ route('penjualan.show', $penjualan) }}"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-white text-blue-600 transition-colors hover:bg-blue-50 hover:border-blue-300 shadow-2xs"
                                    title="Lihat Detail"
                                    aria-label="Lihat Detail"
                                >
                                    <x-heroicon-o-eye class="h-3.5 w-3.5" />
                                </a>

                                {{-- Cetak Struk --}}
                                <a
                                    href="{{ route('penjualan.show', $penjualan) }}"
                                    target="_blank"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-blue-200 bg-white text-blue-600 transition-colors hover:bg-blue-50 hover:border-blue-300 shadow-2xs"
                                    title="Cetak Struk"
                                    aria-label="Cetak Struk"
                                >
                                    <x-heroicon-o-printer class="h-3.5 w-3.5" />
                                </a>

                            </div>
                        </td>

                        {{-- No. Invoice --}}
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <a
                                href="{{ route('penjualan.show', $penjualan) }}"
                                class="font-semibold text-blue-600 hover:text-blue-800 hover:underline print:text-gray-800"
                            >
                                {{ $penjualan->no_faktur }}
                            </a>
                        </td>

                        {{-- Tanggal --}}
                        <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                            {{ $penjualan->tanggal->format('d M Y') }}
                        </td>

                        {{-- Pelanggan --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 text-xs sm:text-sm">
                                    {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                                </span>

                                @if(isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-600 border border-emerald-200/60 print:hidden">
                                        MEMBER
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-semibold text-amber-600 border border-amber-200/60 print:hidden">
                                        PELANGGAN UMUM
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Kasir --}}
                        <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                            {{ $penjualan->user->name ?? 'Admin' }}
                        </td>

                        {{-- Total Kotor --}}
                        <td class="py-3.5 px-4 text-right font-medium text-slate-600 whitespace-nowrap">
                            Rp {{ number_format($penjualan->total + $diskonFaktur, 0, ',', '.') }}
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="py-3.5 px-4 text-right font-bold text-slate-800 whitespace-nowrap">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="py-12 px-4 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-500">
                                    <x-heroicon-o-document-magnifying-glass class="h-6 w-6" />
                                </div>

                                <div class="text-sm font-semibold text-slate-700">
                                    Transaksi Tidak Ditemukan
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
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
                    <tr class="border-t-2 border-slate-200 bg-slate-50/70 font-semibold text-xs sm:text-sm">
                        <td colspan="5" class="py-3.5 px-4 text-right text-slate-600 font-semibold">
                            Total Akumulasi
                        </td>

                        <td class="py-3.5 px-4 text-right font-bold text-slate-700 whitespace-nowrap">
                            Rp {{ number_format($omzet, 0, ',', '.') }}
                        </td>

                        <td class="py-3.5 px-4 text-right font-bold text-blue-700 whitespace-nowrap">
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
                    {{ min(5, $penjualans->count()) }}
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

@if($penjualans->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rows = Array.from(document.querySelectorAll('.penjualan-row'));
    const totalRows = rows.length;
    const pageSize = 5; // 5 baris per halaman sesuai tampilan referensi
    const totalPages = Math.ceil(totalRows / pageSize);
    let currentPage = 1;

    const paginationContainer = document.getElementById('pagination-controls');
    const pageItemCountEl = document.getElementById('page-item-count');

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

        // Tombol Prev
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
            currentPage === 1
                ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
        }`;
        prevBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) renderPage(currentPage - 1);
        });
        paginationContainer.appendChild(prevBtn);

        // Nomor Halaman
        for (let p = 1; p <= totalPages; p++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
                p === currentPage
                    ? 'bg-blue-600 text-white border-blue-600 shadow-2xs font-bold'
                    : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
            }`;
            pageBtn.textContent = p;
            pageBtn.addEventListener('click', () => renderPage(p));
            paginationContainer.appendChild(pageBtn);
        }

        // Tombol Next
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = `h-7 w-7 sm:h-8 sm:w-8 flex items-center justify-center rounded-lg border text-xs font-semibold transition ${
            currentPage === totalPages
                ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50'
                : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-300 cursor-pointer'
        }`;
        nextBtn.innerHTML = '<svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) renderPage(currentPage + 1);
        });
        paginationContainer.appendChild(nextBtn);
    }

    renderPage(1);
});
</script>
@endif
@endsection

