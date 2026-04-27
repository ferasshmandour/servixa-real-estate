@extends('layouts.app')

@section('title', 'Business Account — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.business-accounts.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">Business Accounts</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ $businessAccount->name }}</span>
@endsection

@section('content')

{{-- Reject Modal --}}
<x-modal id="reject-account" title="Reject Business Account" size="md">
    <form id="reject-form" method="POST" action="{{ route('admin.business-accounts.reject', $businessAccount) }}">
        @csrf
        <p class="text-sm text-[#6B7280] mb-4">
            Please provide a clear reason for rejection. This will be visible to the account owner.
        </p>
        <x-textarea
            name="rejection_reason"
            label="Rejection Reason"
            placeholder="e.g. License number could not be verified. Please resubmit with a valid license document."
            rows="4"
        />
        <x-slot name="footer">
            <x-button variant="ghost" type="button" x-on:click="$dispatch('close-modal-reject-account')">
                Cancel
            </x-button>
            <x-button variant="danger" type="submit" form="reject-form">
                Confirm Rejection
            </x-button>
        </x-slot>
    </form>
</x-modal>

{{-- Page Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ $businessAccount->name }}</h1>
            <x-badge :status="$businessAccount->status">{{ ucfirst($businessAccount->status) }}</x-badge>
        </div>
        <p class="text-sm text-[#6B7280]">License: <span class="font-mono font-medium text-[#1F2937]">{{ $businessAccount->license_number }}</span></p>
    </div>

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3" x-data>
        @if(in_array($businessAccount->status, ['pending', 'rejected']))
            <form method="POST" action="{{ route('admin.business-accounts.approve', $businessAccount) }}">
                @csrf
                <x-button variant="primary" type="submit">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approve Account
                </x-button>
            </form>

            <x-button variant="danger" type="button" x-on:click="$dispatch('open-modal-reject-account')">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reject Account
            </x-button>
        @endif

        @if($businessAccount->status === 'approved')
            <x-button variant="danger" type="button" x-on:click="$dispatch('open-modal-reject-account')">
                Revoke Approval
            </x-button>
        @endif
    </div>
</div>

{{-- Rejection Reason Banner --}}
@if($businessAccount->status === 'rejected' && $businessAccount->rejection_reason)
    <div class="mb-6 p-4 bg-[#FEE2E2] border border-red-200 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-[#DC2626] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-[#DC2626]">Rejection Reason</p>
            <p class="text-sm text-[#DC2626] mt-0.5">{{ $businessAccount->rejection_reason }}</p>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main Info --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Account Details --}}
        <x-card title="Account Information">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Business Name (Arabic)</dt>
                    <dd class="text-sm font-medium text-[#1F2937]" dir="rtl">{{ $businessAccount->getTranslation('name', 'ar') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Business Name (English)</dt>
                    <dd class="text-sm font-medium text-[#1F2937]">{{ $businessAccount->getTranslation('name', 'en') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">City</dt>
                    <dd class="text-sm text-[#1F2937]">{{ $businessAccount->city?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Activity Type</dt>
                    <dd class="text-sm text-[#1F2937]">{{ $businessAccount->activityType?->name }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Activities</dt>
                    <dd class="text-sm text-[#1F2937] leading-relaxed">{{ $businessAccount->activities }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Details</dt>
                    <dd class="text-sm text-[#1F2937] leading-relaxed">{{ $businessAccount->details }}</dd>
                </div>
                @if($businessAccount->address)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-1">Address</dt>
                        <dd class="text-sm text-[#1F2937]">{{ $businessAccount->address }}</dd>
                    </div>
                @endif
                @if($businessAccount->latitude && $businessAccount->longitude)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-[#6B7280] uppercase tracking-wide mb-2">Location</dt>
                        <dd>
                            <div class="rounded-xl overflow-hidden border border-[#DDD6FE] h-40">
                                <iframe
                                    src="https://maps.google.com/maps?q={{ $businessAccount->latitude }},{{ $businessAccount->longitude }}&z=14&output=embed"
                                    class="w-full h-full border-0"
                                    loading="lazy"
                                ></iframe>
                            </div>
                        </dd>
                    </div>
                @endif
            </dl>
        </x-card>

        {{-- Files & Documents --}}
        @php
            $accountImages    = $businessAccount->getMedia('images');
            $accountDocuments = $businessAccount->getMedia('documents');
            $hasFiles         = $accountImages->isNotEmpty() || $accountDocuments->isNotEmpty();
        @endphp
        @if($hasFiles)
            <x-card title="Supporting Documents">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($accountImages as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank"
                           class="block rounded-xl overflow-hidden border border-[#DDD6FE] hover:border-[#6B21A8] transition-colors group relative">
                            <img src="{{ $media->getUrl() }}"
                                 alt="Account image"
                                 class="w-full h-32 object-cover group-hover:opacity-90 transition-opacity">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-[#6B21A8]/20 transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                    @foreach($accountDocuments as $media)
                        <a href="{{ $media->getUrl() }}" target="_blank"
                           class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-[#DDD6FE] hover:border-[#6B21A8] hover:bg-[#F5F3FF] transition-colors group">
                            <svg class="w-10 h-10 text-[#6B7280] group-hover:text-[#6B21A8] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-xs text-[#6B7280] group-hover:text-[#6B21A8] transition-colors font-medium truncate w-full text-center">{{ $media->file_name }}</span>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif

    </div>

    {{-- Sidebar: Owner Info --}}
    <div class="space-y-6">
        <x-card title="Account Owner">
            <div class="flex items-center gap-3 mb-4">
                <x-avatar :name="$businessAccount->user?->first_name . ' ' . $businessAccount->user?->last_name" size="lg" />
                <div>
                    <p class="font-semibold text-[#1F2937]">
                        {{ $businessAccount->user?->first_name }} {{ $businessAccount->user?->last_name }}
                    </p>
                    <p class="text-xs text-[#6B7280]">{{ $businessAccount->user?->phone }}</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Country</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $businessAccount->user?->country ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Verified</dt>
                    <dd>
                        @if($businessAccount->user?->is_verified)
                            <span class="text-[#16A34A] font-medium">Yes</span>
                        @else
                            <span class="text-[#DC2626] font-medium">No</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Joined</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $businessAccount->user?->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="Submission Info">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Status</dt>
                    <dd><x-badge :status="$businessAccount->status">{{ ucfirst($businessAccount->status) }}</x-badge></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Submitted</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $businessAccount->created_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Last Updated</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $businessAccount->updated_at->diffForHumans() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">Files</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $businessAccount->getMedia('images')->count() + $businessAccount->getMedia('documents')->count() }}</dd>
                </div>
            </dl>
        </x-card>
    </div>

</div>

@endsection

@push('scripts')
<script>
    // Ensure Alpine is available before dispatch
</script>
@endpush
