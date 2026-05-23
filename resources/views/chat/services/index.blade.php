@extends('layouts.chat')

@section('title', __('chat.nav_browse') . ' — ' . __('chat.app_name'))

@section('content')
<div class="max-w-6xl mx-auto w-full px-4 py-6" x-data="{ actAs: '' }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('chat.browse_title') }}</h1>
            <p class="text-sm text-[#6B7280] mt-1">{{ __('chat.browse_subtitle') }}</p>
        </div>

        {{-- "Act as" wallet selector (applies to every Chat button below) --}}
        @if ($myBusinessAccounts->isNotEmpty())
            <div class="flex items-center gap-2 bg-white border border-[#DDD6FE] rounded-xl px-3 py-2">
                <span class="material-symbols-outlined text-[20px] text-[#6B21A8]">badge</span>
                <label for="actAs" class="text-xs font-medium text-[#6B7280] whitespace-nowrap">{{ __('chat.act_as') }}</label>
                <select id="actAs" x-model="actAs"
                        class="text-sm font-medium text-[#1F2937] bg-transparent focus:outline-none cursor-pointer">
                    <option value="">{{ __('chat.act_as_myself') }}</option>
                    @foreach ($myBusinessAccounts as $ba)
                        <option value="{{ $ba->id }}">{{ $ba->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Errors from a failed "start" attempt --}}
    @if (session('chat_error'))
        <div class="bg-[#FEE2E2]/50 border border-[#DC2626]/30 rounded-xl px-4 py-3 mb-5 text-sm text-[#DC2626]">
            {{ session('chat_error') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('chat.services.index') }}" class="bg-white border border-[#DDD6FE] rounded-xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute inset-s-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[20px] pointer-events-none">search</span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="{{ __('chat.search_placeholder') }}"
                   class="w-full ps-11 pe-4 py-2.5 rounded-xl border border-[#DDD6FE] text-sm focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent">
        </div>
        <select name="type" class="px-4 py-2.5 rounded-xl border border-[#DDD6FE] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#6B21A8] cursor-pointer">
            <option value="">{{ __('chat.type_all') }}</option>
            <option value="sale" @selected(($filters['type'] ?? '') === 'sale')>{{ __('chat.type_sale') }}</option>
            <option value="rent" @selected(($filters['type'] ?? '') === 'rent')>{{ __('chat.type_rent') }}</option>
        </select>
        <select name="sort_by" class="px-4 py-2.5 rounded-xl border border-[#DDD6FE] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#6B21A8] cursor-pointer">
            <option value="newest" @selected(($filters['sort_by'] ?? 'newest') === 'newest')>{{ __('chat.sort_newest') }}</option>
            <option value="oldest" @selected(($filters['sort_by'] ?? '') === 'oldest')>{{ __('chat.sort_oldest') }}</option>
            <option value="price_asc" @selected(($filters['sort_by'] ?? '') === 'price_asc')>{{ __('chat.sort_price_asc') }}</option>
            <option value="price_desc" @selected(($filters['sort_by'] ?? '') === 'price_desc')>{{ __('chat.sort_price_desc') }}</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-[#6B21A8] text-white text-sm font-semibold rounded-xl hover:bg-[#7C3AED] transition-colors">
            {{ __('chat.search') }}
        </button>
    </form>

    {{-- Service grid --}}
    @if ($services->isEmpty())
        <div class="bg-white border border-[#DDD6FE] rounded-xl">
            <x-empty-state :message="__('chat.no_services')" />
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($services as $service)
                @php $isMine = $myBusinessAccountIds->contains($service->business_account_id); @endphp
                <div class="bg-white border border-[#DDD6FE] rounded-xl overflow-hidden shadow-[0_2px_8px_rgba(107,33,168,0.06)] flex flex-col">
                    {{-- Image --}}
                    <div class="aspect-[16/10] bg-[#F5F3FF] relative">
                        @if ($service->getFirstMediaUrl('main-image'))
                            <img src="{{ $service->getFirstMediaUrl('main-image') }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#DDD6FE] text-[48px]">image</span>
                            </div>
                        @endif
                        <span class="absolute top-2 inset-s-2 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $service->type === 'rent' ? 'bg-[#FEF3C7] text-[#D97706]' : 'bg-[#DCFCE7] text-[#16A34A]' }}">
                            {{ $service->type === 'rent' ? __('chat.type_rent') : __('chat.type_sale') }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-semibold text-[#1F2937] line-clamp-1">{{ $service->title }}</h3>
                        <p class="text-sm text-[#6B7280] mt-0.5 line-clamp-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">store</span>
                            {{ $service->businessAccount?->name ?? '—' }}
                        </p>

                        {{-- Price --}}
                        <div class="mt-2 text-[#6B21A8] font-bold">
                            @if ($service->price_syp)
                                {{ number_format($service->price_syp, 0) }} <span class="text-xs font-medium">{{ __('chat.syp') }}</span>
                            @elseif ($service->price_usd)
                                {{ number_format($service->price_usd, 0) }} <span class="text-xs font-medium">{{ __('chat.usd') }}</span>
                            @else
                                <span class="text-sm text-[#6B7280] font-medium">{{ __('chat.price_na') }}</span>
                            @endif
                        </div>

                        {{-- Chat action --}}
                        <div class="mt-4 pt-3 border-t border-[#F5F3FF]">
                            @if ($isMine)
                                <span class="inline-flex items-center gap-1.5 text-xs text-[#6B7280]">
                                    <span class="material-symbols-outlined text-[16px]">info</span>
                                    {{ __('chat.your_service') }}
                                </span>
                            @else
                                <form method="POST" action="{{ route('chat.conversations.start') }}">
                                    @csrf
                                    <input type="hidden" name="service_id" value="{{ $service->id }}">
                                    <input type="hidden" name="initiator_business_account_id" :value="actAs">
                                    <button type="submit"
                                            class="w-full py-2.5 bg-[#6B21A8] text-white text-sm font-semibold rounded-xl hover:bg-[#7C3AED] transition-colors flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]">chat</span>
                                        {{ __('chat.start_chat') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $services->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection
