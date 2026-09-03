@extends('layouts.app')
@section('title', 'Barang Rusak')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
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
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('rusak.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama barang..."
                class="form-input">
        </div>
        <div class="w-full md:w-48">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-1.5 px-4">
                Filter
            </button>
            @if(request()->anyFilled(['cari', 'tanggal']))
                <a href="{{ route('rusak.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<div class="table-custom-container shadow-sm">
    <div class="overflow-x-auto">
    <table class="table-custom min-w-[55rem]">
        <thead class="table-custom-header">
            <tr>
                <th class="w-16 text-center">No</th>
                <th>Tanggal</th>
                <th>Barang / Obat</th>
                <th>No. Batch</th>
                <th class="text-right">Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
                @forelse ($rusaks as $index => $rusak)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="text-center font-medium">{{ $rusaks->firstItem() + $index }}</td>
                    <td class="text-gray-600">{{ $rusak->tanggal->format('d M Y') }}</td>
                    <td class="font-medium text-gray-800">{{ $rusak->detailPenerimaan->barang->nama }}</td>
                    <td class="font-mono text-gray-600">{{ $rusak->detailPenerimaan->no_batch }}</td>
                    <td class="text-right font-bold text-red-600">{{ $rusak->jumlah }}</td>
                    <td class="text-gray-600">{{ $rusak->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">Belum Ada Data Barang Rusak</div>
                            <div class="empty-state-desc">Belum ada pencatatan barang rusak atau kadaluarsa.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4">{{ $rusaks->links() }}</div>
@endsection
