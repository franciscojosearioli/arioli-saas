<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'owner_user_id',
        'name',
        'type',
        'domain_id',
        'hosting_id',
        'ssl_certificate_id',
        'cloudflare_service_id',
        'license_id',
        'production_url',
        'staging_url',
        'status',
        'priority',
        'started_at',
        'delivered_at',
        'archived_at',
        'public_name',
        'commercial_description',
        'problem_solved',
        'key_features',
        'display_order',
        'is_featured',
        'show_publicly',
    ];

    protected $casts = [
        'type'         => ProjectType::class,
        'priority'     => Priority::class,
        'started_at'   => 'date',
        'delivered_at' => 'date',
        'archived_at'  => 'date',
        'key_features' => 'array',
        'is_featured'  => 'boolean',
        'show_publicly' => 'boolean',
    ];

    public function scopePublic($query)
    {
        return $query->where('show_publicly', true)->orderBy('display_order');
    }

    public function displayName(): string
    {
        return $this->public_name ?: $this->name;
    }

    public function coverImage(): ?ProjectImage
    {
        return $this->images->first();
    }

    /**
     * Estado público derivado de fechas ya existentes (started_at/delivered_at/archived_at),
     * sin necesidad de una columna nueva.
     */
    public function publicStatusLabel(): string
    {
        if ($this->archived_at) {
            return 'Finalizado';
        }

        if ($this->delivered_at) {
            return 'Activo';
        }

        return 'En desarrollo';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * FK directa por ahora (decisión de v1) — a futuro evoluciona a un
     * ProjectAsset genérico cuando existan más tipos de activo. Ver nota en
     * el plan de la Etapa 1.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(ClientDomain::class, 'domain_id');
    }

    public function hosting(): BelongsTo
    {
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }

    public function sslCertificate(): BelongsTo
    {
        return $this->belongsTo(SslCertificate::class);
    }

    public function cloudflareService(): BelongsTo
    {
        return $this->belongsTo(CloudflareService::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(ProjectRepository::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('position');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    public function services(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(ClientJob::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class)->orderBy('position');
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
