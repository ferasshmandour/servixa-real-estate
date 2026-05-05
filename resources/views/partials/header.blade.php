<header class="bg-white border-b border-[#DDD6FE] px-6 h-16 flex items-center justify-between shrink-0">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('admin.dashboard') }}" class="text-[#6B7280] hover:text-[#6B21A8] transition-colors">
            {{ __('admin.nav_dashboard') }}
        </a>
        @hasSection('breadcrumb')
            <svg class="w-4 h-4 text-[#6B7280] rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @yield('breadcrumb')
        @endif
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-3">

        {{-- "Enable browser notifications" prompt — shown only when permission is 'default'.
             Click triggers the native permission popup; on grant, the FCM script registers
             the token automatically. Standalone — must NOT be wrapped in notificationBell()
             or that component initializes twice and every notification appears twice. --}}
        <button
            type="button"
            x-data="{ show: false }"
            x-init="
                if ('Notification' in window && Notification.permission === 'default') show = true;
            "
            x-show="show"
            x-cloak
            x-on:click="
                Notification.requestPermission().then(p => {
                    show = false;
                    if (p === 'granted') window.location.reload();
                });
            "
            class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#FEF3C7] hover:bg-[#FDE68A] border border-[#FCD34D] text-xs font-semibold text-[#92400E] transition-colors"
            title="{{ __('admin.notifications_enable_push') }}"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{ __('admin.notifications_enable_push') }}
        </button>

        {{-- Bell wrapper --}}
        <div
            class="relative"
            x-data="notificationBell({
                dropdownUrl: '{{ route('admin.notifications.dropdown') }}',
                indexUrl:    '{{ route('admin.notifications.index') }}',
                readUrlBase: '{{ url('admin/notifications') }}',
                readAllUrl:  '{{ route('admin.notifications.read-all') }}',
                csrf:        '{{ csrf_token() }}'
            })"
            x-init="init()"
        >
            {{-- Bell button — filled rounded-square with badge on top-right corner --}}
            <div class="relative inline-flex shrink-0">
                <button
                    type="button"
                    x-on:click="toggle()"
                    x-on:click.outside="open = false"
                    class="flex items-center justify-center w-12 h-12 rounded-2xl transition-all duration-200"
                    :class="open
                        ? 'bg-[#7C3AED] shadow-[0_6px_20px_rgba(107,33,168,0.5)]'
                        : 'bg-[#6B21A8] shadow-[0_4px_14px_rgba(107,33,168,0.35)] hover:bg-[#7C3AED] hover:shadow-[0_6px_20px_rgba(107,33,168,0.5)]'"
                    aria-label="Notifications"
                >
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </button>
                {{-- Badge sits outside the button so it's never clipped --}}
                <span
                    x-show="count > 0"
                    x-cloak
                    x-text="count > 99 ? '99+' : count"
                    class="pointer-events-none absolute -top-2 -right-2 min-w-5.5 h-5.5 px-1.5 rounded-full bg-[#DC2626] text-white text-[11px] font-bold flex items-center justify-center ring-2 ring-white shadow"
                ></span>
            </div>

            {{-- Dropdown panel --}}
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute inset-e-0 top-full mt-3 w-100 max-w-[calc(100vw-1.5rem)] bg-white rounded-2xl shadow-xl border border-[#DDD6FE] z-50"
                style="box-shadow: 0 8px 32px rgba(107,33,168,0.12), 0 2px 8px rgba(0,0,0,0.06);"
            >
                {{-- Header --}}
                <div class="px-5 py-4 border-b border-[#F3F0FF] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-[15px] font-bold text-[#1F2937]">{{ __('admin.notifications_title') }}</h3>
                        <span
                            x-show="count > 0"
                            x-text="count"
                            class="inline-flex items-center justify-center min-w-5.5 h-5.5 px-1.5 rounded-full bg-[#6B21A8] text-white text-[11px] font-bold"
                        ></span>
                    </div>
                    <button
                        type="button"
                        x-show="count > 0"
                        x-on:click="markAllRead()"
                        class="text-xs font-semibold text-[#6B21A8] hover:text-[#7C3AED] hover:bg-[#F5F3FF] px-2.5 py-1 rounded-lg transition-colors"
                    >
                        {{ __('admin.notifications_mark_all_read') }}
                    </button>
                </div>

                {{-- Scrollable list --}}
                <div class="overflow-y-auto" style="max-height: 420px;">

                    {{-- Loading skeletons --}}
                    <template x-if="loading">
                        <div class="px-5 py-3 space-y-3">
                            <template x-for="i in [1,2,3]" :key="i">
                                <div class="flex items-start gap-3 animate-pulse">
                                    <div class="w-9 h-9 rounded-full bg-[#F3F0FF] shrink-0"></div>
                                    <div class="flex-1 space-y-2 pt-0.5">
                                        <div class="h-3 bg-[#F3F0FF] rounded-full w-3/4"></div>
                                        <div class="h-2.5 bg-[#F3F0FF] rounded-full w-full"></div>
                                        <div class="h-2 bg-[#F3F0FF] rounded-full w-1/3"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!loading && notifications.length === 0">
                        <div class="flex flex-col items-center justify-center py-14 px-6">
                            <div class="w-16 h-16 rounded-2xl bg-[#F5F3FF] flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-[#C4B5FD]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-semibold text-[#1F2937] mb-1">{{ __('admin.notifications_empty') }}</p>
                            <p class="text-xs text-[#9CA3AF] text-center">{{ app()->getLocale() === 'ar' ? 'ستظهر الإشعارات الجديدة هنا' : 'New notifications will appear here' }}</p>
                        </div>
                    </template>

                    {{-- Notification items --}}
                    <template x-for="n in notifications" :key="n.id">
                        <button
                            type="button"
                            x-on:click="openItem(n)"
                            class="w-full text-start flex items-start gap-4 px-4 py-3.5 mx-2 my-1 rounded-2xl transition-all duration-150 active:scale-95"
                            :class="!n.read_at
                                ? 'bg-[#F5F3FF] hover:bg-[#EDE9FE] border border-[#DDD6FE]'
                                : 'bg-white hover:bg-[#F9F8FF] border border-transparent hover:border-[#EDE9FE]'"
                            style="width: calc(100% - 1rem);"
                        >
                            {{-- Icon rounded-square --}}
                            <div class="shrink-0 w-11 h-11 rounded-2xl flex items-center justify-center transition-colors"
                                 :class="!n.read_at ? 'bg-[#6B21A8]' : 'bg-[#F3F4F6]'">
                                <svg class="w-5 h-5"
                                     :class="!n.read_at ? 'text-white' : 'text-[#9CA3AF]'"
                                     fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <p class="text-[13px] leading-snug truncate"
                                       :class="!n.read_at ? 'font-bold text-[#1F2937]' : 'font-medium text-[#374151]'"
                                       x-text="n.data.title"></p>
                                    {{-- Unread dot --}}
                                    <span x-show="!n.read_at"
                                          class="shrink-0 w-2.5 h-2.5 rounded-full bg-[#6B21A8]"></span>
                                </div>
                                <p class="text-[12px] text-[#6B7280] leading-relaxed line-clamp-2" x-text="n.data.body"></p>
                                <div class="flex items-center gap-1.5 mt-2">
                                    <svg class="w-3 h-3 text-[#9CA3AF] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-[11px] text-[#9CA3AF] font-medium" x-text="n.created_at"></p>
                                </div>
                            </div>
                        </button>
                    </template>

                </div>

                {{-- Footer --}}
                <div class="border-t border-[#F3F0FF]">
                    <a
                        :href="indexUrl"
                        class="flex items-center justify-center gap-1.5 px-5 py-3.5 text-[13px] font-semibold text-[#6B21A8] hover:bg-[#F5F3FF] transition-colors rounded-b-2xl"
                    >
                        {{ __('admin.notifications_view_all') }}
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Language toggle --}}
        <div class="flex items-center bg-[#F5F3FF] border border-[#DDD6FE] rounded-full p-0.5">
            <a href="{{ route('locale.switch', 'en') }}"
               class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                      {{ app()->getLocale() === 'en'
                          ? 'bg-[#6B21A8] text-white shadow-sm'
                          : 'text-[#6B7280] hover:text-[#6B21A8]' }}">
                EN
            </a>
            <a href="{{ route('locale.switch', 'ar') }}"
               class="px-3 py-1 text-xs font-semibold rounded-full transition-all duration-200
                      {{ app()->getLocale() === 'ar'
                          ? 'bg-[#6B21A8] text-white shadow-sm'
                          : 'text-[#6B7280] hover:text-[#6B21A8]' }}">
                AR
            </a>
        </div>

        {{-- Admin dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button
                type="button"
                x-on:click="open = !open"
                x-on:click.outside="open = false"
                class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-[#F5F3FF] transition-colors"
            >
                <x-avatar :name="$adminUser?->name ?? 'Admin'" size="sm" />
                <div class="text-start hidden sm:block">
                    <p class="text-sm font-medium text-[#1F2937]">{{ $adminUser?->name ?? 'Admin' }}</p>
                    <p class="text-xs text-[#6B7280]">{{ __('admin.nav_administrator') }}</p>
                </div>
                <svg class="w-4 h-4 text-[#6B7280] transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown menu --}}
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                class="absolute inset-e-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-[#DDD6FE] py-1 z-50"
            >
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-[#DC2626] hover:bg-[#FEE2E2]/50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('admin.action_logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>
