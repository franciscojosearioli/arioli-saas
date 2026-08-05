<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    protected $fillable = [
        'noteable_type',
        'noteable_id',
        'body',
        'is_pinned',
        'user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
