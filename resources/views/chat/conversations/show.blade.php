@extends('layouts.chat')

@section('title', __('chat.nav_chats') . ' — ' . __('chat.app_name'))

@php
    $isInitiator = $conversation->initiator_id === $currentUserId;
    $other       = $isInitiator ? $conversation->receiver : $conversation->initiator;
    $otherName   = trim(($other->first_name ?? '') . ' ' . ($other->last_name ?? '')) ?: ($other->phone ?? '—');

    // The other party's identity in this conversation.
    $otherContext = $isInitiator
        ? $conversation->service?->businessAccount?->name          // receiver = service owner (a business)
        : ($conversation->initiatorBusinessAccount?->name ?? null); // initiator may act as a business or as a user

    // Messages (paginator is latest-first) → chronological for the initial render.
    $initial = $messages->getCollection()->sortBy('id')->values()->map(fn ($m) => [
        'id'         => $m->id,
        'sender_id'  => $m->sender_id,
        'content'    => $m->content,
        'status'     => $m->status,
        'created_at' => $m->created_at->toIso8601String(),
    ]);
@endphp

@section('content')
<div class="flex flex-col h-[calc(100vh-4rem)]"
     x-data="chatThread({
        messages: @js($initial),
        currentUserId: {{ $currentUserId }},
        conversationId: {{ $conversation->id }},
        sendUrl: '{{ route('chat.conversations.send', $conversation->id) }}',
        readUrl: '{{ route('chat.conversations.read', $conversation->id) }}'
     })">

    {{-- Sub-header --}}
    <div class="bg-white border-b border-[#DDD6FE] px-4 py-3 flex items-center gap-3">
        <a href="{{ route('chat.index') }}" class="text-[#6B7280] hover:text-[#6B21A8] shrink-0">
            <span class="material-symbols-outlined rtl:rotate-180">arrow_back</span>
        </a>
        <x-avatar :name="$otherName" :src="$other->profile_image ?? null" size="md" />
        <div class="min-w-0">
            <p class="font-semibold text-[#1F2937] truncate flex items-center gap-1.5">
                {{ $otherName }}
                @if ($otherContext)
                    <span class="px-1.5 py-0.5 rounded-md bg-[#F5F3FF] text-[#6B21A8] text-[10px] font-bold">{{ __('chat.business') }}</span>
                @else
                    <span class="px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[10px] font-bold">{{ __('chat.user') }}</span>
                @endif
            </p>
            <p class="text-xs text-[#6B7280] truncate">
                {{ $otherContext ? $otherContext . ' · ' : '' }}{{ $conversation->service?->title ?? __('chat.deleted_service') }}
            </p>
        </div>
        <div class="ms-auto shrink-0" x-show="connected" x-cloak>
            <span class="flex items-center gap-1 text-xs text-[#16A34A]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#16A34A]"></span>{{ __('chat.live') }}
            </span>
        </div>
    </div>

    {{-- Messages --}}
    <div x-ref="scroller" class="flex-1 overflow-y-auto px-4 py-6 space-y-2 bg-[#F8F7FF]">
        <template x-if="messages.length === 0">
            <div class="text-center text-sm text-[#6B7280] py-10">{{ __('chat.say_hello') }}</div>
        </template>

        <template x-for="m in messages" :key="m.id">
            <div :class="isMine(m) ? 'flex justify-end' : 'flex justify-start'">
                <div class="max-w-[78%] rounded-2xl px-3.5 py-2 shadow-sm"
                     :class="isMine(m) ? 'bg-[#6B21A8] text-white rounded-ee-sm' : 'bg-white text-[#1F2937] border border-[#DDD6FE] rounded-es-sm'">
                    <p class="text-sm whitespace-pre-wrap break-words" x-text="m.content"></p>
                    <div class="flex items-center gap-1 mt-1 text-[10px]"
                         :class="isMine(m) ? 'text-white/70 justify-end' : 'text-[#6B7280] justify-end'">
                        <span x-text="timeLabel(m.created_at)"></span>
                        <template x-if="isMine(m)">
                            <span class="material-symbols-outlined text-[14px]" x-text="m.status === 'read' ? 'done_all' : 'done'"></span>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Composer --}}
    <form x-on:submit.prevent="send()" class="bg-white border-t border-[#DDD6FE] px-4 py-3 flex items-center gap-3">
        <input type="text" x-model="draft" :disabled="sending"
               placeholder="{{ __('chat.type_message') }}" autocomplete="off"
               class="flex-1 px-4 py-2.5 rounded-full border border-[#DDD6FE] text-sm focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent">
        <button type="submit" :disabled="sending || draft.trim() === ''"
                class="w-11 h-11 shrink-0 bg-[#6B21A8] text-white rounded-full hover:bg-[#7C3AED] transition-colors flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed">
            <span class="material-symbols-outlined text-[20px] rtl:rotate-180">send</span>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatThread', (cfg) => ({
            messages: cfg.messages,
            currentUserId: cfg.currentUserId,
            conversationId: cfg.conversationId,
            draft: '',
            sending: false,
            connected: false,
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),

            init() {
                this.$nextTick(() => this.scrollToBottom());

                if (window.Echo) {
                    const channel = window.Echo.private('conversations.' + this.conversationId);
                    channel.listen('.message.sent', (e) => {
                        this.appendMessage(e);
                        if (e.sender_id !== this.currentUserId) {
                            this.markRead();
                        }
                    });
                    // Pusher connection state → "live" indicator.
                    if (window.Echo.connector?.pusher) {
                        const pusher = window.Echo.connector.pusher;
                        this.connected = pusher.connection.state === 'connected';
                        pusher.connection.bind('state_change', (s) => {
                            this.connected = s.current === 'connected';
                        });
                    }
                }
            },

            appendMessage(m) {
                // Dedup by id — the sender also receives their own broadcast.
                if (this.messages.some((x) => x.id === m.id)) return;
                this.messages.push({
                    id: m.id,
                    sender_id: m.sender_id,
                    content: m.content,
                    status: m.status,
                    created_at: m.created_at,
                });
                this.$nextTick(() => this.scrollToBottom());
            },

            async send() {
                const text = this.draft.trim();
                if (text === '' || this.sending) return;
                this.sending = true;
                try {
                    const res = await window.axios.post(cfg.sendUrl, { content: text }, {
                        headers: { 'X-CSRF-TOKEN': this.csrf },
                    });
                    this.appendMessage(res.data.data ?? res.data);
                    this.draft = '';
                } catch (err) {
                    // Keep the draft so the user can retry.
                } finally {
                    this.sending = false;
                }
            },

            async markRead() {
                try {
                    await window.axios.post(cfg.readUrl, {}, { headers: { 'X-CSRF-TOKEN': this.csrf } });
                } catch (e) { /* non-critical */ }
            },

            scrollToBottom() {
                const el = this.$refs.scroller;
                if (el) el.scrollTop = el.scrollHeight;
            },

            isMine(m) {
                return m.sender_id === this.currentUserId;
            },

            timeLabel(iso) {
                try {
                    return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                } catch (e) {
                    return '';
                }
            },
        }));
    });
</script>
@endpush
