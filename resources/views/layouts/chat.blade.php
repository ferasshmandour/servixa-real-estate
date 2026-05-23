<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('chat.app_name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', 'Noto Kufi Arabic', sans-serif; }
        html[lang="ar"] body { font-family: 'Noto Kufi Arabic', 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
            line-height: 1;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-[#F8F7FF] text-[#1F2937] antialiased min-h-screen flex flex-col">

    {{-- ── Top bar ── --}}
    <header class="sticky top-0 z-30 h-16 bg-white border-b border-[#DDD6FE] shadow-[0_2px_8px_rgba(107,33,168,0.05)]">
        <div class="h-full max-w-6xl mx-auto px-4 flex items-center gap-4">
            {{-- Brand --}}
            <a href="{{ route('chat.index') }}" class="flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 bg-[#6B21A8] rounded-lg flex items-center justify-center">
                    <span class="text-white font-black text-sm">S</span>
                </div>
                <span class="font-bold text-lg tracking-tight text-[#1F2937] hidden sm:inline">{{ __('chat.app_name') }}</span>
            </a>

            {{-- Nav --}}
            <nav class="flex items-center gap-1 ms-2">
                @php $isChats = request()->routeIs('chat.index') || request()->routeIs('chat.conversations.*'); @endphp
                <a href="{{ route('chat.index') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors
                        {{ $isChats ? 'bg-[#F5F3FF] text-[#6B21A8]' : 'text-[#6B7280] hover:bg-[#F5F3FF] hover:text-[#6B21A8]' }}">
                    <span class="material-symbols-outlined text-[20px]">forum</span>
                    <span class="hidden sm:inline">{{ __('chat.nav_chats') }}</span>
                </a>
                <a href="{{ route('chat.services.index') }}"
                   class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-colors
                        {{ request()->routeIs('chat.services.*') ? 'bg-[#F5F3FF] text-[#6B21A8]' : 'text-[#6B7280] hover:bg-[#F5F3FF] hover:text-[#6B21A8]' }}">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                    <span class="hidden sm:inline">{{ __('chat.nav_browse') }}</span>
                </a>
            </nav>

            <div class="ms-auto flex items-center gap-3">
                {{-- Language toggle --}}
                <div class="flex items-center bg-[#F5F3FF] border border-[#DDD6FE] rounded-full p-0.5">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#6B21A8] text-white' : 'text-[#6B7280]' }}">EN</a>
                    <a href="{{ route('locale.switch', 'ar') }}"
                       class="px-2.5 py-1 rounded-full text-xs font-semibold transition-colors {{ app()->getLocale() === 'ar' ? 'bg-[#6B21A8] text-white' : 'text-[#6B7280]' }}">AR</a>
                </div>

                {{-- User + logout --}}
                @auth('web')
                    <div class="flex items-center gap-2" x-data="{ open: false }">
                        <button x-on:click="open = !open" x-on:click.outside="open = false"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-[#F5F3FF] transition-colors">
                            <x-avatar :name="trim(auth('web')->user()->first_name . ' ' . auth('web')->user()->last_name) ?: auth('web')->user()->phone" size="sm" />
                            <span class="text-sm font-medium text-[#1F2937] hidden md:inline">
                                {{ trim(auth('web')->user()->first_name . ' ' . auth('web')->user()->last_name) ?: auth('web')->user()->phone }}
                            </span>
                            <span class="material-symbols-outlined text-[18px] text-[#6B7280]" :class="open && 'rotate-180'">expand_more</span>
                        </button>
                        <div x-show="open" x-cloak x-transition
                             class="absolute top-14 inset-e-4 bg-white border border-[#DDD6FE] rounded-xl shadow-lg py-1 w-44 z-40">
                            <form method="POST" action="{{ route('chat.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-start px-4 py-2.5 text-sm text-[#DC2626] hover:bg-[#FEE2E2]/40 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    {{ __('chat.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- ── Page content ── --}}
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
