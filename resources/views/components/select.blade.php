@props([
    'name',
    'label'       => null,
    'options'     => [],
    'selected'    => null,
    'placeholder' => null,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-[#1F2937]">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2.5 rounded-xl border text-sm text-[#1F2937] bg-white appearance-none cursor-pointer transition-colors focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent '
                    . ($errors->has($name) ? 'border-[#DC2626]' : 'border-[#DDD6FE] hover:border-[#6B21A8]')
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $val => $label)
                <option value="{{ $val }}" @selected(old($name, $selected) == $val)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <div class="pointer-events-none absolute inset-y-0 end-3 flex items-center">
            <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    @error($name)
        <p class="text-xs text-[#DC2626] flex items-center gap-1 mt-0.5">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
