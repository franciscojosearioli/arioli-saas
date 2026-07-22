<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class QaTopic extends Model
{
    protected $fillable = [
        'subject',
        'creator_id',
        'receiver_id',
        'sent_at',
    ];

    protected $casts = [
        'creator_id'  => 'integer',
        'receiver_id' => 'integer',
    ];

    public function messages()
    {
        return $this->hasMany(QaMessage::class, 'topic_id')
            ->orderBy('created_at', 'desc');
    }

    public function hasUnreads()
    {
        return $this->messages()->whereNull('read_at')->where('sender_id', '!=', Auth::user()->id)->exists();
    }

    public function receiverOrCreator()
    {
        return $this->creator_id === Auth::user()->id
        ? User::withTrashed()->find($this->receiver_id)
        : User::withTrashed()->find($this->creator_id);
    }

    public static function unreadCount()
    {
        $userId = Auth::id();

        return \App\Models\QaMessage::whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->whereHas('topic', function ($q) use ($userId) {
                $q->where('creator_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->count();
    }

    public static function recentForNavbar(int $limit = 5)
    {
        $userId = Auth::id();

        return self::where(function ($q) use ($userId) {
            $q->where('creator_id', $userId)->orWhere('receiver_id', $userId);
        })
        ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
        ->withCount(['messages as unread_count' => function ($q) use ($userId) {
            $q->whereNull('read_at')->where('sender_id', '!=', $userId);
        }])
        ->orderByDesc('created_at')
        ->limit($limit)
        ->get();
    }
}