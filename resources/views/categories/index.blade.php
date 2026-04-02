@extends('layouts.app')

@section('title', __('admin.cat_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.cat_title') }}</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.cat_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.cat_subtitle') }}</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.categories.create') }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.cat_add') }}
    </x-button>
</div>

<x-card>
    @if($categories->isEmpty())
        <x-empty-state :message="__('admin.cat_empty')" :action="__('admin.cat_add')" :href="route('admin.categories.create')" />
    @else
        <x-data-table :headers="[__('admin.cat_col_category'), __('admin.cat_col_subcategories'), __('admin.label_sort_order'), __('admin.label_actions')]">
            @foreach($categories as $category)
                {{-- Parent Row --}}
                <tr class="hover:bg-[#F8F7FF] transition-colors border-b border-[#F5F3FF]">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($category->icon)
                                <div class="w-8 h-8 rounded-lg bg-[#F5F3FF] flex items-center justify-center text-sm">
                                    {{ $category->icon }}
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#F5F3FF] flex items-center justify-center">
                                    <svg class="w-4 h-4 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="font-semibold text-[#1F2937]">{{ $category->name }}</div>
                                <div class="text-xs text-[#6B7280]" dir="{{ app()->getLocale() === 'ar' ? 'ltr' : 'rtl' }}">{{ $category->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-sm text-[#6B7280]">
                            <span class="w-5 h-5 rounded-full bg-[#F5F3FF] text-[#6B21A8] text-xs font-bold flex items-center justify-center">
                                {{ $category->children_count }}
                            </span>
                            {{ __('admin.cat_subcategories_count') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $category->sort_order }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <x-button variant="secondary" size="sm" :href="route('admin.categories.fields.index', $category)">
                                {{ __('admin.cat_fields_btn') }}
                            </x-button>
                            <x-button variant="outline" size="sm" :href="route('admin.categories.edit', $category)">
                                {{ __('admin.action_edit') }}
                            </x-button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('admin.cat_delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger" size="sm" type="submit">{{ __('admin.action_delete') }}</x-button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Subcategory Rows --}}
                @foreach($category->children as $sub)
                    <tr class="hover:bg-[#F8F7FF] transition-colors bg-[#FAFAFE]">
                        <td class="px-4 py-2.5 ps-12">
                            <div class="flex items-center gap-2">
                                <svg class="w-3 h-3 text-[#DDD6FE]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-[#1F2937]">{{ $sub->name }}</div>
                                    <div class="text-xs text-[#6B7280]" dir="{{ app()->getLocale() === 'ar' ? 'ltr' : 'rtl' }}">{{ $sub->getTranslation('name', app()->getLocale() === 'ar' ? 'en' : 'ar') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-xs text-[#6B7280]">{{ __('admin.cat_subcategory_label') }}</td>
                        <td class="px-4 py-2.5 text-sm text-[#6B7280]">{{ $sub->sort_order }}</td>
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                <x-button variant="secondary" size="sm" :href="route('admin.categories.fields.index', $sub)">
                                    {{ __('admin.cat_fields_btn') }}
                                </x-button>
                                <x-button variant="outline" size="sm" :href="route('admin.categories.edit', $sub)">
                                    {{ __('admin.action_edit') }}
                                </x-button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $sub) }}" onsubmit="return confirm('{{ __('admin.cat_delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger" size="sm" type="submit">{{ __('admin.action_delete') }}</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </x-data-table>
    @endif
</x-card>

@endsection
