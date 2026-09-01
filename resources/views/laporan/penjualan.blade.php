@extends('layouts.app')
@section('title', 'Laporan Penjualan')

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
        <h1>Laporan Penjualan Transaksi</h1>
        <p class="text-caption mt-1">Analisis ringkasan transaksi, omzet, dan diskon penjualan.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.penjualan', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-4 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.penjualan') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Status Pelanggan</label>
            <select name="status_pelanggan" class="form-input">
                <option value="">Semua Pelanggan</option>
                <option value="member" @selected(request('status_pelanggan') === 'member')>Member Only</option>
                <option value="non-member" @selected(request('status_pelanggan') === 'non-member')>Umum / Non-Member</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex-1 py-2">
                Filter
            </button>
            <a href="{{ route('laporan.penjualan') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Rincian Transaksi Penjualan</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card-base p-5">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block border-b pb-2 mb-3">Ringkasan Transaksi</span>
        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-500">Jumlah Transaksi:</span>
                <strong class="text-gray-800">{{ $jumlahTransaksi }} kali</strong>
            </div>
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-500">Transaksi Member:</span>
                <strong class="text-blue-600 font-semibold">{{ $transaksiMember }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Transaksi Umum:</span>
                <strong class="text-gray-700">{{ $transaksiNonMember }}</strong>
            </div>
        </div>
    </div>

    <div class="card-base p-5">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block border-b pb-2 mb-3">Perolehan Keuangan</span>
        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-500">Omzet Kotor:</span>
                <strong class="text-gray-850 font-mono">Rp {{ number_format($omzet, 0, ',', '.') }}</strong>
            </div>
            <div class="flex justify-between border-b pb-1.5">
                <span class="text-gray-500">Total Potongan Diskon:</span>
                <strong class="text-red-600 font-mono">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</strong>
            </div>
            <div class="flex justify-between font-bold text-sm">
                <span class="text-gray-600">Penjualan Bersih (Net):</span>
                <span class="text-green-600 font-mono">Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="card-base p-5 flex items-center justify-center text-center">
        <div class="text-xs text-gray-400 leading-relaxed max-w-xs font-sans">
            Seluruh nominal diskon & harga kotor dihitung secara dinamis dari detail penjualan berdasarkan realisasi FEFO.
        </div>
    </div>
</div>

<!-- Transaction Table -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28">Tanggal</th>
                    <th scope="col" class="w-36">No. Faktur</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col" class="w-36">Member ID</th>
                    <th scope="col" class="text-right w-36">Total Kotor</th>
                    <th scope="col" class="text-right w-28">Diskon</th>
                    <th scope="col" class="text-right w-36">Penjualan Bersih</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($penjualans as $penjualan)
                    @php
                        $diskonFaktur = $penjualan->detail->sum('diskon');
                    @endphp
                    <tr>
                        <td class="text-gray-600">{{ $penjualan->tanggal->format('d M Y') }}</td>
                        <td class="font-semibold text-gray-800 font-mono">
                            <a href="{{ route('penjualan.show', $penjualan) }}" class="text-blue-600 hover:underline print:text-gray-800">
                                {{ $penjualan->no_faktur }}
                            </a>
                        </td>
                        <td class="font-medium text-gray-800">
                            {{ $penjualan->pelanggan->nama ?? 'Umum' }}
                            @if(isset($penjualan->pelanggan) && $penjualan->pelanggan->is_member)
                                <span class="badge-success ml-1.5 py-0.5 px-1 text-[8px] print:hidden">Member</span>
                            @endif
                        </td>
                        <td class="font-mono text-gray-650">{{ $penjualan->pelanggan->member_id ?? '-' }}</td>
                        <td class="table-num text-gray-700">Rp {{ number_format($penjualan->total + $diskonFaktur, 0, ',', '.') }}</td>
                        <td class="table-num font-semibold text-red-600">Rp {{ number_format($diskonFaktur, 0, ',', '.') }}</td>
                        <td class="table-num font-bold text-gray-850">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Transaksi Tidak Ditemukan</div>
                                <div class="empty-state-desc">Tidak ada data transaksi pada filter tanggal atau kriteria status ini.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="4" class="px-5 py-4 text-right text-gray-600">Total Akumulasi:</td>
                    <td class="px-5 py-4 text-right text-gray-800 font-mono">Rp {{ number_format($omzet, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-right text-red-650 font-mono">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-right text-green-600 font-mono text-sm">Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}</td>
                </tr>
            </footer>
        </table>
    </div>
</div>
@endsection
