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
        <a href="{{ route('laporan.rusak', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Ekspor Excel
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.rusak') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
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
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    No. Batch
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="no_batch"
                        value="{{ request('no_batch') }}"
                        placeholder="Cari no. batch..."
                        class="form-input pr-8 font-mono"
                    >
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
                    Tanggal Lapor
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
            @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                <a
                    href="{{ route('laporan.rusak') }}"
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
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Kerugian Barang Rusak & Kadaluarsa</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ $dari ? date('d M Y', strtotime($dari)) : 'Semua Periode' }} {{ $sampai ? 's/d ' . date('d M Y', strtotime($sampai)) : '' }}</p>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom w-full table-fixed min-w-0">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-[5%] text-center">No</th>
                    <th scope="col" class="w-[14%] text-center">Tanggal Lapor</th>
                    <th scope="col" class="w-[20%]">Nama Barang</th>
                    <th scope="col" class="w-[17%] text-center">No. Batch</th>
                    <th scope="col" class="w-[7%] text-center">Jumlah</th>
                    <th scope="col" class="w-[15%] text-right">Total Kerugian</th>
                    <th scope="col" class="w-[23%]">Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($items as $index => $item)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="table-num text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-mono text-gray-600">{{ $item->tanggal ? $item->tanggal->translatedFormat('d F Y') : '—' }}</td>
                        <td class="font-medium text-gray-800">{{ $item->detailPenerimaan->barang->nama ?? '—' }}</td>
                        <td class="font-mono text-gray-600 text-center">{{ $item->detailPenerimaan->no_batch ?? '—' }} </td>
                        <td class="table-num font-bold text-gray-800 text-center"> {{ number_format($item->jumlah, 0, ',', '.') }} </td>
                        <td class="table-num font-bold text-red-600 font-mono text-right">Rp {{ number_format($item->jumlah * ($item->detailPenerimaan->harga_beli ?? 0), 0, ',', '.') }}</td>
                        <td class="text-gray-600 truncate max-w-xs" title="{{ $item->keterangan }}">{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">
                                    @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                                        Barang Rusak Tidak Ditemukan
                                    @else
                                        Barang Rusak Kosong
                                    @endif
                                </div>
                                <div class="empty-state-desc">
                                    @if(request()->anyFilled(['nama_barang', 'barang', 'cari', 'no_batch', 'batch', 'dari', 'sampai', 'jenis', 'keterangan']))
                                        Tidak ditemukan data pelaporan obat rusak atau kadaluarsa yang cocok dengan filter kriteria Anda.
                                    @else
                                        Tidak ditemukan riwayat pelaporan obat rusak atau kadaluarsa yang terdaftar di sistem.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="5" class="px-5 py-4 text-right uppercase tracking-wider text-gray-600">Total Kerugian Finansial:</td>
                    <td class="table-num px-5 py-4 text-right text-red-650 font-mono text-sm font-bold">Rp {{ number_format($totalKerugian, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
