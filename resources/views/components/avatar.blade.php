@props(['name' => '', 'src' => null, 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'w-7 h-7 text-xs',
        'md' => 'w-9 h-9 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full $sizeClass font-semibold shrink-0"]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="w-full h-full rounded-full object-cover">
    @else
        <span class="w-full h-full rounded-full bg-[#F5F3FF] text-[#6B21A8] flex items-center justify-center">
            {{ $initials() }}
        </span>
    @endif
</div>
