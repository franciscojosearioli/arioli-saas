<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use Auditable;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'order_id',
        'license_id',
        'type',
        'cbte_tipo',
        'status',
        'number',
        'customer_name',
        'customer_cuit',
        'doc_tipo',
        'doc_nro',
        'amount',
        'currency',
        'notes',
        'pdf_path',
        'cae',
        'cae_vencimiento',
        'issued_at',
        'voided_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'cae_vencimiento' => 'date',
        'issued_at'       => 'datetime',
        'voided_at'       => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    /**
     * El vínculo vive en charges.invoice_id (Charge::invoice() BelongsTo),
     * no acá — este HasOne es solo la vuelta cómoda para leer desde Invoice.
     */
    public function charge(): HasOne
    {
        return $this->hasOne(Charge::class, 'invoice_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }

    /**
     * True cuando el número/estado vienen de una autorización real de AFIP
     * (WSFEv1 FECAESolicitar), no de la numeración interna de respaldo.
     */
    public function isAfipIssued(): bool
    {
        return ! is_null($this->cae);
    }

    public function cbteTipoLabel(): ?string
    {
        return match ($this->cbte_tipo) {
            1 => 'FACTURA A',
            6 => 'FACTURA B',
            11 => 'FACTURA C',
            default => null,
        };
    }

    /**
     * Emite la factura con numeración interna provisoria (secuencial, por
     * punto de venta configurado). Cuando se integre AFIP real (fase
     * posterior), este número pasa a venir de la respuesta de WSFE (CAE).
     */
    public function markAsIssued(): void
    {
        $puntoVenta = Setting::get('afip.punto_venta', '0001');
        $sequence   = static::where('status', '!=', 'draft')->count() + 1;

        $this->update([
            'status'    => 'issued',
            'number'    => sprintf('%s-%08d', $puntoVenta, $sequence),
            'issued_at' => now(),
        ]);
    }

    public function void(): void
    {
        $this->update(['status' => 'voided', 'voided_at' => now()]);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }
}
