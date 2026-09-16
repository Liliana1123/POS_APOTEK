@extends('layouts.app')
@section('title', 'Penjualan')

@section('content')
{{-- Page Header --}}
<x-page-header
    title="Riwayat Penjualan"
    subtitle="Daftar rekaman seluruh transaksi kasir apotek."
>
    <a href="{{ route('penjualan.create') }}" class="btn-primary flex items-center gap-2">
        <x-heroicon-o-plus class="w-4 h-4" />
        <span>Transaksi Baru</span>
    </a>
</x-page-header>

{{-- Filter & Search --}}
<x-card-filter
    :action="route('penjualan.index')"
    :reset-url="route('penjualan.index')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3"
    :has-reset="request()->anyFilled(['cari', 'tanggal_awal', 'tanggal_akhir'])"
>
    {{-- Cari No. Invoice --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Cari No. Invoice
        </label>
        <div class="relative">
            <input
                type="text"
                name="cari"
                value="{{ request('cari') }}"
                placeholder="Cari no. invoice..."
                class="form-input pr-8"
            >
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>
    </div>

    {{-- Tanggal Awal --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Awal
        </label>
        <input
            type="date"
            name="tanggal_awal"
            value="{{ request('tanggal_awal') }}"
            class="form-input"
        >
    </div>

    {{-- Tanggal Akhir --}}
    <div>
        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5 font-sans">
            Tanggal Akhir
        </label>
        <input
            type="date"
            name="tanggal_akhir"
            value="{{ request('tanggal_akhir') }}"
            class="form-input"
        >
    </div>
</x-card-filter>

{{-- Table --}}
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[67rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28 text-center whitespace-nowrap">Aksi</th>
                    <th scope="col" class="w-52 text-center whitespace-nowrap">No. Invoice</th>
                    <th scope="col" class="w-36 text-center whitespace-nowrap">Tanggal</th>
                    <th scope="col" class="w-52 text-center whitespace-nowrap">Pelanggan</th>
                    <th scope="col" class="w-36 text-center whitespace-nowrap">Kasir</th>
                    <th class="px-5 py-3 text-center">Total Diskon</th>
                    <th scope="col" class="w-44 text-center whitespace-nowrap">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($penjualans as $index => $penjualan)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        {{-- Aksi --}}
                        <td class="text-center">
                            <x-table-action
                                :show-url="route('penjualan.show', $penjualan)"
                                align="justify-center"
                            />
                        </td>

                        {{-- No. Invoice --}}
                        <td class="text-center font-mono font-semibold text-gray-800 whitespace-nowrap">
                            {{ $penjualan->no_faktur }}
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-center whitespace-nowrap">
                            {{ $penjualan->tanggal->format('d M Y') }}
                        </td>

                       {{-- Pelanggan --}}
                        <td class="px-5 py-3.5 text-center text-gray-600 font-medium">
                            @if($penjualan->pelanggan && $penjualan->pelanggan->is_member)
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ $penjualan->pelanggan->nama }}
                                    </span>

                                     @php
                                        $statusMember = trim((string) $penjualan->pelanggan->status_member);

                                        $memberVariant = match($statusMember) {
                                            'Member Keluarga Nakes' => 'info',
                                            'Member Only' => 'warning',
                                            default => 'success',
                                        };
                                    @endphp

                                    <x-badge :variant="$memberVariant">
                                        {{ $statusMember ?: 'Member Pelanggan Tetap' }}
                                    </x-badge>
                                </div>
                            @else
                                <span class="text-gray-600 font-medium">
                                    Umum
                                </span>
                            @endif
                        </td>

                        {{-- Kasir --}}
                        <td class="text-center whitespace-nowrap">
                            {{ $penjualan->user->name }}
                        </td>

                        {{-- Total Diskon --}}
                        <td class="px-5 py-3.5 text-center font-semibold text-red-600">
                            Rp {{ number_format($penjualan->detail->sum('diskon'), 0, ',', '.') }}
                        </td>

                        {{-- Total Transaksi --}}
                        <td class="text-center font-semibold whitespace-nowrap">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $penjualans->links() }}
</div>
@endsection
