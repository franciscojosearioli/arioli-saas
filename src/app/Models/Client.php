<?php

namespace App\Models;

use App\Enums\CommercialStatus;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'cuit',
        'condicion_iva',
        'commercial_status',
        'priority',
        'slug',
        'logo_path',
        'cover_image',
        'category',
        'short_description',
        'challenge',
        'solution',
        'results',
        'testimonial_quote',
        'testimonial_author',
        'testimonial_position',
        'show_on_landing',
        'display_order',
    ];

    protected $casts = [
        'commercial_status'   => CommercialStatus::class,
        'priority'            => Priority::class,
        'show_on_landing'     => 'boolean',
    ];

    public function scopeShowcase($query)
    {
        return $query->where('show_on_landing', true)->orderBy('display_order');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(ClientDomain::class);
    }

    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }

    public function sslCertificates(): HasMany
    {
        return $this->hasMany(SslCertificate::class);
    }

    public function cloudflareServices(): HasMany
    {
        return $this->hasMany(CloudflareService::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(ClientJob::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ClientEvent::class)->latest('created_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function portalUsers(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')->latest();
    }

    public function credentials(): MorphMany
    {
        return $this->morphMany(Credential::class, 'credentialable')->latest();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
