@extends('layouts.app')

@section('title', __('admin.role_create_title') . ' — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.roles.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.role_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.role_create_title') }}</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.role_create_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.role_create_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-5">
        @csrf

        <x-card>
            <x-input
                name="name"
                :label="__('admin.role_name_label')"
                placeholder="e.g. content-manager"
                :value="old('name')"
            />
        </x-card>

        <x-card>
            <x-slot name="title">{{ __('admin.role_permissions_label') }}</x-slot>

            <p class="text-sm text-[#6B7280] mb-4">{{ __('admin.role_permissions_hint') }}</p>

            @if($permissions->isEmpty())
                <p class="text-sm text-[#6B7280]">{{ __('admin.empty_nothing') }}</p>
            @else
                <div class="space-y-5">
                    @foreach($permissions as $resource => $group)
                        <div class="border border-[#DDD6FE] rounded-xl overflow-hidden">
                            <div class="px-4 py-2.5 bg-[#F5F3FF] border-b border-[#DDD6FE]">
                                <p class="text-sm font-semibold text-[#6B21A8] capitalize">{{ str_replace('-', ' ', $resource) }}</p>
                            </div>
                            <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($group as $permission)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->name }}"
                                            {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-[#DDD6FE] text-[#6B21A8] focus:ring-[#6B21A8]/30 cursor-pointer"
                                        >
                                        <span class="text-sm text-[#1F2937] group-hover:text-[#6B21A8] transition-colors">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @error('permissions')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button variant="ghost" href="{{ route('admin.roles.index') }}">{{ __('admin.action_cancel') }}</x-button>
            <x-button variant="primary" type="submit">{{ __('admin.role_create_btn') }}</x-button>
        </div>
    </form>
</div>

@endsection
