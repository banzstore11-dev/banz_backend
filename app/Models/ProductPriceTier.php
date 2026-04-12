<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'min_quantity',
        'max_quantity',
        'price',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the price tier.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
