// AgenDAmay Service Worker - PWA Standalone Support
// Version: 2.0.0
const CACHE_NAME = 'agendamay-pwa-v3';
const OFFLINE_URL = '/offline.html';

// Pre-cache essential shell assets
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/logo-sekolah.png',
    '/manifest.json',
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

    // Static assets: cache-first (icons, fonts, compiled CSS/JS)
    const isStaticAsset =
        request.url.includes('/icons/') ||
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
