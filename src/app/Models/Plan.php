<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'period',
        'period_months',
        'is_perpetual',
        'is_demo',
        'price',
        'base_price',
        'discount_percent',
        'active',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'base_price'       => 'decimal:2',
        'discount_percent' => 'integer',
        'period_months'    => 'integer',
        'is_perpetual'     => 'boolean',
        'is_demo'          => 'boolean',
        'active'           => 'boolean',
    ];

    // Precio final con descuento aplicado — una licencia indefinida no tiene
    // período que multiplicar, el monto único es el precio final tal cual.
    public function getFinalPriceAttribute(): float
    {
        if ($this->is_perpetual || $this->period_months <= 0) {
            return (float) $this->base_price;
        }

        return $this->base_price * $this->period_months * (1 - $this->discount_percent / 100);
    }

    // Etiqueta del período
    public function getPeriodLabelAttribute(): string
    {
        return match($this->period) {
            'monthly'    => 'Mensual',
            'quarterly'  => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual'     => 'Anual',
            'perpetual'  => 'Indefinida',
            default      => $this->period,
        };
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    /**
     * Planes reales, vendibles a un cliente nuevo. Excluye los planes "Demo"
     * (is_demo=true) reservados exclusivamente para el tenant técnico "demo"
     * de cada producto, y las licencias indefinidas (nunca comprables por el
     * público) — esos NUNCA deben poder asignarse a un cliente real, ni
     * siquiera si alguien arma el POST a mano.
     *
     * is_demo es un flag explícito, no una heurística de precio: una licencia
     * indefinida también puede tener price=0 (su costo se define y cobra
     * aparte), así que "price > 0" ya no alcanza para distinguir un Demo.
     */
    public function scopeSellable($query)
    {
        return $query->where('active', true)
            ->whereNotNull('product_id')
            ->where('is_demo', false)
            ->where('is_perpetual', false)
            ->where('period_months', '>', 0);
    }

    /**
     * Planes que un admin puede asignar al dar de alta o editar una licencia
     * — a diferencia de sellable(), acá SÍ entran los planes indefinidos
     * (period_months=0, is_perpetual=true, price frecuentemente 0). Nunca
     * los Demo: siguen excluidos por is_demo=true. Nunca comprables por el
     * público — eso sigue siendo sellable() en CheckoutController/renovación
     * del cliente.
     */
    public function scopeAssignableByAdmin($query)
    {
        return $query->where('active', true)
            ->whereNotNull('product_id')
            ->where('is_demo', false);
    }
}