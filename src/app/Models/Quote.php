<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Quote extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'summary',
        'introduction',
        'project_scope',
        'benefits',
        'exclusions',
        'warranty',
        'payment_terms',
        'timeline_estimate',
        'observations',
        'final_message',
        'status',
        'valid_until',
        'creates_project',
        'project_name',
        'project_type',
        'initial_charge_amount',
        'public_token',
        'public_token_expires_at',
        'sent_at',
        'decided_at',
        'project_id',
        'contract_id',
    ];

    protected $casts = [
        'status'                  => QuoteStatus::class,
        'valid_until'             => 'date',
        'creates_project'         => 'boolean',
        'initial_charge_amount'   => 'decimal:2',
        'public_token_expires_at' => 'datetime',
        'sent_at'                 => 'datetime',
        'decided_at'              => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function isTokenValid(): bool
    {
        return $this->public_token !== null
            && $this->public_token_expires_at !== null
            && $this->public_token_expires_at->isFuture();
    }

    /**
     * Totales agrupados por moneda — cada ítem puede estar en una moneda distinta
     * (ej. desarrollo en USD, costos anuales en ARS), así que no se suman entre sí.
     *
     * @return array<string, float>
     */
    public function totalsByCurrency(): array
    {
        return $this->items
            ->groupBy(fn (QuoteItem $item) => $item->currency->value)
            ->map(fn ($items) => (float) $items->sum(fn (QuoteItem $item) => $item->subtotal))
            ->toArray();
    }

    /**
     * Resumen económico agrupado por cadencia de pago (Pago único / Costos mensuales /
     * Costos anuales) — es lo que se muestra en el PDF y la página pública en vez de una
     * tabla plana de ítems, distinguiendo claramente inversión única de costos recurrentes.
     *
     * @return array<int, array{label: string, items: \Illuminate\Support\Collection, totals: array<string, float>}>
     */
    public function economicSummary(): array
    {
        $labels = [
            'unico'   => ['section' => 'Pago único', 'total' => 'Total pago único'],
            'mensual' => ['section' => 'Costos mensuales', 'total' => 'Total mensual'],
            'anual'   => ['section' => 'Costos anuales', 'total' => 'Total anual'],
        ];
        $order = array_keys($labels);

        return $this->items
            ->groupBy(fn (QuoteItem $item) => $item->billing_cycle?->value ?? 'unico')
            ->sortBy(fn ($items, $cycle) => array_search($cycle, $order))
            ->map(fn ($items, $cycle) => [
                'label'       => $labels[$cycle]['section'] ?? ucfirst($cycle),
                'total_label' => $labels[$cycle]['total'] ?? 'Total',
                'items'       => $items,
                'totals'      => $items->groupBy(fn (QuoteItem $i) => $i->currency->value)
                    ->map(fn ($group) => (float) $group->sum(fn (QuoteItem $i) => $i->subtotal))
                    ->toArray(),
            ])
            ->values()
            ->all();
    }
}
