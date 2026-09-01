@extends('layouts.app')
@section('title', 'Barang Rusak')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Daftar Pencatatan Barang Rusak</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola dan laporkan obat rusak atau kadaluarsa.</p>
    </div>
    <a href="{{ route('rusak.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Catat Barang Rusak</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-150 p-4 mb-6">
    <form method="GET" action="{{ route('rusak.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama barang..."
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">
        </div>
        <div class="w-full md:w-48">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-5 py-1.5 rounded-lg font-semibold shadow-sm transition-colors">
                Filter
            </button>
            @if(request()->anyFilled(['cari', 'tanggal']))
                <a href="{{ route('rusak.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-4 py-1.5 rounded-lg font-semibold transition-colors flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden">
    <table class="w-full text-xs text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-[10px] tracking-wider border-b">
            <tr>
                <th class="px-5 py-3">Tanggal</th>
                <th class="px-5 py-3">Barang / Obat</th>
                <th class="px-5 py-3">No. Batch</th>
                <th class="px-5 py-3 text-right">Jumlah</th>
                <th class="px-5 py-3">Keterangan</th>
            </tr>
        </thead>
<tbody class="divide-y divide-gray-150">
                @forelse ($rusaks as $index => $rusak)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors">
                    <td class="px-5 py-3.5 text-gray-600">{{ $rusak->tanggal->format('d M Y') }}</td>
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $rusak->detailPenerimaan->barang->nama }}</td>
                    <td class="px-5 py-3.5 font-mono text-gray-600">{{ $rusak->detailPenerimaan->no_batch }}</td>
                    <td class="px-5 py-3.5 text-right font-bold text-red-600">{{ $rusak->jumlah }}</td>
                    <td class="px-5 py-3.5 text-gray-600">{{ $rusak->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada data barang rusak.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $rusaks->links() }}</div>
@endsection
