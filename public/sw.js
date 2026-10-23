/* Church Management System - Service Worker for Background Push Notifications */

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function(event) {
    let payload = {
        title: 'Church Announcement',
        body: 'You have a new message from church.',
        url: '/member-portal',
        icon: '/favicon.ico',
        tag: 'church-notification-' + Date.now()
    };

    if (event.data) {
        try {
            const json = event.data.json();
            payload = Object.assign(payload, json);
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const notificationOptions = {
        body: payload.body,
        icon: payload.icon || '/favicon.ico',
        badge: '/favicon.ico',
        tag: payload.tag,
        renotify: true,
        vibrate: [200, 100, 200],
        data: {
            url: payload.url || '/member-portal'
        },
        actions: [
            { action: 'view', title: 'View Details' },
            { action: 'close', title: 'Close' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, notificationOptions)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const targetUrl = event.notification.data?.url || '/member-portal';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
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
