@props([
    'excelUrl' => null,
    'excelId' => null,
    'excelLabel' => 'Ekspor Excel',
    'printAction' => 'typeof printReport === "function" ? printReport() : window.print()',
    'printId' => null,
    'printLabel' => 'Cetak Laporan',
    'showExcel' => true,
    'showPrint' => true,
])

@php
    $finalExcelUrl = $excelUrl ?? request()->fullUrlWithQuery(['export' => 'excel']);
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 shrink-0 flex-wrap print:hidden']) }}>
    {{-- Slot untuk tombol tambahan di awal (jika ada) --}}
    {{ $slot ?? '' }}

    {{-- Tombol Ekspor Excel --}}
    @if($showExcel)
        <a
            @if($excelId) id="{{ $excelId }}" @endif
            href="{{ $finalExcelUrl }}"
            class="btn-secondary py-2 px-3.5 sm:px-4 text-xs font-semibold rounded-xl text-gray-700 hover:bg-gray-100 transition-colors flex items-center justify-center gap-1.5 shadow-2xs"
            title="{{ $excelLabel }}"
            aria-label="{{ $excelLabel }}">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4 text-gray-500 shrink-0" />
            <span>{{ $excelLabel }}</span>
        </a>
    @endif

    {{-- Tombol Cetak Laporan --}}
    @if($showPrint)
        <button
            type="button"
            @if($printId) id="{{ $printId }}" @endif
            onclick="{{ $printAction }}"
            class="btn-primary py-2 px-3.5 sm:px-4 text-xs font-semibold rounded-xl flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer"
            title="{{ $printLabel }}"
            aria-label="{{ $printLabel }}">
            <x-heroicon-o-printer class="w-4 h-4 shrink-0" />
            <span>{{ $printLabel }}</span>
        </button>
    @endif
</div>
