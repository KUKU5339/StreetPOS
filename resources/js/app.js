if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/service-worker.js')
            .then(reg => console.log('ServiceWorker registered', reg))
            .catch(err => console.log('ServiceWorker registration failed', err));
    });
}
