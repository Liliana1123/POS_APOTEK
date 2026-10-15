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
    <button type="button" aria-expanded="false" class="sidebar-group-header flex items-center justify-between w-full text-xs text-blue-200 font-bold uppercase tracking-wider px-3 py-2.5 rounded-xl hover:text-white hover:bg-blue-600/50 transition-colors">
        <span class="flex items-center gap-3 min-w-0">
            <x-dynamic-component :component="$iconComponent" class="w-5 h-5 shrink-0 text-blue-200 group-hover:text-white" aria-hidden="true" />
            <span class="sidebar-nav-label truncate whitespace-nowrap tracking-wider text-[11px] font-bold">{{ $label }}</span>
        </span>
        <x-heroicon-o-chevron-right class="sidebar-group-arrow w-4 h-4 shrink-0 transition-transform duration-200 text-blue-300" aria-hidden="true" />
    </button>
    <div class="sidebar-group-items overflow-hidden max-h-0 transition-all duration-300 ease-in-out flex flex-col space-y-1 my-1 pl-3.5 ml-4 border-l-2 border-blue-400/25" data-has-active="{{ $hasActiveStr }}">
        {{ $slot }}
    </div>
</div>