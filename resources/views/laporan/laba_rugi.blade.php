@extends('layouts.app')
@section('title', 'Laporan Laba Rugi')

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
        <h1>Laporan Laba Rugi Kotor</h1>
        <p class="text-caption mt-1">Analisis pendapatan penjualan terhadap harga pokok penjualan (HPP) obat terjual.</p>
    </div>
    <button onclick="window.print()" class="btn-primary py-2 px-4 shrink-0">
        Cetak Laporan
    </button>
</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.laba-rugi') }}" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input w-40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input w-40">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary py-2 px-4">Filter</button>
            <a href="{{ route('laporan.laba-rugi') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">Reset</a>
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Estimasi Laba Rugi</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Calculation Cards -->
<div class="card-base p-6 max-w-md space-y-4 text-xs">
    <div class="flex justify-between border-b pb-2">
        <span class="text-gray-500">Pendapatan (Total Penjualan Bersih)</span>
        <strong class="text-gray-800 text-sm font-mono">Rp {{ number_format($pendapatan, 0, ',', '.') }}</strong>
    </div>
    <div class="flex justify-between border-b pb-2">
        <span class="text-gray-500">Harga Pokok Penjualan (HPP)</span>
        <strong class="text-red-650 text-sm font-mono">- Rp {{ number_format($hpp, 0, ',', '.') }}</strong>
    </div>
    <div class="flex justify-between font-bold text-sm pt-2">
        <span class="text-gray-700">Estimasi Laba Kotor</span>
        <span class="{{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }} font-mono">
            Rp {{ number_format($labaKotor, 0, ',', '.') }}
        </span>
    </div>
</div>

<div class="card-base p-4 mt-6 max-w-md bg-blue-50/50 border-blue-150 text-xs text-blue-700 leading-relaxed font-sans">
    <strong>Catatan Operasional:</strong> Ini merupakan estimasi laba kotor (pendapatan bersih dikurangi HPP barang terjual). Belum mencakup biaya operasional tidak terduga, listrik, sewa, gaji, atau kerugian dari pembuangan obat rusak.
</div>
@endsection
