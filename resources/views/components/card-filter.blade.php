@props([
    'action' => '',
    'resetUrl' => null,
    'hasReset' => false,
    'grid' => false,
    'gridCols' => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-4',
    'submitLabel' => null,
])

@php
    $isFiltered = $hasReset || ($resetUrl && request()->query());
    $label = $submitLabel ?? ($grid ? 'Filter' : 'Cari');
@endphp

<div class="card-base p-4 mb-6">
    <form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => $grid ? 'space-y-4' : 'flex flex-wrap gap-2 items-center']) }}>
        @if($grid)
            <div class="grid {{ $gridCols }} gap-4">
                {{ $slot }}
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                @if($isFiltered && $resetUrl)
                    <a href="{{ $resetUrl }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                        Reset
                    </a>
                @endif
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <x-heroicon-o-funnel class="w-4 h-4" />
                    <span>{{ $label }}</span>
                </button>
            </div>
        @else
            {{ $slot }}
            <button type="submit" class="btn-primary py-1.5 px-4">
                {{ $label }}
            </button>
            @if($resetUrl && request()->filled('cari'))
                <a href="{{ $resetUrl }}" class="btn-secondary py-1.5 px-4 flex items-center justify-center">
                    Reset
                </a>
            @endif
        @endif
    </form>
</div>
