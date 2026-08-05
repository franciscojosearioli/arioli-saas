<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'uuid',
        'plan_id',
        'tenant_id',
        'customer_name',
        'customer_email',
        'customer_company',
        'amount',
        'currency',
        'status',
        'mp_preference_id',
        'mp_payment_id',
        'mp_status',
        'mp_data',
        'paid_at',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'mp_data'  => 'array',
        'paid_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($order) {
            $order->uuid = Str::uuid();
        });
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}