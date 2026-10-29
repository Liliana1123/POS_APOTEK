@props([
    'paginator',
    'view' => 'vendor.pagination.apotek',
])

@php
    $hasPages = $paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages();
@endphp

<div {{ $attributes->merge(['class' => 'mt-4 pagination-container' . ($hasPages ? '' : ' hidden')]) }}>
    @if ($hasPages)
        {{ $paginator->links($view) }}
    @endif
</div>
