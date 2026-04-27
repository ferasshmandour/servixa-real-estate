@extends('layouts.app')

@section('title', 'Service — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.services.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.svc_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ $service->title }}</span>
@endsection

@section('content')

{{-- Reject Modal --}}
<x-modal id="reject-service" title="{{ __('admin.svc_reject_title') }}" size="md">
    <form id="reject-service-form" method="POST" action="{{ route('admin.services.reject', $service) }}">
        @csrf
        <p class="text-sm text-[#6B7280] mb-4">
            Please provide a clear reason for rejection. This will be visible to the service owner.
        </p>
        <x-textarea
            name="rejection_reason"
            label="{{ __('admin.svc_reject_reason') }}"
            placeholder="e.g. The service description is incomplete. Please add detailed pricing and service conditions."
            rows="4"
        />
        <x-slot name="footer">
            <x-button variant="ghost" type="button" x-on:click="$dispatch('close-modal-reject-service')">
                {{ __('admin.action_cancel') }}
            </x-button>
            <x-button variant="danger" type="submit" form="reject-service-form">
                {{ __('admin.svc_confirm_reject') }}
            </x-button>
        </x-slot>
    </form>
</x-modal>

{{-- Page Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ $service->title }}</h1>
            <x-badge :status="$service->status">{{ ucfirst($service->status) }}</x-badge>
        </div>
        <p class="text-sm text-[#6B7280]">
            {{ $service->type === 'sale' ? __('admin.svc_type_sale') : __('admin.svc_type_rent') }}
            @if($service->price_syp)
                · <span class="font-medium text-[#1F2937]">{{ number_format($service->price_syp) }} ل.س</span>
            @endif
            @if($service->price_usd)
                · <span class="font-medium text-[#1F2937]">$ {{ number_format($service->price_usd, 2) }}</span>
            @endif
        </p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3" x-data>
        @if(in_array($service->status, ['pending', 'rejected']))
            <form method="POST" action="{{ route('admin.services.approve', $service) }}">
                @csrf
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('admin.action_approve') }}
                </x-button>
            </form>
        @endif

        @if(in_array($service->status, ['pending', 'approved']))
            <x-button variant="danger" type="button" x-on:click="$dispatch('open-modal-reject-service')">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('admin.action_reject') }}
            </x-button>
        @endif
    </div>
</div>

