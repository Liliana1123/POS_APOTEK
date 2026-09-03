@extends('layouts.app')
@section('title', 'Penjualan')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Riwayat Penjualan</h1>
        <p class="text-xs text-gray-500 mt-0.5">Daftar rekaman seluruh transaksi kasir apotek.</p>
    </div>
    <a href="{{ route('penjualan.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Transaksi Baru</span>
    </a>
</div>

<!-- Filter & Search Card -->
<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ route('penjualan.index') }}" class="flex flex-wrap gap-2 items-center">
        <div class="relative shrink-0 w-full sm:w-64">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari no. faktur..."
                class="form-input pr-8">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
            </div>
            <button type="submit" class="btn-primary py-1.5 px-4">
                Cari
            </button>
            @if(request()->filled('cari'))
                <a href="{{ route('penjualan.index') }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Clear
                </a>
            @endif
        
    </form>
</div>

<div class="table-custom-container shadow-sm">
    <div class="overflow-x-auto">
    <table class="table-custom min-w-[58rem]">
        <thead class="table-custom-header">
            <tr>
                <th class="w-16 text-center">No</th>
                <th>No. Faktur</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total Transaksi</th>
                <th class="text-right w-32">Aksi</th>
            </tr>
        </thead>
        <tbody class="table-custom-body">
                @forelse ($penjualans as $index => $penjualan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                    <td class="text-center font-medium">{{ $penjualans->firstItem() + $index }}</td>
                    <td class="font-semibold text-gray-800 font-mono">{{ $penjualan->no_faktur }}</td>
                    <td class="text-gray-600">{{ $penjualan->tanggal->format('d M Y') }}</td>
                    <td class="text-gray-600 font-medium">
                        {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                        @if(isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                            <span class="text-[9px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded font-mono ml-1 font-semibold">Member</span>
                        @endif
                    </td>
                    <td class="text-gray-600">{{ $penjualan->user->name ?? '—' }}</td>
                    <td class="text-right font-bold text-gray-800 font-mono">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <a href="{{ route('penjualan.show', $penjualan) }}" class="btn-secondary !p-1.5" title="Detail">
                            <x-heroicon-o-eye class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-0">
                        <div class="empty-state-container">
                            <div class="empty-state-title">Belum Ada Transaksi</div>
                            <div class="empty-state-desc">Belum ada transaksi penjualan yang tercatat di sistem.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<div class="mt-4">{{ $penjualans->links() }}</div>
@endsection
