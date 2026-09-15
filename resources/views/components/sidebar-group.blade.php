@props([
    'name',
    'icon',
    'iconType' => 'solid',
    'label',
    'hasActive' => false,
])

@php
    $iconComponent = 'heroicon-' . ($iconType === 'solid' || $iconType === 's' ? 's' : 'o') . '-' . $icon;
    $hasActiveStr = $hasActive ? 'true' : 'false';
@endphp

<div class="sidebar-group" data-group="{{ $name }}">
    <button type="button" aria-expanded="false" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider pt-3 pb-1 px-3 hover:text-white transition-colors">
        <span class="flex items-center gap-2.5 min-w-0">
            <span class="flex items-center justify-center shrink-0">
                <x-dynamic-component :component="$iconComponent" class="w-5 h-5" aria-hidden="true" />
            </span>
            <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity duration-150 whitespace-nowrap">{{ $label }}</span>
        </span>
        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 transition-transform duration-200 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100" aria-hidden="true" />
    </button>
    <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out" data-has-active="{{ $hasActiveStr }}">
        {{ $slot }}
    </div>
</div>