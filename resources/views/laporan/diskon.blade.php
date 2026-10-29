@extends('layouts.app')
@section('title', 'Laporan Audit Diskon Dan Promo')

@section('content')

@php
    $apotek = \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

{{-- ========================================================= --}}
{{-- TEMPLATE CETAK (KOP & JUDUL LAPORAN) --}}
{{-- ========================================================= --}}
<div class="print-only">
    {{-- KOP APOTEK & JUDUL LAPORAN --}}
    <x-print-kop :apotek="$apotek" title="LAPORAN PENGGUNAAN DISKON DAN PROMO">
        Periode: {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') : 'Semua Periode' }} {{ $sampai ? 's/d ' . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') : '' }}
        @if(request('jenis'))
            &nbsp;•&nbsp; Jenis Diskon: {{ request('jenis') === 'member' ? 'Diskon Member' : 'Custom Promo' }}
        @endif
        @if(request('promo_id') && ($promoAktif = $promos->firstWhere('id', request('promo_id'))))
            &nbsp;•&nbsp; Promo: {{ $promoAktif->nama }}
        @endif
        @if(request('status_pelanggan'))
            &nbsp;•&nbsp; Status:
            {{
                [
                    'pelanggan_tetap' => 'Member Pelanggan Tetap',
                    'keluarga_nakes' => 'Member Keluarga Nakes',
                    'member_only' => 'Member Only',
                    'umum' => 'Umum',
                ][request('status_pelanggan')] ?? 'Semua Pelanggan'
            }}
        @endif
        &nbsp;•&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </x-print-kop>
</div>

<x-page-header
    title="Laporan Penggunaan Diskon dan Promo"
    subtitle="Pantau riwayat audit dan diskon rupiah dari promo/membership."
    class="print:hidden">
    <x-print-actions />
</x-page-header>

<!-- Filter & Search Card -->
<x-card-filter
    :action="route('laporan.diskon')"
    :reset-url="route('laporan.diskon')"
    :grid="true"
    grid-cols="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5"
    :has-reset="request()->anyFilled(['dari', 'sampai', 'jenis', 'promo_id', 'status_pelanggan'])"
    :live-search="true">
    <div class="flex items-center gap-1.5">
        <input
            type="date"
            name="dari"
            value="{{ old('dari', $dari) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Awal"
            title="Tanggal Awal"
        >
        <span class="inline-flex items-center text-gray-400 shrink-0">
            <x-heroicon-m-minus class="w-3.5 h-3.5" />
        </span>
        <input
            type="date"
            name="sampai"
            value="{{ old('sampai', $sampai) }}"
            class="form-input min-w-0"
            aria-label="Tanggal Akhir"
            title="Tanggal Akhir"
        >
    </div>

    <select name="jenis" class="form-input" aria-label="Jenis Diskon" title="Jenis Diskon">
        <option value="">Jenis: Semua</option>
        <option value="member" @selected(request('jenis') === 'member')>Diskon Member</option>
        <option value="custom" @selected(request('jenis') === 'custom')>Custom Promo</option>
    </select>

    <select name="promo_id" class="form-input" aria-label="Promo Custom" title="Promo Custom">
        <option value="">Promo: Semua</option>
        @foreach ($promos as $promo)
            <option value="{{ $promo->id }}" @selected(request('promo_id') == $promo->id)>
                {{ $promo->nama }}
            </option>
        @endforeach
    </select>

    <select name="status_pelanggan" class="form-input" aria-label="Status Pelanggan" title="Status Pelanggan">
        <option value="">Pelanggan: Semua</option>
        <option value="pelanggan_tetap" @selected(request('status_pelanggan') === 'pelanggan_tetap')>Member Pelanggan Tetap</option>
        <option value="keluarga_nakes" @selected(request('status_pelanggan') === 'keluarga_nakes')>Member Keluarga Nakes</option>
        <option value="member_only" @selected(request('status_pelanggan') === 'member_only')>Member Only</option>
        <option value="umum" @selected(request('status_pelanggan') === 'umum')>Umum</option>
    </select>
</x-card-filter>

<div class="filter-alert-container mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 {{ $dateError ? '' : 'hidden' }}" role="alert">
    {{ $dateError }}
</div>

<!-- Table Card -->
<div class="table-custom-container print-table-wrapper">
    <div class="overflow-x-auto print-table-wrapper">
        <table class="table-custom table-diskon print-table w-full min-w-[55rem]">
            <thead class="table-custom-header">
                <tr>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">No. Invoice</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Barang / Obat</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Pelanggan</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Jenis Diskon</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Nama Promo</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Persentase Diskon</th>
                    <th class="text-center !px-3.5 py-3.5 text-[10px] !font-bold uppercase tracking-wider whitespace-nowrap">Diskon Rupiah</th>
                </tr>
            </thead>
            <tbody class="table-custom-body">
                @forelse ($usages as $index => $usage)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-200' }}">
                        <td class="col-diskon-faktur text-center font-semibold text-gray-800 font-mono text-xs !px-2.5 py-3 break-words leading-snug">
                            <a href="{{ route('penjualan.show', ['penjualan' => $usage->penjualan_id, 'from' => 'laporan_diskon']) }}" class="text-blue-600 hover:underline print:text-gray-800">
                                {{ $usage->penjualan->no_faktur ?? 'Faktur Terhapus' }}
                            </a>
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">@tanggalWaktuId($usage->created_at)</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->barang_nama }}</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            @php
                                $pelanggan = $usage->penjualan?->pelanggan;
                                $statusMember = trim((string) ($pelanggan?->status_member ?? ''));

                                $memberVariant = match ($statusMember) {
                                    'Member Pelanggan Tetap' => 'success',
                                    'Member Keluarga Nakes' => 'info',
                                    'Member Only' => 'warning',
                                    default => null,
                                };
                            @endphp

                            <div class="flex flex-col items-center justify-center gap-1 min-w-0"> 
                                <span class="font-semibold text-gray-800 text-xs text-center">
                                    {{ $pelanggan?->nama ?? 'Umum' }}
                                </span>

                                @if ($memberVariant)
                                    <span class="badge-{{ $memberVariant }} self-center py-0.5 px-1.5 text-[9px]">
                                        {{ $statusMember }}
                                    </span>
                                @else
                                    <span class="self-center text-[9px]">
                                        {{ (!$pelanggan || !$pelanggan->is_member) ? 'Umum' : ($statusMember ?: 'Member') }}
                                    </span>
                                @endif

                            </div>
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">
                            <x-status-badge :variant="$usage->jenis === 'member' ? 'info' : 'secondary'" :label="$usage->jenis === 'member' ? 'Member' : 'Custom Promo'" />
                        </td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->custom_discount_nama ?? '-' }}</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">{{ $usage->persentase }}%</td>
                        <td class="align-middle text-center text-gray-600 font-medium text-[11px] !px-3.5 py-3.5 whitespace-nowrap leading-snug">Rp {{ number_format($usage->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="8" />
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-container mt-4 print:hidden">
        {{ $usages->links('vendor.pagination.apotek') }}
    </div>
</div>

{{-- ========================================================= --}}
{{-- TANDA TANGAN (HANYA MUNCUL SAAT CETAK) --}}
{{-- ========================================================= --}}
<x-print-signature :apotek="$apotek" />

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
