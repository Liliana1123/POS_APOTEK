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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
                <div>
                    <h1>Laporan Penjualan</h1>
                    <p class="text-caption mt-1">Analisis ringkasan transaksi dan perolehan keuntungan penjualan.</p>
                </div>

        {{-- Tombol Aksi --}}
        <div class="flex gap-2 shrink-0">
        {{-- Export CSV --}}
        <a
            href="{{ route('laporan.penjualan', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5"
        >
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Export CSV
        </a>

        {{-- Cetak Laporan --}}
        <button
            type="button"
            onclick="window.print()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>

    </div>

</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6 print:hidden">

    <form
        method="GET"
        action="{{ route('laporan.penjualan') }}"
        class="space-y-4"
    >

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    {{-- Tanggal Awal --}}
    <div>
        <label for="dari" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Awal
        </label>

        <input
            type="date"
            id="dari"
            name="dari"
            value="{{ $dari }}"
            class="form-input"
        >
    </div>

    {{-- Tanggal Akhir --}}
    <div>
        <label for="sampai" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Akhir
        </label>

        <input
            type="date"
            id="sampai"
            name="sampai"
            value="{{ $sampai }}"
            class="form-input"
        >
    </div>

    {{-- Metode Pembayaran --}}
    <div>
        <label for="metode_pembayaran" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Metode Pembayaran
        </label>

        <select
            id="metode_pembayaran"
            name="metode_pembayaran"
            class="form-input"
        >
            <option value="">Semua</option>
            <option value="cash" @selected(request('metode_pembayaran') === 'cash')>
                Cash
            </option>
            <option value="qris" @selected(request('metode_pembayaran') === 'qris')>
                QRIS
            </option>
            <option value="debit" @selected(request('metode_pembayaran') === 'debit')>
                Debit
            </option>
            <option value="piutang" @selected(request('metode_pembayaran') === 'piutang')>
                Piutang
            </option>
        </select>
    </div>

    {{-- Pelanggan / Member --}}
    <div>
        <label for="pelanggan" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Pelanggan/Member
        </label>

        <select
            id="pelanggan"
            name="pelanggan"
            class="form-input"
        >
            <option value="">Semua</option>

            <option value="pelanggan_umum" @selected(request('pelanggan') === 'pelanggan_umum')>
                Pelanggan Umum
            </option>

            <option value="pelanggan_tetap" @selected(request('pelanggan') === 'pelanggan_tetap')>
                Member Pelanggan Tetap
            </option>

            <option value="keluarga_nakes" @selected(request('pelanggan') === 'keluarga_nakes')>
                Member Keluarga Nakes
            </option>

            <option value="member_only" @selected(request('pelanggan') === 'member_only')>
                Member Only
            </option>
        </select>
    </div>

    {{-- Jenis Transaksi --}}
    <div>
        <label for="jenis_transaksi" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Jenis Transaksi
        </label>

        <select
            id="jenis_transaksi"
            name="jenis_transaksi"
            class="form-input"
        >
            <option value="">Semua</option>

            <option value="non_resep" @selected(request('jenis_transaksi') === 'non_resep')>
                Non Resep
            </option>

            <option value="resep" @selected(request('jenis_transaksi') === 'resep')>
                Resep
            </option>
        </select>
    </div>

    {{-- Status Piutang --}}
    <div>
        <label for="status_piutang" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Status Piutang
        </label>

        <select
            id="status_piutang"
            name="status_piutang"
            class="form-input"
        >
            <option value="">Semua</option>

            <option value="lunas" @selected(request('status_piutang') === 'lunas')>
                Lunas
            </option>

            <option value="belum_lunas" @selected(request('status_piutang') === 'belum_lunas')>
                Belum Lunas
            </option>

            <option value="terlambat" @selected(request('status_piutang') === 'terlambat')>
                Terlambat
            </option>
        </select>
    </div>
