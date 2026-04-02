@props([
    'name',
    'label'       => null,
    'value'       => null,
    'rows'        => 4,
    'placeholder' => null,
])

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label for="{{ $name }}" class="text-sm font-medium text-[#1F2937]">
            {{ $label }}
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-2.5 rounded-xl border text-sm text-[#1F2937] placeholder-[#6B7280] resize-y transition-colors focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent '
                . ($errors->has($name) ? 'border-[#DC2626] bg-[#FEE2E2]/20' : 'border-[#DDD6FE] bg-white hover:border-[#6B21A8]')
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-xs text-[#DC2626] flex items-center gap-1 mt-0.5">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
