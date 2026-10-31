@props([
    'name' => 'cari',
    'value' => null,
    'placeholder' => 'Cari...',
    'inputClass' => '',
])

@php
    $currentVal = $value !== null ? $value : request($name);
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }}>
    <input 
        type="text" 
        name="{{ $name }}" 
        value="{{ $currentVal }}" 
        placeholder="{{ $placeholder }}" 
        class="form-input pr-8 {{ $inputClass }}"
        aria-label="{{ $placeholder }}"
        title="{{ $placeholder }}"
    >
    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
    </span>
</div>
