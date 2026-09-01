@extends('layouts.app')
@section('title', 'Laporan Barang Rusak')

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
        <h1>Laporan Barang Rusak / Kadaluarsa</h1>
        <p class="text-caption mt-1">Analisis barang rusak dan kerugian finansial berdasarkan harga beli batch.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.rusak', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.rusak') }}" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input w-40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input w-40">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-2 px-4">Filter</button>
            <a href="{{ route('laporan.rusak') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Kerugian Barang Rusak & Kadaluarsa</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28">Tanggal Lapor</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="w-32">No. Batch</th>
                    <th scope="col" class="text-right w-24">Jumlah</th>
                    <th scope="col" class="text-right w-36">Total Kerugian</th>
                    <th scope="col">Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($items as $index => $item)
                    <tr>
                        <td class="text-gray-600">{{ $item->tanggal->format('d M Y') }}</td>
                        <td class="font-medium text-gray-800">{{ $item->detailPenerimaan->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600">{{ $item->detailPenerimaan->no_batch ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800">{{ $item->jumlah }}</td>
                        <td class="table-num font-bold text-red-600 font-mono">Rp {{ number_format($item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0), 0, ',', '.') }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Barang Rusak Kosong</div>
                                <div class="empty-state-desc font-sans">Tidak ditemukan riwayat pelaporan obat rusak atau kadaluarsa pada rentang tanggal ini.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="4" class="px-5 py-4 text-right text-gray-600">Total Kerugian Finansial:</td>
                    <td class="px-5 py-4 text-right text-red-650 font-mono text-sm">Rp {{ number_format($totalKerugian, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
