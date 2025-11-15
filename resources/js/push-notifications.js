/**
 * Push Notifications Manager
 * Handles browser push notification subscriptions for events
 */

export class PushNotificationManager {
    constructor(eventToken, subscribeUrl, unsubscribeUrl) {
        this.eventToken = eventToken;
        this.subscribeUrl = subscribeUrl;
        this.unsubscribeUrl = unsubscribeUrl;
        this.vapidPublicKey = null;
        this.subscription = null;
    }

    /**
     * Check if browser supports push notifications
     */
    isSupported() {
        return (
            'serviceWorker' in navigator &&
            'PushManager' in window &&
            'Notification' in window
        );
    }

    /**
     * Get VAPID public key from server
     */
    async getVapidPublicKey() {
        if (this.vapidPublicKey) {
            return this.vapidPublicKey;
        }

        try {
            const response = await fetch('/api/webpush/vapid-public-key');
            const data = await response.json();
            this.vapidPublicKey = data.publicKey;
            return this.vapidPublicKey;
        } catch (error) {
            console.error('Failed to get VAPID public key:', error);
            throw error;
        }
    }

    /**
     * Convert VAPID key from base64 to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Register service worker
     */
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            throw new Error('Service workers are not supported');
        }

        try {
            const registration = await navigator.serviceWorker.register('/sw.js');
            return registration;
        } catch (error) {
            console.error('Service worker registration failed:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        if (!('Notification' in window)) {
            throw new Error('Notifications are not supported');
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission === 'denied') {
            throw new Error('Notification permission was denied');
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    /**
     * Subscribe to push notifications
     */
    async subscribe() {
        if (!this.isSupported()) {
            throw new Error('Push notifications are not supported in this browser');
        }

        // Request permission first
        await this.requestPermission();

        // Register service worker
        const registration = await this.registerServiceWorker();

        // Wait for service worker to be ready
        await navigator.serviceWorker.ready;

        // Get existing subscription or create new one
        let subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            // Get VAPID public key
            const vapidPublicKey = await this.getVapidPublicKey();
            const convertedVapidKey = this.urlBase64ToUint8Array(vapidPublicKey);

            // Create new subscription
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey,
            });
        }

        this.subscription = subscription;

        // Send subscription to server
        await this.sendSubscriptionToServer(subscription);

        return subscription;
    }

    /**
     * Send subscription to server
     */
    async sendSubscriptionToServer(subscription) {
        const subscriptionData = {
            endpoint: subscription.endpoint,
            keys: {
                p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                auth: this.arrayBufferToBase64(subscription.getKey('auth')),
            },
            contentEncoding: 'aesgcm',
        };

        try {
            const response = await fetch(this.subscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(subscriptionData),
            });

            if (!response.ok) {
                throw new Error('Failed to save subscription');
            }

            return await response.json();
        } catch (error) {
            console.error('Failed to send subscription to server:', error);
            throw error;
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    async unsubscribe() {
        if (!this.subscription) {
            // Try to get existing subscription
            const registration = await navigator.serviceWorker.ready;
            this.subscription = await registration.pushManager.getSubscription();
        }

        if (!this.subscription) {
            return;
        }

        // Unsubscribe from push service
        await this.subscription.unsubscribe();

        // Remove from server
        try {
            await fetch(this.unsubscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    endpoint: this.subscription.endpoint,
                }),
            });
        } catch (error) {
            console.error('Failed to remove subscription from server:', error);
        }

        this.subscription = null;
    }

    /**
     * Check if currently subscribed
     */
    async isSubscribed() {
        if (!this.isSupported()) {
            return false;
        }

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            return subscription !== null;
        } catch (error) {
            return false;
        }
    }

    /**
     * Get current permission status
     */
    getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }

    /**
     * Convert ArrayBuffer to base64
     */
    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }
}

