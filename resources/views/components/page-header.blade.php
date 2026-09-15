@props([
    'title',
    'subtitle' => null,
])

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1>{{ $title }}</h1>
        @if($subtitle)
            <p class="text-caption mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="flex items-center gap-2 flex-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
