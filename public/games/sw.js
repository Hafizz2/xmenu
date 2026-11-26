/* Xmenu games: offline cache.
   Installed by the customer menu (and by each game), scope /games/.
   Everything a game needs is precached, so games open even if the café Wi-Fi drops.
   Bump VERSION whenever a game file changes so phones pick up the new copy. */
var VERSION = 'xm-games-v3';
var FILES = [
  'kit/kit.css', 'kit/kit.js',
  'tictactoe/index.html', 'connect4/index.html', 'memory/index.html', 'tapduel/index.html'
];

self.addEventListener('install', function (e) {
  e.waitUntil(caches.open(VERSION).then(function (c) {
    return c.addAll(FILES.map(function (f) { return new Request(new URL(f, self.registration.scope), { cache: 'reload' }); }));
  }).then(function () { return self.skipWaiting(); }));
});

self.addEventListener('activate', function (e) {
  e.waitUntil(caches.keys().then(function (keys) {
    return Promise.all(keys.filter(function (k) { return k.indexOf('xm-games-') === 0 && k !== VERSION; }).map(function (k) { return caches.delete(k); }));
  }).then(function () { return self.clients.claim(); }));
});

// Cache first (instant, works offline); refresh the cached copy in the background when online.
self.addEventListener('fetch', function (e) {
  // Only our own game files. Leave everything else alone: other sites, and requests
  // made by browser extensions (chrome-extension://), which the Cache API rejects.
  if (e.request.method !== 'GET') return;
  var url = new URL(e.request.url);
  if (url.origin !== self.location.origin || url.pathname.indexOf(new URL(self.registration.scope).pathname) !== 0) return;
  e.respondWith(caches.open(VERSION).then(function (cache) {
    return cache.match(e.request, { ignoreSearch: true }).then(function (hit) {
      var net = fetch(e.request).then(function (res) {
        if (res && res.ok && res.type === 'basic') cache.put(new Request(url.origin + url.pathname), res.clone()).catch(function () {});
        return res;
      }).catch(function () { return hit; });
      return hit || net;
    });
  }));
});
