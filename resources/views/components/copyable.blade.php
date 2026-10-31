@props([
    'text' => null,
    'title' => 'Klik untuk menyalin',
    'showIcon' => false,
])

@php
    $copyValue = $text ?? trim((string)$slot);
    $isValid = $copyValue !== '' && $copyValue !== '—' && $copyValue !== '-';
@endphp

@if(!$isValid)
    <span {{ $attributes->merge(['class' => 'font-mono text-gray-400']) }}>{{ $slot->isNotEmpty() ? $slot : '—' }}</span>
@else
    <span
        {{ $attributes->merge([
            'class' => 'copyable font-mono inline-flex items-center gap-1 group/copy select-all',
            'title' => $title,
            'data-copy' => $copyValue,
            'role' => 'button',
            'tabindex' => '0'
        ]) }}>
        <span>{{ $slot->isNotEmpty() ? $slot : $copyValue }}</span>
        @if($showIcon)
            <svg class="w-3.5 h-3.5 text-gray-400 group-hover/copy:text-blue-600 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
        @endif
    </span>
@endif
