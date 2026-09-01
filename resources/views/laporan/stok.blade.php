@extends('layouts.app')
@section('title', 'Laporan Stok')

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
    @page {
        margin: 15mm 10mm 15mm 10mm;
    }
}
</style>

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Laporan Monitoring Stok & Kadaluarsa</h1>
        <p class="text-caption mt-1">Analisis ketersediaan stok obat serta deteksi dini batch kadaluarsa.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Ketersediaan Stok & Kadaluarsa</h2>
    <p class="text-xs text-gray-500 mt-1">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
</div>

<!-- Section 1: Stok Minimum Status -->
<div class="card-base p-0 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b bg-gray-50/50">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Status Ketersediaan Minimum Barang</h3>
    </div>
    
    <div class="table-custom-container">
        <div class="overflow-x-auto">
            <table class="table-custom min-w-[45rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-16">No</th>
                        <th scope="col">Nama Barang / Obat</th>
                        <th scope="col">Kategori</th>
                        <th scope="col" class="text-right w-36">Stok Saat Ini</th>
                        <th scope="col" class="text-right w-36">Stok Minimum</th>
                        <th scope="col" class="text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="table-custom-body divide-y divide-gray-150">
                    @forelse ($barangs as $index => $barang)
                        @php $stok = $barang->stokTotal(); @endphp
                        <tr>
                            <td class="table-num">{{ $index + 1 }}</td>
                            <td class="font-medium text-gray-800">{{ $barang->nama }}</td>
                            <td class="text-gray-600">{{ $barang->kategori->nama ?? '—' }}</td>
                            <td class="table-num font-bold text-gray-800">{{ $stok }}</td>
                            <td class="table-num text-gray-600">{{ $barang->stok_minimum }}</td>
                            <td class="text-center">
                                @if ($stok <= $barang->stok_minimum)
                                    <span class="badge-danger">Menipis</span>
                                @else
                                    <span class="badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">Barang Kosong</div>
                                    <div class="empty-state-desc">Belum ada data barang terdaftar di sistem.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section 2: Expiring Batches -->
<div class="card-base p-0 overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50/50">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Batch Mendekati Expired (&le; 90 Hari)</h3>
    </div>
    
    <div class="table-custom-container">
        <div class="overflow-x-auto">
            <table class="table-custom min-w-[45rem]">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" class="w-16">No</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">No. Batch</th>
                        <th scope="col" class="text-center w-36">Expired Date</th>
                        <th scope="col" class="text-right w-32">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody class="table-custom-body divide-y divide-gray-150">
                    @forelse ($mendekatiExpired as $index => $item)
                        <tr>
                            <td class="table-num">{{ $index + 1 }}</td>
                            <td class="font-medium text-gray-800">{{ $item->barang->nama ?? '—' }}</td>
                            <td class="font-mono text-gray-600">{{ $item->no_batch }}</td>
                            <td class="text-center font-mono font-semibold {{ $item->sudahExpired() ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $item->expired_date->format('d M Y') }}
                            </td>
                            <td class="table-num font-bold text-gray-700">{{ $item->stok }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class="empty-state-container">
                                    <div class="empty-state-title">Tidak Ada Batch Expired</div>
                                    <div class="empty-state-desc font-sans">Tidak ditemukan batch aktif obat yang mendekati tanggal kadaluarsa dalam waktu dekat.</div>
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
