// AgenDAmay Service Worker - PWA Standalone & Push Notification
// Version: 2.2.0
const CACHE_NAME = 'agendamay-pwa-v5'; // Versi dinaikkan ke v5 agar browser HP memperbarui SW
const OFFLINE_URL = '/offline.html';

// Pre-cache essential shell assets (Hanya mendaftarkan file yang PASTI ada)
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/manifest.json'
    // Ikon & Screenshot sengaja dikeluarkan dari precache agar tidak memicu error 404.
    // Static asset tersebut akan di-cache secara otomatis saat diakses via event 'fetch' di bawah.
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
        request.url.includes('/icons/') ||
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
// WEB PUSH NOTIFICATION LISTENERS (WAJIB UNTUK NOTIFIKASI ANDROID)
// =================================================================

// 1. Menangkap sinyal Push dari Laravel Server / Google FCM
self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = { body: event.data.text() };
        }
    }

    const title = data.title || 'AgenDamay SMKN 2 Indramayu';
    const options = {
        body: data.body || 'Ada pemberitahuan KBM baru.',
        icon: data.icon || '/pwa-icons/icon-192.png',
        badge: '/pwa-icons/icon-192.png',
        vibrate: [200, 100, 200],
        data: {
            url: data.action_url || '/guru/dashboard'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// 2. Ketika Notifikasi di HP diklik oleh Guru
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const targetUrl = event.notification?.data?.url || '/guru/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
```eof

### Ringkasan Perubahan:
1. **Pembersihan `PRECACHE_ASSETS`**: Daftar jalur gambar yang 404 dikeluarkan. Peringatan `Precache skipped` di konsol akan hilang total.
2. **Versi Cache Dinaikkan**: Mengubah nama cache ke `'agendamay-pwa-v5'` memaksa browser HP memperbarui Service Worker baru.
3. **Integrasi Web Push**: Menambahkan event listener `'push'` dan `'notificationclick'` di bagian paling bawah agar notifikasi pengingat mengajar bisa mendarat di HP guru.
