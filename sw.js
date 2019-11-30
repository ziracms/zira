self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) return;

    const sendNotification = msg => {
        const title = msg.title;
        const options = {
            body: msg.body,
            icon: msg.icon,
            data: {
                url: msg.url
            }
        };
        
        return self.registration.showNotification(title, options);
    };
    
    if (event.data) {
        const message = event.data.json();
        event.waitUntil(sendNotification(message));
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});