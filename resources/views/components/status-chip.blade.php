@props(['status'])

@php
    $colors = match ($status) {
        'Habis' => 'bg-red-50 text-red-700 ring-red-600/10',
        'Menipis' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
        default => 'bg-brand-50 text-brand-700 ring-brand-600/10',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset', $colors]) }}>
    {{ $status }}
</span>
