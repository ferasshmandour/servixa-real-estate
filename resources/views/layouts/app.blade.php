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
        Notification bell (Alpine component)
        ────────────────────────────────────
        Polls the dashboard dropdown endpoint every 60s.
        Marks items read when clicked. Triggered by the bell icon in the header.
    --}}
    <script>
    function notificationBell(config) {
        return {
            open: false,
            loading: false,
            count: 0,
            notifications: [],
            indexUrl: config.indexUrl,
            csrf: config.csrf,
            _interval: null,

            // Append the tab ID to any URL so TabAwareSessionGuard can resolve the session
            _url(base) {
                const u = new URL(base, window.location.origin);
                const tab = sessionStorage.getItem('_tab') || '';
                if (tab) u.searchParams.set('_tab', tab);
                return u.toString();
            },

            init() {
                this.refresh();
                // Poll every 15s so the badge feels live for admins who
                // haven't granted (or whose browser blocks) FCM web push.
                this._interval = setInterval(() => this.refresh(), 15000);
                window.addEventListener('notifications:refresh', () => this.refresh());
                // Refresh immediately when the tab regains focus.
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) this.refresh();
                });
            },

            async refresh() {
                this.loading = true;
                try {
                    const res = await fetch(this._url(config.dropdownUrl), {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.count = data.count || 0;
                    this.notifications = data.notifications || [];
                } catch (e) { /* silent */ }
                finally { this.loading = false; }
            },

            toggle() {
                this.open = !this.open;
                if (this.open) this.refresh();
            },

            openItem(n) {
                if (!n.read_at) {
                    n.read_at = new Date().toISOString();
                    this.count = Math.max(0, this.count - 1);
                    this._sendMarkRead(n.id);
                }
                const link = n.data?.data?.deeplink;
                if (link) window.location.href = link;
            },

            async _sendMarkRead(id) {
                try {
                    await fetch(this._url(`${config.readUrlBase}/${id}/read`), {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } catch (e) { /* silent */ }
            },

            async markAllRead() {
                this.notifications.forEach(n => { if (!n.read_at) n.read_at = new Date().toISOString(); });
                this.count = 0;
                try {
                    await fetch(this._url(config.readAllUrl), {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } catch (e) { /* silent */ }
            }
        }
    }
    </script>

    @auth('admin')
    {{--
        Firebase Cloud Messaging (Web Push)
        ────────────────────────────────────
        Initializes Firebase, requests permission, registers the device token
        with our backend, and surfaces foreground messages as toasts. Service
        worker (public/firebase-messaging-sw.js) handles background pushes.

        Note: every server request must carry the `_tab` query string so
        TabAwareSessionGuard can resolve the admin's session — fetch URLs
        below are built via _url() to inject it.
    --}}
    <script type="module">
        import { initializeApp }   from 'https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js';
        import { getMessaging, getToken, onMessage }
                                   from 'https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging.js';

        const firebaseConfig = {
            apiKey:            "AIzaSyCSWDr79iHslRHedMjwM6h0bHyIjtQshsM",
            authDomain:        "servixa-1d1a5.firebaseapp.com",
            projectId:         "servixa-1d1a5",
            storageBucket:     "servixa-1d1a5.firebasestorage.app",
            messagingSenderId: "500565819366",
            appId:             "1:500565819366:web:749edfe8183993b9452027"
        };
        const VAPID_KEY = "BGsJX7Nrp65Dg6mHAIOxk9YI_KL-FDSkAk6zvx7sYQ6gvRbKhU2Sa4eliBSThcolY0GC3AlW_AZ7WQPWBpq9xq4";

        const app       = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        const csrf      = '{{ csrf_token() }}';
        const tokenStoreUrl = '{{ route('admin.device-tokens.store') }}';

        // Append the current tab ID to any URL so TabAwareSessionGuard can
        // resolve the right session — without it, the request appears
        // unauthenticated and the token never reaches the database.
        function _url(base) {
            const u = new URL(base, window.location.origin);
            const tab = sessionStorage.getItem('_tab') || '';
            if (tab) u.searchParams.set('_tab', tab);
            return u.toString();
        }

        async function registerToken() {
            try {
                if (Notification.permission === 'denied') {
                    console.warn('[FCM] notifications denied — push notifications will not be delivered');
                    return;
                }

                if (Notification.permission === 'default') {
                    const perm = await Notification.requestPermission();
                    if (perm !== 'granted') {
                        console.warn('[FCM] permission not granted:', perm);
                        return;
                    }
                }

                const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

                const token = await getToken(messaging, {
                    vapidKey: VAPID_KEY,
                    serviceWorkerRegistration: reg
                });

                if (!token) {
                    console.warn('[FCM] getToken returned empty — VAPID/key mismatch?');
                    return;
                }

                const res = await fetch(_url(tokenStoreUrl), {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ token, platform: 'web' })
                });

                if (!res.ok) {
                    console.warn('[FCM] token registration failed:', res.status, res.statusText);
                }
            } catch (e) {
                console.warn('[FCM] registration failed', e);
            }
        }

        // Foreground messages: show inline toast (browser doesn't auto-display when tab is visible)
        onMessage(messaging, (payload) => {
            const title = payload.notification?.title || payload.data?.title || 'Notification';
            const body  = payload.notification?.body  || payload.data?.body  || '';

            const toast = document.createElement('div');
            toast.className = 'fixed top-5 end-5 max-w-sm bg-white rounded-xl shadow-lg border border-[#DDD6FE] p-4 z-[9999]';
            toast.style.transition = 'all 0.3s ease-out';
            toast.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-9 h-9 rounded-full bg-[#EDE9FE] flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#6B21A8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-[#1F2937]">${title}</p>
                        <p class="text-xs text-[#6B7280] mt-1">${body}</p>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(20px)'; }, 5500);
            setTimeout(() => toast.remove(), 6000);

            // Refresh the bell badge if the component is mounted
            window.dispatchEvent(new CustomEvent('notifications:refresh'));
        });

        if ('serviceWorker' in navigator && 'Notification' in window) {
            window.addEventListener('load', registerToken);
        }
    </script>
    @endauth

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
