/* Manami Education — Service Worker (PWA)
   Caches static assets for offline viewing and faster repeat loads. */

const CACHE_NAME = 'manami-v1';
const STATIC_ASSETS = [
  './',
  './index.php',
  './about.php',
  './services.php',
  './courses.php',
  './books.php',
  './blog.php',
  './contact.php',
  './faq.php',
  './destinations.php',
  './events.php',
  './privacy.php',
  './terms.php',
  './login.php',
  './thanks.php',
  './assets/css/style.css',
  './assets/css/flags.css',
  './assets/js/main.js',
  'https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js',
  'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap',
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
      .catch(() => { /* Some assets may fail (e.g., CDN), that's OK */ })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (e) => {
  /* Only cache GET requests for same-origin or known CDNs */
  if (e.request.method !== 'GET') return;

  const url = new URL(e.request.url);
  const isOwn = url.origin === self.location.origin;
  const isCDN = url.hostname === 'unpkg.com' || url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com';

  if (!isOwn && !isCDN) return;

  e.respondWith(
    caches.match(e.request).then((cached) => {
      if (cached) return cached;

      return fetch(e.request).then((response) => {
        if (!response || response.status !== 200) return response;
        const clone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(e.request, clone));
        return response;
      });
    }).catch(() => {
      /* Offline fallback — return nothing for API calls, cached page for navigation */
      if (e.request.destination === 'document') {
        return caches.match('./index.php');
      }
    })
  );
});
