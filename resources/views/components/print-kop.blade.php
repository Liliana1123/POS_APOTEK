@props([
    'title' => null,
    'periode' => null,
    'subtitle' => null,
    'apotek' => null,
])

@php
    $apotek = $apotek ?? \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );
@endphp

{{-- KOP APOTEK RESMI --}}
<div {{ $attributes->merge(['class' => 'print-kop']) }}>
    @if($apotek?->logo)
        <img
            src="{{ asset('storage/' . $apotek->logo) }}"
            alt="Logo {{ $apotek->nama_apotek }}"
            class="print-kop-logo"
        >
    @endif

    <div class="print-kop-info">
        <h1>{{ $apotek?->nama_apotek ?? 'POS Apotek' }}</h1>

        @if($apotek?->alamat)
            <p>{{ $apotek->alamat }}</p>
        @endif

        @if($apotek?->telepon || $apotek?->email)
            <p>
                @if($apotek?->telepon) Telp: {{ $apotek->telepon }} @endif
                @if($apotek?->telepon && $apotek?->email) &nbsp; | &nbsp; @endif
                @if($apotek?->email) Email: {{ $apotek->email }} @endif
            </p>
        @endif

        @if($apotek?->no_izin_sia || $apotek?->no_sipa)
            <p>
                @if($apotek?->no_izin_sia) SIA: {{ $apotek->no_izin_sia }} @endif
                @if($apotek?->no_izin_sia && $apotek?->no_sipa) &nbsp;&nbsp;•&nbsp;&nbsp; @endif
                @if($apotek?->no_sipa) SIPA: {{ $apotek->no_sipa }} @endif
            </p>
        @endif
    </div>
</div>

{{-- JUDUL LAPORAN OPSIONAL --}}
@if($title)
    <div class="print-report-title">
        <h2>{{ $title }}</h2>
        @if($periode || $subtitle || (isset($slot) && $slot->isNotEmpty()))
            <p>
                @if($periode)
                    <span>Periode: {{ $periode }}</span>
                @endif
                @if($subtitle)
                    @if($periode) &nbsp;•&nbsp; @endif
                    <span>{{ $subtitle }}</span>
                @endif
                {{ $slot }}
            </p>
        @endif
    </div>
@endif
