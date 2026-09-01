@extends('layouts.app')
@section('title', 'Laporan Audit Diskon')

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
        <h1>Laporan Penggunaan Diskon & Promo</h1>
        <p class="text-caption mt-1">Pantau riwayat audit & hemat rupiah dari promo/membership.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('laporan.diskon', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Laporan
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-5 mb-6 print:hidden">
    <form method="GET" action="{{ route('laporan.diskon') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Jenis Diskon</label>
            <select name="jenis" class="form-input">
                <option value="">Semua Jenis</option>
                <option value="member" @selected(request('jenis') === 'member')>Diskon Member</option>
                <option value="custom" @selected(request('jenis') === 'custom')>Custom Promo</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Promo Custom</label>
            <select name="promo_id" class="form-input">
                <option value="">Semua Promo</option>
                @foreach ($promos as $promo)
                    <option value="{{ $promo->id }}" @selected(request('promo_id') == $promo->id)>
                        {{ $promo->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Status Pelanggan</label>
            <select name="status_pelanggan" class="form-input">
                <option value="">Semua Pelanggan</option>
                <option value="member" @selected(request('status_pelanggan') === 'member')>Member</option>
                <option value="umum" @selected(request('status_pelanggan') === 'umum')>Umum</option>
            </select>
        </div>
        
        <div class="md:col-span-5 flex justify-end gap-2 border-t pt-3 mt-1">
            <a href="{{ route('laporan.diskon') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
                Reset
            </a>
            <button type="submit" class="btn-primary py-2 px-5">
                Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Laporan Rincian Audit Diskon & Promo</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Audit List Table -->
<div class="table-custom-container">
    <div class="p-4 border-b bg-gray-50/50 flex justify-between items-center print:border-b-0 print:p-0 print:mb-4">
        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Daftar Audit Penggunaan Diskon</span>
        <span class="text-xs font-bold text-green-600 font-mono">Total Hemat: Rp {{ number_format($totalNominal, 0, ',', '.') }}</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-28">Tanggal</th>
                    <th scope="col" class="w-36">No. Faktur</th>
                    <th scope="col">Barang / Obat</th>
                    <th scope="col">Pelanggan</th>
                    <th scope="col" class="w-28">Jenis Diskon</th>
                    <th scope="col">Nama Promo</th>
                    <th scope="col" class="text-center w-24">Persentase</th>
                    <th scope="col" class="text-right w-36">Hemat Rupiah</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-y divide-gray-150">
                @forelse ($usages as $usage)
                    <tr>
                        <td class="text-gray-600">{{ $usage->created_at->format('d M Y H:i') }}</td>
                        <td class="font-semibold text-gray-800 font-mono">
                            <a href="{{ route('penjualan.show', $usage->penjualan_id) }}" class="text-blue-600 hover:underline print:text-gray-800">
                                {{ $usage->penjualan->no_faktur ?? 'Faktur Terhapus' }}
                            </a>
                        </td>
                        <td class="text-gray-750 font-medium">{{ $usage->barang_nama }}</td>
                        <td class="text-gray-700">
                            {{ $usage->penjualan->pelanggan->nama ?? 'Umum' }}
                            @if (isset($usage->penjualan->pelanggan) && $usage->penjualan->pelanggan->is_member)
                                <span class="badge-success ml-1.5 py-0.5 px-1 text-[8px] print:hidden">Member</span>
                            @endif
                        </td>
                        <td>
                            @if ($usage->jenis === 'member')
                                <span class="badge-info">Member</span>
                            @else
                                <span class="badge-neutral">Custom Promo</span>
                            @endif
                        </td>
                        <td class="text-gray-600">{{ $usage->custom_discount_nama ?? '-' }}</td>
                        <td class="text-center font-bold text-gray-750">{{ $usage->persentase }}%</td>
                        <td class="table-num font-bold text-gray-800 font-mono">Rp {{ number_format($usage->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Riwayat Diskon Kosong</div>
                                <div class="empty-state-desc font-sans">Tidak ditemukan riwayat audit diskon yang cocok dengan filter pencarian Anda.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50/50 border-t font-bold text-xs">
                <tr>
                    <td colspan="7" class="px-5 py-4 text-right text-gray-600">Total Hemat:</td>
                    <td class="px-5 py-4 text-right text-green-600 font-mono text-sm">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
