import './bootstrap';
import Alpine from 'alpinejs';
import confetti from 'canvas-confetti';
import { PushNotificationManager } from './push-notifications';

window.Alpine = Alpine;
window.confetti = confetti;
window.PushNotificationManager = PushNotificationManager;

window.pushNotificationComponent = function(config) {
    return {
        manager: null,
        isSupported: false,
        isSubscribed: false,
        isLoading: false,
        isSendingTest: false,
        permissionStatus: 'default',
        config: config,
        async init() {
            if (typeof PushNotificationManager === 'undefined') {
                console.error('PushNotificationManager is not available');
                return;
            }

            this.manager = new PushNotificationManager(
                this.config.eventToken,
                this.config.subscribeUrl,
                this.config.unsubscribeUrl
            );

            this.isSupported = this.manager.isSupported();
            this.permissionStatus = this.manager.getPermissionStatus();

            if (this.isSupported) {
                this.isSubscribed = await this.manager.isSubscribed();
            }
        },
        async subscribe() {
            if (!this.manager || this.isLoading) {
                return;
            }

            this.isLoading = true;

            try {
                await this.manager.subscribe();
                this.isSubscribed = true;
                this.permissionStatus = this.manager.getPermissionStatus();
            } catch (error) {
                console.error('Failed to subscribe:', error);
                alert('Failed to enable notifications: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },
        async unsubscribe() {
            if (!this.manager || this.isLoading) {
                return;
            }

            this.isLoading = true;

            try {
                await this.manager.unsubscribe();
                this.isSubscribed = false;
            } catch (error) {
                console.error('Failed to unsubscribe:', error);
                alert('Failed to disable notifications: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        },
        async sendTestNotification() {
            if (this.isSendingTest) {
                return;
            }

            this.isSendingTest = true;

            try {
                const response = await fetch(this.config.testUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to send test notification');
                }

                const data = await response.json();
                // The actual push notification will appear as a browser/system notification
                // This alert just confirms the request was sent successfully
                alert('Test notification sent! You should see a browser notification appear shortly (check your system notifications if the browser tab is not active).');
            } catch (error) {
                console.error('Failed to send test notification:', error);
                alert('Failed to send test notification: ' + error.message);
            } finally {
                this.isSendingTest = false;
            }
        },
    };
};

Alpine.start();
