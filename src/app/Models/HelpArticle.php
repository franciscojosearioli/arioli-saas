<?php

namespace App\Models;

use App\Enums\HelpArticleContentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpArticle extends Model
{
    protected $fillable = [
        'help_category_id',
        'title',
        'slug',
        'content',
        'content_type',
        'video_url',
        'external_url',
        'position',
        'published',
    ];

    protected $casts = [
        'content_type' => HelpArticleContentType::class,
        'published'    => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
