@extends('layouts.app')
@section('title', 'Penjualan')

@section('content')
<style>
    @media screen {
        .print-only {
            display: none !important;
        }
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        .print-only,
        .print-only * {
            visibility: visible !important;
        }

        .print-only {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: white !important;
            color: black !important;
        }

        .screen-only {
            display: none !important;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        .grand-total-print {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #1e40af;
            page-break-inside: avoid;
        }

        .grand-total-print > div {
            display: flex;
            justify-content: flex-end;
            gap: 30px;
            margin-bottom: 5px;
        }

        .grand-total-print span {
            font-weight: 600;
        }

        .grand-total-print strong {
            min-width: 150px;
            text-align: right;
        }
            }
</style>

<div class="screen-only">
{{-- Page Header --}}
<x-page-header
    title="Riwayat Penjualan"
    subtitle="Daftar rekaman seluruh transaksi kasir apotek."
>
    <div class="flex items-center gap-2">

        {{-- Tambah Transaksi Baru --}}
        <a
            href="{{ route('penjualan.create') }}"
            class="btn-primary flex items-center gap-2 py-2 px-4"
        >
            <x-heroicon-o-plus class="w-4 h-4" />
            <span>Tambah Transaksi Baru</span>
        </a>
    </div>
</x-page-header>

{{-- Filter & Search --}}
<x-card-filter
    :action="route('penjualan.index')"
    :reset-url="route('penjualan.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-8"
    action-cols="sm:col-span-2 md:col-span-2"
    :live-search="true"
    :has-reset="request()->anyFilled(['cari', 'tanggal_awal', 'tanggal_akhir'])"
>
    {{-- Cari No. Invoice (Dipotong 1/4 menjadi 3 dari 8 kolom) --}}
    <div class="relative sm:col-span-2 md:col-span-3">
        <input
            type="text"
            name="cari"
            value="{{ request('cari') }}"
            placeholder="Cari no. invoice..."
            class="form-input pr-8 font-mono"
            aria-label="No. Invoice"
            title="No. Invoice"
        >
        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
        </span>
    </div>

    {{-- Rentang Tanggal (Tanggal Awal - Tanggal Akhir, 3 dari 8 kolom) --}}
    <div class="flex items-center gap-1.5 sm:col-span-2 md:col-span-3">
        <input
            type="date"
            name="tanggal_awal"
            value="{{ request('tanggal_awal') }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="inline-flex items-center text-gray-400 shrink-0">
            <x-heroicon-m-minus class="w-3.5 h-3.5" />
        </span>
        <input
            type="date"
            name="tanggal_akhir"
            value="{{ request('tanggal_akhir') }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>
</x-card-filter>

{{-- Table --}}
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[67rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28 text-center whitespace-nowrap">Aksi</th>
                    <th class="w-52">
                    @php
                        $invoiceDirection =
                            request('sort', 'no_faktur') === 'no_faktur'
                            && request('direction', 'desc') === 'desc'
                                ? 'asc'
                                : 'desc';
                    @endphp

                    <a
                        href="{{ request()->fullUrlWithQuery([
                            'sort' => 'no_faktur',
                            'direction' => $invoiceDirection,
                            'page' => 1,
                        ]) }}"
                        class="flex w-full items-center justify-center gap-1 whitespace-nowrap hover:text-blue-600"
                    >
                        <span>No. Invoice</span>

                        <span class="inline-flex flex-col leading-[7px]">
                            <span
                                class="text-[7px] {{ request('sort', 'no_faktur') === 'no_faktur' && request('direction', 'desc') === 'asc'
                                    ? 'text-blue-600'
                                    : 'text-gray-300' }}"
                            >
                                ▲
                            </span>

                            <span
                                class="text-[7px] {{ request('sort', 'no_faktur') === 'no_faktur' && request('direction', 'desc') === 'desc'
                                    ? 'text-blue-600'
                                    : 'text-gray-300' }}"
                            >
                                ▼
                            </span>
                        </span>
                    </a>
                </th>
                    <th scope="col" class="w-36 text-center whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="w-52 text-center whitespace-nowrap">Pelanggan</th>
                    <th scope="col" class="w-44 text-center whitespace-nowrap">Kasir</th>
                    <th scope="col" class="w-28 text-center whitespace-nowrap">Total Diskon</th>
                    <th scope="col" class="w-32 text-center whitespace-nowrap">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($penjualans as $index => $penjualan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">

                        {{-- Aksi: Lihat Detail / Cetak Struk --}}
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Tombol Lihat Detail --}}
                                <button
                                    type="button"
                                    class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors inline-flex items-center justify-center"
                                    style="color: #2563EB;"
                                    title="Lihat Detail Penjualan"
                                    aria-label="Lihat Detail Penjualan"
                                    onclick="bukaDetailPenjualan({{ $penjualan->id }})"
                                >
                                    <x-heroicon-o-eye class="w-4 h-4" />
                                </button>

                                {{-- Tombol Cetak Struk (tetap seperti semula) --}}
                                <a
                                    href="{{ route('penjualan.show', ['penjualan' => $penjualan, 'from' => 'penjualan_index']) }}"
                                    class="btn-secondary !p-1.5 hover:bg-blue-50 hover:border-blue-300 transition-colors inline-flex items-center justify-center"
                                    style="color: #000000;"
                                    title="Lihat Struk Penjualan"
                                    aria-label="Lihat Struk Penjualan"
                                >
                                    <x-heroicon-o-printer class="w-4 h-4" />
                                </a>

                            </div>
                        </td>

                        {{-- No. Invoice --}}
                        <td class="text-center font-semibold text-gray-800 whitespace-nowrap">
                            <x-copyable :text="$penjualan->no_faktur" />
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-center whitespace-nowrap">
                            @tanggalId($penjualan->tanggal)
                        </td>

                       {{-- Pelanggan --}}
                        <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                            @if($penjualan->pelanggan && $penjualan->pelanggan->is_member)
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ $penjualan->pelanggan->nama }}
                                    </span>

                                    <x-status-badge type="member" :value="$penjualan->pelanggan->status_member ?: 'Member Pelanggan Tetap'" />
                                </div>
                            @else
                                <span class="text-gray-600 font-medium">
                                    Umum
                                </span>
                            @endif
                        </td>

                        {{-- Kasir --}}
                        <td class="text-center whitespace-nowrap">
                            {{ $penjualan->user->name }}
                        </td>

                        {{-- Total Diskon --}}
                        <td class="px-5 py-3.5 text-center font-semibold text-red-600">
                            @rupiah($penjualan->detail->sum('diskon'))
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="text-center font-semibold whitespace-nowrap">
                            @rupiah($penjualan->total)
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$penjualans" />

