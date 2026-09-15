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
        ? 'bg-white text-blue-800 font-semibold border-l-4 border-white pl-2'
        : 'text-gray-200 hover:bg-blue-600';
    $iconComponent = 'heroicon-' . ($iconType === 'solid' || $iconType === 's' ? 's' : 'o') . '-' . $icon;
@endphp

<a href="{{ route($route) }}" title="{{ $label }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-colors text-xs {{ $activeClass }}" {{ $attributes }}>
    @if ($variant === 'top')
        <span class="flex items-center justify-center shrink-0">
            <x-dynamic-component :component="$iconComponent" class="w-5 h-5" aria-hidden="true" />
        </span>
        <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity duration-150 whitespace-nowrap font-bold uppercase tracking-wider">{{ $label }}</span>
    @else
        <x-dynamic-component :component="$iconComponent" class="w-4 h-4 shrink-0" aria-hidden="true" />
        <span class="truncate lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity duration-150 whitespace-nowrap">{{ $label }}</span>
    @endif
</a>