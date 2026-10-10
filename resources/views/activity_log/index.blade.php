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
    <x-page-header title="Log Audit Aktivitas User" subtitle="Riwayat tindakan dan jejak audit lengkap yang dilakukan oleh kasir dan administrator.">
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
    <x-card-filter :action="route('activity-log')" :reset-url="route('activity-log')" :grid="true" grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5" :has-filter="request()->anyFilled(['cari', 'kategori', 'user_id', 'dari', 'sampai'])">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Cari Aksi / Target</label>
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Ketik kata kunci..." class="form-input">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Kategori</label>
            <select name="kategori" class="form-input">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $catKey => $catInfo)
                    <option value="{{ $catKey }}" @selected(request('kategori') === $catKey)>
                        {{ $catInfo['label'] }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">User / Staf</label>
            <select name="user_id" class="form-input">
                <option value="">Semua User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                        {{ $user->name }} ({{ ucfirst($user->role ?? 'Staf') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Awal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1.5 font-sans">Tanggal Akhir</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input">
        </div>
    </x-card-filter>
</div>

<!-- Laporan Info (Khusus Print) -->
<div class="hidden print:block mb-6 border-b border-gray-300 pb-3">
    <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Log Audit Aktivitas User</h2>
    <p class="text-xs text-gray-500 mt-1">
        Periode: {{ date('d M Y', strtotime($dari)) }} s/d {{ date('d M Y', strtotime($sampai)) }}
        @if(request('kategori'))
            | Kategori: {{ strtoupper(request('kategori')) }}
        @endif
    </p>
</div>

<!-- Table Card -->
<div class="table-custom-container">
    <div class="overflow-x-auto">
        <table class="table-custom min-w-[56rem]">
            <thead class="table-custom-header">
                <tr>
                    <th scope="col" class="w-40">Waktu</th>
                    <th scope="col" class="w-36">Kategori</th>
                    <th scope="col" class="w-44">User / Staf</th>
                    <th scope="col" class="w-52">Tindakan</th>
                    <th scope="col">Rincian Target</th>
                </tr>
            </thead>
            <tbody class="table-custom-body divide-gray-150">
                @forelse ($logs as $index => $log)
                    @php
                        // Badge Kategori Styling
                        $cat = $log->kategori ?? 'sistem';
                        $catBadges = [
                            'penjualan'  => 'bg-blue-50 text-blue-700 border-blue-200',
                            'inventaris' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'member'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'promo'      => 'bg-amber-50 text-amber-700 border-amber-200',
                            'keuangan'   => 'bg-purple-50 text-purple-700 border-purple-200',
                            'keamanan'   => 'bg-slate-100 text-slate-700 border-slate-300',
                            'sistem'     => 'bg-gray-100 text-gray-700 border-gray-200',
                        ];
                        $catBadgeClass = $catBadges[$cat] ?? $catBadges['sistem'];
                        $catLabel = $categories[$cat]['label'] ?? ucfirst($cat);

                        // Badge Aksi Severity
                        $actionLower = strtolower($log->action);
                        if (str_contains($actionLower, 'register') || str_contains($actionLower, 'upgrade')) {
                            $badgeVariant = 'success';
                        } elseif (str_contains($actionLower, 'promo') || str_contains($actionLower, 'diskon')) {
                            $badgeVariant = 'warning';
                        } elseif (str_contains($actionLower, 'penjualan') || str_contains($actionLower, 'penerimaan')) {
                            $badgeVariant = 'info';
                        } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'hapus') || str_contains($actionLower, 'rusak') || str_contains($actionLower, 'batal') || str_contains($actionLower, 'void')) {
                            $badgeVariant = 'danger';
                        } elseif (str_contains($actionLower, 'login') || str_contains($actionLower, 'logout')) {
                            $badgeVariant = 'secondary';
                        } else {
                            $badgeVariant = 'secondary';
                        }
                    @endphp
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="font-mono text-gray-600 text-xs whitespace-nowrap">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $catBadgeClass }}">
                                {{ $catLabel }}
                            </span>
                        </td>
                        <td>
                            <div class="font-medium text-gray-900 text-xs leading-snug">
                                {{ $log->user->name ?? $log->user_name }}
                            </div>
                            @if(optional($log->user)->role)
                                <span class="text-[10px] text-gray-400 capitalize font-mono">{{ $log->user->role }}</span>
                            @endif
                        </td>
                        <td>
                            <x-badge :variant="$badgeVariant">{{ $log->action }}</x-badge>
                        </td>
                        <td class="text-gray-700 text-xs font-sans break-words" title="{{ $log->target }}">
                            {{ $log->target ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="5" title="Audit Log Kosong" description="Tidak ditemukan catatan log audit aktivitas user yang sesuai dengan filter atau kategori yang dipilih." />
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 print:hidden">
    {{ $logs->links() }}
</div>
@endsection
