@extends('layouts.app')

@section('title', __('admin.slider_create_title') . ' — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.sliders.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.slider_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.slider_create_title') }}</span>
@endsection

@section('content')

<div class="max-w-xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.slider_create_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.slider_create_subtitle') }}</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Image Upload --}}
            <div>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">
                    {{ __('admin.slider_col_image') }}
                    <span class="text-red-500 ms-0.5">*</span>
                </label>
                <div x-data="{ preview: null }"
                     class="relative">
                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="hidden"
                        id="slider-image-input"
                        x-ref="fileInput"
                        @change="preview = URL.createObjectURL($event.target.files[0])"
                    >
                    <label for="slider-image-input"
                           class="flex flex-col items-center justify-center w-full h-44 rounded-xl border-2 border-dashed border-[#DDD6FE] bg-[#F8F7FF] cursor-pointer hover:border-[#6B21A8] hover:bg-[#F5F3FF] transition-colors group overflow-hidden @error('image') border-red-400 bg-red-50 @enderror">
                        <template x-if="!preview">
                            <div class="flex flex-col items-center gap-2 p-4 text-center">
                                <div class="w-12 h-12 rounded-xl bg-[#F5F3FF] border border-[#DDD6FE] flex items-center justify-center group-hover:bg-white transition-colors">
                                    <svg class="w-6 h-6 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-[#6B21A8]">{{ __('admin.slider_upload_click') }}</p>
                                <p class="text-xs text-[#6B7280]">PNG, JPG, WEBP — {{ __('admin.slider_upload_max') }}</p>
                            </div>
                        </template>
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover">
                        </template>
                    </label>
                </div>
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link --}}
            <div>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">{{ __('admin.slider_col_link') }}</label>
                <input
                    type="url"
                    name="link"
                    value="{{ old('link') }}"
                    placeholder="https://example.com"
                    class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition
                           @error('link') border-red-400 bg-red-50 @enderror"
                >
                @error('link')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Sort Order --}}
            <div>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">{{ __('admin.slider_col_order') }}</label>
                <input
                    type="number"
                    name="sort_order"
                    value="{{ old('sort_order', 0) }}"
                    min="0"
                    class="w-32 px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition
                           @error('sort_order') border-red-400 bg-red-50 @enderror"
                >
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active Toggle --}}
            <div x-data="{ active: {{ old('is_active', '1') == '1' ? 'true' : 'false' }} }"
                 class="flex items-center justify-between p-4 bg-[#F8F7FF] rounded-xl border border-[#DDD6FE]">
                <div>
                    <p class="text-sm font-medium text-[#1F2937]">{{ __('admin.slider_active') }}</p>
                    <p class="text-xs text-[#6B7280] mt-0.5">{{ __('admin.slider_active_hint') }}</p>
                </div>
                <button type="button"
                        @click="active = !active"
                        :class="active ? 'bg-[#6B21A8]' : 'bg-[#D1D5DB]'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30">
                    <span :class="active ? 'translate-x-6' : 'translate-x-1'"
                          class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform"></span>
                </button>
                <input type="hidden" name="is_active" :value="active ? '1' : '0'">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#F5F3FF]">
                <x-button variant="ghost" href="{{ route('admin.sliders.index') }}">{{ __('admin.action_cancel') }}</x-button>
                <x-button variant="primary" type="submit">{{ __('admin.slider_create_btn') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
