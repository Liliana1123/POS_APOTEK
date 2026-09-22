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

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.penerimaan') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    No. Faktur
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="no_faktur"
                        value="{{ request('no_faktur') }}"
                        placeholder="Cari no. faktur..."
                        class="form-input pr-8 font-mono"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Supplier
                </label>
                <select name="supplier_id" class="form-input">
                    <option value="">Semua Supplier</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>
                            {{ $s->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Nama Barang
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="nama_barang"
                        value="{{ request('nama_barang') }}"
                        placeholder="Cari nama barang..."
                        class="form-input pr-8"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Penerimaan
                </label>

                <div class="flex items-center gap-2">
                    <input
                        type="date"
                        name="dari"
                        value="{{ $dari }}"
                        class="form-input min-w-0"
                    >

                    <span class="text-sm text-gray-400">-</span>

                    <input
                        type="date"
                        name="sampai"
                        value="{{ $sampai }}"
                        class="form-input min-w-0"
                    >
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai']))
                <a
                    href="{{ route('laporan.penerimaan') }}"
                    class="btn-secondary py-1.5 px-4 flex items-center justify-center"
                >
                    Reset
                </a>
            @endif

            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
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
        <table class="table-custom min-w-[70rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-12 text-center">No</th>
                    <th scope="col" class="w-28 text-center">Tanggal</th>
                    <th scope="col" class="w-36">No. Faktur</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col" class="text-right w-24">Jumlah</th>
                    <th scope="col" class="text-right w-32">Harga Beli</th>
                    <th scope="col" class="text-right w-36">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono text-gray-600">{{ $item->penerimaan->tanggal->format('d M Y') }}</td>
                        <td class="font-semibold text-gray-800 font-mono">{{ $item->penerimaan->no_faktur }}</td>
                        <td class="text-gray-600 font-medium">{{ $item->penerimaan->supplier->nama ?? '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $item->barang->nama ?? '—' }}</td>
                        <td class="table-num font-bold text-gray-800 text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="table-num text-gray-600 font-mono text-right">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-800 font-mono text-right">Rp {{ number_format($item->harga_beli * $item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai']))
                                        Penerimaan Tidak Ditemukan
                                    @else
                                        Penerimaan Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['no_faktur', 'supplier_id', 'nama_barang', 'dari', 'sampai']))
                                        Tidak ada data penerimaan barang masuk yang cocok dengan filter kriteria Anda.
                                    @else
                                        Tidak ada data penerimaan barang masuk pada rentang tanggal ini.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="7" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">Total Nilai Penerimaan:</td>
                    <td class="table-num px-5 py-4 text-right text-emerald-700 font-mono text-sm font-bold">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
