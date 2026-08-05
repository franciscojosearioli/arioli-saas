<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppVersion extends Model
{
    protected $fillable = [
        'product_id',
        'version',
        'type',
        'changelog',
        'released_at',
        'is_current',
        'min_php_version',
        'docker_tag',
    ];

    protected $casts = [
        'changelog'   => 'array',
        'released_at' => 'datetime',
        'is_current'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (AppVersion $version) {
            if ($version->is_current) {
                static::where('product_id', $version->product_id)
                    ->where('id', '!=', $version->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function latestFor(string $productSlug): ?static
    {
        return static::whereHas('product', fn($q) => $q->where('slug', $productSlug))
            ->where('is_current', true)
            ->first();
    }
}
