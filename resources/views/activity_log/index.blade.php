@extends('layouts.app')
@section('title', 'Log Aktivitas User')

@section('content')

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP, JUDUL & TABEL LENGKAP) --}}
{{-- ========================================================= --}}
<div class="print-only">
    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="LOG AUDIT AKTIVITAS USER">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        @if(request('kategori') && isset($categories[request('kategori')]))
            &nbsp;•&nbsp; Kategori: {{ $categories[request('kategori')]['label'] }}
        @endif
        @if(request('user_id') && ($selectedUser = $users->firstWhere('id', request('user_id'))))
            &nbsp;•&nbsp; User: {{ $selectedUser->name }}
        @endif
        @if(request('cari'))
            &nbsp;•&nbsp; Kata Kunci: "{{ request('cari') }}"
        @endif
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>

    {{-- TABEL DATA LOG (SELURUH DATA SESUAI FILTER UNTUK CETAK) --}}
    <div class="table-custom-container print-table-wrapper">
        <div class="overflow-x-auto print-table-wrapper">
            <table class="table-custom print-table w-full">
                <thead class="table-custom-header">
                    <tr>
                        <th scope="col" style="width: 15%;">Waktu</th>
                        <th scope="col" style="width: 13%;">Kategori</th>
                        <th scope="col" style="width: 17%;">User / Staf</th>
                        <th scope="col" style="width: 19%;">Tindakan</th>
                        <th scope="col" style="width: 36%;">Rincian Target</th>
                    </tr>
                </thead>
                <tbody class="table-custom-body">
                    @forelse ($printLogs as $index => $log)
                        @php
                            $cat = $log->kategori ?? 'sistem';
                            $catBadges = [
                                'penjualan'  => 'badge-info',
                                'inventaris' => 'badge-indigo',
                                'member'     => 'badge-success',
                                'promo'      => 'badge-warning',
                                'keuangan'   => 'badge-purple',
                                'keamanan'   => 'badge-neutral',
                                'sistem'     => 'badge-neutral',
                            ];
                            $catBadgeClass = $catBadges[$cat] ?? 'badge-neutral';
                            $catLabel = $categories[$cat]['label'] ?? ucfirst($cat);
                        @endphp
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-100' }}">
                            <td class="font-mono text-center whitespace-nowrap">
                                @tanggalWaktuId($log->created_at, 'd/m/Y H:i:s')
                            </td>
                            <td class="text-center whitespace-nowrap">
                                <span class="{{ $catBadgeClass }}">
                                    {{ $catLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-gray-900">{{ $log->user->name ?? $log->user_name }}</span>
                                @if(optional($log->user)->role)
                                    <span class="text-gray-500 font-mono text-[8px] block">({{ ucfirst($log->user->role) }})</span>
                                @endif
                            </td>
                            <td class="font-semibold text-gray-900">
                                {{ $log->action }}
                            </td>
                            <td class="break-words">
                                {{ $log->target ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">
                                Tidak ditemukan catatan log audit aktivitas user yang sesuai dengan filter atau kategori yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
    <x-print-signature :apotek="$apotek" />

</div>

<!-- Page Header (Tampilan Layar) -->
<div class="print:hidden">
    <x-page-header title="Log Audit Aktivitas User" subtitle="Riwayat tindakan dan jejak audit lengkap yang dilakukan oleh kasir dan administrator.">
        <a href="{{ route('activity-log', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-secondary flex items-center gap-1.5">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            <span>Ekspor Excel</span>
        </a>
        <button type="button" onclick="printReport()" class="btn-primary flex items-center gap-1.5">
            <x-heroicon-o-printer class="w-4 h-4" />
            <span>Cetak Log</span>
        </button>
    </x-page-header>
</div>

<!-- Filter Card (Tampilan Layar) -->
<div class="print:hidden">
    <x-card-filter :action="route('activity-log')" :reset-url="route('activity-log')" :grid="true" grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7" :has-filter="request()->anyFilled(['cari', 'kategori', 'user_id', 'dari', 'sampai'])" :live-search="true">
        <div class="relative sm:col-span-2 lg:col-span-2">
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari aksi, user, target..." class="form-input pr-8" aria-label="Cari Log" title="Cari Log">
            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
            </span>
        </div>

        <select name="kategori" class="form-input" aria-label="Kategori Log" title="Kategori Log">
            <option value="">Kategori: Semua</option>
            @foreach ($categories as $catKey => $catInfo)
                <option value="{{ $catKey }}" @selected(request('kategori') === $catKey)>
                    {{ $catInfo['label'] }}
                </option>
            @endforeach
        </select>
        
        <select name="user_id" class="form-input" aria-label="User / Staf" title="User / Staf">
            <option value="">User: Semua</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                    {{ $user->name }} ({{ ucfirst($user->role ?? 'Staf') }})
                </option>
            @endforeach
        </select>

        <div class="flex items-center gap-1.5 sm:col-span-2 lg:col-span-2">
            <input type="date" name="dari" value="{{ $dari }}" class="form-input min-w-0" aria-label="Tanggal Awal" title="Tanggal Awal">
            <span class="text-sm text-gray-400">-</span>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input min-w-0" aria-label="Tanggal Akhir" title="Tanggal Akhir">
        </div>
    </x-card-filter>
</div>

<!-- Table Card (Tampilan Layar) -->
<div class="table-custom-container print:hidden">
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
                            @tanggalWaktuId($log->created_at, 'd M Y H:i:s')
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

<div class="mt-4 print:hidden pagination-container">
    {{ $logs->links() }}
</div>

<script>
    function printReport() {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
        setTimeout(function () {
            window.print();
        }, 50);
    }

    window.addEventListener('beforeprint', function () {
        document.querySelectorAll('.overflow-y-auto, .overflow-x-auto, .overflow-hidden, html, body').forEach(function (el) {
            el.scrollTop = 0;
            el.scrollLeft = 0;
        });
        window.scrollTo(0, 0);
    });
</script>
@endsection
