<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Requests\IndexReviewRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Review::with(['user', 'product']);

        // Filter by product
        if (isset($validated['product_id'])) {
            $query->where('product_id', $validated['product_id']);
        }

        // Filter by rating
        if (isset($validated['rating'])) {
            $query->where('rating', $validated['rating']);
        }

        // Sorting
        $sortBy = $validated['sort_by'] ?? 'date';
        $sortOrder = $validated['sort_order'] ?? 'desc';

        if ($sortBy === 'rating') {
            $query->orderBy('rating', $sortOrder)->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $validated['per_page'] ?? 15;
        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Check if user already reviewed this product
        if ($user) {
            $existingReview = Review::where('product_id', $validated['product_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product. You can update your existing review.',
                ], 400);
            }

            // Check if user has purchased this product (for verified purchase badge)
            $hasPurchased = Order::where('user_id', $user->id)
                ->whereHas('items', function ($query) use ($validated) {
                    $query->where('product_id', $validated['product_id']);
                })
                ->whereIn('status', ['delivered', 'shipped', 'processing'])
                ->exists();

            $validated['user_id'] = $user->id;
            $validated['name'] = $user->name;
            $validated['email'] = $user->email;
            $validated['is_verified_purchase'] = $hasPurchased;
        }

        $review = Review::create($validated);
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review): JsonResponse
    {
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review): JsonResponse
    {
        $validated = $request->validated();
        $review->update($validated);
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review->fresh(['user', 'product']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): JsonResponse
    {
        // Check authorization
        $user = request()->user();
        if ($user && ($user->id === $review->user_id || ($user->is_admin ?? false))) {
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 403);
    }

    /**
     * Get review statistics for a product.
     */
    public function getProductStats(Product $product): JsonResponse
    {
        $baseQuery = Review::where('product_id', $product->id);

        // Get total count and average rating
        $totalReviews = $baseQuery->count();
        $averageRating = round($baseQuery->avg('rating') ?? 0, 2);

        // Get rating distribution using a single grouped query
        $distribution = Review::where('product_id', $product->id)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Initialize all ratings to 0, then fill in actual counts
        $ratingDistribution = [
            5 => $distribution[5] ?? 0,
            4 => $distribution[4] ?? 0,
            3 => $distribution[3] ?? 0,
            2 => $distribution[2] ?? 0,
            1 => $distribution[1] ?? 0,
        ];

        $stats = [
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'rating_distribution' => $ratingDistribution,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
