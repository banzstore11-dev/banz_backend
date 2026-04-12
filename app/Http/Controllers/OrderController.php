<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\IndexOrderRequest;
use App\Notifications\User\OrderConfirmationNotification;
use App\Notifications\User\OrderStatusUpdatedNotification;
use App\Notifications\Admin\NewOrderNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $query = Order::with(['user', 'items.product']);

        // Filter by user
        if (isset($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        // Filter by status
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Filter by payment status
        if (isset($validated['payment_status'])) {
            $query->where('payment_status', $validated['payment_status']);
        }

        // Pagination
        $perPage = $validated['per_page'] ?? 15;
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        DB::beginTransaction();
        try {
            // First pass: Validate stock availability and lock products
            $products = [];
            $items = [];
            $subtotal = 0;
            $stockErrors = [];

            foreach ($validated['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $quantity = $itemData['quantity'];
                
                // Check stock availability
                if (!$product->isInStock($quantity)) {
                    $stockErrors[] = "Product '{$product->name}' is out of stock. Available: {$product->stock_quantity}, Requested: {$quantity}";
                    continue;
                }

                // Check if product is active
                if (!$product->is_active) {
                    $stockErrors[] = "Product '{$product->name}' is not available.";
                    continue;
                }

                // Get price based on quantity (using price tiers if available)
                $unitPrice = $product->getPriceForQuantity($quantity);
                $totalPrice = $unitPrice * $quantity;
                $subtotal += $totalPrice;

                $products[] = [
                    'model' => $product,
                    'quantity' => $quantity,
                ];

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // If there are stock errors, return them
            if (!empty($stockErrors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Stock validation failed',
                    'errors' => $stockErrors,
                ], 400);
            }

            // Calculate totals
            $tax = $validated['tax'] ?? 0;
            $shippingCost = $validated['shipping_cost'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $subtotal + $tax + $shippingCost - $discount;

            // Create order
            $order = Order::create([
                'user_id' => $validated['user_id'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'currency' => 'USD',
                'payment_method' => $validated['payment_method'] ?? null,
                'payment_status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and decrease stock
            foreach ($items as $index => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Decrease stock quantity
                $product = $products[$index]['model'];
                $quantity = $products[$index]['quantity'];
                
                if (!$product->decreaseStock($quantity)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Failed to update stock for product '{$product->name}'",
                    ], 500);
                }

                // Check for low stock after decreasing
                NotificationService::checkLowStock($product);
            }

            DB::commit();

            $order->load(['user', 'items.product']);

            // Send order confirmation email to user
            $userEmail = $order->user ? $order->user->email : ($order->shipping_address['email'] ?? null);
            if ($userEmail) {
                NotificationService::notifyUser($userEmail, new OrderConfirmationNotification($order));
            }

            // Send new order notification to admin (Mail + Database)
            NotificationService::notifyAdmin(new NewOrderNotification($order));

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['user', 'items.product']);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Lookup order by order number (for public tracking)
     */
    public function lookupByOrderNumber(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['user', 'items.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $validated = $request->validated();
        $oldStatus = $order->status;
        $newStatus = $validated['status'] ?? $oldStatus;

        DB::beginTransaction();
        try {
            // Handle stock restoration if order is cancelled or refunded
            if (in_array($oldStatus, ['pending', 'processing']) && 
                in_array($newStatus, ['cancelled', 'refunded'])) {
                // Restore stock for all items
                foreach ($order->items as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $product->increaseStock($orderItem->quantity);
                    }
                }
            }

            // Update status timestamps
            if (isset($validated['status'])) {
                if ($validated['status'] === 'shipped' && !$order->shipped_at) {
                    $validated['shipped_at'] = now();
                }
                if ($validated['status'] === 'delivered' && !$order->delivered_at) {
                    $validated['delivered_at'] = now();
                }
            }

            $order->update($validated);
            DB::commit();

            $order->load(['user', 'items.product']);

            // Send status update email to user if status changed
            if (isset($validated['status']) && $oldStatus !== $newStatus) {
                $userEmail = $order->user ? $order->user->email : ($order->shipping_address['email'] ?? null);
                
                if ($userEmail) {
                    NotificationService::notifyUser($userEmail, new OrderStatusUpdatedNotification($order, $oldStatus));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order->fresh(['user', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Restore stock if order is being deleted and was not cancelled/refunded
            if (!in_array($order->status, ['cancelled', 'refunded'])) {
                foreach ($order->items as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    if ($product) {
                        $product->increaseStock($orderItem->quantity);
                    }
                }
            }

            $order->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
