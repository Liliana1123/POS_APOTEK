@props([
    'route',
    'pattern' => null,
    'icon',
    'iconType' => 'outline',
    'label',
    'variant' => 'child',
])

@php
    $matchPattern = $pattern ?? $route;
    $isActive = request()->routeIs($matchPattern);
    $activeClass = $isActive
        ? 'sidebar-nav-active bg-white text-blue-800 font-semibold shadow-sm'
        : 'text-blue-100 hover:bg-blue-600/60 hover:text-white';
    $iconComponent = 'heroicon-' . ($iconType === 'solid' || $iconType === 's' ? 's' : 'o') . '-' . $icon;
@endphp

@if ($variant === 'top')
    <a href="{{ route($route) }}" title="{{ $label }}" class="sidebar-nav-link sidebar-nav-top flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-xs {{ $activeClass }}" {{ $attributes }}>
        <x-dynamic-component :component="$iconComponent" class="w-5 h-5 shrink-0" aria-hidden="true" />
        <span class="sidebar-nav-label truncate whitespace-nowrap font-bold uppercase tracking-wider">{{ $label }}</span>
    </a>
@else
    <a href="{{ route($route) }}" title="{{ $label }}" class="sidebar-nav-link sidebar-nav-child flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-xs {{ $activeClass }}" {{ $attributes }}>
        <x-dynamic-component :component="$iconComponent" class="w-4 h-4 shrink-0" aria-hidden="true" />
        <span class="sidebar-nav-label truncate whitespace-nowrap font-medium text-xs">{{ $label }}</span>
    </a>
@endif