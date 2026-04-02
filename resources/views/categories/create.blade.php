@extends('layouts.app')

@section('title', __('admin.cat_create_title') . ' — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.cat_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.cat_create_title') }}</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.cat_create_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.cat_create_subtitle') }}</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
            @csrf

            {{-- Subcategory Toggle --}}
            <div x-data="{ isSub: {{ old('parent_id') ? 'true' : 'false' }} }">
                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-[#DDD6FE] hover:bg-[#F5F3FF] transition-colors">
                    <input
                        type="checkbox"
                        x-model="isSub"
                        class="w-4 h-4 rounded border-[#DDD6FE] text-[#6B21A8] focus:ring-[#6B21A8]"
                    >
                    <div>
                        <span class="text-sm font-medium text-[#1F2937]">{{ __('admin.cat_is_sub') }}</span>
                        <p class="text-xs text-[#6B7280]">{{ __('admin.cat_is_sub_hint') }}</p>
                    </div>
                </label>

                <div x-show="isSub" x-cloak class="mt-3">
                    <x-select
                        name="parent_id"
                        :label="__('admin.cat_parent')"
                        :options="$parents->mapWithKeys(fn($p) => [$p->id => $p->name])->toArray()"
                        :selected="old('parent_id')"
                        :placeholder="__('admin.cat_parent_placeholder')"
                        :disabled="false"
                        x-bind:disabled="!isSub"
                    />
                </div>
            </div>

            {{-- Bilingual Name --}}
            <div>
                <p class="text-sm font-medium text-[#1F2937] mb-3">{{ __('admin.cat_col_category') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        name="name_en"
                        :label="__('admin.label_name_en')"
                        placeholder="e.g. Construction Equipment"
                        :value="old('name_en')"
                    />
                    <div>
                        <label class="block text-sm font-medium text-[#1F2937] mb-1.5">{{ __('admin.label_name_ar') }}</label>
                        <input
                            type="text"
                            name="name_ar"
                            value="{{ old('name_ar') }}"
                            placeholder="مثال: معدات البناء"
                            dir="rtl"
                            class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition
                                   @error('name_ar') border-red-400 bg-red-50 @enderror"
                        >
                        @error('name_ar')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Icon & Sort Order --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    name="icon"
                    label="Icon (emoji or slug)"
                    placeholder="e.g. 🏗️ or construction"
                    :value="old('icon')"
                />
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
                <x-button variant="ghost" href="{{ route('admin.categories.index') }}">Cancel</x-button>
                <x-button variant="primary" type="submit">Create Category</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
