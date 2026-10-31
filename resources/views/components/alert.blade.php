@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => true,
])

@php
    $normalizedType = match ($type) {
        'error', 'danger' => 'danger',
        'success' => 'success',
        'warning' => 'warning',
        default => 'info',
    };

    $styles = [
        'success' => [
            'wrapper' => 'bg-emerald-50/90 border-emerald-200/90 text-emerald-900',
            'iconColor' => 'text-emerald-600',
            'btnHover' => 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-100/60',
        ],
        'danger' => [
            'wrapper' => 'bg-rose-50/90 border-rose-200/90 text-rose-900',
            'iconColor' => 'text-rose-600',
            'btnHover' => 'text-rose-500 hover:text-rose-700 hover:bg-rose-100/60',
        ],
        'warning' => [
            'wrapper' => 'bg-amber-50/90 border-amber-200/90 text-amber-900',
            'iconColor' => 'text-amber-600',
            'btnHover' => 'text-amber-500 hover:text-amber-700 hover:bg-amber-100/60',
        ],
        'info' => [
            'wrapper' => 'bg-sky-50/90 border-sky-200/90 text-sky-900',
            'iconColor' => 'text-sky-600',
            'btnHover' => 'text-sky-500 hover:text-sky-700 hover:bg-sky-100/60',
        ],
    ];

    $cfg = $styles[$normalizedType];
@endphp

<div
    {{ $attributes->merge(['class' => 'rounded-xl p-3.5 mb-4 text-xs border shadow-xs flex items-start gap-3 transition-all duration-200 ' . $cfg['wrapper']]) }}
    role="alert"
>
    <div class="shrink-0 mt-0.5">
        @if ($normalizedType === 'success')
            <x-heroicon-o-check-circle class="w-5 h-5 {{ $cfg['iconColor'] }}" />
        @elseif ($normalizedType === 'danger')
            <x-heroicon-o-exclamation-circle class="w-5 h-5 {{ $cfg['iconColor'] }}" />
        @elseif ($normalizedType === 'warning')
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 {{ $cfg['iconColor'] }}" />
        @else
            <x-heroicon-o-information-circle class="w-5 h-5 {{ $cfg['iconColor'] }}" />
        @endif
    </div>

    <div class="flex-1 min-w-0">
        @if ($title)
            <h5 class="font-bold mb-0.5 leading-tight">{{ $title }}</h5>
        @endif
        <div class="leading-relaxed font-normal">
            {{ $slot }}
        </div>
    </div>

    @if ($dismissible)
        <button
            type="button"
            onclick="this.closest('[role=alert]').remove()"
            class="shrink-0 p-1 rounded-lg {{ $cfg['btnHover'] }} transition-colors"
            aria-label="Tutup pesan"
        >
            <x-heroicon-o-x-mark class="w-4 h-4" />
        </button>
    @endif
</div>
