@extends('layouts.app')

@section('title', __('admin.slider_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.slider_title') }}</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.slider_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.slider_subtitle') }}</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.sliders.create') }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.slider_add') }}
    </x-button>
</div>

<x-card>
    @if($sliders->isEmpty())
        <x-empty-state :message="__('admin.slider_empty')" :action="__('admin.slider_add')" :href="route('admin.sliders.create')" />
    @else
        <x-data-table :headers="[__('admin.slider_col_image'), __('admin.slider_col_link'), __('admin.slider_col_order'), __('admin.label_status'), __('admin.label_actions')]">
            @foreach($sliders as $slider)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                {{-- Image Thumbnail --}}
                <td class="px-4 py-3">
                    @if($slider->getFirstMediaUrl('image'))
                        <img src="{{ $slider->getFirstMediaUrl('image') }}"
                             alt="Slider image"
                             class="w-24 h-14 rounded-lg object-cover border border-[#DDD6FE]">
                    @else
                        <div class="w-24 h-14 rounded-lg bg-[#F5F3FF] border border-[#DDD6FE] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </td>

                {{-- Link --}}
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    @if($slider->link)
                        <a href="{{ $slider->link }}" target="_blank"
                           class="text-[#6B21A8] hover:underline truncate max-w-[180px] block">
                            {{ $slider->link }}
                        </a>
                    @else
                        <span class="text-[#9CA3AF]">—</span>
                    @endif
                </td>

                {{-- Sort Order --}}
                <td class="px-4 py-3 text-sm text-center text-[#1F2937] font-medium">
                    {{ $slider->sort_order }}
                </td>

                {{-- Active Badge --}}
                <td class="px-4 py-3">
                    @if($slider->is_active)
                        <x-badge status="approved">{{ __('admin.slider_active') }}</x-badge>
                    @else
                        <x-badge status="rejected">{{ __('admin.slider_inactive') }}</x-badge>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <x-button variant="outline" size="sm" :href="route('admin.sliders.edit', $slider)">
                            {{ __('admin.action_edit') }}
                        </x-button>
                        <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}"
                              onsubmit="return confirm('{{ __('admin.slider_delete_confirm') }}')">
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
            {{ $sliders->links() }}
        </div>
    @endif
</x-card>

@endsection
