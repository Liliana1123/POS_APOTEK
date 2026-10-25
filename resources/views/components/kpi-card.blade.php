@props([
    'title',
    'value',
    'subtitle' => null,
    'variant' => null,
    'icon' => null,
    'iconClass' => null,
    'trend' => null,
    'trendType' => null,
])

@php
    $variantClass = match($variant) {
        'kotor', 'amber', 'warning' => 'kpi-kotor',
        'diskon', 'green', 'emerald', 'success' => 'kpi-diskon',
        'bersih', 'blue', 'primary' => 'kpi-bersih',
        'member', 'purple' => 'kpi-member',
        null, '', 'none' => '',
        default => str_starts_with($variant, 'kpi-') ? $variant : 'kpi-' . $variant,
    };

    $defaultIconClass = match($variant) {
        'kotor', 'amber', 'warning' => 'bg-amber-50 text-amber-600',
        'diskon', 'green', 'emerald', 'success' => 'bg-emerald-50 text-emerald-600',
        'bersih', 'blue', 'primary' => 'bg-blue-50 text-blue-600',
        'member', 'purple' => 'bg-purple-50 text-purple-600',
        'red', 'rose', 'danger' => 'bg-rose-50 text-rose-600',
        default => 'bg-gray-50 text-gray-600',
    };

    $finalIconClass = $iconClass ?? $defaultIconClass;

    $rawString = trim((string)$value);
    $hasPrefixRp = str_starts_with($rawString, 'Rp');
    $numericOnly = preg_replace('/[^0-9]/', '', $rawString);
    $hasCountUp = is_numeric($numericOnly) && (float)$numericOnly > 0;

    $isTrendUp = $trendType === 'up' || ($trend && (str_starts_with($trend, '+') || !str_starts_with($trend, '-')));
@endphp

<div {{ $attributes->merge(['class' => "kpi-card $variantClass flex items-center justify-between"]) }}>
    <div>
        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block">
            {{ $title }}
        </span>
        <div class="flex items-baseline gap-2 mt-1">
            <div class="text-xl font-black text-gray-900 tracking-tight"
                 @if($hasCountUp)
                     data-countup="true"
                     data-target="{{ $numericOnly }}"
                     data-prefix="{{ $hasPrefixRp ? 'Rp ' : '' }}"
                 @endif>
                {{ $value }}
            </div>
            @if($trend)
                <span class="kpi-trend-pill {{ $isTrendUp ? 'trend-up' : 'trend-down' }}" title="Tren performa metrik">
                    <span class="kpi-trend-icon">{!! $isTrendUp ? '&#8599;' : '&#8600;' !!}</span>
                    <span>{{ $trend }}</span>
                </span>
            @endif
        </div>
        @if($subtitle)
            <span class="text-[11px] text-gray-500 block mt-1">
                {{ $subtitle }}
            </span>
        @endif
    </div>

    @if($icon || (isset($slot) && $slot->isNotEmpty()))
        <div class="kpi-icon {{ $finalIconClass }} shadow-2xs">
            @if($icon)
                <x-dynamic-component :component="$icon" class="w-6 h-6" />
            @else
                {{ $slot }}
            @endif
        </div>
    @endif
</div>
