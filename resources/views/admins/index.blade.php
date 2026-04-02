@extends('layouts.app')

@section('title', __('admin.admin_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.admin_title') }}</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.admin_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.admin_subtitle') }}</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.admins.create') }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.admin_add') }}
    </x-button>
</div>

@if(session('success'))
    <x-alert type="success" :message="session('success')" class="mb-4" />
@endif

<x-card>
    @if($admins->isEmpty())
        <x-empty-state :message="__('admin.admin_empty')" :action="__('admin.admin_add')" :href="route('admin.admins.create')" />
    @else
        <x-data-table :headers="[__('admin.admin_col_admin'), __('admin.admin_col_email'), __('admin.admin_col_role'), __('admin.label_actions')]">
            @foreach($admins as $adminUser)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$adminUser->name" size="sm" />
                        <div>
                            <p class="font-semibold text-[#1F2937]">{{ $adminUser->name }}</p>
                            @if($adminUser->id === auth('admin')->id())
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-[#F5F3FF] text-[#6B21A8]">{{ __('admin.admin_you') }}</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-sm text-[#6B7280]">{{ $adminUser->email }}</td>
                <td class="px-4 py-3">
                    @if($role = $adminUser->roles->first())
                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full bg-[#F5F3FF] text-[#6B21A8] border border-[#DDD6FE]">
                            {{ $role->name }}
                        </span>
                    @else
                        <span class="text-xs text-[#6B7280]">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <x-button variant="outline" size="sm" :href="route('admin.admins.edit', $adminUser)">{{ __('admin.action_edit') }}</x-button>
                        @if($adminUser->id !== auth('admin')->id())
                            <form method="POST" action="{{ route('admin.admins.destroy', $adminUser) }}" onsubmit="return confirm('{{ __('admin.admin_delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger" size="sm" type="submit">{{ __('admin.action_delete') }}</x-button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </x-data-table>
    @endif
</x-card>

@endsection
