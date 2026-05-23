@extends('layouts.app')

@section('title', __('admin.reports_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.reports_title') }}</span>
@endsection

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.reports_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.reports_subtitle') }}</p>
    </div>
</div>

{{-- Status Filter Tabs --}}
<div class="flex gap-1 mb-0 border-b border-[#DDD6FE]">
    @foreach(['all' => __('admin.reports_tab_all'), 'pending' => __('admin.reports_tab_pending'), 'approved' => __('admin.reports_tab_approved'), 'rejected' => __('admin.reports_tab_rejected')] as $value => $label)
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

    @if($reports->isEmpty())
        <x-empty-state :message="__('admin.reports_empty')" />
    @else
        <x-data-table :headers="['#', __('admin.reports_col_service'), __('admin.reports_col_reporter'), __('admin.reports_col_reason'), __('admin.label_status'), __('admin.reports_col_submitted'), '']">
            @foreach($reports as $report)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3 text-xs text-[#6B7280] font-mono">{{ $report->id }}</td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-[#1F2937]">{{ $report->service?->title ?? '—' }}</div>
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">
                    {{ $report->user?->first_name }} {{ $report->user?->last_name }}
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280] max-w-xs truncate">
                    {{ \Illuminate\Support\Str::limit($report->reason, 60) }}
                </td>
                <td class="px-4 py-3">
                    <x-badge :status="$report->status">
                        {{ __('admin.reports_status_' . $report->status) }}
                    </x-badge>
                </td>
                <td class="px-4 py-3 text-xs text-[#6B7280]">
                    {{ $report->created_at->format('M d, Y') }}
                </td>
                <td class="px-4 py-3">
                    <x-button variant="secondary" size="sm" :href="route('admin.reports.show', $report)">
                        {{ __('admin.reports_review') }}
                    </x-button>
                </td>
            </tr>
            @endforeach
        </x-data-table>

        <div class="mt-4 px-2">
            {{ $reports->withQueryString()->links() }}
        </div>
    @endif
</x-card>

@endsection
