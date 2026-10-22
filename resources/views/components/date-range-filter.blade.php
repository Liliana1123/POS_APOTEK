@props([
    'action',
    'periode' => 'bulan_ini',
    'dari' => null,
    'sampai' => null,
    'hasReset' => null,
    'resetUrl' => null,
    'queryParams' => [],
    'containerId' => 'customDateContainer',
    'btnId' => 'btnToggleCustom',
])

@php
    $currentPeriode = $periode ?? request('periode', 'bulan_ini');
    $valDari = $dari ?? request('dari');
    $valSampai = $sampai ?? request('sampai');
    $showReset = $hasReset !== null ? $hasReset : (
        (request()->filled('periode') && request('periode') !== 'bulan_ini') ||
        request()->filled('dari') ||
        request()->filled('sampai')
    );
    $finalResetUrl = $resetUrl ?? $action;

    $presets = [
        'hari_ini' => 'Hari Ini',
        'minggu_ini' => 'Minggu Ini',
        'bulan_ini' => 'Bulan Ini',
        'tahun_ini' => 'Tahun Ini',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2.5']) }}>
    {{-- Tombol Preset Periode --}}
    <div class="inline-flex items-center rounded-lg border border-gray-200 bg-white p-1 shadow-2xs">
        @foreach ($presets as $key => $label)
            <a href="{{ $action }}?{{ http_build_query(array_merge($queryParams, ['periode' => $key])) }}"
                class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentPeriode === $key ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach

        <button type="button" 
            onclick="toggleCustomDate('{{ $containerId }}')" 
            id="{{ $btnId }}"
            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentPeriode === 'custom' ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            Custom
        </button>
    </div>

    {{-- Form Rentang Tanggal Custom --}}
    <form method="GET" action="{{ $action }}" id="{{ $containerId }}" class="{{ $currentPeriode === 'custom' ? 'flex' : 'hidden' }} items-center gap-1.5 bg-white p-1 rounded-lg border border-gray-200 shadow-2xs">
        <input type="hidden" name="periode" value="custom">
        @foreach ($queryParams as $qKey => $qVal)
            @if(!in_array($qKey, ['periode', 'dari', 'sampai']))
                <input type="hidden" name="{{ $qKey }}" value="{{ $qVal }}">
            @endif
        @endforeach
        <input type="date" name="dari" id="inputDari_{{ $containerId }}" value="{{ $valDari }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
        <span class="text-xs text-gray-400">s/d</span>
        <input type="date" name="sampai" id="inputSampai_{{ $containerId }}" value="{{ $valSampai }}" required class="text-xs border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
        <button type="submit" class="btn-primary !py-1 !px-2.5 !text-xs">Terapkan</button>
    </form>

    {{-- Tombol Reset --}}
    @if($showReset)
        <a href="{{ $finalResetUrl }}"
           class="btn-secondary !py-1 !px-2.5 !text-xs flex items-center gap-1 text-gray-600 hover:text-red-600 hover:border-red-300 transition-colors shadow-2xs"
           title="Reset filter">
            <x-heroicon-o-arrow-path class="w-3.5 h-3.5 text-gray-500" />
            <span>Reset</span>
        </a>
    @endif
</div>

@once
<script>
    if (typeof window.toggleCustomDate !== 'function') {
        window.toggleCustomDate = function (id) {
            var containerId = id || 'customDateContainer';
            var container = document.getElementById(containerId);
            if (!container) return;
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                container.classList.add('flex');
            } else {
                container.classList.add('hidden');
                container.classList.remove('flex');
            }
        };
    }
</script>
@endonce
