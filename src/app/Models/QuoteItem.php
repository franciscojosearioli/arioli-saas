<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\Currency;
use App\Enums\QuoteItemType;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'description',
        'item_type',
        'service_type',
        'billing_cycle',
        'quantity',
        'unit_label',
        'unit_price',
        'currency',
    ];

    protected $casts = [
        'item_type'     => QuoteItemType::class,
        'service_type'  => ServiceType::class,
        'billing_cycle' => BillingCycle::class,
        'currency'      => Currency::class,
        'quantity'      => 'decimal:2',
        'unit_price'    => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->quantity * (float) $this->unit_price,
        );
    }
}
