@props([
    'paginator',
    'view' => 'vendor.pagination.apotek',
])

@if ($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-4 pagination-container']) }}>
        {{ $paginator->links($view) }}
    </div>
@endif