{{-- Rejection Reason Banner --}}
@if($service->status === 'rejected' && $service->rejection_reason)
    <div class="mb-6 p-4 bg-[#FEE2E2] border border-red-200 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-[#DC2626] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-[#DC2626]">{{ __('admin.svc_reject_reason') }}</p>
            <p class="text-sm text-[#DC2626] mt-0.5">{{ $service->rejection_reason }}</p>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Main Image --}}
        @if($service->getFirstMediaUrl('main-image'))
            <x-card title="Main Image">
                <div class="rounded-xl overflow-hidden border border-[#DDD6FE]">
                    <img src="{{ $service->getFirstMediaUrl('main-image') }}"
                         alt="{{ $service->title }}"
                         class="w-full max-h-80 object-cover">
                </div>
            </x-card>
        @endif

        {{-- Service Details --}}
        <x-card title="Service Information">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Title (Arabic)</dt>
                    <dd class="text-sm font-medium text-[#1F2937]" dir="rtl">{{ $service->getTranslation('title', 'ar') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Title (English)</dt>
                    <dd class="text-sm font-medium text-[#1F2937]">{{ $service->getTranslation('title', 'en') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Category</dt>
                    <dd class="text-sm text-[#1F2937]">{{ $service->category?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Subcategory</dt>
                    <dd class="text-sm text-[#1F2937]">{{ $service->subcategory?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Type</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $service->type === 'sale' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $service->type === 'sale' ? __('admin.svc_type_sale') : __('admin.svc_type_rent') }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Available Quantity</dt>
                    <dd class="text-sm text-[#1F2937]">{{ $service->available_quantity }}</dd>
                </div>

                {{-- Pricing --}}
                @if($service->price_syp)
                    <div>
                        <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Price (Syrian Pound)</dt>
                        <dd class="text-sm font-semibold text-[#6B21A8]">{{ number_format($service->price_syp) }} ل.س</dd>
                    </div>
                @endif
                @if($service->price_usd)
                    <div>
                        <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Price (USD)</dt>
                        <dd class="text-sm font-semibold text-[#6B21A8]">$ {{ number_format($service->price_usd, 2) }}</dd>
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Description (Arabic)</dt>
                    <dd class="text-sm text-[#1F2937] leading-relaxed" dir="rtl">{{ $service->getTranslation('description', 'ar') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Description (English)</dt>
                    <dd class="text-sm text-[#1F2937] leading-relaxed">{{ $service->getTranslation('description', 'en') }}</dd>
                </div>
            </dl>
        </x-card>

        {{-- Location Map --}}
        @if($service->latitude && $service->longitude)
            <x-card title="Service Location">
                <div class="rounded-xl overflow-hidden border border-[#DDD6FE] h-56">
                    <iframe
                        src="https://maps.google.com/maps?q={{ $service->latitude }},{{ $service->longitude }}&z=14&output=embed"
                        class="w-full h-full border-0"
                        loading="lazy"
                    ></iframe>
                </div>
                <p class="mt-2 text-xs text-[#6B7280]">
                    Lat: {{ $service->latitude }} · Lng: {{ $service->longitude }}
                </p>
            </x-card>
        @endif

        {{-- Dynamic Field Values --}}
        @if($service->dynamicValues->isNotEmpty())
            <x-card title="Additional Details">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach($service->dynamicValues as $value)
                        <div>
                            <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">
                                {{ $value->dynamicField?->label ?? 'Field #' . $value->dynamic_field_id }}
                            </dt>
                            <dd class="text-sm text-[#1F2937]">{{ $value->value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-card>
        @endif

        {{-- Additional Images --}}
        @if($service->getMedia('additional-images')->isNotEmpty())
            <x-card title="Gallery">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($service->getMedia('additional-images') as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank"
                           class="block rounded-xl overflow-hidden border border-[#DDD6FE] hover:border-[#6B21A8] transition-colors group relative">
                            <img src="{{ $media->getUrl() }}"
                                 alt="Service image"
                                 class="w-full h-32 object-cover group-hover:opacity-90 transition-opacity">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-[#6B21A8]/20 transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Business Account --}}
        <x-card title="Business Account">
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-[#6B7280] uppercase tracking-wide mb-1">Account Name</p>
                    <a href="{{ route('admin.business-accounts.show', $service->businessAccount) }}"
                       class="text-sm font-semibold text-[#6B21A8] hover:underline">
                        {{ $service->businessAccount?->name }}
                    </a>
                </div>
                @if($service->businessAccount?->user)
                    <div class="flex items-center gap-3 pt-2 border-t border-[#F5F3FF]">
                        <x-avatar :name="$service->businessAccount->user->first_name . ' ' . $service->businessAccount->user->last_name" size="md" />
                        <div>
                            <p class="text-sm font-medium text-[#1F2937]">
                                {{ $service->businessAccount->user->first_name }} {{ $service->businessAccount->user->last_name }}
                            </p>
                            <p class="text-xs text-[#6B7280]">{{ $service->businessAccount->user->phone }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Submission Info --}}
        <x-card title="Submission Info">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">{{ __('admin.label_status') }}</dt>
                    <dd><x-badge :status="$service->status">{{ ucfirst($service->status) }}</x-badge></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Submitted</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $service->created_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Last Updated</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $service->updated_at->diffForHumans() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Gallery Images</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $service->getMedia('additional-images')->count() }}</dd>
                </div>
            </dl>
        </x-card>

    </div>

</div>

@endsection
