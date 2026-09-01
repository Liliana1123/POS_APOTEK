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
<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
    <!-- Card 1: Total Members -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Member</span>
            <span class="text-2xl font-bold text-gray-800 block mt-1.5">{{ number_format($totalMembers) }}</span>
            <span class="text-caption block mt-1">+{{ $newMembersThisMonth }} baru bulan ini</span>
        </div>
        <div class="bg-blue-50 text-blue-600 rounded-lg p-3 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Card 2: Total Discount Given This Month -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Hemat (Bulan Ini)</span>
            <span class="text-2xl font-bold text-green-600 block mt-1.5">Rp {{ number_format($totalDiscountThisMonth, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Rp {{ number_format($memberSavingsTotal, 0, ',', '.') }} dari Member</span>
        </div>
        <div class="bg-green-50 text-green-600 rounded-lg p-3 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
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
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>

    <!-- Card 4: Total Active Sales -->
    <div class="card-base hoverable flex items-center justify-between">
        <div>
            <span class="text-caption font-semibold uppercase tracking-wider block">Total Omset Penjualan</span>
            <span class="text-2xl font-bold text-gray-800 block mt-1.5">Rp {{ number_format($memberSalesTotal + $nonMemberSalesTotal, 0, ',', '.') }}</span>
            <span class="text-caption block mt-1">Rp {{ number_format($memberSalesTotal, 0, ',', '.') }} dari Member</span>
        </div>
        <div class="bg-amber-50 text-amber-600 rounded-lg p-3 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
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
