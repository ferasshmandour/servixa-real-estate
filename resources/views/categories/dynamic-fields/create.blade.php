@extends('layouts.app')

@section('title', 'Add Dynamic Field — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">Categories</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.categories.fields.index', $category) }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ $category->name }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">Add Field</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">Add Dynamic Field</h1>
        <p class="text-sm text-[#6B7280] mt-1">
            Add a custom field for <span class="font-medium text-[#6B21A8]">{{ $category->name }}</span>
        </p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.categories.fields.store', $category) }}" class="space-y-6"
              x-data="{ fieldType: '{{ old('field_type', 'text') }}' }">
            @csrf

            {{-- Bilingual Label --}}
            <div>
                <p class="text-sm font-medium text-[#1F2937] mb-3">Field Label</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        name="label_en"
                        label="English Label"
                        placeholder="e.g. Floor Area"
                        :value="old('label_en')"
                    />
                    <div>
                        <label class="block text-sm font-medium text-[#1F2937] mb-1.5">Arabic Label</label>
                        <input
                            type="text"
                            name="label_ar"
                            value="{{ old('label_ar') }}"
                            placeholder="مثال: مساحة الطابق"
                            dir="rtl"
                            class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition
                                   @error('label_ar') border-red-400 bg-red-50 @enderror"
                        >
                        @error('label_ar')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Field Type --}}
            <div>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">Field Type</label>
                <select
                    name="field_type"
                    x-model="fieldType"
                    class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition @error('field_type') border-red-400 bg-red-50 @enderror"
                >
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Select (Dropdown)</option>
                    <option value="textarea">Textarea</option>
                    <option value="boolean">Boolean (Yes/No)</option>
                </select>
                @error('field_type')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Select Options (only shown when type = select) --}}
            <div x-show="fieldType === 'select'" x-cloak>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">
                    Options
                    <span class="text-[#6B7280] font-normal">(one per line)</span>
                </label>
                <textarea
                    name="options_raw"
                    rows="5"
                    placeholder="Option 1&#10;Option 2&#10;Option 3"
                    class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition resize-y @error('options_raw') border-red-400 bg-red-50 @enderror"
                >{{ old('options_raw') }}</textarea>
                @error('options_raw')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Required & Sort Order --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-[#DDD6FE] hover:bg-[#F5F3FF] transition-colors">
                        <input
                            type="checkbox"
                            name="is_required"
                            value="1"
                            {{ old('is_required') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-[#DDD6FE] text-[#6B21A8] focus:ring-[#6B21A8]"
                        >
                        <div>
                            <span class="text-sm font-medium text-[#1F2937]">Required</span>
                            <p class="text-xs text-[#6B7280]">Field must be filled</p>
                        </div>
                    </label>
                </div>
                <x-input
                    name="sort_order"
                    type="number"
                    label="Sort Order"
                    placeholder="0"
                    :value="old('sort_order', 0)"
                />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#F5F3FF]">
                <x-button variant="ghost" :href="route('admin.categories.fields.index', $category)">Cancel</x-button>
                <x-button variant="primary" type="submit">Add Field</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
