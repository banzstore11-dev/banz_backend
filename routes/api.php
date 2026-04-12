<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShippingChargeController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

// Authentication Routes
Route::post('/login', [AuthController::class , 'login']);
Route::get('/user', [AuthController::class , 'user'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class , 'logout'])->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json([
    'success' => true,
    'data' => [
    'status' => 'API is working'
    ]
    ]);
});

// Public GET Routes - No authentication required for viewing
Route::get('/categories', [CategoryController::class , 'index']);
Route::get('/categories/{category}', [CategoryController::class , 'show']);
Route::get('/products', [ProductController::class , 'index']);
Route::get('/products/{product}', [ProductController::class , 'show']);
Route::get('/products/{product}/price', [ProductController::class , 'getPrice']);

// Review Routes - Public (no authentication required for viewing and submitting)
Route::get('/reviews', [ReviewController::class , 'index']);
Route::get('/reviews/{review}', [ReviewController::class , 'show']);
Route::get('/products/{product}/reviews/stats', [ReviewController::class , 'getProductStats']);
Route::post('/reviews', [ReviewController::class , 'store']); // Public - allows both authenticated and guest users

// Testimonial Routes - Public (no authentication required for viewing and submitting)
Route::get('/testimonials', [TestimonialController::class , 'index']);
Route::get('/testimonials/{testimonial}', [TestimonialController::class , 'show']);
Route::post('/testimonials', [TestimonialController::class , 'store']); // Public - allows both authenticated and guest users

// Order Routes - Public POST and GET for guest orders, protected for other operations
Route::post('/orders', [OrderController::class , 'store']); // Public - allows both authenticated and guest users
Route::get('/orders/{order}', [OrderController::class , 'show']); // Public - allows viewing order details (e.g., on success page)
Route::get('/orders/track/{orderNumber}', [OrderController::class , 'lookupByOrderNumber']); // Public - allows tracking orders by order number

// Payment Routes - Public for checkout process
Route::post('/payments/create-intent', [PaymentController::class , 'createPaymentIntent']); // Public - create payment intent for order
Route::post('/payments/confirm', [PaymentController::class , 'confirmPayment']); // Public - confirm payment
Route::post('/payments/webhook', [PaymentController::class , 'handleWebhook']); // Public - Stripe webhook endpoint (no CSRF)

// Shipping Charge Routes - Public GET for viewing, POST for calculating shipping, protected for management
Route::get('/shipping-charges', [ShippingChargeController::class , 'index']); // Public - view all shipping charges
Route::post('/shipping-charges/calculate', [ShippingChargeController::class , 'calculate']); // Public - calculate shipping cost
Route::get('/shipping-charges/{shippingCharge}', [ShippingChargeController::class , 'show']); // Public - view single shipping charge

// Protected Routes - Require Authentication for create, update, delete
Route::middleware('auth:sanctum')->group(function () {
    // Category Routes (POST, PUT, PATCH, DELETE)
    Route::post('/categories', [CategoryController::class , 'store']);
    Route::put('/categories/{category}', [CategoryController::class , 'update']);
    Route::patch('/categories/{category}', [CategoryController::class , 'update']);
    Route::delete('/categories/{category}', [CategoryController::class , 'destroy']);

    // Product Routes (POST, PUT, PATCH, DELETE)
    Route::post('/products', [ProductController::class , 'store']);
    Route::put('/products/{product}', [ProductController::class , 'update']);
    Route::patch('/products/{product}', [ProductController::class , 'update']);
    Route::delete('/products/{product}', [ProductController::class , 'destroy']);

    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class , 'index']);

    // Order Routes (GET list, PUT, PATCH, DELETE require authentication)
    Route::get('/orders', [OrderController::class , 'index']);
    Route::put('/orders/{order}', [OrderController::class , 'update']);
    Route::patch('/orders/{order}', [OrderController::class , 'update']);
    Route::delete('/orders/{order}', [OrderController::class , 'destroy']);

    // Testimonial Routes (Update and Delete require authentication)
    Route::put('/testimonials/{testimonial}', [TestimonialController::class , 'update']);
    Route::patch('/testimonials/{testimonial}', [TestimonialController::class , 'update']);
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class , 'destroy']);

    // Review Routes (Update and Delete require authentication)
    Route::put('/reviews/{review}', [ReviewController::class , 'update']);
    Route::patch('/reviews/{review}', [ReviewController::class , 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class , 'destroy']);

    // Notification Routes
    Route::get('/notifications', [NotificationController::class , 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class , 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class , 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class , 'destroy']);
    Route::delete('/notifications', [NotificationController::class , 'clearAll']);

    // Shipping Charge Routes (POST, PUT, PATCH, DELETE require authentication)
    Route::post('/shipping-charges', [ShippingChargeController::class , 'store']);
    Route::put('/shipping-charges/{shippingCharge}', [ShippingChargeController::class , 'update']);
    Route::patch('/shipping-charges/{shippingCharge}', [ShippingChargeController::class , 'update']);
    Route::delete('/shipping-charges/{shippingCharge}', [ShippingChargeController::class , 'destroy']);
});

// Cart Routes (public - can be accessed without auth for guest users)
Route::get('/cart', [CartController::class , 'index']);
Route::post('/cart', [CartController::class , 'store']);
Route::put('/cart/{cart}', [CartController::class , 'update']);
Route::delete('/cart/{cart}', [CartController::class , 'destroy']);
Route::delete('/cart', [CartController::class , 'clear']);
