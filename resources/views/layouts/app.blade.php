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

    {{--
        Tab-Aware Session Manager
        ─────────────────────────
        Every browser tab has its own sessionStorage. On first load this script
        generates a unique `_tab` identifier and stores it there. Every
        subsequent page load reads the same value, giving each tab a stable ID.

        The server-side TabAwareSessionGuard namespaces the session key with
        this ID, so Tab A and Tab B can be logged in as different admin accounts
        at the same time without overwriting each other.
    --}}
    <script>
    (function () {
        // ── 1. Establish the tab ID ────────────────────────────────────────────
        var url    = new URL(window.location.href);
        var urlTab = url.searchParams.get('_tab');

        if (urlTab) {
            // URL already carries a tab ID (came from a link/redirect).
            // Sync it into sessionStorage so link/form injectors use the same value.
            sessionStorage.setItem('_tab', urlTab);
        } else {
            // No _tab in URL — generate one if this is a fresh tab, then redirect.
            if (!sessionStorage.getItem('_tab')) {
                sessionStorage.setItem('_tab',
                    Math.random().toString(36).slice(2, 11) +
                    Math.random().toString(36).slice(2, 11)
                );
            }
            url.searchParams.set('_tab', sessionStorage.getItem('_tab'));
            // replace() avoids adding an extra history entry for this silent redirect.
            window.location.replace(url.toString());
            return; // Stop — page will reload immediately with _tab in the URL.
        }

        var tabId = sessionStorage.getItem('_tab');

        // ── 2. Intercept form submissions ──────────────────────────────────────
        // Uses event delegation so Alpine.js-generated forms are also covered.
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.querySelector('input[name="_tab"]')) {
                var input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = '_tab';
                input.value = tabId;
                form.appendChild(input);
            }
        }, true);

        // ── 3. Intercept link clicks ───────────────────────────────────────────
        // Rewrites same-origin hrefs to include _tab before navigation.
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (!link) return;

            var href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

            try {
                var dest = new URL(link.href, window.location.origin);
                // Skip external links and links that already have _tab.
                if (dest.origin !== window.location.origin) return;
                if (dest.searchParams.get('_tab')) return;

                e.preventDefault();
                dest.searchParams.set('_tab', tabId);

                // Honour target="_blank" (open in new tab).
                if (link.target === '_blank') {
                    window.open(dest.toString(), '_blank');
                } else {
                    window.location.href = dest.toString();
                }
            } catch (_) {
                // Malformed href — let the browser handle it naturally.
            }
        });
    })();
    </script>
</body>
</html>
