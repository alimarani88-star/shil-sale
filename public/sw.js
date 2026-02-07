const CACHE_NAME = 'my-laravel-pwa-v1';
const urlsToCache = [
    '/',
    '/offline',
    '/assets/css/main.css',
    '/assets/js/main.js',
    '/manifest.json',
    // می‌تونی بقیه فایل‌های استاتیک رو هم اضافه کنی
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys
          .filter(key => key !== CACHE_NAME)
          .map(key => caches.delete(key))
      )
    )
  );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
            .catch(() => caches.match('/offline'))
    );
});
