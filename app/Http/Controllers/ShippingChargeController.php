<?php

namespace App\Http\Controllers;

use App\Models\ShippingCharge;
use App\Http\Requests\StoreShippingChargeRequest;
use App\Http\Requests\UpdateShippingChargeRequest;
use Illuminate\Http\JsonResponse;

class ShippingChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $shippingCharges = ShippingCharge::orderedByPriority()->get();

        return response()->json([
            'success' => true,
            'data' => $shippingCharges,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShippingChargeRequest $request): JsonResponse
    {
        $shippingCharge = ShippingCharge::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Shipping charge created successfully',
            'data' => $shippingCharge,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShippingCharge $shippingCharge): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $shippingCharge,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShippingChargeRequest $request, ShippingCharge $shippingCharge): JsonResponse
    {
        $shippingCharge->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Shipping charge updated successfully',
            'data' => $shippingCharge->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingCharge $shippingCharge): JsonResponse
    {
        $shippingCharge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping charge deleted successfully',
        ]);
    }

    /**
     * Calculate shipping cost based on country and order value.
     */
    public function calculate(): JsonResponse
    {
        $request = request();
        $country = $request->input('country');
        $orderValue = (float) $request->input('order_value', 0);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country is required',
            ], 400);
        }

        $shippingCost = $this->calculateShippingCost($country, $orderValue);

        return response()->json([
            'success' => true,
            'data' => [
                'shipping_cost' => $shippingCost,
                'country' => $country,
                'order_value' => $orderValue,
            ],
        ]);
    }

    /**
     * Calculate shipping cost based on country and order value.
     */
    private function calculateShippingCost(string $country, float $orderValue): float
    {
        // Get active shipping charges ordered by priority
        $shippingCharges = ShippingCharge::active()
            ->orderedByPriority()
            ->get();

        foreach ($shippingCharges as $charge) {
            // Check if country matches (null means all countries)
            if ($charge->country !== null && $charge->country !== $country) {
                continue;
            }

            // Check if order value is within range
            if ($charge->min_order_value !== null && $orderValue < $charge->min_order_value) {
                continue;
            }

            if ($charge->max_order_value !== null && $orderValue > $charge->max_order_value) {
                continue;
            }

            // Calculate charge
            if ($charge->charge_type === 'percentage') {
                return round($orderValue * ($charge->charge / 100), 2);
            } else {
                return $charge->charge;
            }
        }

        // Default: no shipping charge if no rule matches
        return 0;
    }
}
