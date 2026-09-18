@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')

<style>
    @media print {
        aside,
        nav,
        header,
        [role="navigation"],
        .print\:hidden,
        .no-print {
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

        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
    }
</style>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Monitoring Stok & Kadaluarsa</h1>

        <p class="text-caption mt-1">
            Analisis ketersediaan stok obat serta deteksi dini batch kadaluarsa.
        </p>
    </div>

    <div class="flex gap-2 shrink-0">
        <a
            href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'csv'])) }}"
            class="btn-secondary py-2 px-4 flex items-center justify-center"
        >
            Export CSV
        </a>

        <button
            onclick="window.print()"
            class="btn-primary py-2 px-4"
        >
            Cetak Laporan
        </button>
    </div>
</div>

<form method="GET" action="{{ route('laporan.stok') }}" class="card-base p-4 mb-6 print:hidden">
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <label for="nama" class="block text-sm font-medium mb-1">
                Nama Barang
            </label>

            <input
                type="text"
                name="nama"
                id="nama"
                value="{{ request('nama') }}"
                placeholder="Cari nama barang..."
                class="input-base w-full"
            >
        </div>

        <div class="flex-1">
            <label for="kategori_id" class="block text-sm font-medium mb-1">
                Kategori
            </label>

            <select
                name="kategori_id"
                id="kategori_id"
                class="input-base w-full"
            >
                <option value="">Semua Kategori</option>

                @foreach ($kategoris as $kategori)
                    <option
                        value="{{ $kategori->id }}"
                        {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}
                    >
                        {{ $kategori->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1">
            <label for="status_expired" class="block text-sm font-medium mb-1">
                Status Kadaluarsa
            </label>

            <select
                name="status_expired"
                id="status_expired"
                class="input-base w-full"
            >
                <option value="">Semua Status</option>

                <option value="kadaluarsa" {{ request('status_expired') === 'kadaluarsa' ? 'selected' : '' }}>
                    Kadaluarsa
                </option>

                <option value="1_bulan" {{ request('status_expired') === '1_bulan' ? 'selected' : '' }}>
                    ≤ 1 Bulan
                </option>

                <option value="3_bulan" {{ request('status_expired') === '3_bulan' ? 'selected' : '' }}>
                    ≤ 3 Bulan
                </option>

                <option value="normal" {{ request('status_expired') === 'normal' ? 'selected' : '' }}>
                    Normal
                </option>

                <option value="tidak_ada" {{ request('status_expired') === 'tidak_ada' ? 'selected' : '' }}>
                    Tidak Ada Tanggal
                </option>
            </select>
        </div>

        <div class="flex-1">
            <label for="status_stok" class="block text-sm font-medium mb-1">
                Status Stok
            </label>
            <select
                name="status_stok"
                id="status_stok"
                class="input-base w-full"
            >
                <option value="">Semua Status</option>
                <option value="habis" {{ request('status_stok') === 'habis' ? 'selected' : '' }}>
                    Habis
                </option>
                <option value="menipis" {{ request('status_stok') === 'menipis' ? 'selected' : '' }}>
                    Menipis
                </option>
                <option value="aman" {{ request('status_stok') === 'aman' ? 'selected' : '' }}>
                    Aman
                </option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="btn-primary py-2 px-4">
                Cari
            </button>

            <a
                href="{{ route('laporan.stok') }}"
                class="btn-secondary py-2 px-4"
            >
                Reset
            </a>
        </div>
    </div>
</form>

<!-- Informasi khusus print -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">
        Laporan Ketersediaan Stok & Kadaluarsa
    </h2>

    <p class="text-xs text-gray-500 mt-1">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </p>
</div>


<!-- ========================================================= -->
<!-- SECTION 1 : STOK PER BATCH -->
<!-- ========================================================= -->

<div class="card-base p-0 overflow-hidden mb-6">

    <div class="px-5 py-4 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-150">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">
                Stok Per Batch
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Rincian mutasi stok per batch: Stok Awal − Stok Terjual − Stok Rusak = Sisa Stok.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan {{ $stokPerBatch->count() }} batch
        </div>
    </div>

    <div class="table-custom-container">
        <div class="overflow-x-auto">

            <table class="table-custom min-w-[72rem]">

                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-12 text-center">
                            No
                        </th>

                        <th scope="col">
                            Barang
                        </th>

                        <th scope="col">
                            Kategori
                        </th>

                        <th scope="col">
                            No. Batch
                        </th>

                        <th scope="col">
                            No. Rak
                        </th>

                        <th scope="col" class="text-center w-28">
                            Expired
                        </th>

                        <th scope="col" class="text-center w-28">
                            Status Expired
                        </th>

                        <th scope="col" class="text-right w-24">
                            Stok Awal
                        </th>

                        <th scope="col" class="text-right w-24">
                            Stok Terjual
                        </th>

                        <th scope="col" class="text-right w-24">
                            Stok Rusak
                        </th>

                        <th scope="col" class="text-right w-28">
                            Sisa Stok Saat Ini
                        </th>

                        <th scope="col" class="text-center w-24">
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

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">

                            <!-- No -->
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <!-- Barang -->
                            <td class="font-medium text-gray-800">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <!-- Kategori -->
                            <td class="text-gray-600">
                                {{ $item->barang->kategori->nama ?? '—' }}
                            </td>

                            <!-- Batch -->
                            <td class="font-mono text-gray-600">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <!-- Rak -->
                            <td class="font-mono text-gray-600">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <!-- Expired -->
                            <td class="text-center font-mono">
                                {{ $expiredDate ? $expiredDate->format('d M Y') : '—' }}
                            </td>

                            <!-- Status Expired -->
                            <td class="text-center">

                                @if (!$expiredDate)

                                    <span class="badge-secondary">
                                        Tidak Ada Tanggal
                                    </span>

                                @elseif ($expiredDate->isSameDay($today) || $expiredDate->isBefore($today))

                                    <span class="badge-danger">
                                        Kadaluarsa
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
                            <td class="table-num font-medium text-gray-700">
                                {{ number_format($stokAwal, 0, ',', '.') }}
                            </td>

                            <!-- Stok Terjual -->
                            <td class="table-num font-semibold text-indigo-600">
                                {{ number_format($stokTerjual, 0, ',', '.') }}
                            </td>

                            <!-- Stok Rusak -->
                            <td class="table-num font-semibold text-rose-600">
                                {{ number_format($stokRusak, 0, ',', '.') }}
                            </td>

                            <!-- Sisa Stok Saat Ini -->
                            <td class="table-num font-bold {{ $sisaStok <= 0 ? 'text-red-600' : ($sisaStok <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ number_format($sisaStok, 0, ',', '.') }}
                            </td>

                            <!-- Status Stok -->
                            <td class="text-center">

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
                                        Stok Kosong
                                    </div>

                                    <div class="empty-state-desc">
                                        Belum ada stok batch yang tersedia.
                                    </div>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if ($stokPerBatch->isNotEmpty())
                    <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300 text-gray-800">
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-right uppercase tracking-wider text-xs text-gray-600">
                                Total (Ringkasan Data Tampil):
                            </td>
                            <td class="table-num px-4 py-3 text-right text-gray-800 font-bold">
                                {{ number_format($totalStokAwal, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-4 py-3 text-right text-indigo-700 font-bold">
                                {{ number_format($totalStokTerjual, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-4 py-3 text-right text-rose-700 font-bold">
                                {{ number_format($totalStokRusak, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-4 py-3 text-right {{ $totalSisaStok <= 0 ? 'text-red-600' : 'text-emerald-700' }} font-bold">
                                {{ number_format($totalSisaStok, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                @endif

            </table>

        </div>
    </div>

</div>


<!-- ========================================================= -->
<!-- SECTION 2 : BATCH MENDEKATI EXPIRED -->
<!-- ========================================================= -->

<div class="card-base p-0 overflow-hidden">

    <div class="px-5 py-4 bg-gray-50/50">

        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">
            Batch Mendekati Expired (&le; 90 Hari)
        </h3>

    </div>

    <div class="table-custom-container">

        <div class="overflow-x-auto">

            <table class="table-custom min-w-[50rem]">

                <thead class="table-custom-header">

                    <tr>

                        <th scope="col" class="w-16">
                            No
                        </th>

                        <th scope="col">
                            Barang
                        </th>

                        <th scope="col">
                            No. Batch
                        </th>

                        <th scope="col">
                            No. Rak
                        </th>

                        <th scope="col" class="text-center">
                            Expired
                        </th>

                        <th scope="col" class="text-right">
                            Sisa Stok
                        </th>

                    </tr>

                </thead>

                <tbody class="table-custom-body">

                    @forelse ($mendekatiExpired as $index => $item)

                        @php
                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;
                            $sisaStokExp = (int) $item->jumlah - (int) ($item->stok_terjual ?? 0) - (int) ($item->stok_rusak ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">

                            <td class="table-num">
                                {{ $index + 1 }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600">
                                {{ $item->no_batch ?? '—' }}
                            </td>

                            <td class="font-mono text-gray-600">
                                {{ $item->no_rak ?? '—' }}
                            </td>

                            <td class="text-center font-mono">
                                {{ $expiredDate ? $expiredDate->format('d M Y') : '—' }}
                            </td>

                            <td class="table-num font-bold text-gray-800">
                                {{ number_format($sisaStokExp, 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="p-0">

                                <div class="empty-state-container">

                                    <div class="empty-state-title">
                                        Tidak Ada Batch Mendekati Expired
                                    </div>

                                    <div class="empty-state-desc">
                                        Tidak terdapat batch dengan stok aktif yang akan kadaluarsa dalam 90 hari.
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection