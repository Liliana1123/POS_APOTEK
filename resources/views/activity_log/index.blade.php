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

<!-- Page Header Pattern -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 print:hidden">
    <div>
        <h1>Log Audit Aktivitas User</h1>
        <p class="text-caption mt-1">Riwayat tindakan penting yang dilakukan oleh administrator dan kasir.</p>
    </div>
    <div class="flex gap-2 shrink-0">
        <a href="{{ route('activity-log', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
            Export CSV
        </a>
        <button onclick="window.print()" class="btn-primary py-2 px-4">
            Cetak Log
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card-base p-5 mb-6 print:hidden">
    <form method="GET" action="{{ route('activity-log') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
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
        <div class="flex gap-2">
            <button type="submit" class="btn-primary !p-1.5" title="Filter">
                <x-heroicon-o-funnel class="w-4 h-4" />
            </button>
            @if(request()->anyFilled(['cari', 'dari', 'sampai']))
                <a href="{{ route('activity-log') }}" class="btn-secondary py-2 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b pb-3">
    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">Log Audit Aktivitas User</h2>
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
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="font-mono text-gray-600">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="font-semibold text-gray-800">{{ $log->user_name }}</td>
                        <td>
                            @if(in_array($log->action, ['Register Member', 'Upgrade Member']))
                                <span class="badge-success">{{ $log->action }}</span>
                            @elseif(str_contains($log->action, 'Promo'))
                                <span class="badge-warning">{{ $log->action }}</span>
                            @elseif($log->action === 'Transaksi Penjualan')
                                <span class="badge-info">{{ $log->action }}</span>
                            @else
                                <span class="badge-neutral">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td class="text-gray-650 truncate max-w-xs" title="{{ $log->target }}">{{ $log->target ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-0">
                            <div class="empty-state-container">
                                <div class="empty-state-title">Audit Log Kosong</div>
                                <div class="empty-state-desc font-sans">Tidak ditemukan catatan log audit aktivitas user yang sesuai filter.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 print:hidden">
    {{ $logs->links() }}
</div>
@endsection
