<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'role',
        'content',
        'rating',
        'avatar',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Get approved testimonials.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get testimonials ordered by rating.
     */
    public function scopeOrderedByRating($query)
    {
        return $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
    }
}
