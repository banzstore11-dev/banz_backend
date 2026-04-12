<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Http\Requests\StoreTestimonialRequest;
use App\Http\Requests\UpdateTestimonialRequest;
use App\Http\Requests\IndexTestimonialRequest;
use App\Notifications\Admin\NewTestimonialNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexTestimonialRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Testimonial::query();

        // Filter by approval status (default to approved only for public)
        if (isset($validated['is_approved'])) {
            $query->where('is_approved', $validated['is_approved']);
        } else {
            // Default to showing only approved testimonials
            $query->where('is_approved', true);
        }

        // Order by rating and date
        $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $validated['per_page'] ?? 15;
        $testimonials = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestimonialRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // New testimonials are not approved by default
        $validated['is_approved'] = false;
        
        $testimonial = Testimonial::create($validated);

        // Send notification to admin (Mail + Database)
        NotificationService::notifyAdmin(new NewTestimonialNotification($testimonial));

        return response()->json([
            'success' => true,
            'message' => 'Testimonial submitted successfully. It will be reviewed before being published.',
            'data' => $testimonial,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $testimonial,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): JsonResponse
    {
        $testimonial->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully',
            'data' => $testimonial->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully',
        ]);
    }
}
