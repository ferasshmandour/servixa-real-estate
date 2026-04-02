@extends('layouts.app')

@section('title', __('admin.admin_create_title') . ' — Servixa Admin')

@section('breadcrumb')
    <a href="{{ route('admin.admins.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">{{ __('admin.admin_title') }}</a>
    <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    <span class="text-[#1F2937] font-medium">{{ __('admin.admin_create_title') }}</span>
@endsection

@section('content')

<div class="max-w-lg">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.admin_create_title') }}</h1>
        <p class="text-sm text-[#6B7280] mt-1">{{ __('admin.admin_create_subtitle') }}</p>
    </div>

    <x-card>
        <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-5">
            @csrf

            <x-input
                name="name"
                :label="__('admin.admin_name_label')"
                placeholder="Full Name"
                :value="old('name')"
            />

            <x-input
                name="email"
                type="email"
                :label="__('admin.admin_email_label')"
                placeholder="admin@servixa.com"
                :value="old('email')"
            />

            <x-input
                name="password"
                type="password"
                :label="__('admin.admin_password_label')"
                placeholder="Min 8 characters"
            />

            <x-input
                name="password_confirmation"
                type="password"
                :label="__('admin.admin_password_confirm')"
                placeholder="Repeat password"
            />

            <x-select
                name="role_id"
                :label="__('admin.admin_role_label')"
                :options="$roles->pluck('name', 'id')->toArray()"
                :selected="old('role_id')"
                :placeholder="__('admin.admin_role_placeholder')"
            />

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-[#F5F3FF]">
                <x-button variant="ghost" href="{{ route('admin.admins.index') }}">{{ __('admin.action_cancel') }}</x-button>
                <x-button variant="primary" type="submit">{{ __('admin.admin_create_btn') }}</x-button>
            </div>
        </form>
    </x-card>
</div>

@endsection
