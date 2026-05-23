@extends('layouts.app')

@section('title', __('admin.reports_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('admin.reports.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.reports_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">#{{ $report->id }}</span>
@endsection

@section('content')

{{-- Approve Modal (with optional admin note) --}}
<x-modal id="approve-report" title="{{ __('admin.reports_approve_button') }}" size="md">
    <form id="approve-form" method="POST" action="{{ route('admin.reports.approve', $report) }}">
        @csrf
        <p class="text-sm text-[#6B7280] mb-4">
            {{ __('admin.reports_approve_help') }}
        </p>
        <x-textarea
            name="admin_note"
            :label="__('admin.reports_admin_note_label')"
            placeholder="{{ __('admin.reports_admin_note_placeholder') }}"
            rows="4"
        />
        <x-slot name="footer">
            <x-button variant="ghost" type="button" x-on:click="$dispatch('close-modal-approve-report')">
                {{ __('admin.action_cancel') }}
            </x-button>
            <x-button variant="primary" type="submit" form="approve-form">
                {{ __('admin.reports_approve_button') }}
            </x-button>
        </x-slot>
    </form>
</x-modal>

{{-- Reject Modal --}}
<x-modal id="reject-report" title="{{ __('admin.reports_reject_button') }}" size="md">
    <form id="reject-form" method="POST" action="{{ route('admin.reports.reject', $report) }}">
        @csrf
        <p class="text-sm text-[#6B7280] mb-4">
            {{ __('admin.reports_reject_help') }}
        </p>
        <x-textarea
            name="admin_note"
            :label="__('admin.reports_admin_note_label')"
            placeholder="{{ __('admin.reports_admin_note_placeholder') }}"
            rows="4"
            required
        />
        <x-slot name="footer">
            <x-button variant="ghost" type="button" x-on:click="$dispatch('close-modal-reject-report')">
                {{ __('admin.action_cancel') }}
            </x-button>
            <x-button variant="danger" type="submit" form="reject-form">
                {{ __('admin.reports_reject_button') }}
            </x-button>
        </x-slot>
    </form>
</x-modal>

{{-- Page Header --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.reports_title') }} #{{ $report->id }}</h1>
            <x-badge :status="$report->status">{{ __('admin.reports_status_' . $report->status) }}</x-badge>
        </div>
        <p class="text-sm text-[#6B7280]">
            {{ __('admin.reports_submitted_at') }}: {{ $report->created_at->format('M d, Y H:i') }}
        </p>
    </div>

    @if($report->status === 'pending')
        <div class="flex items-center gap-3" x-data>
            <x-button variant="primary" type="button" x-on:click="$dispatch('open-modal-approve-report')">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('admin.reports_approve_button') }}
            </x-button>

            <x-button variant="danger" type="button" x-on:click="$dispatch('open-modal-reject-report')">
                <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ __('admin.reports_reject_button') }}
            </x-button>
        </div>
    @endif
</div>

{{-- Admin note banner (after action) --}}
@if($report->status !== 'pending' && $report->admin_note)
    <div class="mb-6 p-4 rounded-xl flex items-start gap-3
                {{ $report->status === 'approved' ? 'bg-[#DCFCE7] border border-green-200' : 'bg-[#FEE2E2] border border-red-200' }}">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 {{ $report->status === 'approved' ? 'text-[#16A34A]' : 'text-[#DC2626]' }}"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold {{ $report->status === 'approved' ? 'text-[#16A34A]' : 'text-[#DC2626]' }}">
                {{ __('admin.reports_admin_note_label') }}
            </p>
            <p class="text-sm mt-0.5 {{ $report->status === 'approved' ? 'text-[#16A34A]' : 'text-[#DC2626]' }}">
                {{ $report->admin_note }}
            </p>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Reported service + reason --}}
    <div class="lg:col-span-2 space-y-6">

        <x-card title="{{ __('admin.reports_reason_label') }}">
            <p class="text-sm text-[#1F2937] leading-relaxed whitespace-pre-line">{{ $report->reason }}</p>
        </x-card>

        @if($report->service)
            <x-card title="{{ __('admin.reports_reported_service') }}">
                <div class="flex gap-4">
                    @php $mainImage = $report->service->getFirstMediaUrl('main-image'); @endphp
                    @if($mainImage)
                        <img src="{{ $mainImage }}"
                             alt="Service image"
                             class="w-32 h-32 object-cover rounded-xl border border-[#DDD6FE] flex-shrink-0">
                    @else
                        <div class="w-32 h-32 rounded-xl bg-[#F5F3FF] border border-[#DDD6FE] flex items-center justify-center flex-shrink-0">
                            <svg class="w-10 h-10 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-[#1F2937] mb-1">{{ $report->service->title }}</h3>
                        <p class="text-sm text-[#6B7280] mb-2">
                            {{ \Illuminate\Support\Str::limit($report->service->description, 140) }}
                        </p>
                        <div class="flex items-center gap-3 text-xs">
                            <x-badge :status="$report->service->status">{{ ucfirst($report->service->status) }}</x-badge>
                            <span class="text-[#6B7280]">{{ ucfirst($report->service->type) }}</span>
                            @if($report->service->price_syp)
                                <span class="text-[#6B7280]">{{ number_format((float) $report->service->price_syp) }} SYP</span>
                            @endif
                            @if($report->service->price_usd)
                                <span class="text-[#6B7280]">${{ number_format((float) $report->service->price_usd, 2) }}</span>
                            @endif
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.services.show', $report->service) }}"
                               class="text-sm font-medium text-[#6B21A8] hover:underline">
                                {{ __('admin.reports_open_service') }} →
                            </a>
                        </div>
                    </div>
                </div>
            </x-card>
        @endif

    </div>

    {{-- Right: Reporter + status --}}
    <div class="space-y-6">

        <x-card title="{{ __('admin.reports_col_reporter') }}">
            <div class="flex items-center gap-3 mb-4">
                <x-avatar :name="$report->user?->first_name . ' ' . $report->user?->last_name" size="lg" />
                <div>
                    <p class="font-semibold text-[#1F2937]">
                        {{ $report->user?->first_name }} {{ $report->user?->last_name }}
                    </p>
                    <p class="text-xs text-[#6B7280]">{{ $report->user?->phone }}</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">{{ __('admin.reports_reporter_country') }}</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $report->user?->country ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">{{ __('admin.reports_reporter_joined') }}</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $report->user?->created_at?->format('M d, Y') }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card title="{{ __('admin.reports_status_card') }}">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">{{ __('admin.label_status') }}</dt>
                    <dd><x-badge :status="$report->status">{{ __('admin.reports_status_' . $report->status) }}</x-badge></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B7280]">{{ __('admin.reports_col_submitted') }}</dt>
                    <dd class="font-medium text-[#1F2937]">{{ $report->created_at->format('M d, Y') }}</dd>
                </div>
                @if($report->status !== 'pending')
                    <div class="flex justify-between">
                        <dt class="text-[#6B7280]">{{ __('admin.reports_reviewed_at') }}</dt>
                        <dd class="font-medium text-[#1F2937]">{{ $report->updated_at->diffForHumans() }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>

    </div>

</div>

@endsection
