@props([
    'paginator',
    'view' => 'vendor.pagination.apotek',
])

@php
    $hasPages = $paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages();
@endphp

@if ($hasPages)
    <div {{ $attributes->merge(['class' => 'mt-4 pagination-container']) }}>
        {{ $paginator->links($view) }}
    </div>
@endif
