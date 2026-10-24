@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        .batch-history-row {
            display: none !important;
        }
    }

    .batch-row {
        cursor: pointer;
        user-select: none;
    }
    .batch-row:hover {
        background-color: rgba(219, 234, 254, 0.45) !important;
    }
    .batch-row-active {
        background-color: #eff6ff !important;
        box-shadow: inset 4px 0 0 #2563eb;
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
    <x-print-kop :apotek="$apotek" title="Laporan Ketersediaan Stok dan Kadaluarsa">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>
</div>

<x-page-header
    title="Laporan Monitoring Stok dan Kadaluarsa"
    subtitle="Analisis ketersediaan stok obat serta deteksi dini batch kadaluarsa."
    class="print:hidden no-print page-header-web">
    <x-print-actions />
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.stok')"
    :reset-url="route('laporan.stok')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6"
    :has-reset="request()->anyFilled(['nama', 'barang', 'kategori_id', 'supplier_id', 'batch', 'no_batch', 'status_expired', 'status_stok', 'tanggal', 'dari', 'sampai'])"
    :live-search="true">
    <div class="relative sm:col-span-2 lg:col-span-2">
        <input
            type="text"
            name="nama"
            id="nama"
            value="{{ request('nama') }}"
            placeholder="Cari nama barang..."
            class="form-input pr-8"
            aria-label="Nama Barang"
            title="Nama Barang"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    <select
        name="kategori_id"
        id="kategori_id"
        class="form-input"
        aria-label="Kategori"
        title="Kategori"
    >
        <option value="">Kategori: Semua</option>
        @foreach ($kategoris as $kategori)
            <option
                value="{{ $kategori->id }}"
                @selected(request('kategori_id') == $kategori->id)
            >
                {{ $kategori->nama }}
            </option>
        @endforeach
    </select>

    <select
        name="status_expired"
        id="status_expired"
        class="form-input"
        aria-label="Status Kadaluarsa"
        title="Status Kadaluarsa"
    >
        <option value="">Kadaluarsa: Semua</option>
        <option value="kadaluarsa" @selected(request('status_expired') === 'kadaluarsa')>Kadaluarsa</option>
        <option value="1_bulan" @selected(request('status_expired') === '1_bulan')>< 1 Bulan</option>
        <option value="3_bulan" @selected(request('status_expired') === '3_bulan')>< 3 Bulan</option>
        <option value="normal" @selected(request('status_expired') === 'normal')>Normal</option>
        <option value="tidak_ada" @selected(request('status_expired') === 'tidak_ada')>Tidak Ada Tanggal</option>
    </select>

    <select
        name="status_stok"
        id="status_stok"
        class="form-input"
        aria-label="Status Stok"
        title="Status Stok"
    >
        <option value="">Stok: Semua</option>
        <option value="habis" @selected(request('status_stok') === 'habis')>Habis</option>
        <option value="menipis" @selected(request('status_stok') === 'menipis')>Menipis</option>
        <option value="aman" @selected(request('status_stok') === 'aman')>Aman</option>
    </select>
</x-card-filter>


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

            <table class="table-custom table-stok print-table w-full min-w-[62rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="col-stok-no w-10 text-center !px-2">
                            No
                        </th>
                        <th scope="col" class="col-stok-barang w-44 max-w-[12rem] !px-2.5">
                            Barang
                        </th>
                        <th scope="col" class="col-stok-kategori w-28 max-w-[7.5rem] !px-2 whitespace-nowrap">
                            Kategori
                        </th>
                        <th scope="col" class="col-stok-batch w-24 text-center !px-2 whitespace-nowrap">
                            No. Batch
                        </th>
                        <th scope="col" class="col-stok-rak w-20 text-center !px-2 whitespace-nowrap">
                            No. Rak
                        </th>
                        <th scope="col" class="col-stok-expired text-center w-28 !px-2 whitespace-nowrap">
                            Expired
                        </th>
                        <th scope="col" class="col-stok-status-exp text-center w-24 !px-2 whitespace-nowrap">
                            Status Expired
                        </th>
                        <th scope="col" class="col-stok-awal text-right w-16 !px-2 whitespace-nowrap">
                            Stok Awal
                        </th>
                        <th scope="col" class="col-stok-terjual text-right w-16 !px-2 whitespace-nowrap">
                            Stok Terjual
                        </th>
                        <th scope="col" class="col-stok-rusak text-right w-16 !px-2 whitespace-nowrap">
                            Stok Rusak
                        </th>
                        <th scope="col" class="col-stok-sisa text-right w-16 !px-2 whitespace-nowrap">
                            Sisa Stok
                        </th>
                        <th scope="col" class="col-stok-status-stok text-center w-20 !px-2 whitespace-nowrap">
                            Status Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($stokPerBatch as $index => $item)
                        @php
                            $today = now()->startOfDay();
                            $oneMonth = $today->copy()->addMonth();
                            $threeMonths = $today->copy()->addMonths(3);

                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;

                            $stokAwal = (int) $item->jumlah;
                            $stokTerjual = (int) ($item->stok_terjual ?? 0);
                            $stokRusak = (int) ($item->stok_rusak ?? 0);
                            $sisaStok = $stokAwal - $stokTerjual - $stokRusak;
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr 
                            id="batch-row-{{ $item->id }}"
                            class="batch-row cursor-pointer transition-colors {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}"
                            data-batch-id="{{ $item->id }}"
                            data-barang-nama="{{ $item->barang->nama ?? '—' }}"
                            data-no-batch="{{ $item->no_batch ?? '—' }}"
                            title="Klik untuk melihat riwayat penjualan batch ini"
                        >
                            <!-- No -->
                            <td class="col-stok-no table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <!-- Barang -->
                            <td class="col-stok-barang font-medium text-gray-800 !px-2.5 max-w-[12rem] break-words text-xs leading-snug">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <!-- Kategori -->
                            <td class="col-stok-kategori text-gray-600 !px-2 text-xs max-w-[7.5rem] truncate" title="{{ $item->barang->kategori->nama ?? '—' }}">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <!-- Batch -->
                            <td class="col-stok-batch font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <!-- Rak -->
                            <td class="col-stok-rak font-mono text-gray-600 text-center !px-2 whitespace-nowrap">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <!-- Expired -->
                            <td class="col-stok-expired text-center font-mono !px-2 whitespace-nowrap">
                                @tanggalId($expiredDate, 'd F Y')
                            </td>

                            <!-- Status Expired -->
                            <td class="col-stok-status-exp text-center !px-2 whitespace-nowrap">
                                <x-status-badge type="expired" :date="$expiredDate" />
                            </td>

                            <!-- Stok Awal -->
                            <td class="col-stok-awal table-num font-medium text-gray-700 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokAwal, 0, ',', '.') }}
                            </td>

                            <!-- Stok Terjual -->
                            <td class="col-stok-terjual table-num font-semibold text-indigo-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokTerjual, 0, ',', '.') }}
                            </td>

                            <!-- Stok Rusak -->
                            <td class="col-stok-rusak table-num font-semibold text-rose-600 !px-2 text-right whitespace-nowrap">
                                {{ number_format($stokRusak, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Saat Ini -->
                            <td class="col-stok-sisa table-num font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }} !px-2 text-right whitespace-nowrap">
                                {{ number_format($sisaStok, 0, ',', '.') }}
                            </td>

                            <!-- Status Stok -->
                            <td class="col-stok-status-stok text-center !px-2 whitespace-nowrap">
                                <x-status-badge type="stok" :stok="$sisaStok" :min="$stokMinimum" />
                            </td>
                        </tr>

                        <!-- Panel Riwayat Penjualan Batch (Collapsible) -->
                        <tr 
                            id="batch-history-row-{{ $item->id }}" 
                            class="batch-history-row hidden print:hidden no-print bg-slate-50 border-b border-blue-200"
                        >
                            <td colspan="12" class="p-0">
                                <div id="batch-history-container-{{ $item->id }}" class="p-4 sm:p-5 bg-gradient-to-b from-blue-50/40 via-white to-slate-50 border-t border-blue-100 shadow-inner">
                                    {{-- Konten dimuat secara asinkron lewat JS --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-state colspan="12" />
                    @endforelse
                </tbody>

                @if ($stokPerBatch->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t border-[#D1D5DB] font-bold text-xs">
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

    // =========================================================================
    // FITUR PENELUSURAN RIWAYAT PENJUALAN PER BATCH
    // =========================================================================
    const batchHistoryCache = {};

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toggleBatchHistory(batchId, barangNama, noBatch) {
        const historyRow = document.getElementById(`batch-history-row-${batchId}`);
        const batchRow = document.getElementById(`batch-row-${batchId}`);
        if (!historyRow || !batchRow) return;

        const isCurrentlyOpen = !historyRow.classList.contains('hidden');

        // Tutup semua panel riwayat batch yang sedang terbuka
        document.querySelectorAll('.batch-history-row').forEach(row => {
            row.classList.add('hidden');
        });
        document.querySelectorAll('.batch-row').forEach(row => {
            row.classList.remove('batch-row-active');
        });

        // Jika baris yang sama diklik lagi, biarkan tertutup (toggle off)
        if (isCurrentlyOpen) {
            return;
        }

        // Buka panel riwayat batch yang dipilih
        historyRow.classList.remove('hidden');
        batchRow.classList.add('batch-row-active');

        // Ambil dan tampilkan data
        loadBatchHistoryData(batchId, barangNama, noBatch);
    }

    function closeBatchHistory(batchId) {
        const historyRow = document.getElementById(`batch-history-row-${batchId}`);
        const batchRow = document.getElementById(`batch-row-${batchId}`);
        if (historyRow) historyRow.classList.add('hidden');
        if (batchRow) batchRow.classList.remove('batch-row-active');
    }

    function loadBatchHistoryData(batchId, barangNama, noBatch) {
        const container = document.getElementById(`batch-history-container-${batchId}`);
        if (!container) return;

        // Gunakan cache jika data batch ini sudah pernah dimuat dalam sesi ini
        if (batchHistoryCache[batchId]) {
            renderBatchHistoryView(container, batchHistoryCache[batchId], barangNama, noBatch, batchId);
            return;
        }

        // Tampilkan indikator loading halus
        container.innerHTML = `
            <div class="py-8 flex flex-col items-center justify-center text-gray-500 gap-2">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-medium text-gray-600">Memuat riwayat penjualan batch...</span>
            </div>
        `;

        const url = `/laporan/stok/batch/${batchId}/riwayat-penjualan`;
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data riwayat penjualan (status ' + response.status + ')');
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                batchHistoryCache[batchId] = result;
                renderBatchHistoryView(container, result, barangNama, noBatch, batchId);
            } else {
                throw new Error(result.message || 'Terjadi kesalahan sistem');
            }
        })
        .catch(err => {
            container.innerHTML = `
                <div class="py-6 flex flex-col items-center justify-center text-red-500 gap-2">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs font-semibold">${escapeHtml(err.message || 'Gagal memuat riwayat penjualan.')}</span>
                    <button 
                        type="button" 
                        onclick="delete batchHistoryCache[${batchId}]; loadBatchHistoryData(${batchId}, '${escapeHtml(barangNama)}', '${escapeHtml(noBatch)}')"
                        class="text-xs px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-md transition-colors mt-1 font-semibold cursor-pointer"
                    >
                        Coba Lagi
                    </button>
                </div>
            `;
        });
    }

    function renderBatchHistoryView(container, resData, barangNama, noBatch, batchId) {
        const items = resData.data || [];
        const totalTerjual = resData.total_terjual ?? 0;
        const bNama = resData.batch?.barang_nama || barangNama;
        const bNo = resData.batch?.no_batch || noBatch;

        let rowsHtml = '';
        if (items.length === 0) {
            rowsHtml = `
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500 bg-white">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <span class="text-xs sm:text-sm font-medium text-gray-500">Belum ada riwayat penjualan untuk batch ini.</span>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            rowsHtml = items.map((item, idx) => `
                <tr class="${idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/60'} hover:bg-blue-50/50 transition-colors">
                    <td class="py-2.5 px-3 text-center font-mono text-gray-500 text-xs">${item.no}</td>
                    <td class="py-2.5 px-3.5 font-medium text-gray-800 whitespace-nowrap text-xs">${escapeHtml(item.tanggal)}</td>
                    <td class="py-2.5 px-3.5 text-gray-700 text-xs">${escapeHtml(item.nama_pembeli)}</td>
                    <td class="py-2.5 px-3.5 font-mono text-blue-600 font-semibold text-xs">${escapeHtml(item.no_invoice)}</td>
                    <td class="py-2.5 px-3.5 text-gray-600 text-xs">${escapeHtml(item.petugas)}</td>
                    <td class="py-2.5 px-3.5 text-right font-bold text-indigo-700 text-xs">${item.jumlah.toLocaleString('id-ID')}</td>
                </tr>
            `).join('');
        }

        const footerHtml = items.length > 0 ? `
            <tfoot class="bg-blue-50/60 font-semibold text-gray-700 text-xs border-t border-blue-200">
                <tr>
                    <td colspan="5" class="py-2.5 px-3.5 text-right text-gray-600 uppercase text-[10px] tracking-wider font-semibold">
                        Total Terjual Batch Ini:
                    </td>
                    <td class="py-2.5 px-3.5 text-right font-bold text-indigo-700 text-xs">
                        ${totalTerjual.toLocaleString('id-ID')}
                    </td>
                </tr>
            </tfoot>
        ` : '';

        container.innerHTML = `
            <div class="flex items-center justify-between mb-3 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-800">
                        Riwayat Penjualan Batch: <span class="text-blue-700 font-extrabold">${escapeHtml(bNama)}</span> <span class="text-gray-500 font-normal">(Batch: ${escapeHtml(bNo)})</span>
                    </h4>
                </div>
                <button 
                    type="button" 
                    onclick="event.stopPropagation(); closeBatchHistory(${batchId})" 
                    class="text-gray-400 hover:text-gray-700 hover:bg-gray-200/70 p-1.5 rounded-md transition-colors text-xs font-semibold flex items-center gap-1 cursor-pointer" 
                    title="Tutup Riwayat"
                >
                    <span class="text-[10px] uppercase font-bold text-gray-500 mr-0.5">Tutup</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-x-auto rounded-lg border border-blue-200 shadow-sm bg-white">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-blue-600 text-white font-semibold text-[11px] uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="py-2.5 px-3 text-center w-12 !text-white font-semibold">No.</th>
                            <th scope="col" class="py-2.5 px-3.5 !text-white font-semibold whitespace-nowrap">Tanggal Penjualan</th>
                            <th scope="col" class="py-2.5 px-3.5 !text-white font-semibold">Nama Pembeli/Pelanggan</th>
                            <th scope="col" class="py-2.5 px-3.5 !text-white font-semibold whitespace-nowrap">No. Invoice</th>
                            <th scope="col" class="py-2.5 px-3.5 !text-white font-semibold">Petugas/Kasir</th>
                            <th scope="col" class="py-2.5 px-3.5 text-right !text-white font-semibold whitespace-nowrap">Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        ${rowsHtml}
                    </tbody>
                    ${footerHtml}
                </table>
            </div>
        `;
    }

    // Pasang listener klik pada seluruh baris batch
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.batch-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.closest('button, a, input, select, textarea')) return;
                const batchId = this.dataset.batchId;
                const barangNama = this.dataset.barangNama;
                const noBatch = this.dataset.noBatch;
                toggleBatchHistory(batchId, barangNama, noBatch);
            });
        });
    });
</script>

@endsection