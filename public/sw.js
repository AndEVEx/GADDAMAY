// AgenDAmay Service Worker - PWA Standalone & Full Web Push Notification
// Version: 2.4.0
const CACHE_NAME = 'agendamay-pwa-v8';
const OFFLINE_URL = '/offline.html';

// Pre-cache essential shell assets
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/pwa-icons/icon-192.png',
    '/pwa-icons/icon-512.png',
    '/screenshots/1280-1.png',
    '/screenshots/1280-2.png',
    '/screenshots/screenshoot-720-1.png',
    '/screenshots/screenshoot-720-2.png',
    '/manifest.json'
];

// Install: pre-cache offline shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.all(
                PRECACHE_ASSETS.map((url) =>
                    cache.add(new Request(url, { cache: 'reload' })).catch((err) => {
                        console.warn('Precache skipped:', url, err);
                    })
                )
            );
        }).then(() => self.skipWaiting())
    );
});

// Activate: clean old caches & claim clients immediately
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// Fetch: network-first for navigation, cache-first for static assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET requests from same origin
    if (request.method !== 'GET') return;
    if (!request.url.startsWith(self.location.origin)) return;

    // Navigation requests: network-first with offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(() =>
                    caches.match(request).then((cached) =>
                        cached || caches.match(OFFLINE_URL)
                    )
                )
        );
        return;
    }

    // Static assets: cache-first
    const isStaticAsset =
        request.url.includes('/pwa-icons/') ||
        request.url.includes('/screenshots/') ||
        request.url.includes('/build/assets/') ||
        request.url.includes('.woff2') ||
        request.url.includes('.woff') ||
        request.url.includes('.css') ||
        request.url.includes('.js');

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                }).catch(() => new Response('', { status: 408, statusText: 'Offline' }));
            })
        );
        return;
    }
});

// =================================================================
// WEB PUSH NOTIFICATION LISTENERS (STANDARD W3C / FCM / VAPID)
// =================================================================

// 1. Menangkap sinyal Push dari Server Laravel / Google FCM
self.addEventListener('push', function(event) {
    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'AgenDAmay SMKN 2 Indramayu',
                body: event.data.text()
            };
        }
    }

    const title = data.title || 'AgenDAmay SMKN 2 Indramayu';
    const options = {
        body: data.body || 'Pemberitahuan KBM & Agenda Sekolah.',
        icon: data.icon || '/pwa-icons/icon-192.png',
        badge: data.badge || '/pwa-icons/icon-192.png',
        vibrate: data.vibrate || [300, 150, 300, 150, 300],
        tag: data.tag || ('agendamay-' + Date.now()),
        renotify: true,
        requireInteraction: true,
        data: {
            url: data.action_url || (data.data && data.data.url) || '/guru/dashboard',
            timestamp: Date.now()
        },
        actions: [
            { action: 'open', title: 'Buka Aplikasi' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// 2. Ketika Notifikasi di HP / Desktop diklik oleh Guru
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Use root URL '/' as safe fallback - it will redirect to the correct dashboard based on role
    const notifUrl = event.notification?.data?.url || '/';
    // Always use origin-based absolute URL to avoid path issues in standalone PWA
    const targetUrl = new URL(notifUrl, self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Try to focus an existing window first
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if ('focus' in client) {
                    // If we find any open window, navigate it to the target URL
                    return client.focus().then(function(focusedClient) {
                        if (focusedClient && 'navigate' in focusedClient) {
                            return focusedClient.navigate(targetUrl);
                        }
                    });
                }
            }
            // No existing window found - open root URL (safest, ensures auth session loads)
            if (clients.openWindow) {
                return clients.openWindow('/');
            }
        })
    );
});

// 3. Menangani pembaruan token langganan otomatis oleh browser
self.addEventListener('pushsubscriptionchange', function(event) {
    event.waitUntil(
        self.registration.pushManager.subscribe(event.oldSubscription.options)
            .then(function(newSubscription) {
                return fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newSubscription)
                });
            })
    );
});
