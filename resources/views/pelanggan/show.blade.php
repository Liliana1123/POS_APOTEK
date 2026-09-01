@extends('layouts.app')
@section('title', 'Detail Pelanggan')

@section('content')
<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>Profil Pelanggan</h1>
        <p class="text-caption mt-1">Detail profil, statistik belanja, dan riwayat transaksi.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('pelanggan.index') }}" class="btn-secondary py-2 px-4">
            Kembali
        </a>
        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn-primary py-2 px-4">
            Edit Profil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Profil Card -->
    <div class="card-base p-6 md:col-span-1">
        <div class="flex items-center gap-3.5 border-b pb-4 mb-4">
            <div class="bg-blue-50 text-blue-600 rounded-full p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-base leading-tight">{{ $pelanggan->nama }}</h3>
                <span class="text-xs text-gray-500 mt-1 block">
                    @if ($pelanggan->is_member)
                        <span class="badge-success">Member</span>
                    @else
                        <span class="badge-neutral">Umum</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="space-y-3.5 text-xs text-gray-600">
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Member ID</span>
                <span class="font-mono text-gray-800 font-semibold">{{ $pelanggan->member_id ?? '—' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-semibold">Nomor Telepon / HP</span>
                <span class="text-gray-800">{{ $pelanggan->telepon ?? '—' }}</span>
            </div>
            @if ($pelanggan->is_member)
                <div>
                    <span class="text-gray-400 block text-[10px] uppercase font-semibold">Member Sejak</span>
                    <span class="text-gray-800">{{ $pelanggan->member_since ? $pelanggan->member_since->format('d M Y') : '—' }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Card 1: Total Transaksi -->
        <div class="card-base p-6 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Transaksi</span>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-gray-800 block">{{ $pelanggan->penjualan_count }}</span>
                <span class="text-xs text-gray-500 mt-1 block font-sans">kali belanja di apotek</span>
            </div>
        </div>

        <!-- Card 2: Total Belanja -->
        <div class="card-base p-6 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Belanja</span>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-gray-850 block font-mono">Rp {{ number_format($pelanggan->total_belanja, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-500 mt-1 block font-sans">akumulasi total kotor</span>
            </div>
        </div>

        <!-- Card 3: Total Penghematan -->
        <div class="card-base p-6 flex flex-col justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Penghematan</span>
            <div class="mt-4">
                <span class="text-2xl font-extrabold text-green-600 block font-mono">Rp {{ number_format($pelanggan->total_hemat, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-500 mt-1 block font-sans">akumulasi hemat diskon</span>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History List -->
<div class="table-custom-container">
    <div class="p-4 border-b bg-gray-50/50">
        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Riwayat Transaksi Pelanggan</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[45rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-36">Tanggal</th>
                    <th scope="col">Nomor Faktur</th>
                    <th scope="col" class="text-right w-36">Hemat Diskon</th>
                    <th scope="col" class="text-right w-40">Total Akhir</th>
                    <th scope="col" class="text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($penjualans as $penjualan)
                    @php
                        $totalDiskon = $penjualan->detail()->sum('diskon');
                    @endphp
                    <tr>
                        <td class="text-gray-600">{{ $penjualan->tanggal->format('d M Y') }}</td>
                        <td class="font-semibold text-gray-850 font-mono">{{ $penjualan->no_faktur }}</td>
                        <td class="table-num font-semibold text-green-600">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-850">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <a href="{{ route('penjualan.show', $penjualan) }}" class="btn-secondary py-1 px-2.5 text-[10px] font-semibold">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Riwayat Transaksi Kosong</div>
                                <div class="empty-state-desc">Pelanggan ini belum pernah melakukan transaksi penjualan di apotek.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $penjualans->links() }}
</div>
@endsection
