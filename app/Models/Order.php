<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'currency',
        'payment_method',
        'payment_status',
        'stripe_payment_intent_id',
        'shipping_address',
        'billing_address',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number) || is_null($order->order_number)) {
                do {
                    $order->order_number = 'ORD-' . strtoupper(Str::random(10));
                } while (static::where('order_number', $order->order_number)->exists());
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate total from items.
     */
    public function calculateTotal(): void
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->total = $this->subtotal + $this->tax + $this->shipping_cost - $this->discount;
    }
}
