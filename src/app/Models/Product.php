<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'public_domain',
        'description',
        'icon',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function activePlans()
    {
        return $this->hasMany(Plan::class)->where('active', true);
    }

    public function appVersions()
    {
        return $this->hasMany(AppVersion::class);
    }
}