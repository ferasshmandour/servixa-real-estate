@extends('layouts.chat')

@section('title', __('chat.nav_chats') . ' — ' . __('chat.app_name'))

@section('content')
<div class="max-w-3xl mx-auto w-full px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('chat.inbox_title') }}</h1>
            <p class="text-sm text-[#6B7280] mt-1">{{ __('chat.inbox_subtitle') }}</p>
        </div>
        <a href="{{ route('chat.services.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#6B21A8] text-white text-sm font-semibold rounded-xl hover:bg-[#7C3AED] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span>
            {{ __('chat.new_chat') }}
        </a>
    </div>

    @if ($conversations->isEmpty())
        <div class="bg-white border border-[#DDD6FE] rounded-xl">
            <x-empty-state :message="__('chat.no_conversations')" :action="__('chat.browse_services')" :href="route('chat.services.index')" />
        </div>
    @else
        <div class="bg-white border border-[#DDD6FE] rounded-xl divide-y divide-[#F5F3FF] overflow-hidden">
            @foreach ($conversations as $conversation)
                @php
                    $other = $conversation->initiator_id === $currentUserId ? $conversation->receiver : $conversation->initiator;
                    $otherName = trim(($other->first_name ?? '') . ' ' . ($other->last_name ?? '')) ?: ($other->phone ?? '—');
                    $last = $conversation->messages->first();
                    $unread = $conversation->messages()
                        ->where('sender_id', '!=', $currentUserId)
                        ->whereNull('read_at')
                        ->count();
                @endphp
                <a href="{{ route('chat.conversations.show', $conversation->id) }}"
                   class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#F8F7FF] transition-colors">
                    <x-avatar :name="$otherName" :src="$other->profile_image ?? null" size="lg" />

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-[#1F2937] truncate">{{ $otherName }}</p>
                            @if ($last)
                                <span class="text-xs text-[#6B7280] shrink-0">{{ $last->created_at->diffForHumans(null, true) }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-[#6B21A8] truncate flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">sell</span>
                            {{ $conversation->service?->title ?? __('chat.deleted_service') }}
                        </p>
                        <p class="text-sm text-[#6B7280] truncate mt-0.5">
                            {{ $last ? Str::limit($last->content, 60) : __('chat.no_messages_yet') }}
                        </p>
                    </div>

                    @if ($unread > 0)
                        <span class="shrink-0 min-w-5 h-5 px-1.5 rounded-full bg-[#6B21A8] text-white text-xs font-bold flex items-center justify-center">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
    @endif

</div>
@endsection
