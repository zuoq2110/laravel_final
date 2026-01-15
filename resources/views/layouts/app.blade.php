<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Notification Component -->
            @if(auth()->check())
                @include('components.notifications')

                <script>
                // User dropdown toggle function
                function toggleUserDropdown() {
                    const dropdown = document.getElementById('user-dropdown');
                    dropdown.classList.toggle('hidden');
                }
                
                // Notification panel toggle function
                function toggleNotificationPanel() {
                    const panel = document.getElementById('notification-panel');
                    panel.classList.toggle('hidden');
                    
                    // Load notifications when panel is opened
                    if (!panel.classList.contains('hidden')) {
                        loadNotifications();
                    }
                }
                
                // Load notifications from database
                async function loadNotifications() {
                    try {
                        const response = await fetch('/notifications', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            updateNotificationPanel(data.notifications);
                            updateNotificationBadge(data.unread_count);
                        }
                    } catch (error) {
                        // Silent error handling
                    }
                }
                
                // Update notification panel with data
                function updateNotificationPanel(notifications) {
                    const list = document.getElementById('notification-list');
                    
                    if (notifications.length === 0) {
                        list.innerHTML = '<div class="p-4 text-gray-500 text-center">Không có thông báo mới</div>';
                        return;
                    }
                    
                    let html = '';
                    notifications.forEach(notification => {
                        const readClass = notification.is_read ? 'bg-gray-50' : 'bg-blue-50';
                        const ticketUrl = notification.data?.ticket_id ? `/tickets/${notification.data.ticket_id}` : '#';
                        html += `
                            <div class="p-4 border-b ${readClass} hover:bg-gray-100 cursor-pointer" onclick="handleNotificationClick(${notification.id}, '${ticketUrl}')">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">${notification.title}</h4>
                                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                        <span class="text-xs text-gray-400 mt-2 block">${notification.created_at}</span>
                                    </div>
                                    ${!notification.is_read ? '<div class="w-2 h-2 bg-blue-500 rounded-full mt-1 ml-2"></div>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    
                    list.innerHTML = html;
                }
                
                // Update notification badge
                function updateNotificationBadge(count) {
                    const badge = document.getElementById('notification-badge');
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count;
                            badge.style.display = 'flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
                
                // Handle notification click
                async function handleNotificationClick(notificationId, url) {
                    await markAsRead(notificationId);
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                }
                
                // Mark notification as read
                async function markAsRead(notificationId) {
                    try {
                        const response = await fetch(`/notifications/${notificationId}/read`, {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        
                        if (response.ok) {
                            loadNotifications(); // Reload notifications
                        }
                    } catch (error) {
                        // Silent error handling
                    }
                }
                
                // Clear all notifications
                async function clearAllNotifications() {
                    try {
                        const response = await fetch('/notifications/read-all', {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        
                        if (response.ok) {
                            loadNotifications(); // Reload notifications
                        }
                    } catch (error) {
                        // Silent error handling
                    }
                }
                
                // Add new notification to panel (for real-time updates)
                function addNotificationToPanel(notificationData) {
                    // Reload all notifications to get fresh data
                    loadNotifications();
                }
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', function(event) {
                    const userDropdown = document.getElementById('user-dropdown');
                    const notificationPanel = document.getElementById('notification-panel');
                    
                    if (userDropdown && !userDropdown.contains(event.target) && !event.target.closest('[onclick*="toggleUserDropdown"]')) {
                        userDropdown.classList.add('hidden');
                    }
                    
                    if (notificationPanel && !notificationPanel.contains(event.target) && !event.target.closest('[onclick*="toggleNotificationPanel"]')) {
                        notificationPanel.classList.add('hidden');
                    }
                });
                
                // Load initial notification count
                document.addEventListener('DOMContentLoaded', function() {
                    loadNotifications();
                });
                
                // Make functions global
                window.toggleUserDropdown = toggleUserDropdown;
                window.toggleNotificationPanel = toggleNotificationPanel;
                window.loadNotifications = loadNotifications;
                window.markAsRead = markAsRead;
                window.clearAllNotifications = clearAllNotifications;
                window.addNotificationToPanel = addNotificationToPanel;
                window.handleNotificationClick = handleNotificationClick;
                </script>
            @endif
        </div>
    </body>
</html>
