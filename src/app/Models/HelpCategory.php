<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'parent_id',
        'position',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(HelpCategory::class, 'parent_id')->orderBy('position');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(HelpArticle::class)->orderBy('position');
    }
}
