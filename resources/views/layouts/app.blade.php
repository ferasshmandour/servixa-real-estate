<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Servixa Admin')</title>

    {{-- Google Fonts: non-blocking async load --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;600;700&family=Inter:wght@400;600;700&display=optional" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;600;700&family=Inter:wght@400;600;700&display=optional"></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', 'Noto Kufi Arabic', sans-serif; }
        html[lang="ar"] body { font-family: 'Noto Kufi Arabic', 'Inter', sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#F8F7FF] text-[#1F2937] antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar (fixed) --}}
        @include('partials.sidebar')

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col overflow-hidden ms-64">

            {{-- Topbar --}}
            @include('partials.header')

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                <x-alert />
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('partials.footer')

        </div>
    </div>

    @stack('scripts')
</body>
</html>
