{{-- Notification Component for SSE --}}
<div id="notification-container" class="fixed top-4 right-4 z-50">
    {{-- Notifications will be dynamically added here --}}
</div>

{{-- Connection Status Indicator --}}
<div id="sse-status-container" class="fixed bottom-4 right-4 z-40" style="display: none;">
    <div id="sse-status" class="px-3 py-1 rounded-full text-sm font-medium">
        Đang kết nối...
    </div>
</div>

<style>
.sse-status-connected {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.sse-status-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.sse-status-failed {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.sse-status-polling {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.notification {
    margin-bottom: 8px;
    min-width: 300px;
    max-width: 400px;
    word-wrap: break-word;
}

.notification-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.notification-message {
    flex: 1;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.7;
    flex-shrink: 0;
}

.notification-close:hover {
    opacity: 1;
}

.notification-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
}

.notification-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.notification-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border: 1px solid #bee5eb;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}

.notification.fade-out {
    animation: fadeOut 0.3s ease-out forwards;
}
</style>

{{-- Add meta tags for JavaScript --}}
@if(auth()->check())
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endif