<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use Auditable;

    protected $fillable = ['key', 'group', 'value', 'is_encrypted', 'meta'];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'meta'         => 'array',
    ];

    protected const CACHE_KEY = 'settings.all';
    protected const CACHE_TTL = 86400;

    public function setValueAttribute(?string $value): void
    {
        $this->attributes['value'] = ($this->is_encrypted && $value !== null)
            ? Crypt::encryptString($value)
            : $value;
    }

    public function getValueAttribute(?string $value): ?string
    {
        if (! $this->is_encrypted || $value === null) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Todas las settings, cacheadas. Se invalida en cada set().
     */
    protected static function allCached(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => static::all()->keyBy('key'));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allCached()->get($key)?->value ?? $default;
    }

    public static function set(string $key, ?string $value, string $group, bool $encrypted = false, ?array $meta = null): self
    {
        // 'is_encrypted' debe llenarse antes que 'value': el mutator de 'value' lo lee para
        // decidir si encripta, y Eloquent respeta el orden del array al hacer fill().
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['group' => $group, 'is_encrypted' => $encrypted, 'value' => $value, 'meta' => $meta]
        );

        Cache::forget(self::CACHE_KEY);

        return $setting;
    }

    public static function group(string $group): Collection
    {
        return static::allCached()->filter(fn (self $s) => $s->group === $group)->values();
    }
}
