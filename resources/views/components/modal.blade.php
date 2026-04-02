@props(['id', 'title' => '', 'size' => 'md'])

@php
    $maxWidth = match($size) {
        'sm'  => 'max-w-sm',
        'lg'  => 'max-w-2xl',
        'xl'  => 'max-w-4xl',
        default => 'max-w-lg',
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    aria-labelledby="modal-{{ $id }}-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        x-on:click="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- Panel --}}
    <div
        class="relative w-full {{ $maxWidth }} bg-white rounded-2xl shadow-2xl"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#DDD6FE]">
            <h3 id="modal-{{ $id }}-title" class="text-base font-semibold text-[#1F2937]">
                {{ $title }}
            </h3>
            <button
                type="button"
                x-on:click="open = false"
                class="p-1.5 rounded-lg text-[#6B7280] hover:text-[#1F2937] hover:bg-[#F5F3FF] transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            {{ $slot }}
        </div>

        {{-- Footer (optional slot) --}}
        @if (isset($footer))
            <div class="px-6 py-4 border-t border-[#DDD6FE] flex items-center justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
