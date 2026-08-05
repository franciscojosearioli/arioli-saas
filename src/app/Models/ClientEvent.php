<?php

namespace App\Models;

use App\Enums\ClientEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ClientEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'event',
        'type',
        'subject_type',
        'subject_id',
        'subject_label',
        'user_id',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'type'       => ClientEventType::class,
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $e) => $e->created_at ??= now());
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('ClientEvent es de solo lectura — es la línea de tiempo del cliente.');
    }

    public function delete(): bool
    {
        throw new RuntimeException('ClientEvent es de solo lectura — es la línea de tiempo del cliente.');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        Client $client,
        string $event,
        ClientEventType $type,
        ?Model $subject = null,
        array $metadata = [],
    ): self {
        return static::create([
            'client_id'     => $client->id,
            'event'         => $event,
            'type'          => $type,
            'subject_type'  => $subject ? get_class($subject) : null,
            'subject_id'    => $subject?->getKey(),
            'subject_label' => $subject === null ? null : (method_exists($subject, 'label') ? $subject->label() : ($subject->name ?? null)),
            'user_id'       => Auth::id(),
            'metadata'      => $metadata ?: null,
        ]);
    }
}
