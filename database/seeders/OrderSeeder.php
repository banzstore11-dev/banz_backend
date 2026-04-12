<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $products = Product::all();

        if ($products->isEmpty() || !$user) {
            return;
        }

        // Create sample orders
        $order1 = Order::create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'subtotal' => 1299.99,
            'tax' => 104.00,
            'shipping_cost' => 15.00,
            'discount' => 0,
            'total' => 1418.99,
            'currency' => 'USD',
            'payment_method' => 'credit_card',
            'payment_status' => 'paid',
            'shipping_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'address_line_1' => '123 Main Street',
                'address_line_2' => 'Apt 4B',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
            ],
            'billing_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'address_line_1' => '123 Main Street',
                'address_line_2' => 'Apt 4B',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'USA',
            ],
            'shipped_at' => now()->subDays(5),
            'delivered_at' => now()->subDays(2),
        ]);

        $laptop = $products->where('slug', 'premium-laptop-pro-15')->first();
        if ($laptop) {
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $laptop->id,
                'product_name' => $laptop->name,
                'product_sku' => $laptop->sku,
                'quantity' => 1,
                'unit_price' => 1299.99,
                'total_price' => 1299.99,
            ]);
        }

        // Order 2
        $order2 = Order::create([
            'user_id' => $user->id,
            'status' => 'shipped',
            'subtotal' => 179.98,
            'tax' => 14.40,
            'shipping_cost' => 10.00,
            'discount' => 10.00,
            'total' => 194.38,
            'currency' => 'USD',
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'shipping_address' => [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1987654321',
                'address_line_1' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'USA',
            ],
            'billing_address' => [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+1987654321',
                'address_line_1' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'USA',
            ],
            'shipped_at' => now()->subDays(1),
        ]);

        $jeans = $products->where('slug', 'slim-fit-denim-jeans')->first();
        $tshirt = $products->where('slug', 'classic-cotton-tshirt')->first();
        
        if ($jeans) {
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $jeans->id,
                'product_name' => $jeans->name,
                'product_sku' => $jeans->sku,
                'quantity' => 1,
                'unit_price' => 79.99,
                'total_price' => 79.99,
            ]);
        }
        
        if ($tshirt) {
            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $tshirt->id,
                'product_name' => $tshirt->name,
                'product_sku' => $tshirt->sku,
                'quantity' => 4,
                'unit_price' => 24.99,
                'total_price' => 99.96,
            ]);
        }

        // Order 3 - Pending
        $order3 = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal' => 89.99,
            'tax' => 7.20,
            'shipping_cost' => 5.00,
            'discount' => 0,
            'total' => 102.19,
            'currency' => 'USD',
            'payment_method' => null,
            'payment_status' => 'pending',
            'shipping_address' => [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '+1555555555',
                'address_line_1' => '789 Pine Road',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'USA',
            ],
            'billing_address' => [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '+1555555555',
                'address_line_1' => '789 Pine Road',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'USA',
            ],
        ]);

        $coffeeMaker = $products->where('slug', 'premium-coffee-maker')->first();
        if ($coffeeMaker) {
            OrderItem::create([
                'order_id' => $order3->id,
                'product_id' => $coffeeMaker->id,
                'product_name' => $coffeeMaker->name,
                'product_sku' => $coffeeMaker->sku,
                'quantity' => 1,
                'unit_price' => 89.99,
                'total_price' => 89.99,
            ]);
        }
    }
}