</div>

{{-- Laporan khusus untuk dicetak --}}
<div class="print-only">
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold">
            LAPORAN RIWAYAT PENJUALAN
        </h1>

        <p class="text-sm">
            Periode:
            {{ request('tanggal_awal') ?: 'Semua tanggal' }}
            s.d.
            {{ request('tanggal_akhir') ?: 'Semua tanggal' }}
        </p>

        @if(request('cari'))
            <p class="text-sm">
                No. Invoice: {{ request('cari') }}
            </p>
        @endif
    </div>

    <table class="w-full border-collapse text-sm">
        <thead>
            <tr>
                <th class="border p-2">No.</th>
                <th class="border p-2">No. Invoice</th>
                <th class="border p-2">Tanggal</th>
                <th class="border p-2">Pelanggan</th>
                <th class="border p-2">Kasir</th>
                <th class="border p-2">Total Diskon</th>
                <th class="border p-2">Total Transaksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($laporanPenjualans as $index => $penjualan)
                <tr>
                    <td class="border p-2 text-center">
                        {{ $index + 1 }}
                    </td>

                    <td class="border p-2">
                        {{ $penjualan->no_faktur }}
                    </td>

                    <td class="border p-2">
                        @tanggalId($penjualan->tanggal)
                    </td>

                    <td class="border p-2">
                        {{ $penjualan->pelanggan?->nama ?? 'Umum' }}
                    </td>

                    <td class="border p-2">
                        {{ $penjualan->user?->name ?? '-' }}
                    </td>

                    <td class="border p-2 text-right">
                        @rupiah($penjualan->detail->sum('diskon'))
                    </td>

                    <td class="border p-2 text-right">
                        @rupiah($penjualan->total)
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border p-2 text-center">
                        Tidak ada data penjualan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

        <div class="grand-total-print">
        <div>
            <span>Total Diskon</span>
            <strong>
                @rupiah($totalDiskonLaporan)
            </strong>
        </div>

        <div>
            <span>Total Transaksi</span>
            <strong>
                @rupiah($totalTransaksiLaporan)
            </strong>
        </div>
    </div>
