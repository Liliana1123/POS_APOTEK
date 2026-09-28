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
    }
</style>

<div class="screen-only">
{{-- Page Header --}}
<x-page-header
    title="Riwayat Penjualan"
    subtitle="Daftar rekaman seluruh transaksi kasir apotek."
>
    <a href="{{ route('penjualan.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Transaksi Baru</span>
    </a>
</x-page-header>

{{-- Filter & Search --}}
<x-card-filter
    :action="route('penjualan.index')"
    :reset-url="route('penjualan.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3"
    :has-reset="request()->anyFilled(['cari', 'tanggal_awal', 'tanggal_akhir'])"
>
    {{-- Cari No. Invoice --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Cari No. Invoice
        </label>
        <div class="relative">
            <input
                type="text"
                name="cari"
                value="{{ request('cari') }}"
                placeholder="Cari no. invoice..."
                class="form-input pr-8"
            >
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
    </div>

    {{-- Tanggal Awal --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Awal
        </label>
        <input
            type="date"
            name="tanggal_awal"
            value="{{ request('tanggal_awal') }}"
            class="form-input"
        >
    </div>

    {{-- Tanggal Akhir --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Akhir
        </label>
        <input
            type="date"
            name="tanggal_akhir"
            value="{{ request('tanggal_akhir') }}"
            class="form-input"
        >
    </div>
</x-card-filter>

{{-- Tombol Cetak Laporan --}}
<div class="mb-4 flex justify-end print:hidden">
    <button
        type="button"
        onclick="window.print()"
        class="btn-secondary flex items-center justify-center gap-2 py-2 px-4"
    >
        <x-heroicon-o-printer class="h-4 w-4" />
        <span>Cetak Laporan</span>
    </button>
</div>

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
                    <th scope="col" class="w-36 text-center whitespace-nowrap">Kasir</th>
                    <th class="px-5 py-3 text-center">Total Diskon</th>
                    <th scope="col" class="w-44 text-center whitespace-nowrap">Total Transaksi</th>
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
                                    href="{{ route('penjualan.show', $penjualan) }}"
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
                        <td class="text-center font-mono font-semibold text-gray-800 whitespace-nowrap">
                            {{ $penjualan->no_faktur }}
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-center whitespace-nowrap">
                            {{ $penjualan->tanggal->format('d M Y') }}
                        </td>

                       {{-- Pelanggan --}}
                        <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                            @if($penjualan->pelanggan && $penjualan->pelanggan->is_member)
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ $penjualan->pelanggan->nama }}
                                    </span>

                                     @php
                                        $statusMember = trim((string) $penjualan->pelanggan->status_member);

                                        $memberVariant = match($statusMember) {
                                            'Member Keluarga Nakes' => 'info',
                                            'Member Only' => 'warning',
                                            default => 'success',
                                        };
                                    @endphp

                                    <x-badge :variant="$memberVariant">
                                        {{ $statusMember ?: 'Member Pelanggan Tetap' }}
                                    </x-badge>
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
                            Rp {{ number_format($penjualan->detail->sum('diskon'), 0, ',', '.') }}
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="text-center font-semibold whitespace-nowrap">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $penjualans->links() }}
</div>

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
                        {{ $penjualan->tanggal->format('d M Y') }}
                    </td>

                    <td class="border p-2">
                        {{ $penjualan->pelanggan?->nama ?? 'Umum' }}
                    </td>

                    <td class="border p-2">
                        {{ $penjualan->user?->name ?? '-' }}
                    </td>

                    <td class="border p-2 text-right">
                        Rp {{ number_format($penjualan->detail->sum('diskon'), 0, ',', '.') }}
                    </td>

                    <td class="border p-2 text-right">
                        Rp {{ number_format($penjualan->total, 0, ',', '.') }}
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

        <tfoot>
            <tr>
                <th colspan="5" class="border p-2 text-right">
                    Total
                </th>

                <th class="border p-2 text-right">
                    Rp {{ number_format($totalDiskonLaporan, 0, ',', '.') }}
                </th>

                <th class="border p-2 text-right">
                    Rp {{ number_format($totalTransaksiLaporan, 0, ',', '.') }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>


{{-- Modal Detail Penjualan --}}
    <div
    id="modalDetailPenjualan"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    aria-labelledby="judulDetailPenjualan"
    >

    <div
        class="flex w-full flex-col overflow-hidden rounded-xl border border-blue-100 bg-white shadow-2xl"
        style="width: min(900px, 96vw); max-width: 900px; height: 90dvh; max-height: 90dvh;"
    >

        {{-- Header Modal --}}
        <div class="flex shrink-0 items-center justify-between border-b border-blue-100 px-5 py-4">
            <div class="flex items-center gap-4">
                
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <x-heroicon-o-document-text class="h-6 w-6" />
            </div>
                <div>
                    <h2
                        id="judulDetailPenjualan"
                        class="text-xl font-bold text-slate-800"
                    >
                        Detail Penjualan
                    </h2>

                    <p class="mt-1 text-sm text-blue-600">
                        Informasi lengkap transaksi penjualan
                    </p>
                </div>
            </div>

            <button
                type="button"
                onclick="tutupDetailPenjualan()"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="Tutup modal"
            >
                <x-heroicon-o-x-mark class="h-6 w-6" />
            </button>
        </div>

        {{-- Isi Modal --}}
        <div
            id="isiDetailPenjualan"
            class="min-h-0 flex-1 space-y-2 overflow-y-auto p-3"
        >
            <div class="py-10 text-center text-sm text-slate-500">
                Memuat detail penjualan...
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="flex shrink-0 justify-end border-t border-blue-100 px-6 py-4">
            <button
                type="button"
                onclick="tutupDetailPenjualan()"
                class="rounded-lg border border-blue-300 px-7 py-3 text-sm font-semibold text-blue-600 transition hover:bg-blue-50"
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

            const formatTanggal = (tanggal) => {
                if (!tanggal) return '-';

                const date = new Date(tanggal);

                if (Number.isNaN(date.getTime())) {
                    return tanggal;
                }

                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            };

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

                return `
                    <tr class="border-b border-blue-100 last:border-b-0">
                        <td class="px-3 py-4 text-center">
                            ${index + 1}
                        </td>

                        <td class="px-3 py-4 font-medium text-slate-800">
                            ${escapeHtml(namaBarang)}
                        </td>

                        <td class="px-3 py-4 text-center">
                            ${escapeHtml(item.jumlah ?? 0)}
                        </td>

                        <td class="px-3 py-4 text-right whitespace-nowrap">
                            ${rupiah(item.harga_jual)}
                        </td>

                        <td class="px-3 py-4 text-right whitespace-nowrap">
                            ${rupiah(item.diskon)}
                        </td>

                        <td class="px-3 py-4 text-right font-medium whitespace-nowrap">
                            ${rupiah(item.subtotal)}
                        </td>
                    </tr>
                `;
            }).join('');

            
            const pelanggan = penjualan.pelanggan?.nama || 'Umum';

            const statusMember = penjualan.pelanggan?.status_member || '';

            const isMember = Boolean(penjualan.pelanggan?.is_member);

            const memberVariant = {
                'Member Keluarga Nakes': 'info',
                'Member Only': 'warning',
            }[statusMember] || 'success';

            const labelMember = statusMember || 'Member Pelanggan Tetap';

            const kasir = penjualan.user?.name || '-';
            const metode = penjualan.metode_pembayaran || '-';
            const isPiutang = String(penjualan.metode_pembayaran || '').toLowerCase() === 'piutang';

            isiModal.innerHTML = `
                {{-- Informasi Transaksi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 ${isPiutang ? 'xl:grid-cols-6' : 'xl:grid-cols-5'} gap-3">
                    
                {{-- No. Invoice --}}
                <div class="min-w-0 rounded-xl border border-blue-100 bg-gradient-to-br from-white-50 to-white p-3">
                    <div class="flex items-center gap-2 text-blue-600 mb-3">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                        <span class="text-xs text-blue-500">No. Invoice</span>
                    </div>

                    <p class="font-bold text-sm text-slate-800 break-all">
                        ${escapeHtml(penjualan.no_faktur)}
                    </p>
                </div>

                {{-- Tanggal --}}
                <div class="min-w-0 rounded-xl border border-blue-100 bg-gradient-to-br from-white-50 to-white p-3">
                    <div class="flex items-center gap-2 text-blue-600 mb-3">
                        <x-heroicon-o-calendar class="w-5 h-5" />
                        <span class="text-xs text-blue-500">Tanggal</span>
                    </div>

                    <p class="font-bold text-sm text-slate-800">
                        ${escapeHtml(formatTanggal(penjualan.tanggal))}
                    </p>
                </div>

                {{-- Pelanggan --}}
                <div class="min-w-0 rounded-xl border border-blue-100 bg-gradient-to-br from-white-50 to-white p-3">
                    <div class="flex items-center gap-2 text-blue-600 mb-3">
                        <x-heroicon-o-user class="w-5 h-5" />
                        <span class="text-xs text-blue-500">Pelanggan</span>
                    </div>

                    <div class="flex flex-col items-start gap-2">
                        <p class="font-bold text-sm text-slate-800">
                            ${escapeHtml(pelanggan)}
                        </p>

                        ${
                            isMember
                                ? `<span class="inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-[10px] leading-tight font-medium uppercase tracking-normal text-center
                                    ${
                                        memberVariant === 'info'
                                            ? 'bg-blue-50 text-blue-700'
                                            : memberVariant === 'warning'
                                                ? 'bg-amber-50 text-amber-700'
                                                : 'bg-green-50 text-green-700'
                                    }">
                                    ${escapeHtml(labelMember)}
                                </span>`
                                : ''
                        }

                        ${(() => {
                            const discountPercent = penjualan.member_discount_percent_used;
                            if (!isMember || discountPercent === null || discountPercent === undefined) return '';
                            return `<span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-1 text-[10px] font-bold text-emerald-700 uppercase tracking-wide">
                                DISKON MEMBER ${Number(discountPercent)}%
                            </span>`;
                        })()}
                    </div>
                </div>

                {{-- Kasir --}}
                <div class="min-w-0 rounded-xl border border-blue-100 bg-gradient-to-br from-white-50 to-white p-3">
                    <div class="flex items-center gap-2 text-blue-600 mb-3">
                        <x-heroicon-o-user class="w-5 h-5" />
                        <span class="text-xs text-blue-500">Kasir</span>
                    </div>

                    <p class="font-bold text-sm text-slate-800">
                        ${escapeHtml(kasir)}
                    </p>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="min-w-0 rounded-xl border border-blue-100 bg-gradient-to-br from-white-50 to-white p-3">
                    <div class="flex items-center gap-2 text-blue-600 mb-3">
                        <x-heroicon-o-credit-card class="w-5 h-5" />
                        <span class="text-xs text-blue-500">Metode Pembayaran</span>
                    </div>

                    <p class="font-bold text-sm text-slate-800 uppercase">
                        ${escapeHtml(metode)}
                    </p>
                </div>

                ${isPiutang ? `
                {{-- Jatuh Tempo --}}
                <div class="min-w-0 rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50/50 to-white p-3">
                    <div class="flex items-center gap-2 text-amber-600 mb-3">
                        <x-heroicon-o-calendar-days class="w-5 h-5" />
                        <span class="text-xs text-amber-600 font-semibold">Jatuh Tempo</span>
                    </div>

                    <p class="font-bold text-sm text-slate-800">
                        ${escapeHtml(penjualan.due_date_formatted || '-')}
                    </p>
                </div>
                ` : ''}

            </div>

                {{-- Rincian Barang: tanpa ikon --}}
                <div class="rounded-xl border border-blue-100 p-3 sm:p-4">
                    <h3 class="mb-4 text-lg font-bold text-slate-800">
                        Rincian Barang
                    </h3>

                    <div class="overflow-x-auto rounded-lg border border-blue-100">
                        <table class="w-full min-w-[700px] text-sm">
                            <thead class="bg-blue-50 text-slate-700">
                                <tr>
                                    <th class="px-3 py-4 text-center">No.</th>
                                    <th class="px-3 py-4 text-left">Nama Barang</th>
                                    <th class="px-3 py-4 text-center">Jumlah</th>
                                    <th class="px-3 py-4 text-right">Harga</th>
                                    <th class="px-3 py-4 text-right">Diskon</th>
                                    <th class="px-3 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody class="text-slate-700">
                                ${
                                    barisBarang ||
                                    `<tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-slate-500">
                                            Tidak ada rincian barang.
                                        </td>
                                    </tr>`
                                }
                            </tbody>
                        </table>
                    </div>

                    {{-- Ringkasan --}}
                    <div class="mt-2 rounded-lg bg-white p-3">
                        <div class="ml-auto w-full space-y-4 sm:w-72">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm text-slate-600">
                                    Total Diskon
                                </span>
                                <span class="font-semibold text-red-600">
                                    ${rupiah(totalDiskon)}
                                </span>
                            </div>

                            <div class="border-t border-blue-200 pt-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="font-bold text-slate-800">
                                        Total Transaksi
                                    </span>
                                   <span class="text-base font-bold text-blue-600">
                                        ${rupiah(penjualan.total)}
                                    </span>
                                </div>
                            </div>
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
