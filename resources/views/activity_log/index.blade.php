@extends('layouts.app')
@section('title', 'Log Aktivitas User')

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

<!-- Page Header -->
<div class="print:hidden">
    <x-page-header title="Log Audit Aktivitas User" subtitle="Riwayat tindakan penting yang dilakukan oleh administrator dan kasir.">
        <a href="{{ route('activity-log', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary flex items-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            <span>Export CSV</span>
        </a>
        <button type="button" onclick="window.print()" class="btn-primary flex items-center gap-1.5">
            <x-heroicon-o-printer class="w-4 h-4" />
            <span>Cetak Log</span>
        </button>
    </x-page-header>
</div>

<!-- Filter Card -->
<div class="print:hidden">
    <x-card-filter :action="route('activity-log')" :reset-url="route('activity-log')" :grid="true" grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-4" :has-filter="request()->anyFilled(['cari', 'dari', 'sampai'])">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Cari Aksi / User</label>
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik kata kunci..." class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input">
        </div>
    </x-card-filter>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b border-gray-300 pb-3">
    <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Log Audit Aktivitas User</h2>
    <p class="text-xs text-gray-500 mt-1">Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}</p>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[50rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-44">Waktu</th>
                    <th scope="col" class="w-48">User</th>
                    <th scope="col" class="w-56">Tindakan / Aksi</th>
                    <th scope="col">Rincian Target</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($logs as $index => $log)
                    @php
                        if (in_array($log->action, ['Register Member', 'Upgrade Member'])) {
                            $badgeType = 'success';
                        } elseif (str_contains($log->action, 'Promo')) {
                            $badgeType = 'warning';
                        } elseif ($log->action === 'Transaksi Penjualan') {
                            $badgeType = 'info';
                        } elseif (in_array($log->action, ['Delete', 'Hapus', 'Barang Rusak'])) {
                            $badgeType = 'danger';
                        } else {
                            $badgeType = 'secondary';
                        }
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="font-mono text-gray-600 text-xs">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="font-semibold text-gray-800">{{ $log->user_name }}</td>
                        <td>
                            <x-badge :type="$badgeType">{{ $log->action }}</x-badge>
                        </td>
                        <td class="text-gray-600 truncate max-w-xs text-xs" title="{{ $log->target }}">{{ $log->target ?? '—' }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="4" title="Audit Log Kosong" description="Tidak ditemukan catatan log audit aktivitas user yang sesuai filter." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 print:hidden">
    {{ $logs->links() }}
</div>
@endsection
