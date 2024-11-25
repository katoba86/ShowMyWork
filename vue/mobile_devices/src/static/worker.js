//&https://auth0.com/blog/introduction-to-progressive-apps-part-one/


var cacheName = 'fbb-vx';

var cacheDir = "/mobile/";

var filesToCache = [
    cacheDir+'css/app.css',
    cacheDir+'js/vendor.js',
    cacheDir+'js/manifest.js',
    cacheDir+'js/app.js'
];

// Install Service Worker
self.addEventListener('install', function(event) {



    event.waitUntil(

        // Open the Cache
        caches.open(cacheName).then(function(cache) {


            // Add Files to the Cache
            return cache.addAll(filesToCache);
        })
    );
});


// Fired when the Service Worker starts up
self.addEventListener('activate', function(event) {



    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(cacheNames.map(function(key) {
                if( key !== cacheName) {

                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

