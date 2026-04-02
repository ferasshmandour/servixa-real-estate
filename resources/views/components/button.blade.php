@props([
    'variant'  => 'primary',
    'type'     => 'button',
    'href'     => null,
    'size'     => 'md',
    'confirm'  => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $variants = [
        'primary'   => 'bg-[#6B21A8] text-white hover:bg-[#7C3AED] focus:ring-[#6B21A8]',
        'secondary' => 'bg-white text-[#6B21A8] border border-[#DDD6FE] hover:bg-[#F5F3FF] focus:ring-[#6B21A8]',
        'danger'    => 'bg-[#DC2626] text-white hover:bg-red-700 focus:ring-red-500',
        'outline'   => 'bg-transparent text-[#6B21A8] border border-[#6B21A8] hover:bg-[#F5F3FF] focus:ring-[#6B21A8]',
        'ghost'     => 'bg-transparent text-[#6B7280] hover:bg-gray-100 focus:ring-gray-400',
    ];

    $classes = "$base {$sizes[$size]} {$variants[$variant]}";
    $confirmAttr = $confirm ? "onclick=\"return confirm('Are you sure?')\"" : '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {!! $confirmAttr !!} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
