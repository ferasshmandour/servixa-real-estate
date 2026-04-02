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
                <x-avatar :name="auth('admin')->user()?->name ?? 'Admin'" size="sm" />
                <div class="text-start hidden sm:block">
                    <p class="text-sm font-medium text-[#1F2937]">{{ auth('admin')->user()?->name ?? 'Admin' }}</p>
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
                class="absolute end-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-[#DDD6FE] py-1 z-50"
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
