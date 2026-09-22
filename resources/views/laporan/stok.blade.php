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

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.stok') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="nama" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Nama Barang
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        value="{{ request('nama') }}"
                        placeholder="Cari nama barang..."
                        class="form-input pr-8"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label for="kategori_id" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Kategori
                </label>
                <select
                    name="kategori_id"
                    id="kategori_id"
                    class="form-input"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            @selected(request('kategori_id') == $kategori->id)
                        >
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_expired" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Kadaluarsa
                </label>
                <select
                    name="status_expired"
                    id="status_expired"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="kadaluarsa" @selected(request('status_expired') === 'kadaluarsa')>
                        Kadaluarsa
                    </option>
                    <option value="1_bulan" @selected(request('status_expired') === '1_bulan')>
                        ≤ 1 Bulan
                    </option>
                    <option value="3_bulan" @selected(request('status_expired') === '3_bulan')>
                        ≤ 3 Bulan
                    </option>
                    <option value="normal" @selected(request('status_expired') === 'normal')>
                        Normal
                    </option>
                    <option value="tidak_ada" @selected(request('status_expired') === 'tidak_ada')>
                        Tidak Ada Tanggal
                    </option>
                </select>
            </div>

            <div>
                <label for="status_stok" class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Status Stok
                </label>
                <select
                    name="status_stok"
                    id="status_stok"
                    class="form-input"
                >
                    <option value="">Semua Status</option>
                    <option value="habis" @selected(request('status_stok') === 'habis')>
                        Habis
                    </option>
                    <option value="menipis" @selected(request('status_stok') === 'menipis')>
                        Menipis
                    </option>
                    <option value="aman" @selected(request('status_stok') === 'aman')>
                        Aman
                    </option>
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                <a
                    href="{{ route('laporan.stok') }}"
                    class="btn-secondary py-1.5 px-4 flex items-center justify-center"
                >
                    Reset
                </a>
            @endif

            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>

            <a
                href="{{ route('laporan.stok') }}"
                class="btn-secondary py-2 px-4"
            >
                Clear
            </a>
        </div>
    </form>
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
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans">
                Stok Per Batch
            </h2>
            <p class="text-caption mt-0.5">
                Rincian mutasi stok per batch: Stok Awal − Stok Terjual − Stok Rusak = Sisa Stok.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $stokPerBatch->count() }}</span> batch
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
                            Sisa Stok
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

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
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
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Stok Batch Tidak Ditemukan
                                        @else
                                            Stok Batch Kosong
                                        @endif
                                    </div>
                                    <div class="empty-state-desc">
                                        @if(request()->anyFilled(['nama', 'kategori_id', 'status_expired', 'status_stok']))
                                            Tidak ada data batch obat yang cocok dengan filter kriteria Anda.
                                        @else
                                            Belum ada data stok batch obat yang terdaftar di sistem.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($stokPerBatch->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total (Ringkasan Data Tampil):
                            </td>
                            <td class="table-num px-5 py-4 text-right text-gray-800 font-bold">
                                {{ number_format($totalStokAwal, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right text-indigo-700 font-bold">
                                {{ number_format($totalStokTerjual, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right text-rose-700 font-bold">
                                {{ number_format($totalStokRusak, 0, ',', '.') }}
                            </td>
                            <td class="table-num px-5 py-4 text-right {{ $totalSisaStok <= 0 ? 'text-red-600' : 'text-emerald-700' }} font-bold">
                                {{ number_format($totalSisaStok, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4"></td>
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
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-sans">
                Batch Mendekati Expired (&le; 90 Hari)
            </h2>
            <p class="text-caption mt-0.5">
                Daftar obat dengan sisa stok aktif yang akan kadaluarsa dalam rentang waktu dekat.
            </p>
        </div>
        <div class="text-xs font-medium text-gray-500">
            Menampilkan <span class="font-semibold text-gray-700">{{ $mendekatiExpired->count() }}</span> batch
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
                        <th scope="col" class="text-right w-28">
                            Sisa Stok
                        </th>
                        <th scope="col" class="text-center w-24">
                            Status Stok
                        </th>
                    </tr>
                </thead>

                <tbody class="table-custom-body">
                    @forelse ($mendekatiExpired as $index => $item)
                        @php
                            $today = now()->startOfDay();
                            $expiredDate = $item->expired_date
                                ? \Carbon\Carbon::parse($item->expired_date)->startOfDay()
                                : null;
                            $sisaStokExp = (int) $item->jumlah - (int) ($item->stok_terjual ?? 0) - (int) ($item->stok_rusak ?? 0);
                            $stokMinimum = (int) ($item->barang->stok_minimum ?? 0);
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                            <td class="table-num text-center">
                                {{ $index + 1 }}
                            </td>

                            <td class="font-medium text-gray-800">
                                {{ $item->barang->nama ?? '—' }}
                            </td>

                            <td class="text-gray-600">
                                {{ $item->barang->kategori->nama ?? '—' }}
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

                            <td class="table-num font-bold {{ $sisaStokExp <= 0 ? 'text-red-600' : ($sisaStokExp <= $stokMinimum ? 'text-amber-600' : 'text-emerald-700') }}">
                                {{ number_format($sisaStokExp, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @if ($sisaStokExp <= 0)
                                    <span class="badge-danger">Habis</span>
                                @elseif ($sisaStokExp <= $stokMinimum)
                                    <span class="badge-warning">Menipis</span>
                                @else
                                    <span class="badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">
                                        Tidak Ada Batch Mendekati Expired
                                    </div>
                                    <div class="empty-state-desc">
                                        Tidak terdapat batch dengan sisa stok aktif yang akan kadaluarsa dalam 90 hari.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if ($mendekatiExpired->isNotEmpty())
                    <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                        <tr>
                            <td colspan="7" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">
                                Total Sisa Stok Mendekati Expired:
                            </td>
                            <td class="table-num px-5 py-4 text-right text-red-600 font-bold">
                                {{ number_format($mendekatiExpired->sum(fn ($i) => (int) $i->jumlah - (int) ($i->stok_terjual ?? 0) - (int) ($i->stok_rusak ?? 0)), 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@endsection