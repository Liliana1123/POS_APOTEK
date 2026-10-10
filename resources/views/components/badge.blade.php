@props([
    'variant' => 'info', // success, warning, danger, info, secondary
])

@php
    $classMap = [
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'yellow' => 'badge-warning',
        'orange' => 'badge-orange',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        'secondary' => 'badge-secondary',
    ];
    $badgeClass = $classMap[$variant] ?? ('badge-' . $variant);
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    {{ $slot }}
</span>
