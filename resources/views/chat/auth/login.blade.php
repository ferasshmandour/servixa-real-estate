<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('chat.login_title') }} — {{ __('chat.app_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', 'Noto Kufi Arabic', sans-serif; }
        html[lang="ar"] body { font-family: 'Noto Kufi Arabic', 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; line-height: 1; }
    </style>
</head>
<body class="bg-[#F8F7FF] text-[#1F2937] antialiased min-h-screen flex items-center justify-center p-4">

    {{-- Language toggle --}}
    <div class="fixed top-5 inset-e-5 flex items-center bg-white border border-[#DDD6FE] rounded-full p-0.5 shadow-sm">
        <a href="{{ route('locale.switch', 'en') }}"
           class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ app()->getLocale() === 'en' ? 'bg-[#6B21A8] text-white' : 'text-[#6B7280]' }}">EN</a>
        <a href="{{ route('locale.switch', 'ar') }}"
           class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ app()->getLocale() === 'ar' ? 'bg-[#6B21A8] text-white' : 'text-[#6B7280]' }}">AR</a>
    </div>

    <div class="w-full max-w-md" x-data="{ showPass: false }">
        {{-- Brand --}}
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 bg-[#6B21A8] rounded-2xl flex items-center justify-center shadow-[0_8px_24px_rgba(107,33,168,0.3)] mb-4">
                <span class="material-symbols-outlined text-white text-[28px]" style="font-variation-settings:'FILL' 1;">forum</span>
            </div>
            <h1 class="text-2xl font-bold text-[#1F2937]">{{ __('chat.login_title') }}</h1>
            <p class="text-sm text-[#6B7280] mt-1">{{ __('chat.login_subtitle') }}</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl border border-[#DDD6FE] shadow-[0_2px_8px_rgba(107,33,168,0.08)] p-7">

            @if ($errors->any())
                <div class="bg-[#FEE2E2]/50 border border-[#DC2626]/30 rounded-xl px-4 py-3 mb-5 text-sm text-[#DC2626]">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('chat.login.submit') }}" class="flex flex-col gap-5">
                @csrf

                {{-- Phone --}}
                <div class="flex flex-col gap-1.5">
                    <label for="phone" class="text-sm font-medium text-[#1F2937]">{{ __('chat.phone_label') }}</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute inset-s-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[20px] pointer-events-none">call</span>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" autofocus required
                               placeholder="+963999000111" dir="ltr"
                               class="w-full ps-11 pe-4 py-3 rounded-xl border text-sm text-[#1F2937] placeholder-[#6B7280] transition-colors focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent {{ $errors->has('phone') ? 'border-[#DC2626] bg-[#FEE2E2]/20' : 'border-[#DDD6FE] hover:border-[#6B21A8]' }}">
                    </div>
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-medium text-[#1F2937]">{{ __('chat.password_label') }}</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute inset-s-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-[20px] pointer-events-none">lock</span>
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full ps-11 pe-11 py-3 rounded-xl border text-sm text-[#1F2937] placeholder-[#6B7280] transition-colors focus:outline-none focus:ring-2 focus:ring-[#6B21A8] focus:border-transparent {{ $errors->has('password') ? 'border-[#DC2626] bg-[#FEE2E2]/20' : 'border-[#DDD6FE] hover:border-[#6B21A8]' }}">
                        <button type="button" x-on:click="showPass = !showPass" tabindex="-1"
                                class="absolute inset-e-3 top-1/2 -translate-y-1/2 text-[#6B7280] hover:text-[#6B21A8]">
                            <span class="material-symbols-outlined text-[20px]" x-text="showPass ? 'visibility_off' : 'visibility'">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded accent-[#6B21A8]">
                    <span class="text-sm text-[#6B7280]">{{ __('chat.remember_me') }}</span>
                </label>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 bg-[#6B21A8] text-white font-semibold text-sm rounded-xl hover:bg-[#7C3AED] transition-colors shadow-[0_4px_20px_rgba(107,33,168,0.35)] flex items-center justify-center gap-2">
                    <span>{{ __('chat.sign_in') }}</span>
                    <span class="material-symbols-outlined text-[20px] rtl:rotate-180">arrow_forward</span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-[#6B7280] mt-6">{{ __('chat.login_hint') }}</p>
    </div>
</body>
</html>
