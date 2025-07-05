import React, { useEffect } from 'react';

const BrowserNotifications = () => {
    useEffect(() => {
        initializeNotifications();
        setupPusherListener();
    }, []);

    const initializeNotifications = async () => {
        // Check if browser supports notifications
        if (!('serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window)) {
            console.log('Browser notifications not supported');
            return;
        }

        try {
            // Register service worker silently
            const registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registered for notifications');

            // Automatically request permission if not already set
            if (Notification.permission === 'default') {
                // Request permission automatically
                const permission = await Notification.requestPermission();
                console.log('Notification permission:', permission);
                
                if (permission === 'granted') {
                    console.log('Notification permission granted automatically');
                } else {
                    console.log('Notification permission denied');
                }
            } else if (Notification.permission === 'granted') {
                console.log('Notification permission already granted');
            }
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    };

    const setupPusherListener = () => {
        // Listen for Pusher events if available
        if (window.Echo) {
            window.Echo.channel('browser-notifications')
                .listen('.browser.notification', (e) => {
                    console.log('Received browser notification via Pusher:', e);
                    showBrowserNotification(e.notification);
                });
        } else {
            console.log('Pusher not available, using fallback method');
        }
    };



    const showBrowserNotification = (notification) => {
        if (Notification.permission !== 'granted') {
            console.error('Notification permission not granted');
            return;
        }

        try {
            const notif = new Notification(notification.title, {
                body: notification.body || 'New notification',
                icon: notification.icon || '/favicon.ico',
                badge: notification.badge || '/favicon.ico',
                tag: notification.tag || 'love248-notification',
                requireInteraction: notification.requireInteraction || false,
                data: notification.data || {},
                silent: false
            });

            notif.onclick = () => {
                if (notification.data?.url) {
                    window.open(notification.data.url, '_blank');
                }
                notif.close();
            };

            // Auto close after 8 seconds
            setTimeout(() => {
                notif.close();
            }, 8000);

        } catch (error) {
            console.error('Failed to create notification:', error);
        }
    };

    // Return null to render nothing (invisible component)
    return null;
};

export default BrowserNotifications; 