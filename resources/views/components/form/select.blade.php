@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'options' => [],
    'selected' => null,
    'errorFor' => null,
    'hint' => null,
    'selectClass' => '',
])

@php
    $errKey = $errorFor ?? $name;
    $currentVal = $selected ?? old($name);
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

    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        @if($required) required @endif
        {{ $attributes->except('class')->merge(['class' => 'form-input ' . $selectClass]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if(!empty($options))
            @foreach($options as $val => $text)
                <option value="{{ $val }}" @selected((string)$currentVal === (string)$val)>
                    {{ $text }}
                </option>
            @endforeach
        @endif

        {{ $slot }}
    </select>

    @if($hint)
        <p class="text-[11px] text-gray-400 mt-1">{{ $hint }}</p>
    @endif

    <p class="modal-field-error text-red-600 text-xs mt-1 hidden" data-error-for="{{ $errKey }}"></p>
</div>
