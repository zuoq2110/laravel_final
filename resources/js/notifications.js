
class NotificationManager {
    constructor() {
        this.eventSource = null;
        this.reconnectDelay = 3000; // 3 seconds
        this.maxReconnectAttempts = 3; // Reduced from 5 to 3
        this.reconnectAttempts = 0;
        this.isConnected = false;
        this.usePolling = false;
        this.pollingInterval = null;
        this.reconnectTimer = null; // Timer for proactive reconnection
        this.pollingDelay = 5000; // 5 seconds for better responsiveness
        
        this.init();
    }

    init() {
        // Try SSE first, fallback to polling if not supported
        if (typeof(EventSource) === "undefined") {
            this.usePolling = true;
            this.startPolling();
            return;
        }

        // Start with SSE connection
        this.usePolling = false;
        this.connect();
    }

    connect() {
        if (this.usePolling) {
            this.startPolling();
            return;
        }

        if (this.eventSource) {
            this.eventSource.close();
        }

        const url = '/notifications/stream';
        this.eventSource = new EventSource(url);

        this.eventSource.onopen = () => {
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.showConnectionStatus('connected');
            
            // Auto-reconnect after 4.5 minutes to prevent server timeout
            if (this.reconnectTimer) {
                clearTimeout(this.reconnectTimer);
            }
            this.reconnectTimer = setTimeout(() => {
                if (this.isConnected) {
                    this.showNotification('Refreshing connection...', 'info');
                    this.connect(); // Reconnect proactively
                }
            }, 270000); // 4.5 minutes
        };

        this.eventSource.onerror = (error) => {
            this.isConnected = false;
            this.showConnectionStatus('error');
            
            // Close current connection
            if (this.eventSource) {
                this.eventSource.close();
            }
            
            if (this.reconnectAttempts < this.maxReconnectAttempts) {
                this.reconnectAttempts++;
                this.showNotification(`Connection error, reconnecting... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`, 'info');
                setTimeout(() => this.connect(), this.reconnectDelay);
            } else {
                this.showNotification('SSE failed, switching to polling mode', 'warning');
                this.usePolling = true;
                this.showConnectionStatus('polling');
                this.startPolling();
            }
        };

        // Handle connection event
        this.eventSource.addEventListener('connected', (event) => {
            const data = JSON.parse(event.data);
            this.showNotification('Kết nối thông báo thành công', 'success');
        });

        // Handle timeout event
        this.eventSource.addEventListener('timeout', (event) => {
            const data = JSON.parse(event.data);
            this.showNotification('Kết nối timeout, đang kết nối lại...', 'info');
            setTimeout(() => this.connect(), 2000); // Reconnect after 2 seconds
        });

        // Handle ticket assigned event
        this.eventSource.addEventListener('ticket.assigned', (event) => {
            const data = JSON.parse(event.data);
            this.handleTicketAssignedNotification(data);
        });

        // Handle heartbeat
        this.eventSource.addEventListener('heartbeat', (event) => {
            // Silent heartbeat
        });
    }

    handleTicketAssignedNotification(data) {
        // Show browser notification if permission granted
        this.showBrowserNotification(data.message, {
            body: `Ticket: ${data.ticket?.title || 'N/A'}\nPriority: ${data.ticket?.priority || 'N/A'}`,
            icon: '/favicon.ico',
            tag: 'ticket-assigned'
        });

        // Show in-page notification
        this.showNotification(data.message, 'info', {
            ticketId: data.ticket_id,
            persistent: true
        });

        // Add to notification panel
        if (typeof window.addNotificationToPanel === 'function') {
            window.addNotificationToPanel(data);
        }

        // Update notification badge if exists
        this.updateNotificationBadge();

        // Play notification sound (optional)
        this.playNotificationSound();
    }

    showBrowserNotification(title, options = {}) {
        if (Notification.permission === 'granted') {
            new Notification(title, options);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification(title, options);
                }
            });
        }
    }

    showNotification(message, type = 'info', options = {}) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        `;

        // Style the notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
            color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 10000;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
        `;

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .notification-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .notification-close {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                margin-left: 10px;
            }
        `;
        document.head.appendChild(style);

        // Add to page
        document.body.appendChild(notification);

        // Auto remove after 5 seconds unless persistent
        if (!options.persistent) {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Add click handler for ticket notifications
        if (options.ticketId) {
            notification.style.cursor = 'pointer';
            notification.onclick = () => {
                window.location.href = `/tickets/${options.ticketId}`;
            };
        }
    }

    showConnectionStatus(status) {
        const statusElement = document.getElementById('sse-status');
        if (statusElement) {
            statusElement.className = `sse-status sse-status-${status}`;
            const statusText = {
                'connected': 'Kết nối SSE',
                'error': 'Lỗi kết nối',
                'failed': 'Kết nối thất bại',
                'polling': 'Polling mode'
            };
            statusElement.textContent = statusText[status] || status;
        }
    }

    updateNotificationBadge() {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            let count = parseInt(badge.textContent || '0');
            count++;
            badge.textContent = count;
            badge.style.display = 'inline';
        }
    }

    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.3;
            audio.play().catch(e => {/* ignore audio errors */});
        } catch (e) {
            // Ignore audio errors
        }
    }

    // Polling fallback mechanism
    startPolling() {
        this.showNotification('Đã kết nối thông báo (polling mode)', 'success');
        this.showConnectionStatus('polling');
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        // Poll immediately once
        this.pollForNotifications();

        this.pollingInterval = setInterval(() => {
            this.pollForNotifications();
        }, this.pollingDelay);
    }

    async pollForNotifications() {
        try {
            const response = await fetch('/notifications/poll', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.has_notification && data.notification) {
                // Handle the notification
                if (data.notification.event === 'ticket.assigned') {
                    this.handleTicketAssignedNotification(data.notification.data);
                }
            }
        } catch (error) {
            // Silent error handling
        }
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
        
        if (this.reconnectTimer) {
            clearTimeout(this.reconnectTimer);
            this.reconnectTimer = null;
        }
        
        this.isConnected = false;
        this.usePolling = false;
    }

    // Request notification permission on first load
    static requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    NotificationManager.requestNotificationPermission();
    
    // Initialize notification manager for authenticated users
    if (document.querySelector('meta[name="user-id"]')) {
        window.notificationManager = new NotificationManager();
    }
});

// Export for use in other scripts
window.NotificationManager = NotificationManager;