</div>


{{-- Modal Detail Penjualan --}}
<div
    id="modalDetailPenjualan"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-3 sm:p-4 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    aria-labelledby="judulDetailPenjualan"
>
    <div
        class="relative w-full flex flex-col bg-white rounded-2xl shadow-2xl p-4 sm:p-5 overflow-hidden"
        style="max-width: 900px; width: min(900px, 95vw); max-height: 85vh;"
    >
        {{-- Header Modal --}}
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shrink-0">
                    <x-heroicon-o-document-text class="h-5 w-5 text-blue-600" />
                </div>
                <div>
                    <h2
                        id="judulDetailPenjualan"
                        class="text-base sm:text-lg font-bold text-slate-800 leading-tight"
                    >
                        Detail Penjualan
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Informasi lengkap transaksi penjualan
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="tutupDetailPenjualan()"
                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="Tutup modal"
            >
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        {{-- Isi Modal (Scrollable jika konten melebihi 85vh) --}}
        <div id="isiDetailPenjualan" class="min-h-0 flex-1 overflow-y-auto pr-1 space-y-3">
            <div class="py-12 text-center text-sm text-slate-400">
                Memuat detail penjualan...
            </div>
        </div>

        {{-- Footer Modal / Tombol Tutup --}}
        <div class="pt-3 border-t border-slate-100 mt-2 flex justify-end shrink-0">
            <button
                type="button"
                onclick="tutupDetailPenjualan()"
                class="rounded-xl border border-blue-400 px-6 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50 cursor-pointer shadow-sm"
            >
                Tutup
            </button>
        </div>
    </div>
</div>


