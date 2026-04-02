@extends('layouts.app')

@section('title', __('admin.city_edit_title') . ' — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.cities.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.city_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.city_edit_title') }}</span>
@endsection

@section('content')

<div class="max-w-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.city_edit_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.city_edit_subtitle') }}</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <x-input
                name="name_en"
                :label="__('admin.label_name_en')"
                :value="old('name_en', $city->getTranslation('name', 'en'))"
            />

            <div>
                <label class="block text-sm font-medium text-[#1F2937] mb-1.5">{{ __('admin.label_name_ar') }}</label>
                <input
                    type="text"
                    name="name_ar"
                    value="{{ old('name_ar', $city->getTranslation('name', 'ar')) }}"
                    dir="rtl"
                    class="w-full px-3 py-2.5 text-sm border border-[#DDD6FE] rounded-xl bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition
                           @error('name_ar') border-red-400 bg-red-50 @enderror"
                >
                @error('name_ar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#F5F3FF]">
                <x-button variant="ghost" href="{{ route('admin.cities.index') }}">{{ __('admin.action_cancel') }}</x-button>
                <x-button variant="primary" type="submit">{{ __('admin.action_save') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
