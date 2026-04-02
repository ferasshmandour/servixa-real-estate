@extends('layouts.app')

@section('title', __('admin.dashboard_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#1F2937] font-medium">{{ __('admin.nav_dashboard') }}</span>
@endsection

@section('content')

{{-- Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <x-stat-card
        :label="__('admin.dashboard_pending_accounts')"
        :value="$stats['pending_accounts']"
        icon="briefcase"
        color="yellow"
    />
    <x-stat-card
        :label="__('admin.dashboard_pending_services')"
        :value="$stats['pending_services']"
        icon="shopping-bag"
        color="yellow"
    />
    <x-stat-card
        :label="__('admin.dashboard_total_users')"
        :value="$stats['total_users']"
        icon="users"
        color="purple"
    />
    <x-stat-card
        :label="__('admin.dashboard_approved_services')"
        :value="$stats['approved_services']"
        icon="chart-bar"
        color="green"
    />
</div>

{{-- Two-column tables --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- Recent Pending Business Accounts --}}
    <x-card :title="__('admin.dashboard_pending_accounts_table')">
        <x-slot name="actions">
            <x-button variant="secondary" size="sm" href="{{ route('admin.business-accounts.index') }}">
                {{ __('admin.action_view_all') }}
            </x-button>
        </x-slot>

        @if($recentPendingAccounts->isEmpty())
            <x-empty-state :message="__('admin.dashboard_no_pending_accounts')" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#F5F3FF] text-[#6B21A8] text-xs uppercase tracking-wide">
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.ba_col_business') }}</th>
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.ba_col_owner') }}</th>
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.ba_col_submitted') }}</th>
                            <th class="px-4 py-3 text-start font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F3FF]">
                        @foreach($recentPendingAccounts as $account)
                        <tr class="hover:bg-[#F8F7FF] transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-[#1F2937]">{{ $account->name }}</div>
                                <div class="text-xs text-[#6B7280]">{{ $account->city?->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-[#6B7280]">
                                {{ $account->user?->first_name }} {{ $account->user?->last_name }}
                            </td>
                            <td class="px-4 py-3 text-xs text-[#6B7280]">
                                {{ $account->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3">
                                <x-button variant="secondary" size="sm" :href="route('admin.business-accounts.show', $account)">
                                    {{ __('admin.ba_review') }}
                                </x-button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    {{-- Recent Pending Services --}}
    <x-card :title="__('admin.dashboard_pending_services_table')">
        <x-slot name="actions">
            <x-button variant="secondary" size="sm" href="{{ route('admin.services.index') }}">
                {{ __('admin.action_view_all') }}
            </x-button>
        </x-slot>

        @if($recentPendingServices->isEmpty())
            <x-empty-state :message="__('admin.dashboard_no_pending_services')" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-[#F5F3FF] text-[#6B21A8] text-xs uppercase tracking-wide">
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.nav_services') }}</th>
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.nav_categories') }}</th>
                            <th class="px-4 py-3 text-start font-semibold">{{ __('admin.ba_col_submitted') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F3FF]">
                        @foreach($recentPendingServices as $service)
                        <tr class="hover:bg-[#F8F7FF] transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-[#1F2937]">{{ $service->title }}</div>
                                <div class="text-xs text-[#6B7280]">{{ $service->businessAccount?->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-[#6B7280]">
                                {{ $service->category?->name }}
                            </td>
                            <td class="px-4 py-3 text-xs text-[#6B7280]">
                                {{ $service->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

</div>

@endsection
