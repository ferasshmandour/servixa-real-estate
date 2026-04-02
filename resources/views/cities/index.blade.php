@extends('layouts.app')

@section('title', __('admin.city_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.city_title') }}</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.city_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.city_subtitle') }}</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.cities.create') }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.city_add') }}
    </x-button>
</div>

<x-card>
    <x-slot name="actions">
        <form method="GET" class="flex gap-2">
            <div class="relative">
                <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.label_search') }}"
                    class="ps-9 pe-4 py-2 text-sm border border-[#DDD6FE] rounded-lg bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition w-52">
            </div>
            <x-button variant="primary" size="sm" type="submit">{{ __('admin.action_search') }}</x-button>
            @if(request('search'))
                <x-button variant="ghost" size="sm" href="{{ route('admin.cities.index') }}">{{ __('admin.action_clear') }}</x-button>
            @endif
        </form>
    </x-slot>

    @if($cities->isEmpty())
        <x-empty-state :message="__('admin.city_empty')" :action="__('admin.city_add')" :href="route('admin.cities.create')" />
    @else
        <x-data-table :headers="[__('admin.city_col_en'), __('admin.city_col_ar'), __('admin.label_actions')]">
            @foreach($cities as $city)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3 font-medium text-[#1F2937]">{{ $city->getTranslation('name', 'en') }}</td>
                <td class="px-4 py-3 text-[#6B7280]" dir="rtl">{{ $city->getTranslation('name', 'ar') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <x-button variant="outline" size="sm" :href="route('admin.cities.edit', $city)">{{ __('admin.action_edit') }}</x-button>
                        <form method="POST" action="{{ route('admin.cities.destroy', $city) }}" onsubmit="return confirm('{{ __('admin.city_delete_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <x-button variant="danger" size="sm" type="submit">{{ __('admin.action_delete') }}</x-button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </x-data-table>

        <div class="mt-4 px-2">
            {{ $cities->withQueryString()->links() }}
        </div>
    @endif
</x-card>

@endsection
