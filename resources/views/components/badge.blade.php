@props(['status' => 'pending'])

@php
    $styles = match($status) {
        'approved', 'active'  => 'bg-[#DCFCE7] text-[#16A34A]',
        'rejected'            => 'bg-[#FEE2E2] text-[#DC2626]',
        'pending'             => 'bg-[#FEF3C7] text-[#D97706]',
        default               => 'bg-gray-100 text-gray-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $styles"]) }}>
    {{ $slot }}
</span>
