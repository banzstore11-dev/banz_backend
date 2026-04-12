<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    protected $appends = [
        'full_url',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the full URL for the image.
     * Converts relative paths like "storage/products/uuid.jpg" to full URLs.
     */
    public function getFullUrlAttribute(): string
    {
        if (!$this->image_url) {
            return '';
        }

        // If already a full URL, return as is
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        // If it starts with "storage/", use asset() helper to generate full URL
        if (str_starts_with($this->image_url, 'storage/')) {
            return asset($this->image_url);
        }

        // Otherwise, assume it's a storage path and get the URL
        // Try Storage first, fallback to asset if needed
        try {
            $url = Storage::disk('public')->url($this->image_url);
            // If Storage returns a relative URL, prepend the app URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return asset('storage/' . $this->image_url);
            }
            return $url;
        } catch (\Exception $e) {
            // Fallback to asset helper
            return asset('storage/' . $this->image_url);
        }
    }
}
