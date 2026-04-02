@extends('layouts.app')

@section('title', __('admin.role_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#6B7280]">Admin</span>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.role_title') }}</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.role_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.role_subtitle') }}</p>
    </div>
    <x-button variant="primary" href="{{ route('admin.roles.create') }}">
        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('admin.role_add') }}
    </x-button>
</div>

@if(session('success'))
    <x-alert type="success" :message="session('success')" class="mb-4" />
@endif

<x-card>
    @if($roles->isEmpty())
        <x-empty-state :message="__('admin.role_empty')" :action="__('admin.role_add')" :href="route('admin.roles.create')" />
    @else
        <x-data-table :headers="[__('admin.role_col_name'), __('admin.role_col_permissions'), __('admin.label_actions')]">
            @foreach($roles as $role)
            <tr class="hover:bg-[#F8F7FF] transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#F5F3FF] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-[#1F2937]">{{ $role->name }}</p>
                            @if(in_array($role->name, ['super-admin', 'admin']))
                                <span class="inline-flex items-center gap-1 text-xs text-[#6B7280]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    {{ __('admin.role_protected') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($role->permissions->take(4) as $permission)
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-[#F5F3FF] text-[#6B21A8] border border-[#DDD6FE]">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-xs text-[#6B7280]">—</span>
                        @endforelse
                        @if($role->permissions->count() > 4)
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-[#6B7280]/10 text-[#6B7280]">
                                +{{ $role->permissions->count() - 4 }} {{ __('admin.field_more') }}
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        @if(!in_array($role->name, ['super-admin']))
                            <x-button variant="outline" size="sm" :href="route('admin.roles.edit', $role)">{{ __('admin.action_edit') }}</x-button>
                        @endif
                        @if(!in_array($role->name, ['super-admin', 'admin']))
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('admin.role_delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger" size="sm" type="submit">{{ __('admin.action_delete') }}</x-button>
                            </form>
                        @endif
                        @if(in_array($role->name, ['super-admin', 'admin']))
                            <span class="text-xs text-[#6B7280]">{{ __('admin.role_protected') }}</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </x-data-table>
    @endif
</x-card>

@endsection
