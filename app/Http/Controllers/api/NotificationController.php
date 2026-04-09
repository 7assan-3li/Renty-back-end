<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List all notifications for the user.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;

        $formatted = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'info',
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'time' => $notification->created_at->diffForHumans(),
                'read_at' => $notification->read_at,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}
