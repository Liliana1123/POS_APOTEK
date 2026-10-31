@props([
'variant' => 'info', // success, warning, danger, info, secondary
'dot' => false,
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
'neutral' => 'badge-neutral',
'indigo' => 'badge-indigo',
'purple' => 'badge-purple',
];

$dotMap = [
'success' => 'bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.7)]',
'warning' => 'bg-amber-500 shadow-[0_0_5px_rgba(245,158,11,0.7)]',
'yellow' => 'bg-amber-500 shadow-[0_0_5px_rgba(245,158,11,0.7)]',
'orange' => 'bg-orange-500 shadow-[0_0_5px_rgba(249,115,22,0.7)]',
'danger' => 'bg-rose-500 shadow-[0_0_5px_rgba(244,63,94,0.7)]',
'info' => 'bg-sky-500 shadow-[0_0_5px_rgba(14,165,233,0.7)]',
'indigo' => 'bg-indigo-500 shadow-[0_0_5px_rgba(99,102,241,0.7)]',
'purple' => 'bg-purple-500 shadow-[0_0_5px_rgba(168,85,247,0.7)]',
'secondary' => 'bg-slate-400',
'neutral' => 'bg-slate-400',
];

$badgeClass = $classMap[$variant] ?? ('badge-' . $variant);
$dotClass = $dotMap[$variant] ?? 'bg-slate-400';
@endphp

<span {{ $attributes->merge(['class' => $badgeClass . ($dot ? ' inline-flex items-center gap-1.5' : '')]) }}>
    @if($dot)
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }} inline-block shrink-0"></span>
    @endif
    {{ $slot }}
</span>