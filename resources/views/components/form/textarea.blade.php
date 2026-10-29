@props([
    'name',
    'label' => null,
    'rows' => 3,
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'errorFor' => null,
    'hint' => null,
    'textareaClass' => '',
])

@php
    $errKey = $errorFor ?? $name;
@endphp

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-gray-500 mb-1 font-sans">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        {{ $attributes->except('class')->merge(['class' => 'form-input ' . $textareaClass]) }}
    >{{ $value ?? old($name) }}</textarea>

    @if($hint)
        <p class="text-[11px] text-gray-400 mt-1">{{ $hint }}</p>
    @endif

    <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="{{ $errKey }}"></p>
</div>
