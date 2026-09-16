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
    <form method="GET" action="{{ route('penjualan.index') }}">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Cari No. Invoice --}}
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                    Cari No. Invoice
                </label>

                <div class="relative">
                    <input
                        type="text"
                        name="cari"
                        value="{{ request('cari') }}"
                        placeholder="Cari no. invoice..."
                        class="form-input w-full pr-8"
                    >

                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                </div>
            </div>

            {{-- Tanggal Awal --}}
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                    Tanggal Awal
                </label>

                <input
                    type="date"
                    name="tanggal_awal"
                    value="{{ request('tanggal_awal') }}"
                    class="form-input w-full"
                >
            </div>

            {{-- Tanggal Akhir --}}
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                    Tanggal Akhir
                </label>

                <input
                    type="date"
                    name="tanggal_akhir"
                    value="{{ request('tanggal_akhir') }}"
                    class="form-input w-full"
                >
            </div>

        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">

            <button
                type="submit"
                class="btn-primary py-1.5 px-4"
            >
                Cari
            </button>

            @if(request()->filled('cari') || request()->filled('tanggal_awal') || request()->filled('tanggal_akhir'))
                <a
                    href="{{ route('penjualan.index') }}"
                    class="btn-secondary py-1.5 px-4 flex items-center justify-center"
                >
                    Clear
                </a>
            @endif

        </div>

    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden">
    <table class="w-full table-fixed text-xs text-left">
        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-[10px] tracking-wider border-b">
            <tr>
                <th class="px-5 py-3 text-center w-32">Aksi</th>
                <th class="px-5 py-3 text-center">No. Invoice</th>
                <th class="px-5 py-3 text-center">Tanggal</th>
                <th class="px-5 py-3 text-center">Pelanggan</th>
                <th class="px-5 py-3 text-center">Kasir</th>
                <th class="px-5 py-3 text-center">Total Transaksi</th>
            </tr>
        </thead>
<tbody class="divide-y divide-gray-150">
    @forelse ($penjualans as $index => $penjualan)
        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors">

            {{-- Aksi --}}
            <td class="px-5 py-3.5 text-center">
                <a href="{{ route('penjualan.show', $penjualan) }}"
                   class="btn-secondary !p-1.5 inline-flex items-center justify-center"
                   title="Detail">
                    <x-heroicon-o-eye class="w-4 h-4" />
                </a>
            </td>

            {{-- No. Invoice --}}
            <td class="px-5 py-3.5 text-center font-semibold text-gray-800 font-mono">
                {{ $penjualan->no_faktur }}
            </td>

            {{-- Tanggal --}}
            <td class="px-5 py-3.5 text-center text-gray-600">
                {{ $penjualan->tanggal->format('d M Y') }}
            </td>

            {{-- Pelanggan --}}
            <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                <div class="flex items-center justify-center gap-1.5">
                    <span>{{ $penjualan->pelanggan->nama ?? 'Umum' }}</span>

                    @if(isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                        <span class="text-[9px] bg-green-50 text-green-700 px-1.5 py-0.5 rounded font-mono font-semibold">
                            Member
                        </span>
                    @endif
                </div>
            </td>

            {{-- Kasir --}}
            <td class="px-5 py-3.5 text-center text-gray-600">
                {{ $penjualan->user->name }}
            </td>

            {{-- Total Transaksi --}}
            <td class="px-5 py-3.5 text-center font-bold text-gray-800">
                Rp {{ number_format($penjualan->total, 0, ',', '.') }}
            </td>

        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                Belum ada transaksi.
            </td>
        </tr>
    @endforelse
</tbody>
    </table>
    </div>
</div>

<div class="mt-4">{{ $penjualans->links() }}</div>
@endsection
