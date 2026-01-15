<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationController extends Controller
{
    /**
     * Server-Sent Events endpoint for real-time notifications
     */
    public function stream(Request $request): StreamedResponse
    {
        // Set no time limit for SSE connections
        set_time_limit(0);
        ignore_user_abort(false);
        
        $response = new StreamedResponse();
        
        $response->setCallback(function () use ($request) {
            // Set headers for SSE
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Credentials: true');
            header('X-Accel-Buffering: no'); // Disable Nginx buffering
            
            // Get user ID from authenticated user
            $userId = auth()->id();
            
            if (!$userId) {
                echo "data: " . json_encode(['error' => 'Unauthorized']) . "\n\n";
                flush();
                return;
            }

            // Send initial connection message
            echo "event: connected\n";
            echo "data: " . json_encode([
                'message' => 'Đã kết nối đến notification stream',
                'user_id' => $userId,
                'timestamp' => now()->toISOString()
            ]) . "\n\n";
            flush();

            $lastCheck = time();
            $startTime = time();
            $maxConnectionTime = 300; // 5 minutes max connection time (reduced from 1 hour)
            $heartbeatInterval = 20; // Heartbeat every 20 seconds
            $checkInterval = 2; // Check notifications every 2 seconds (faster)
            
            // Keep connection alive and listen for events
            while (true) {
                // Check if client disconnected
                if (connection_aborted()) {
                    break;
                }

                // Break after max connection time to prevent indefinite connections
                if ((time() - $startTime) > $maxConnectionTime) {
                    echo "event: timeout\n";
                    echo "data: " . json_encode([
                        'message' => 'Connection timeout, please refresh page',
                        'timestamp' => now()->toISOString()
                    ]) . "\n\n";
                    flush();
                    break;
                }

                $currentTime = time();
                
                // Check for new notifications every 2 seconds (faster)
                if ($currentTime - $lastCheck >= $checkInterval) {
                    // Check cache for new notifications
                    $cacheKey = "notification_for_user_{$userId}";
                    $notification = cache()->get($cacheKey);
                    
                    if ($notification) {
                        // Send the notification
                        echo "event: {$notification['event']}\n";
                        echo "data: " . json_encode($notification['data']) . "\n\n";
                        flush();
                        
                        // Clear the notification from cache
                        cache()->forget($cacheKey);
                    }
                    
                    $lastCheck = $currentTime;
                }

                // Send heartbeat every 20 seconds (more frequent)
                if ($currentTime % $heartbeatInterval == 0 && ($currentTime - $lastCheck) < $checkInterval) {
                    echo "event: heartbeat\n";
                    echo "data: " . json_encode([
                        'timestamp' => now()->toISOString(),
                        'status' => 'alive'
                    ]) . "\n\n";
                    flush();
                }

                // Sleep for 1 second before next check to reduce CPU usage
                sleep(1);
                
                // Flush output buffer periodically
                if (ob_get_level()) {
                    ob_flush();
                }
            }
        });

        return $response;
    }

    /**
     * Handle ticket assignment notification
     */
    public static function handleTicketAssigned($ticketId, $agentId, $assignerId, $ticketData = [])
    {
        // Save notification to database
        $notification = Notification::create([
            'type' => 'ticket_assigned',
            'user_id' => $agentId,
            'title' => 'Ticket mới được assign',
            'message' => 'Bạn đã được assign ticket mới: ' . ($ticketData['title'] ?? "Ticket #{$ticketId}"),
            'data' => [
                'ticket_id' => $ticketId,
                'agent_id' => $agentId,
                'assigner_id' => $assignerId,
                'ticket' => $ticketData,
                'timestamp' => now()->toISOString(),
            ]
        ]);

        // Also store in cache for immediate polling
        $cacheNotification = [
            'event' => 'ticket.assigned',
            'data' => [
                'id' => $notification->id,
                'ticket_id' => $ticketId,
                'agent_id' => $agentId,
                'assigner_id' => $assignerId,
                'message' => $notification->message,
                'ticket' => $ticketData,
                'timestamp' => $notification->created_at->toISOString(),
                'type' => 'ticket_assigned'
            ]
        ];

        // Store notification in cache for SSE to pick up (expire after 5 minutes)
        cache()->put("notification_for_user_{$agentId}", $cacheNotification, 300);
        
        return $notification;
    }

    /**
     * Get notifications for user
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::forUser($userId)->unread()->count()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $userId = auth()->id();
        
        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
            
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        Notification::forUser($userId)
            ->unread()
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }
}