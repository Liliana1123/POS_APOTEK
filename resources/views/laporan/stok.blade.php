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

    <div class="px-5 py-4 border-b bg-gray-50/50">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">
            Stok Per Batch
        </h3>
    </div>

    <div class="table-custom-container">
        <div class="overflow-x-auto">

            <table class="table-custom min-w-[60rem]">

                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-16">
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

                        <th scope="col" class="text-center w-36">
                            Expired
                        </th>

                        <th scope="col" class="text-center w-36">
                            Status Expired
                        </th>

                        <th scope="col" class="text-right w-28">
                            Stok
                        </th>

                        <th scope="col" class="text-center w-28">
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
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">

                            <!-- No -->
                            <td class="table-num">
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

                                    <span class="badge-warning">
                                        ≤ 1 Bulan
                                    </span>

                                @elseif ($expiredDate->lte($today->copy()->addMonths(3)))

                                    <span class="badge-orange">
                                        ≤ 3 Bulan
                                    </span>

                                @else

                                    <span class="badge-success">
                                        Normal
                                    </span>

                                @endif

                            </td>

                            <!-- Stok -->
                            <td class="table-num font-bold text-gray-800">
                                {{ $item->stok }}
                            </td>

                            <!-- Status Stok -->
                            <td class="text-center">

                                @if ($item->stok <= $item->barang->stok_minimum)

                                    <span class="badge-danger">
                                        Menipis
                                    </span>

                                @else

                                    <span class="badge-success">
                                        Aman
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="p-0">

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

            </table>

        </div>
    </div>

</div>


<!-- ========================================================= -->
<!-- SECTION 2 : BATCH MENDEKATI EXPIRED -->
<!-- ========================================================= -->

<div class="card-base p-0 overflow-hidden">

    <div class="px-5 py-4 border-b bg-gray-50/50">

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
                            Stok
                        </th>

                    </tr>

                </thead>

                <tbody class="table-custom-body">

                    @forelse ($mendekatiExpired as $index => $item)

                        @php
                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;
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
                                {{ $item->stok }}
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