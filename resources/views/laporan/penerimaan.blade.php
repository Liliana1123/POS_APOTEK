@extends('layouts.app')
@section('title', 'Laporan Penerimaan')

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
        <h1>Laporan Penerimaan Barang</h1>
        <p class="text-caption mt-1">Ringkasan barang masuk dan akumulasi nilai pembelian dari supplier.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.penerimaan', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.penerimaan') }}" class="flex flex-wrap gap-4 items-end">
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
            <a href="{{ route('laporan.penerimaan') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Penerimaan Barang Masuk</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28">Tanggal</th>
                    <th scope="col" class="w-36">No. Faktur</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="text-right w-24">Jumlah</th>
                    <th scope="col" class="text-right w-32">Harga Beli</th>
                    <th scope="col" class="text-right w-36">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($items as $index => $item)
                    <tr>
                        <td class="text-gray-600">{{ $item->penerimaan->tanggal->format('d M Y') }}</td>
                        <td class="font-semibold text-gray-800 font-mono">{{ $item->penerimaan->no_faktur }}</td>
                        <td class="text-gray-600 font-medium">{{ $item->penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800">{{ $item->jumlah }}</td>
                        <td class="table-num text-gray-600 font-mono">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-800 font-mono">Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Penerimaan Kosong</div>
                                <div class="empty-state-desc">Tidak ada data penerimaan barang masuk pada rentang tanggal ini.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="6" class="px-5 py-4 text-right text-gray-600">Total Nilai Penerimaan:</td>
                    <td class="px-5 py-4 text-right text-green-600 font-mono text-sm">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
