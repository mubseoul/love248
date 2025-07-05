// Service Worker for AWS SNS Browser Push Notifications
const CACHE_NAME = 'love248-v1.0.0';
const STATIC_CACHE = 'love248-static-v1.0.0';
const DYNAMIC_CACHE = 'love248-dynamic-v1.0.0';

// Assets to cache immediately
const STATIC_ASSETS = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/images/default-profile-pic.png',
  '/images/default-cover-pic.png',
  '/images/loader.gif',
  '/sounds/coins.mp3',
  '/fonts/fontawesome/css/all.min.css',
  '/offline.html'
];

// Network-first resources (always try network first)
const NETWORK_FIRST = [
  '/api/',
  '/broadcasting/',
  '/sanctum/',
  '/login',
  '/register',
  '/dashboard'
];

// Cache-first resources (static assets)
const CACHE_FIRST = [
  '/css/',
  '/js/',
  '/fonts/',
  '/images/icons/',
  '/images/avatars/',
  'https://fonts.googleapis.com/',
  'https://fonts.gstatic.com/'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then((cache) => {
        console.log('Service Worker: Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('Service Worker: Installed successfully');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('Service Worker: Installation failed', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
              console.log('Service Worker: Deleting old cache', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('Service Worker: Activated successfully');
        return self.clients.claim();
      })
  );
});

// Fetch event - handle different caching strategies
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Skip chrome-extension and other non-http requests
  if (!request.url.startsWith('http')) {
    return;
  }

  // Handle different types of requests with appropriate strategies
  if (isNetworkFirst(request.url)) {
    event.respondWith(networkFirst(request));
  } else if (isCacheFirst(request.url)) {
    event.respondWith(cacheFirst(request));
  } else if (isStreamingContent(request.url)) {
    event.respondWith(networkOnly(request));
  } else {
    event.respondWith(staleWhileRevalidate(request));
  }
});

// Network-first strategy (for API calls, auth, etc.)
async function networkFirst(request) {
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      const cache = await caches.open(DYNAMIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('Network failed, trying cache:', error);
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // Return offline page for navigation requests
    if (request.mode === 'navigate') {
      return caches.match('/offline.html');
    }
    
    throw error;
  }
}

// Cache-first strategy (for static assets)
async function cacheFirst(request) {
  const cachedResponse = await caches.match(request);
  
  if (cachedResponse) {
    return cachedResponse;
  }
  
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      const cache = await caches.open(STATIC_CACHE);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('Cache and network failed:', error);
    throw error;
  }
}

// Network-only strategy (for streaming content)
async function networkOnly(request) {
  return fetch(request);
}

// Stale-while-revalidate strategy (for general content)
async function staleWhileRevalidate(request) {
  const cache = await caches.open(DYNAMIC_CACHE);
  const cachedResponse = await cache.match(request);
  
  const fetchPromise = fetch(request).then((networkResponse) => {
    if (networkResponse.ok) {
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  }).catch(() => {
    // Network failed, return cached version if available
    return cachedResponse;
  });
  
  return cachedResponse || fetchPromise;
}

// Helper functions to determine caching strategy
function isNetworkFirst(url) {
  return NETWORK_FIRST.some(pattern => url.includes(pattern));
}

function isCacheFirst(url) {
  return CACHE_FIRST.some(pattern => url.includes(pattern));
}

function isStreamingContent(url) {
  return url.includes('/stream/') || 
         url.includes('/hls/') || 
         url.includes('.m3u8') || 
         url.includes('/live/') ||
         url.includes('pusher') ||
         url.includes('broadcasting');
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('Service Worker: Background sync triggered', event.tag);
  
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  // Handle offline actions when connection is restored
  console.log('Service Worker: Performing background sync');
  
  // You can implement offline form submissions, 
  // chat message queuing, etc. here
}

// Push notification handling
self.addEventListener('push', (event) => {
  console.log('Service Worker: Push notification received', event);
  
  const options = {
    body: event.data ? event.data.text() : 'New notification from Love248',
    icon: '/images/icons/icon-192x192.png',
    badge: '/images/icons/icon-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Open App',
        icon: '/images/icons/icon-192x192.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/images/icons/icon-192x192.png'
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification('Love248', options)
  );
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
  console.log('Service Worker: Notification clicked', event);
  
  event.notification.close();
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/')
    );
  } else if (event.action === 'close') {
    // Just close the notification
    return;
  } else {
    // Default action - open the app
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Message handling from main thread
self.addEventListener('message', (event) => {
  console.log('Service Worker: Message received', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CACHE_URLS') {
    event.waitUntil(
      caches.open(DYNAMIC_CACHE)
        .then(cache => cache.addAll(event.data.payload))
    );
  }
});

console.log('Service Worker: Script loaded successfully'); 