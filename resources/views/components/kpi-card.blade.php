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
        'hutang', 'supplier', 'sky', 'cyan' => 'kpi-hutang',
        'piutang', 'rose', 'danger', 'red' => 'kpi-piutang',
        null, '', 'none' => '',
        default => str_starts_with($variant, 'kpi-') ? $variant : 'kpi-' . $variant,
    };

    $defaultIconClass = match($variant) {
        'kotor', 'amber', 'warning' => 'bg-amber-50 text-amber-600',
        'diskon', 'green', 'emerald', 'success' => 'bg-emerald-50 text-emerald-600',
        'bersih', 'blue', 'primary' => 'bg-blue-50 text-blue-600',
        'member', 'purple' => 'bg-purple-50 text-purple-600',
        'hutang', 'supplier', 'sky', 'cyan' => 'bg-sky-50 text-sky-600',
        'piutang', 'red', 'rose', 'danger' => 'bg-rose-50 text-rose-600',
        default => 'bg-gray-50 text-gray-600',
    };

    $finalIconClass = $iconClass ?? $defaultIconClass;

    $rawString = trim((string)$value);
    $hasPrefixRp = str_starts_with($rawString, 'Rp');
    $numericOnly = preg_replace('/[^0-9]/', '', $rawString);
    $hasCountUp = is_numeric($numericOnly) && (float)$numericOnly > 0;

    // Angka tanpa Rp untuk tampilan terpisah proporsional
    $numberDisplay = $hasPrefixRp ? trim(preg_replace('/^Rp\s*/i', '', $rawString)) : $rawString;

    // Hitung panjang digit angka untuk auto-scaling font dinamis agar nominal besar tidak overflow
    $digitCount = strlen($numericOnly);
    $fontSizeClass = match(true) {
        $digitCount >= 11 => 'text-sm xl:text-[13px] 2xl:text-sm',        // >= 10 Miliar (11+ digit)
        $digitCount >= 9  => 'text-base xl:text-[15px] 2xl:text-base',     // 100 Juta - 9 Miliar (9-10 digit)
        $digitCount >= 7  => 'text-lg xl:text-[17px] 2xl:text-lg',         // 1 Juta - 99 Juta (7-8 digit)
        default           => 'text-xl xl:text-[19px] 2xl:text-xl',         // < 1 Juta (<= 6 digit)
    };

    $isTrendUp = $trendType === 'up' || ($trend && (str_starts_with($trend, '+') || !str_starts_with($trend, '-')));
@endphp

<div {{ $attributes->merge(['class' => "kpi-card $variantClass flex flex-col justify-between"]) }}>
    {{-- Header: Judul di kiri, Icon di kanan atas --}}
    <div class="flex items-start justify-between gap-1.5 w-full">
        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block truncate" title="{{ $title }}">
            {{ $title }}
        </span>

        @if($icon || (isset($slot) && $slot->isNotEmpty()))
            <div class="kpi-icon {{ $finalIconClass }} shadow-2xs shrink-0 !w-8 !h-8 !rounded-lg flex items-center justify-center">
                @if($icon)
                    <x-dynamic-component :component="$icon" class="w-4 h-4" />
                @else
                    {{ $slot }}
                @endif
            </div>
        @endif
    </div>

    {{-- Value: Lebar penuh 100%, whitespace-nowrap (tidak pernah pecah baris), auto-scaling font --}}
    <div class="my-1.5 flex items-baseline whitespace-nowrap min-w-0 overflow-hidden" title="{{ $value }}">
        @if($hasPrefixRp)
            <span class="text-xs font-bold text-gray-400 mr-1 shrink-0 select-none">Rp</span>
        @endif
        <span class="{{ $fontSizeClass }} font-black text-gray-900 tracking-tight truncate leading-tight"
              @if($hasCountUp)
                  data-countup="true"
                  data-target="{{ $numericOnly }}"
                  data-prefix=""
              @endif>
            {{ $numberDisplay }}
        </span>
    </div>

    {{-- Footer: Trend pill + Subtitle dalam satu baris bawah yang rapi --}}
    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 min-w-0 mt-auto">
        @if($trend)
            <span class="kpi-trend-pill {{ $isTrendUp ? 'trend-up' : 'trend-down' }} shrink-0" title="Tren performa metrik">
                <span class="kpi-trend-icon inline-flex items-center">
                    @if($isTrendUp)
                        <x-heroicon-m-arrow-trending-up class="w-3 h-3 shrink-0" />
                    @else
                        <x-heroicon-m-arrow-trending-down class="w-3 h-3 shrink-0" />
                    @endif
                </span>
                <span>{{ $trend }}</span>
            </span>
        @endif
        @if($subtitle)
            <span class="truncate block text-[10.5px] text-gray-400" title="{{ $subtitle }}">
                {{ $subtitle }}
            </span>
        @endif
    </div>
</div>
