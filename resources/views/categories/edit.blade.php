@extends('layouts.app')

@section('title', 'Edit Category — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">Categories</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">Edit Category</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">Edit Category</h1>
        <p class="text-sm text-[#6B7280] mt-1">Update category information</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Subcategory Toggle --}}
            <div x-data="{ isSub: {{ old('parent_id', $category->parent_id) ? 'true' : 'false' }} }">
                <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-[#DDD6FE] hover:bg-[#F5F3FF] transition-colors">
                    <input
                        type="checkbox"
                        x-model="isSub"
                        class="w-4 h-4 rounded border-[#DDD6FE] text-[#6B21A8] focus:ring-[#6B21A8]"
                    >
                    <div>
                        <span class="text-sm font-medium text-[#1F2937]">This is a subcategory</span>
                        <p class="text-xs text-[#6B7280]">Select a parent category below</p>
                    </div>
                </label>

                <div x-show="isSub" x-cloak class="mt-3">
                    <x-select
                        name="parent_id"
                        label="Parent Category"
                        :options="$parents->mapWithKeys(fn($p) => [$p->id => $p->name])->toArray()"
                        :selected="old('parent_id', $category->parent_id)"
                        placeholder="Select parent category..."
                    />
                </div>

                {{-- Hidden: when checkbox unchecked, send empty parent_id --}}
                <input type="hidden" name="parent_id" value="" x-show="!isSub">
            </div>

            {{-- Bilingual Name --}}
            <div>
                <p class="text-sm font-medium text-[#1F2937] mb-3">Category Name</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input
                        name="name_en"
                        label="English Name"
                        placeholder="e.g. Construction Equipment"
                        :value="old('name_en', $category->getTranslation('name', 'en'))"
                    />
                    <div>
                        <label class="block text-sm font-medium text-[#1F2937] mb-1.5">Arabic Name</label>
                        <input
                            type="text"
                            name="name_ar"
                            value="{{ old('name_ar', $category->getTranslation('name', 'ar')) }}"
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
                    :value="old('icon', $category->icon)"
                />
                <x-input
                    name="sort_order"
                    type="number"
                    label="Sort Order"
                    :value="old('sort_order', $category->sort_order)"
                />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#F5F3FF]">
                <x-button variant="ghost" href="{{ route('admin.categories.index') }}">Cancel</x-button>
                <x-button variant="primary" type="submit">Save Changes</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
