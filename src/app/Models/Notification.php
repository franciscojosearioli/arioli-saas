<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'icon',
        'color',
        'read',
        'link',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
    ];

    public static function create_notification(
        string $type,
        string $title,
        string $message,
        string $color = 'blue',
        ?string $link = null,
        ?array $data = null
    ): self {
        return self::create([
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'color'   => $color,
            'link'    => $link,
            'data'    => $data,
            'read'    => false,
        ]);
    }

    public static function unreadCount(): int
    {
        return self::where('read', false)->count();
    }

    public static function latestUnread(int $limit = 10)
    {
        return self::where('read', false)
            ->latest()
            ->take($limit)
            ->get();
    }

    public static function markAllRead(): void
    {
        self::where('read', false)->update(['read' => true]);
    }
}