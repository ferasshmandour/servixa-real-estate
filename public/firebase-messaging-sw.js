// Firebase Cloud Messaging — Background Service Worker
// ─────────────────────────────────────────────────────
// Receives FCM messages when the dashboard tab is in the background or closed,
// and shows them as native browser notifications. Foreground messages are
// handled by the inline onMessage handler in layouts/app.blade.php.

importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey:            "AIzaSyCSWDr79iHslRHedMjwM6h0bHyIjtQshsM",
    authDomain:        "servixa-1d1a5.firebaseapp.com",
    projectId:         "servixa-1d1a5",
    storageBucket:     "servixa-1d1a5.firebasestorage.app",
    messagingSenderId: "500565819366",
    appId:             "1:500565819366:web:749edfe8183993b9452027"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const title = (payload.notification && payload.notification.title)
                 || (payload.data && payload.data.title)
                 || 'Servixa';
    const options = {
        body:  (payload.notification && payload.notification.body)
              || (payload.data && payload.data.body) || '',
        icon:  '/favicon.ico',
        badge: '/favicon.ico',
        data:  payload.data || {}
    };
    self.registration.showNotification(title, options);
});

// Click → navigate existing dashboard tab to the deeplink (preserving _tab session).
// We post a message to the existing client so it navigates with its own _tab value,
// avoiding a session mismatch that would redirect to the login page.
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const deeplink = (event.notification.data && event.notification.data.deeplink) || '/admin/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((wins) => {
            // Find an open dashboard tab and tell it to navigate.
            for (const win of wins) {
                if (win.url && win.url.includes('/admin')) {
                    win.postMessage({ type: 'FCM_NAVIGATE', deeplink });
                    return win.focus();
                }
            }
            // No open tab — open a new one. The tab-aware script will attach _tab
            // and then the server will create a new session for it (login required).
            // This is unavoidable when the browser is closed.
            if (clients.openWindow) return clients.openWindow(deeplink);
        })
    );
});
