<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'retail_price',
        'sku',
        'stock_quantity',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'retail_price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the primary image or first image as a single model.
     */
    public function getPrimaryImageAttribute(): ?ProductImage
    {
        $primary = $this->images()->where('is_primary', true)->first();
        return $primary ?? $this->images()->orderBy('sort_order')->first();
    }

    /**
     * Get the price tiers for the product.
     */
    public function priceTiers(): HasMany
    {
        return $this->hasMany(ProductPriceTier::class)->orderBy('min_quantity');
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->orderBy('created_at', 'desc');
    }

    /**
     * Calculate average rating for the product.
     */
    public function getAverageRating(): float
    {
        return (float) $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total review count.
     */
    public function getReviewCount(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Get the price for a given quantity.
     */
    public function getPriceForQuantity(int $quantity): float
    {
        // Check if there's a price tier for this quantity
        $tier = $this->priceTiers()
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_quantity')
                    ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('min_quantity', 'desc')
            ->first();

        return $tier ? (float) $tier->price : (float) $this->retail_price;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Get available stock quantity.
     */
    public function getAvailableStock(): int
    {
        return max(0, $this->stock_quantity);
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->stock_quantity -= $quantity;
        return $this->save();
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock_quantity += $quantity;
        return $this->save();
    }
}
