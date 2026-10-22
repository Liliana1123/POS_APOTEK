@props([
    'leftTitle' => 'Mengetahui,',
    'leftRole' => 'Penanggung Jawab Apotek',
    'leftName' => null,
    'leftSipa' => null,
    'rightTitle' => 'Dibuat oleh,',
    'rightRole' => 'Pembuat Laporan',
    'rightName' => null,
    'apotek' => null,
])

@php
    $apotek = $apotek ?? \Illuminate\Support\Facades\Cache::remember(
        'info_apotek',
        now()->addHours(6),
        fn () => \App\Models\InfoApotek::first()
    );

    $resolvedLeftName = $leftName ?? ($apotek?->nama_apoteker_pj ?: '(____________________)');
    $resolvedLeftSipa = $leftSipa ?? $apotek?->no_sipa;
    $resolvedRightName = $rightName ?? (auth()->user()?->name ?: '(____________________)');
@endphp

<div {{ $attributes->merge(['class' => 'no-break print-signature']) }}>
    <div class="print-signature-grid">
        <div class="print-signature-box">
            <p class="print-signature-title">{{ $leftTitle }}</p>
            <p class="print-signature-role">{{ $leftRole }}</p>
            <div class="print-signature-space"></div>
            <p class="print-signature-name">{{ $resolvedLeftName }}</p>
            @if(!empty($resolvedLeftSipa))
                <p class="print-signature-sipa">SIPA: {{ $resolvedLeftSipa }}</p>
            @endif
        </div>
        <div class="print-signature-box">
            <p class="print-signature-title">{{ $rightTitle }}</p>
            <p class="print-signature-role">{{ $rightRole }}</p>
            <div class="print-signature-space"></div>
            <p class="print-signature-name">{{ $resolvedRightName }}</p>
        </div>
    </div>
</div>
