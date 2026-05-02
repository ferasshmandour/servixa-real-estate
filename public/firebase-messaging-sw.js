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

// Click → focus the dashboard tab (or open it) at the deeplink, if provided.
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = (event.notification.data && event.notification.data.deeplink) || '/admin/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((wins) => {
            for (const win of wins) {
                if ('focus' in win) {
                    win.navigate(target);
                    return win.focus();
                }
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
