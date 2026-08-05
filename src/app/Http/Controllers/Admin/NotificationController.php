<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        Notification::markAllRead();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function unread()
    {
        return response()->json([
            'count'         => Notification::unreadCount(),
            'notifications' => Notification::latestUnread(8)->map(fn($n) => [
                'id'      => $n->id,
                'title'   => $n->title,
                'message' => $n->message,
                'color'   => $n->color,
                'link'    => $n->link,
                'time'    => $n->created_at->diffForHumans(),
                'read'    => $n->read,
            ]),
        ]);
    }

    public function markRead()
    {
        Notification::markAllRead();
        return response()->json(['ok' => true]);
    }
}