</div>

     {{-- Area Tombol Filter --}}
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">

            <button
                type="submit"
                class="btn-primary py-1.5 px-4 flex items-center justify-center gap-1.5"
            >
                <x-heroicon-o-funnel class="h-4 w-4" />
                Filter
            </button>

        </div>

    </form>

</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Rincian Transaksi Penjualan</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

    {{-- CARD 1: Ringkasan Transaksi --}}
    <div class="card-base p-4">

        <div class="flex items-center justify-center gap-2.5 mb-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shrink-0">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
            </div>

            <h2 class="text-sm sm:text-base font-bold text-slate-800">
                Ringkasan Transaksi
            </h2>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center">

            {{-- Jumlah Transaksi --}}
            <div class="px-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Jumlah Transaksi
                </span>

                <strong class="mt-1.5 block text-base sm:text-lg lg:text-xl font-bold text-blue-600 tracking-tight">
                    {{ $jumlahTransaksi }} kali
                </strong>
            </div>

            {{-- Member --}}
            <div class="px-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Transaksi Member
                </span>

                <strong class="mt-1.5 block text-base sm:text-lg lg:text-xl font-bold text-blue-600 tracking-tight">
                    {{ $transaksiMember }} kali
                </strong>
            </div>

            {{-- Umum --}}
            <div class="px-2">
                <span class="block text-xs text-slate-500 font-medium">
                    Transaksi Umum
                </span>

                <strong class="mt-1.5 block text-base sm:text-lg lg:text-xl font-bold text-blue-600 tracking-tight">
                    {{ $transaksiNonMember }} kali
                </strong>
            </div>

        </div>

    </div>

    {{-- CARD 2: Perolehan Keuntungan --}}
    <div class="card-base p-4 sm:p-5">

       <div class="flex items-center justify-center gap-2.5 mb-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                <x-heroicon-o-circle-stack class="h-5 w-5" />
            </div>

            <h2 class="text-sm sm:text-base font-bold text-slate-800">
                Perolehan Keuntungan
            </h2>
        </div>

        <div class="grid grid-cols-3 gap-4 items-center text-center">

            {{-- Omzet Kotor --}}
            <div class="w-full px-3">
                <span class="block text-xs text-slate-500 font-medium">
                    Omzet Kotor
                </span>

                <strong class="mt-1.5 block text-base sm:text-lg lg:text-xl font-bold text-blue-600 tracking-tight">
                    Rp {{ number_format($omzet, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Total Potongan Diskon --}}
            <div class="w-full px-3">
                <span class="block text-xs text-slate-500 font-medium">
                    Total Potongan Diskon
                </span>

                <strong class="mt-1.5 block text-base sm:text-lg lg:text-xl font-bold text-blue-600 tracking-tight">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Penjualan Bersih (Net) --}}
            <div class="w-full px-3">
                <div class="w-full rounded-xl bg-emerald-50/80 border border-emerald-100/90 px-3 py-2">
                    <span class="block text-xs font-semibold text-emerald-800">
                        Penjualan Bersih (Net)
                    </span>

                    <strong class="mt-1 block text-base sm:text-lg lg:text-xl font-bold text-emerald-600 tracking-tight">
                        Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                    </strong>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Data Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-penjualan print-table w-full min-w-[60rem]">

            {{-- Table Header --}}
            <thead class="table-custom-header">
            <tr>

                <th scope="col" class="w-[11%] min-w-[6rem] text-center !px-2 py-3">
                    Aksi
                </th>

                <th scope="col" class="w-[16%] min-w-[8rem] !px-2.5 py-3 break-words">
                    <div class="inline-flex items-center gap-1">
                        <span>No. Invoice</span>
                        <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                    </div>
                </th>

                <th scope="col" class="w-[13%] min-w-[7rem] text-center !px-2 py-3 whitespace-nowrap">
                    <div class="inline-flex items-center justify-center gap-1">
                        <span>Tanggal</span>
                        <x-heroicon-o-chevron-up-down class="h-3.5 w-3.5 opacity-80" />
                    </div>
                </th>

                <th scope="col" class="w-[22%] min-w-[10rem] !px-3 py-3 break-words">
                    Pelanggan
                </th>

                <th scope="col" class="w-[14%] min-w-[7rem] !px-2.5 py-3 break-words">
                    Kasir
                </th>

                <th scope="col" class="w-[12%] min-w-[7rem] text-right !px-2.5 py-3 whitespace-nowrap">
                    Total Kotor
                </th>

                <th scope="col" class="w-[12%] min-w-[7rem] text-right !px-2.5 py-3 whitespace-nowrap">
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
                    >

                        {{-- Aksi --}}
                        <td class="text-center whitespace-nowrap !px-2 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Detail --}}
                                <a
                                    href="{{ route('penjualan.show', $penjualan) . '?from=laporan_penjualan&' . http_build_query(request()->query()) }}"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-white text-blue-600 transition-colors hover:bg-blue-50 hover:border-blue-300 shadow-2xs"
                                    title="Lihat Detail"
                                    aria-label="Lihat Detail"
                                >
                                    <x-heroicon-o-eye class="h-3.5 w-3.5" />
                                </a>

                                {{-- Cetak Struk --}}
                                <a
                                    href="{{ route('penjualan.show', $penjualan) . '?from=laporan_penjualan&' . http_build_query(request()->query()) }}"
                                    target="_blank"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-white text-blue-600 transition-colors hover:bg-blue-50 hover:border-blue-300 shadow-2xs"
                                    title="Cetak Struk"
                                    aria-label="Cetak Struk"
                                >
                                    <x-heroicon-o-printer class="h-3.5 w-3.5" />
                                </a>

                            </div>
                        </td>

                        {{-- No. Invoice --}}
                        <td class="font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">
                            <a
                                href="{{ route('penjualan.show', $penjualan) . '?from=laporan_penjualan&' . http_build_query(request()->query()) }}"
                                class="font-semibold text-blue-600 hover:text-blue-800 hover:underline print:text-gray-800"
                            >
                                {{ $penjualan->no_faktur }}
                            </a>
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-center font-mono text-gray-600 text-xs !px-2 py-3 whitespace-nowrap">
                            {{ $penjualan->tanggal->format('d M Y') }}
                        </td>

                        {{-- Pelanggan --}}
                        <td class="font-medium text-gray-800 text-xs !px-3 py-3 break-words leading-snug">
                            <div class="flex flex-col gap-1 min-w-0">
                                <span class="font-semibold text-slate-800 text-xs sm:text-sm truncate">
                                    {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                                </span>

                                @if(isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                                    <span class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-semibold text-emerald-600 border border-emerald-200/60 print:hidden">
                                        MEMBER
                                    </span>
                                @else
                                    <span class="inline-flex w-fit items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-semibold text-amber-600 border border-amber-200/60 print:hidden">
                                        PELANGGAN UMUM
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Kasir --}}
                       <td class="text-gray-600 font-medium text-xs !px-2.5 py-3 break-words leading-snug">
                            {{ $penjualan->user->name ?? 'Admin' }}
                        </td>

                        {{-- Total Kotor --}}
                        <td class="table-num text-gray-600 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">
                            Rp {{ number_format($penjualan->total + $diskonFaktur, 0, ',', '.') }}
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="table-num font-bold text-gray-800 font-mono text-right text-xs !px-2.5 py-3 whitespace-nowrap">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="p-0">
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
                    <tr class="bg-gray-50/50 border-t font-bold text-xs">
                        <td colspan="5" class="!px-3 py-3.5 text-right uppercase tracking-wider text-gray-600">
                            Total Akumulasi
                        </td>

                        <td class="table-num !px-2.5 py-3.5 text-right text-gray-800 font-mono text-sm font-bold whitespace-nowrap">
                            Rp {{ number_format($omzet, 0, ',', '.') }}
                        </td>

                        <td class="table-num !px-2.5 py-3.5 text-right text-blue-700 font-mono text-sm font-bold whitespace-nowrap">
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

