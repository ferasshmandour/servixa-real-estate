@extends('layouts.app')

@section('title', __('admin.notifications_title') . ' — Servixa Admin')

@section('breadcrumb')
    <span class="text-[#1F2937] font-medium">{{ __('admin.notifications_title') }}</span>
@endsection

@push('styles')
<style>
    .notification-row { transition: background 0.15s; }
    .notification-row.marking { opacity: 0.6; pointer-events: none; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('admin.notifications_title') }}</h1>
        @if($notifications->total() > 0)
            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-[#EDE9FE] text-[#6B21A8] text-xs font-bold">
                {{ $notifications->total() }} {{ app()->getLocale() === 'ar' ? 'إشعار' : 'total' }}
            </span>
        @endif
    </div>

    @if($notifications->total() > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl border border-[#DDD6FE] text-[#6B21A8] hover:bg-[#F5F3FF] transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('admin.notifications_mark_all_read') }}
            </button>
        </form>
    @endif
</div>

{{-- Notification list --}}
@if($notifications->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 bg-white rounded-2xl border border-[#DDD6FE]"
         style="box-shadow: 0 2px 8px rgba(107,33,168,0.06);">
        <div class="w-20 h-20 rounded-2xl bg-[#F5F3FF] flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-[#C4B5FD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <p class="text-base font-bold text-[#1F2937] mb-1">{{ __('admin.notifications_no_data') }}</p>
        <p class="text-sm text-[#9CA3AF]">
            {{ app()->getLocale() === 'ar' ? 'ستظهر الإشعارات هنا عند وصولها' : 'Notifications will show up here when they arrive' }}
        </p>
    </div>
@else
    <div class="bg-white rounded-2xl border border-[#DDD6FE] overflow-hidden"
         style="box-shadow: 0 2px 8px rgba(107,33,168,0.06);">

        <ul class="divide-y divide-[#F9F7FF]">
            @foreach($notifications as $n)
                @php
                    $deeplink = $n->data['data']['deeplink'] ?? null;
                    $isUnread = $n->read_at === null;
                    $title    = $n->data['title'] ?? '';
                    $body     = $n->data['body']  ?? '';
                @endphp

                <li
                    id="notif-{{ $n->id }}"
                    class="notification-row flex items-start gap-4 px-6 py-4 {{ $isUnread ? 'bg-[#FAF8FF]' : 'bg-white' }} hover:bg-[#F5F3FF] transition-colors"
                    data-id="{{ $n->id }}"
                    data-unread="{{ $isUnread ? 'true' : 'false' }}"
                    data-deeplink="{{ $deeplink }}"
                    data-csrf="{{ csrf_token() }}"
                    data-read-url="{{ url('admin/notifications/' . $n->id . '/read') }}"
                    @if($deeplink || $isUnread)
                        onclick="handleNotifClick(this)"
                        style="cursor: pointer;"
                    @endif
                >
                    {{-- Icon --}}
                    <div data-icon
                         class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5
                                {{ $isUnread ? 'bg-[#EDE9FE]' : 'bg-[#F3F4F6]' }}">
                        <svg data-icon-svg
                             class="w-5 h-5 {{ $isUnread ? 'text-[#6B21A8]' : 'text-[#9CA3AF]' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <p data-title
                               class="text-sm leading-snug {{ $isUnread ? 'font-semibold text-[#111827]' : 'font-medium text-[#374151]' }}">
                                {{ $title }}
                            </p>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($isUnread)
                                    <span data-unread-badge
                                          class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EDE9FE] text-[#6B21A8]">
                                        {{ __('admin.notifications_unread') }}
                                    </span>
                                @endif
                                <span data-unread-dot
                                      class="w-2 h-2 rounded-full {{ $isUnread ? 'bg-[#6B21A8]' : 'bg-transparent' }} shrink-0 mt-0.5"></span>
                            </div>
                        </div>
                        <p class="text-sm text-[#6B7280] mt-1 leading-relaxed">{{ $body }}</p>
                        <p class="text-xs text-[#9CA3AF] mt-1.5 font-medium">{{ $n->created_at?->diffForHumans() }}</p>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-[#F3F0FF] bg-[#FAFAFF]">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endif

@endsection

@push('scripts')
<script>
function handleNotifClick(el) {
    const isUnread  = el.dataset.unread === 'true';
    const deeplink  = el.dataset.deeplink || null;
    const id        = el.dataset.id;
    const readUrl   = el.dataset.readUrl;
    const csrf      = el.dataset.csrf;
    const tab       = sessionStorage.getItem('_tab') || '';

    function go() {
        if (deeplink) {
            const u = new URL(deeplink, window.location.origin);
            if (tab) u.searchParams.set('_tab', tab);
            window.location.href = u.toString();
        }
    }

    if (!isUnread) { go(); return; }

    // Instant visual flip — no flicker on navigate
    el.dataset.unread = 'false';
    el.classList.remove('bg-[#FAF8FF]');
    el.classList.add('bg-white');

    const badge = el.querySelector('[data-unread-badge]');
    const dot   = el.querySelector('[data-unread-dot]');
    const title = el.querySelector('[data-title]');
    const icon    = el.querySelector('[data-icon]');
    const iconSvg = el.querySelector('[data-icon-svg]');
    if (badge)   badge.remove();
    if (dot)     { dot.classList.remove('bg-[#6B21A8]'); dot.classList.add('bg-transparent'); }
    if (title)   { title.classList.remove('font-semibold', 'text-[#111827]'); title.classList.add('font-medium', 'text-[#374151]'); }
    if (icon)    { icon.classList.remove('bg-[#EDE9FE]'); icon.classList.add('bg-[#F3F4F6]'); }
    if (iconSvg) { iconSvg.classList.remove('text-[#6B21A8]'); iconSvg.classList.add('text-[#9CA3AF]'); }

    // Server call with _tab
    const u = new URL(readUrl, window.location.origin);
    if (tab) u.searchParams.set('_tab', tab);

    fetch(u.toString(), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).finally(() => go());
}
</script>
@endpush
