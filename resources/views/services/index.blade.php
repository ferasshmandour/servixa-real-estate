@extends('layouts.app')

@section('title', __('admin.svc_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.svc_title') }}</span>
@endsection

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.svc_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.svc_subtitle') }}</p>
    </div>
</div>

{{-- Status Filter Tabs --}}
<div class="flex gap-1 mb-0 border-b border-[#DDD6FE]">
    @foreach(['all' => __('admin.svc_tab_all'), 'pending' => __('admin.svc_tab_pending'), 'approved' => __('admin.svc_tab_approved'), 'rejected' => __('admin.svc_tab_rejected')] as $value => $label)
        @php $isActive = request('status', 'all') === $value; @endphp
        <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => 1]) }}"
           class="px-5 py-2.5 text-sm font-medium rounded-t-lg border-b-2 transition-colors
                  {{ $isActive
                      ? 'border-[#6B21A8] text-[#6B21A8] bg-[#F5F3FF]'
                      : 'border-transparent text-[#6B7280] hover:text-[#1F2937] hover:bg-[#F8F7FF]' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Search + Table --}}
<x-card>
    <x-slot name="actions">
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="status" value="{{ request('status', 'all') }}">
            <div class="relative">
                <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('admin.label_search') }}"
                    class="ps-9 pe-4 py-2 text-sm border border-[#DDD6FE] rounded-lg bg-white text-[#1F2937] placeholder-[#6B7280] focus:outline-none focus:ring-2 focus:ring-[#6B21A8]/30 focus:border-[#6B21A8] transition w-64"
                >
            </div>
            <x-button variant="primary" size="sm" type="submit">{{ __('admin.action_search') }}</x-button>
            @if(request('search'))
                <x-button variant="ghost" size="sm" href="{{ request()->fullUrlWithQuery(['search' => null]) }}">{{ __('admin.action_clear') }}</x-button>
            @endif
        </form>
    </x-slot>

    @if($services->isEmpty())
        <x-empty-state :message="__('admin.svc_empty')" />
    @else
        <x-data-table :headers="[__('admin.svc_col_service'), __('admin.svc_col_account'), __('admin.svc_col_category'), __('admin.svc_col_type'), __('admin.svc_col_price'), __('admin.label_status'), __('admin.svc_col_submitted'), '']">
            @foreach($services as $service)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($service->getFirstMediaUrl('main-image'))
                            <img src="{{ $service->getFirstMediaUrl('main-image') }}"
                                 alt="{{ $service->title }}"
                                 class="w-10 h-10 rounded-lg object-cover border border-[#DDD6FE] flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-[#F5F3FF] border border-[#DDD6FE] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <div class="font-semibold text-[#1F2937] text-sm leading-tight">{{ $service->title }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    {{ $service->businessAccount?->name }}
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    <div>{{ $service->category?->name }}</div>
                    @if($service->subcategory)
                        <div class="text-xs text-[#9CA3AF] mt-0.5">{{ $service->subcategory->name }}</div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $service->type === 'sale' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $service->type === 'sale' ? __('admin.svc_type_sale') : __('admin.svc_type_rent') }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    @if($service->price_syp)
                        <div class="font-medium text-[#1F2937]">{{ number_format($service->price_syp) }} ل.س</div>
                    @endif
                    @if($service->price_usd)
                        <div class="{{ $service->price_syp ? 'text-xs text-[#9CA3AF]' : 'font-medium text-[#1F2937]' }}">$ {{ number_format($service->price_usd, 2) }}</div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <x-badge :status="$service->status">{{ ucfirst($service->status) }}</x-badge>
                </td>
                <td class="px-4 py-3 text-xs text-[#6B7280]">
                    {{ $service->created_at->format('M d, Y') }}
                </td>
                <td class="px-4 py-3">
                    <x-button variant="secondary" size="sm" :href="route('admin.services.show', $service)">
                        {{ __('admin.svc_review') }}
                    </x-button>
                </td>
            </tr>
            @endforeach
        </x-data-table>

        <div class="mt-4 px-2">
            {{ $services->withQueryString()->links() }}
        </div>
    @endif
</x-card>

@endsection
