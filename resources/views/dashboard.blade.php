@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <h1>Selamat datang, {{ auth()->user()->name }}</h1>
    <p class="text-caption mt-1">
        @if (auth()->user()->isAdmin())
            Peran: <span class="font-semibold text-blue-600">Administrator</span> &bull; Akses penuh ke seluruh sistem POS Apotek.
        @else
            Peran: <span class="font-semibold text-green-600">Kasir</span> &bull; Akses penjualan & kasir transaksi.
        @endif
    </p>
</div>

<!-- Stats Grid (Top KPIs) -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <!-- Card 1: Total Members -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Member</span>
            <span class="text-2xl font-bold text-gray-800 block mt-1.5">{{ number_format($totalMembers) }}</span>
            <span class="text-caption block mt-1">+{{ $newMembersThisMonth }} baru bulan ini</span>
        </div>
        <div class="bg-blue-50 text-blue-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-user-group class="w-5 h-5" />
        </div>
    </div>

    <!-- Card 4: Total Gross Sales -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Omset Kotor</span>
            <span class="text-2xl font-bold text-gray-800 block mt-1.5">Rp {{ number_format($totalSalesGross, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Sebelum dikurangi diskon</span>
        </div>
        <div class="bg-amber-50 text-amber-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-arrow-trending-up class="w-5 h-5" />
        </div>
    </div>


    <!-- Card: Total Discount -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Diskon</span>
            <span class="text-2xl font-bold text-green-600 block mt-1.5">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Total diskon seluruh transaksi</span>
        </div>
        <div class="bg-green-50 text-green-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-currency-dollar class="w-5 h-5" />
        </div>
    </div>

    <!-- Card: Real Net Sales -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Real Omset / Omset Bersih</span>
            <span class="text-2xl font-bold text-blue-700 block mt-1.5">Rp {{ number_format($realSalesTotal, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Omset kotor - total diskon</span>
        </div>
        <div class="bg-blue-50 text-blue-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-calculator class="w-5 h-5" />
        </div>
    </div>

    <!-- Card: Monthly Discount Summary -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Hemat (Bulan Ini)</span>
            <span class="text-2xl font-bold text-green-600 block mt-1.5">Rp {{ number_format($totalDiscountThisMonth, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Rp {{ number_format($memberSavingsTotal, 0, ',', '.') }} dari Member</span>
        </div>
        <div class="bg-green-50 text-green-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-currency-dollar class="w-5 h-5" />
        </div>
    </div>

    <!-- Card 3: Active Custom Discounts -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Promo Custom Aktif</span>
            <span class="text-2xl font-bold text-purple-600 block mt-1.5">{{ number_format($activePromosCount) }}</span>
            <span class="text-caption block mt-1">berjalan pada hari ini</span>
        </div>
        <div class="bg-purple-50 text-purple-600 rounded-lg p-3 shrink-0">
            <x-heroicon-o-gift class="w-5 h-5" />
        </div>
    </div>


</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="card-base">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3>10 Obat Terlaris</h3>
                <p class="text-caption mt-1">Berdasarkan total jumlah obat terjual.</p>
            </div>
            <span class="badge-success">Terlaris</span>
        </div>
        <div class="table-custom-container shadow-sm">
            <div class="overflow-x-auto">
            <table class="table-custom min-w-full">
                <thead class="table-custom-header">
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th class="text-center">Nama Obat</th>
                        <th class="w-28 text-center">Terjual</th>
                    </tr>
                </thead>
                <tbody class="table-custom-body divide-y divide-gray-150">
                    @forelse ($topSellingMedicines as $index => $medicine)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }} align-middle">
                            <td class="text-center font-medium">{{ $index + 1 }}</td>
                            <td class="text-center font-medium text-gray-800">{{ $medicine->nama }}</td>
                            <td class="text-center font-bold text-gray-800">{{ number_format($medicine->total_terjual) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-4 text-center text-caption">Belum ada data penjualan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="card-base">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3>10 Obat Paling Sedikit Terjual</h3>
                <p class="text-caption mt-1">Termasuk obat yang belum pernah terjual.</p>
            </div>
            <span class="badge-warning">Perlu perhatian</span>
        </div>
        <div class="table-custom-container shadow-sm">
            <div class="overflow-x-auto">
            <table class="table-custom min-w-full">
                <thead class="table-custom-header">
                    <tr>
                        <th class="w-16 text-center">No</th>
                        <th class="text-center">Nama Obat</th>
                        <th class="w-28 text-center">Terjual</th>
                    </tr>
                </thead>
                <tbody class="table-custom-body divide-y divide-gray-150">
                    @forelse ($leastSellingMedicines as $index => $medicine)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }} align-middle">
                            <td class="text-center font-medium">{{ $index + 1 }}</td>
                            <td class="text-center font-medium text-gray-800">{{ $medicine->nama }}</td>
                            <td class="text-center font-bold text-gray-800">{{ number_format($medicine->total_terjual) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-4 text-center text-caption">Belum ada data obat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Member Stats Card -->
    <div class="card-base">
        <h3 class="mb-4">Statistik Belanja Member</h3>
        <div class="space-y-4 text-small">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Total Transaksi Member:</span>
                <strong class="text-gray-800">{{ $memberTransactionsCount }} kali</strong>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Total Belanja Member:</span>
                <strong class="text-gray-800">Rp {{ number_format($memberSalesTotal, 0, ',', '.') }}</strong>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Total Hemat Member:</span>
                <strong class="text-green-600">Rp {{ number_format($memberSavingsTotal, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Promo Highlights Card -->
    <div class="card-base">
        <h3 class="mb-4">Performa Promo Custom</h3>
        <div class="space-y-4 text-small">
            <div class="border-b pb-2">
                <span class="text-gray-500 block mb-1">Promo Paling Sering Digunakan:</span>
                <strong class="text-gray-800 text-small block">
                    {{ $mostUsedPromo ? $mostUsedPromo->custom_discount_nama : '-' }}
                </strong>
                @if($mostUsedPromo)
                    <span class="text-caption block mt-0.5">({{ $mostUsedPromo->usage_count }}x digunakan)</span>
                @endif
            </div>
            <div class="pb-2">
                <span class="text-gray-500 block mb-1">Nominal Diskon Terbesar:</span>
                <strong class="text-gray-800 text-small block">
                    {{ $biggestDiscountPromo ? $biggestDiscountPromo->custom_discount_nama : '-' }}
                </strong>
                @if($biggestDiscountPromo)
                    <span class="text-caption text-green-600 block mt-0.5">(Total Rp {{ number_format($biggestDiscountPromo->total_nominal, 0, ',', '.') }})</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Access Card -->
    <div class="card-base flex flex-col justify-between">
        <div>
            <h3 class="mb-2">Akses Cepat Kasir POS</h3>
            <p class="text-small text-gray-500 leading-relaxed">
                Jalankan transaksi obat dengan mudah. Scan barcode, tambahkan pelanggan member, otomatis terapkan diskon, serta audit FEFO batch langsung dalam satu layar kasir.
            </p>
        </div>
        <div class="mt-4 flex gap-3">
            <a href="{{ route('penjualan.create') }}" class="btn-primary">
                Buka Kasir Transaksi
            </a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('custom-discount.index') }}" class="btn-secondary">
                    Kelola Promo
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
