@props([
    'message' => 'No records found.',
    'action'  => null,
    'href'    => null,
])

<div class="flex flex-col items-center justify-center py-16 px-4">
    {{-- Purple folder illustration --}}
    <div class="w-20 h-20 rounded-2xl bg-[#F5F3FF] flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-[#6B21A8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
        </svg>
    </div>

    <p class="text-base font-medium text-[#1F2937] mb-1">{{ $message }}</p>
    <p class="text-sm text-[#6B7280] mb-6">There's nothing here yet.</p>

    @if ($action && $href)
        <a
            href="{{ $href }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#6B21A8] text-white text-sm font-semibold rounded-xl hover:bg-[#7C3AED] transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            {{ $action }}
        </a>
    @endif
</div>