<script>
    async function bukaDetailPenjualan(id) {
        const modal = document.getElementById('modalDetailPenjualan');
        const isiModal = document.getElementById('isiDetailPenjualan');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        isiModal.innerHTML = `
            <div class="py-10 text-center text-sm text-slate-500">
                Memuat detail penjualan...
            </div>
        `;

        try {
            const response = await fetch(
                `/penjualan/${id}/detail`,
                {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil detail penjualan.');
            }

            const data = await response.json();
            const penjualan = data.penjualan;

            const escapeHtml = (value) => {
                return String(value ?? '-').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[char]);
            };

            const rupiah = (angka) =>
                'Rp ' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(Number(angka) || 0);

            const formatTanggalIndo = (tanggalStr) => {
                if (!tanggalStr) return '-';
                const date = new Date(tanggalStr);
                if (Number.isNaN(date.getTime())) return tanggalStr;
                const bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const d = String(date.getDate()).padStart(2, '0');
                const m = bulanIndo[date.getMonth()];
                const y = date.getFullYear();
                return `${d} ${m} ${y}`;
            };

            const formatJamIndo = (createdStr) => {
                if (!createdStr) return '';
                const date = new Date(createdStr);
                if (Number.isNaN(date.getTime())) return '';
                const h = String(date.getHours()).padStart(2, '0');
                const min = String(date.getMinutes()).padStart(2, '0');
                return `${h}:${min}`;
            };

            const displayTanggal = penjualan.tanggal_formatted || formatTanggalIndo(penjualan.tanggal);
            const displayJam = penjualan.jam_formatted ?? '';

            const detailBarang = Array.isArray(penjualan.detail)
                ? penjualan.detail
                : [];

            const totalDiskon = detailBarang.reduce(
                (total, item) =>
                    total + (Number(item.diskon) || 0),
                0
            );

            const barisBarang = detailBarang.map((item, index) => {
                const namaBarang =
                    item.detail_penerimaan?.barang?.nama
                    || 'Barang tidak ditemukan';

                const satuanBarang = item.detail_penerimaan?.barang?.satuan?.nama || '';

                return `
                    <tr class="border-b border-slate-100 last:border-b-0">
                        <td class="py-2.5 px-3 text-center text-slate-500">${index + 1}</td>
                        <td class="py-2.5 px-3 text-left font-medium text-slate-800">${escapeHtml(namaBarang)}</td>
                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                            <span class="font-medium text-slate-800">${escapeHtml(item.jumlah ?? 0)} ${escapeHtml(satuanBarang)}</span>
                        </td>
                        <td class="py-2.5 px-3 text-center whitespace-nowrap tabular-nums text-slate-700">${rupiah(item.harga_jual)}</td>
                        <td class="py-2.5 px-3 text-center whitespace-nowrap tabular-nums text-slate-700">${rupiah(item.diskon)}</td>
                        <td class="py-2.5 px-3 text-right font-semibold text-slate-800 whitespace-nowrap tabular-nums">${rupiah(item.subtotal)}</td>
                    </tr>
                `;
            }).join('');

            const pelanggan = penjualan.pelanggan?.nama || 'Umum';
            const statusMember = penjualan.pelanggan?.status_member || '';
            const isMember = Boolean(penjualan.pelanggan?.is_member);

            const memberVariant = {
                'Member Keluarga Nakes': 'indigo',
                'Member Only': 'warning',
            }[statusMember] || 'success';

            const labelMember = statusMember || 'Member Pelanggan Tetap';

            const kasir = penjualan.user?.name || '-';
            const metode = penjualan.metode_pembayaran || '-';
            const isPiutang = String(penjualan.metode_pembayaran || '').toLowerCase() === 'piutang';

            const isResep = (penjualan.is_resep === true)
                || String(penjualan.jenis_transaksi || '').toLowerCase() === 'resep';
            const idDokter    = isResep && penjualan.id_dokter    ? String(penjualan.id_dokter).trim()    : '';
            const namaDokter  = isResep && penjualan.nama_dokter  ? String(penjualan.nama_dokter).trim()  : '';
            const alamatKlinik = isResep && penjualan.alamat_lembaga ? String(penjualan.alamat_lembaga).trim() : '';

            isiModal.innerHTML = `
                {{-- Kartu Informasi Transaksi --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 ${isPiutang ? 'lg:grid-cols-6' : 'lg:grid-cols-5'} gap-2 sm:gap-2.5">

                    {{-- No. Invoice --}}
                    <div class="min-w-0 rounded-xl border border-blue-100/90 bg-white p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                            <span class="text-[11px] font-medium text-slate-400">No. Invoice</span>
                        </div>
                        <p class="font-bold text-[13px] text-slate-800 break-all leading-tight font-mono">
                            ${escapeHtml(penjualan.no_faktur)}
                        </p>
                    </div>

                    {{-- Tanggal dan Jam --}}
                    <div class="min-w-0 rounded-xl border border-blue-100/90 bg-white p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="text-[11px] font-medium text-slate-400">Tanggal dan Jam</span>
                        </div>
                        <div class="text-[13px] font-bold text-slate-800 leading-tight flex items-center gap-2">
                            <span>${escapeHtml(displayTanggal)}</span>

                            ${displayJam ? `
                                <span class="font-normal text-[#9fbcfaff]">|</span>
                                <span>${escapeHtml(displayJam)}</span>
                            ` : ''}
                        </div>
                    </div>

                    {{-- Pelanggan --}}
                    <div class="min-w-0 rounded-xl border border-blue-100/90 bg-white p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span class="text-[11px] font-medium text-slate-400">Pelanggan</span>
                        </div>
                        <div class="flex flex-col items-start gap-1">
                            <p class="font-bold text-[13px] text-slate-800 leading-tight">
                                ${escapeHtml(pelanggan)}
                            </p>
                            ${
                                isMember
                                    ? `<span class="inline-flex items-center justify-center rounded-md px-1.5 py-0.5 text-[9px] leading-tight font-semibold uppercase tracking-normal
                                        ${ memberVariant === 'indigo' ? 'bg-indigo-50 text-indigo-700'
                                         : memberVariant === 'info' ? 'bg-blue-50 text-blue-700'
                                         : memberVariant === 'warning' ? 'bg-amber-50 text-amber-700'
                                         : 'bg-emerald-50 text-emerald-700' }">
                                        ${escapeHtml(labelMember)}
                                    </span>`
                                    : ''
                            }
                        </div>
                    </div>

                    {{-- Kasir --}}
                    <div class="min-w-0 rounded-xl border border-blue-100/90 bg-white p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                            <span class="text-[11px] font-medium text-slate-400">Kasir</span>
                        </div>
                        <p class="font-bold text-[13px] text-slate-800 leading-tight">
                            ${escapeHtml(kasir)}
                        </p>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="min-w-0 rounded-xl border border-blue-100/90 bg-white p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            <span class="text-[11px] font-medium text-slate-400">Metode Pembayaran</span>
                        </div>
                        <p class="font-bold text-[13px] text-slate-800 uppercase leading-tight">
                            ${escapeHtml(metode)}
                        </p>
                    </div>

                    ${isPiutang ? `
                    {{-- Jatuh Tempo --}}
                    <div class="min-w-0 rounded-xl border border-amber-200 bg-amber-50/20 p-2.5 sm:p-3 flex flex-col justify-between min-h-[64px]">
                        <div class="flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span class="text-[11px] font-semibold text-amber-600">Jatuh Tempo</span>
                        </div>
                        <p class="font-bold text-[13px] text-slate-800 leading-tight">
                            ${escapeHtml(penjualan.due_date_formatted || '-')}
                        </p>
                    </div>
                    ` : ''}

                </div>

                {{-- Baris Jenis Transaksi & Informasi Resep --}}
                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; align-items:stretch;">

                    {{-- Panel Jenis Transaksi --}}
                    <div style="flex:0 0 auto; width:220px; max-width:100%; border:1px solid #dbeafe; border-radius:12px; background:#fff; padding:10px 14px; display:flex; align-items:center; gap:10px; min-height:64px; box-sizing:border-box;">
                        <div style="width:36px; height:36px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z"/>
                                <path d="m8.5 8.5 7 7"/>
                            </svg>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:4px; min-width:0;">
                            <span style="font-size:11px; font-weight:500; color:#94a3b8;">Jenis Transaksi</span>
                            ${
                                isResep
                                    ? `<span style="display:inline-flex; align-items:center; padding:2px 10px; border-radius:9999px; font-size:11px; font-weight:700; background:#dbeafe; color:#2563eb;">Resep</span>`
                                    : `<span style="display:inline-flex; align-items:center; padding:2px 10px; border-radius:9999px; font-size:11px; font-weight:700; background:#d1fae5; color:#059669;">Non Resep</span>`
                            }
                        </div>
                    </div>

                    {{-- Panel Informasi Resep --}}
                    <div style="flex:1 1 300px; min-width:0; border:1px solid #dbeafe; border-radius:12px; background:#fff; padding:10px 14px; display:flex; align-items:center; gap:10px; min-height:64px; box-sizing:border-box; overflow:hidden;">
                        ${
                            isResep
                                ? `<div style="width:36px; height:36px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div style="flex:1; min-width:0; display:grid; grid-template-columns:1fr 1.5fr 2fr; gap:8px; overflow:hidden;">
                                    <div style="min-width:0; overflow:hidden;">
                                        <span style="display:block; font-size:11px; font-weight:500; color:#94a3b8;">ID Dokter</span>
                                        <p style="font-weight:700; font-size:13px; color:#1e293b; word-break:break-word; margin:2px 0 0; line-height:1.3;">${escapeHtml(idDokter || '-')}</p>
                                    </div>
                                    <div style="min-width:0; overflow:hidden; border-left:1px solid #9fbcfaff; padding-left:8px;">
                                        <span style="display:block; font-size:11px; font-weight:500; color:#94a3b8;">Nama Dokter</span>
                                        <p style="font-weight:700; font-size:13px; color:#1e293b; word-break:break-word; margin:2px 0 0; line-height:1.3;">${escapeHtml(namaDokter || '-')}</p>
                                    </div>
                                    <div style="min-width:0; overflow:hidden; border-left:1px solid #9fbcfaff; padding-left:8px;">
                                        <span style="display:block; font-size:11px; font-weight:500; color:#94a3b8;">Alamat Klinik</span>
                                        <p style="font-weight:700; font-size:13px; color:#1e293b; word-break:break-word; margin:2px 0 0; line-height:1.3;">${escapeHtml(alamatKlinik || '-')}</p>
                                    </div>
                                </div>`
                                : `<div style="width:36px; height:36px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                                        <line x1="7" y1="8" x2="17" y2="8"/>
                                        <line x1="7" y1="12" x2="13" y2="12"/>
                                    </svg>
                                </div>
                                <div style="min-width:0;">
                                    <span style="font-size:11px; font-weight:500; color:#94a3b8;">Informasi Resep</span>
                                    <span style="display:block; font-size:11px; color:#000000ff; margin-top:2px;">Tidak ada data resep (Transaksi Non Resep)</span>
                                </div>`
                        }
                    </div>

                </div>

                {{-- Section Rincian Barang --}}
                <div class="mt-3.5">

                    {{-- Header Rincian Barang --}}
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-5 h-5 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                <path d="m3.3 7 8.7 5 8.7-5"/>
                                <path d="M12 22V12"/>
                            </svg>
                        </div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Rincian Barang</h3>
                    </div>

                    {{-- Tabel Barang --}}
                    <div class="overflow-x-auto rounded-lg border border-slate-100">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-blue-50/70 text-slate-600">
                                    <th class="py-2.5 px-3 text-center w-12 font-semibold">No.</th>
                                    <th class="py-2.5 px-3 text-left font-semibold">Nama Barang</th>
                                    <th class="py-2.5 px-3 text-center w-28 font-semibold">Jumlah</th>
                                    <th class="py-2.5 px-3 text-center w-28 font-semibold">Harga</th>
                                    <th class="py-2.5 px-3 text-center w-24 font-semibold">Diskon</th>
                                    <th class="py-2.5 px-3 text-right w-32 font-semibold">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                ${
                                    barisBarang ||
                                    `<tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-slate-400">
                                            Tidak ada rincian barang.
                                        </td>
                                    </tr>`
                                }
                            </tbody>
                        </table>
                    </div>

                    {{-- Ringkasan Diskon & Total Transaksi --}}
                    <div class="mt-3 flex flex-col items-end gap-1.5">
                        <div class="flex items-center justify-between gap-8 w-64 sm:w-72 text-xs">
                            <span class="text-slate-500 font-medium whitespace-nowrap">Total Diskon</span>
                            <span class="text-slate-700 font-semibold tabular-nums text-right whitespace-nowrap">${rupiah(totalDiskon)}</span>
                        </div>
                        <div class="flex items-center justify-between gap-8 w-64 sm:w-72 border-t border-slate-100 pt-1.5">
                            <span class="text-xs sm:text-sm font-bold text-slate-800 whitespace-nowrap">Total Transaksi</span>
                            <span class="text-sm sm:text-base font-bold text-blue-600 tabular-nums text-right whitespace-nowrap">${rupiah(penjualan.total)}</span>
                        </div>
                    </div>

                </div>
            `;

        } catch (error) {
            isiModal.innerHTML = `
                <div class="rounded-lg bg-red-50 p-4 text-sm text-red-600">
                    Gagal memuat detail penjualan.
                    Silakan coba lagi.
                </div>
            `;

            console.error(error);
        }
    }

    function tutupDetailPenjualan() {
        const modal = document.getElementById('modalDetailPenjualan');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('modalDetailPenjualan')
        .addEventListener('click', function (event) {
            if (event.target === this) {
                tutupDetailPenjualan();
            }
        });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            tutupDetailPenjualan();
        }
    });
</script>

@endsection
