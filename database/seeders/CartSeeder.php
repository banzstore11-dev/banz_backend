<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
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

        // Add some products to user's cart
        $laptop = $products->where('slug', 'premium-laptop-pro-15')->first();
        if ($laptop && $laptop->stock_quantity >= 1) {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $laptop->id,
                'quantity' => 1,
            ]);
        }

        $tshirt = $products->where('slug', 'classic-cotton-tshirt')->first();
        if ($tshirt && $tshirt->stock_quantity >= 3) {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $tshirt->id,
                'quantity' => 3,
            ]);
        }

        $coffeeMaker = $products->where('slug', 'premium-coffee-maker')->first();
        if ($coffeeMaker && $coffeeMaker->stock_quantity >= 1) {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $coffeeMaker->id,
                'quantity' => 1,
            ]);
        }
    }
}
