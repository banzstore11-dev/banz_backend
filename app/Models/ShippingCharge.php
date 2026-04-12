<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'min_order_value',
        'max_order_value',
        'charge',
        'charge_type',
        'priority',
        'is_active',
        'description',
    ];

    protected $casts = [
        'min_order_value' => 'decimal:2',
        'max_order_value' => 'decimal:2',
        'charge' => 'decimal:2',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active shipping charges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by priority (highest first).
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
}
