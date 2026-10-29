@props([
    'title',
    'subtitle' => null,
    'backUrl' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6']) }}>
    <div class="flex items-start gap-3">
        @if($backUrl)
            <a href="{{ $backUrl }}"
               class="btn-secondary !p-2 mt-0.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors shrink-0 rounded-lg shadow-2xs"
               title="Kembali"
               aria-label="Kembali">
                <x-heroicon-o-arrow-left class="w-4 h-4" />
            </a>
        @endif
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 font-sans">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-caption mt-1 text-xs sm:text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
