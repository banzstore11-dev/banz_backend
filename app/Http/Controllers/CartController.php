<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Requests\IndexCartRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of cart items.
     */
    public function index(IndexCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $validated['user_id'] ?? $request->user()?->id;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $cartItems = Cart::with(['product.category', 'product.images'])
            ->where('user_id', $userId)
            ->get();

        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->getPriceForQuantity($item->quantity);
            $subtotal += $price * $item->quantity;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'item_count' => $cartItems->sum('quantity'),
            ],
        ]);
    }

    /**
     * Add item to cart.
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()?->id;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User authentication required',
            ], 401);
        }

        $product = Product::findOrFail($validated['product_id']);
        $quantity = $validated['quantity'];

        // Check if item already exists in cart
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $quantity;
            
            // Check stock again with new total
            if ($product->stock_quantity < $newQuantity) {
                $available = $product->stock_quantity - $cartItem->quantity;
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock. Only {$available} more item(s) can be added.",
                ], 400);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        $cartItem->load(['product.category', 'product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => $cartItem,
        ], 201);
    }

    /**
     * Update cart item quantity.
     */
    public function update(UpdateCartRequest $request, Cart $cart): JsonResponse
    {
        $validated = $request->validated();
        
        // Check if user owns this cart item
        if ($request->user() && $cart->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cart->quantity = $validated['quantity'];
        $cart->save();

        $cart->load(['product.category', 'product.images']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => $cart,
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Cart $cart): JsonResponse
    {
        // Check if user owns this cart item
        if (request()->user() && $cart->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
        ]);
    }

    /**
     * Clear all items from cart.
     */
    public function clear(IndexCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $validated['user_id'] ?? $request->user()?->id;

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
