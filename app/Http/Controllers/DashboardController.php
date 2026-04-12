<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and chart data.
     */
    public function index(): JsonResponse
    {
        // 1. Core Stats
        $totalRevenue = Order::sum('total');
        $totalSales = Order::count();
        $totalCustomers = User::count();
        $activeProducts = Product::where('is_active', true)->count();

        // 2. Revenue Chart Data (Last 12 Months)
        $chartData = $this->getRevenueChartData();

        // 3. Recent Sales
        $recentSales = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->user ? $order->user->name : ($order->shipping_address['name'] ?? 'Guest'),
                    'customer_email' => $order->user ? $order->user->email : ($order->shipping_address['email'] ?? 'N/A'),
                    'total' => $order->total,
                    'created_at' => $order->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_revenue' => (float)$totalRevenue,
                    'total_sales' => $totalSales,
                    'total_customers' => $totalCustomers,
                    'active_products' => $activeProducts,
                ],
                'revenue_chart' => $chartData,
                'recent_sales' => $recentSales,
            ]
        ]);
    }

    /**
     * Helper to get monthly revenue for the last 12 months.
     */
    private function getRevenueChartData(): array
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->format('M');
            
            $revenue = Order::whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->sum('total');

            $months[] = [
                'name' => $monthName,
                'total' => (float)$revenue,
            ];
        }

        return $months;
    }
}
