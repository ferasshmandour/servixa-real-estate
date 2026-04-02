@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-[0_2px_8px_rgba(107,33,168,0.08)] border border-[#DDD6FE]']) }}>
    @if ($title)
        <div class="px-6 py-4 border-b border-[#DDD6FE]">
            <h3 class="text-base font-semibold text-[#1F2937]">{{ $title }}</h3>
        </div>
    @endif

    @if (isset($actions))
        <div class="px-6 py-4 border-b border-[#DDD6FE] flex items-center justify-between">
            <h3 class="text-base font-semibold text-[#1F2937]">{{ $title }}</h3>
            <div class="flex items-center gap-2">{{ $actions }}</div>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
