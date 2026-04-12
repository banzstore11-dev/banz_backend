<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;

class PaymentController extends Controller
{
    /**
     * Initialize Stripe API key
     */
    private function initializeStripe(): void
    {
        $secretKey = config('services.stripe.secret');
        
        if (empty($secretKey)) {
            throw new \Exception('Stripe secret key is not configured. Please set STRIPE_SECRET in your .env file.');
        }
        
        Stripe::setApiKey($secretKey);
    }

    /**
     * Create a payment intent for an order
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        try {
            $this->initializeStripe();
            $order = Order::findOrFail($request->order_id);

            // Check if order already has a payment intent
            if ($order->stripe_payment_intent_id) {
                try {
                    $existingIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);
                    
                    // If the existing intent is still valid, return it
                    if (in_array($existingIntent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'])) {
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'client_secret' => $existingIntent->client_secret,
                                'payment_intent_id' => $existingIntent->id,
                            ],
                        ]);
                    }
                } catch (\Exception $e) {
                    // If retrieval fails, create a new one
                    Log::warning('Failed to retrieve existing payment intent: ' . $e->getMessage());
                }
            }

            // Create a new payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => (int) ($order->total * 100), // Convert to cents
                'currency' => strtolower($order->currency ?? 'usd'),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
                'description' => "Order #{$order->order_number}",
            ]);

            // Store payment intent ID in order
            $order->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Payment Intent Creation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm a payment intent
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $this->initializeStripe();
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            
            $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->firstOrFail();

            if ($paymentIntent->status === 'succeeded') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed successfully',
                    'data' => [
                        'order' => $order->load(['user', 'items.product']),
                        'payment_status' => $paymentIntent->status,
                    ],
                ]);
            } elseif ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'success' => true,
                    'requires_action' => true,
                    'data' => [
                        'client_secret' => $paymentIntent->client_secret,
                        'payment_status' => $paymentIntent->status,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed. Status: ' . $paymentIntent->status,
                    'data' => [
                        'payment_status' => $paymentIntent->status,
                    ],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment Confirmation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $this->initializeStripe();
            
            if (empty($endpointSecret)) {
                Log::warning('Stripe webhook secret is not configured');
                return response()->json(['error' => 'Webhook secret not configured'], 500);
            }
            
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailure($paymentIntent);
                break;

            case 'payment_intent.canceled':
                $paymentIntent = $event->data->object;
                $this->handlePaymentCanceled($paymentIntent);
                break;

            default:
                Log::info('Unhandled webhook event type: ' . $event->type);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess($paymentIntent): void
    {
        try {
            $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                Log::info("Payment succeeded for order #{$order->order_number}");
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment success: ' . $e->getMessage());
        }
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailure($paymentIntent): void
    {
        try {
            $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                Log::warning("Payment failed for order #{$order->order_number}");
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment failure: ' . $e->getMessage());
        }
    }

    /**
     * Handle canceled payment
     */
    private function handlePaymentCanceled($paymentIntent): void
    {
        try {
            $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            
            if ($order && $order->status === 'pending') {
                // Restore stock if order was canceled
                foreach ($order->items as $orderItem) {
                    $product = \App\Models\Product::find($orderItem->product_id);
                    if ($product) {
                        $product->increaseStock($orderItem->quantity);
                    }
                }

                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);

                Log::info("Payment canceled for order #{$order->order_number}");
            }
        } catch (\Exception $e) {
            Log::error('Error handling payment cancellation: ' . $e->getMessage());
        }
    }
}
