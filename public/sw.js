const CACHE_NAME = "streetpos-v18"; // Stale-while-revalidate for HTML routes, cache-first for static assets
// Only pre-cache static assets that don't require authentication
// Authenticated pages (/, /quick-sale, etc.) will be cached when visited
const urlsToCache = [
    "/offline-db.js",
    "/manifest.json",
    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
    "https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    console.log("[ServiceWorker] Installing...");
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log("[ServiceWorker] Caching app shell");
            // Cache pages one by one to avoid failure if one fails
            return Promise.all(
                urlsToCache.map((url) => {
                    return cache.add(url).catch((err) => {
                        console.log("[ServiceWorker] Failed to cache:", url, err);
                    });
                })
            );
        }).catch((err) => {
            console.error("[ServiceWorker] Installation failed:", err);
        })
    );
    self.skipWaiting();
});

// Activate Service Worker
self.addEventListener("activate", (event) => {
    console.log("[ServiceWorker] Activating...");
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log(
                            "[ServiceWorker] Removing old cache:",
                            cacheName
                        );
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch event - Network first for API, Cache first for static assets
self.addEventListener("fetch", (event) => {
    // Skip chrome extensions and browser extensions
    if (event.request.url.startsWith("chrome-extension://")) return;
    if (event.request.url.startsWith("moz-extension://")) return;
    if (event.request.url.startsWith("safari-extension://")) return;

    // Skip API sync calls (handled separately)
    if (event.request.url.includes("/api/")) return;

    // Skip non-GET requests - let them pass through to the network
    // The page-level JavaScript will handle offline scenarios
    if (event.request.method !== "GET") {
        return;
    }

    const url = new URL(event.request.url);

    // Only cache specific routes - let everything else pass through
    // Note: /login and /register are excluded to ensure fresh CSRF tokens
    const cacheableRoutes = [
        "/",
        "/products",
        "/sales",
        "/quick-sale",
        "/stock-alerts",
        "/expenses",
        "/reports/daily-sales",
    ];

    // Static assets that must be served from cache when offline
    const cacheableAssets = [
        "/offline-db.js",
        "/manifest.json",
    ];

    // Check if this is a cacheable route, asset, or CDN resource
    const isCacheableRoute = cacheableRoutes.includes(url.pathname);
    const isCacheableAsset = cacheableAssets.includes(url.pathname);
    const isCDN = url.hostname.includes("cdnjs.cloudflare.com") || url.hostname.includes("cdn.jsdelivr.net");

    // If not cacheable, let the browser handle it normally
    if (!isCacheableRoute && !isCacheableAsset && !isCDN) {
        return;
    }

    // HTML routes: stale-while-revalidate (instant load from cache, background update)
    if (isCacheableRoute) {
        // Strip _fresh param for cache key so we don't store duplicates
        const cacheUrl = new URL(event.request.url);
        cacheUrl.searchParams.delete('_fresh');
        const cacheKey = new Request(cacheUrl.toString());

        event.respondWith(
            caches.match(cacheKey).then((cachedResponse) => {
                // Always fetch in background to update cache
                const networkFetch = fetch(event.request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(cacheKey, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => null);

                // If we have a cached version, serve it immediately
                if (cachedResponse) {
                    return cachedResponse;
                }

                // No cache - must wait for network (first visit or after cache clear)
                return networkFetch.then((response) => {
                    if (response) return response;
                    return new Response(
                        `<!DOCTYPE html><html><head><title>Offline - StreetPOS</title>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <style>
                            body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;background:linear-gradient(135deg,#800000,#FFD700);color:white;text-align:center;padding:20px;box-sizing:border-box}
                            .box{background:rgba(255,255,255,0.15);padding:40px;border-radius:20px;max-width:400px;backdrop-filter:blur(10px)}
                            h1{margin-top:0}
                            p{opacity:0.9;line-height:1.6}
                            .tip{background:rgba(0,0,0,0.2);padding:15px;border-radius:10px;margin:20px 0;font-size:14px}
                            button{margin-top:10px;padding:15px 30px;background:#FFD700;color:#800000;border:none;border-radius:10px;font-weight:bold;cursor:pointer;font-size:16px}
                            button:hover{background:#fff}
                        </style></head>
                        <body><div class="box">
                            <h1>You're Offline</h1>
                            <p>This page hasn't been cached yet.</p>
                            <div class="tip">
                                <strong>Tip:</strong> Visit pages while online to enable offline access. The Quick Sale page will then work without internet.
                            </div>
                            <button onclick="location.reload()">Try Again</button>
                        </div></body></html>`,
                        { headers: { "Content-Type": "text/html" } }
                    );
                });
            })
        );
        return;
    }

    // Static assets & CDN: cache-first with background revalidation
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            }).catch(() => cachedResponse);
            return cachedResponse || fetchPromise;
        })
    );
});

// Listen for messages from the main app
self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    // Navigate to the app when notification is clicked
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // If app is already open, focus it
                for (const client of clientList) {
                    if ('focus' in client) {
                        return client.focus();
                    }
                }
                // Otherwise open a new window
                if (clients.openWindow) {
                    return clients.openWindow('/');
                }
            })
    );
});
