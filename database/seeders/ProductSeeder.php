<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPriceTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronicsCategory = Category::where('slug', 'electronics')->first();
        $clothingCategory = Category::where('slug', 'clothing')->first();
        $homeKitchenCategory = Category::where('slug', 'home-kitchen')->first();
        $sportsCategory = Category::where('slug', 'sports-outdoors')->first();

        // Electronics Products
        if ($electronicsCategory) {
            $laptop = Product::create([
                'category_id' => $electronicsCategory->id,
                'name' => 'Premium Laptop Pro 15"',
                'slug' => 'premium-laptop-pro-15',
                'description' => 'High-performance laptop with Intel i7 processor, 16GB RAM, 512GB SSD, and stunning 15-inch display. Perfect for professionals and creatives.',
                'short_description' => 'High-performance laptop with Intel i7, 16GB RAM, 512GB SSD',
                'retail_price' => 1299.99,
                'sku' => 'LAP-PRO-15-001',
                'stock_quantity' => 50,
                'is_active' => true,
                'is_featured' => true,
            ]);

            ProductImage::create([
                'product_id' => $laptop->id,
                'image_url' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductImage::create([
                'product_id' => $laptop->id,
                'image_url' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=800',
                'sort_order' => 1,
                'is_primary' => false,
            ]);

            ProductPriceTier::create([
                'product_id' => $laptop->id,
                'min_quantity' => 5,
                'max_quantity' => 9,
                'price' => 1199.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $laptop->id,
                'min_quantity' => 10,
                'max_quantity' => null,
                'price' => 1099.99,
            ]);

            $smartphone = Product::create([
                'category_id' => $electronicsCategory->id,
                'name' => 'Smartphone X Pro',
                'slug' => 'smartphone-x-pro',
                'description' => 'Latest flagship smartphone with 128GB storage, triple camera system, 5G connectivity, and all-day battery life.',
                'short_description' => 'Flagship smartphone with triple camera and 5G',
                'retail_price' => 899.99,
                'sku' => 'PHONE-X-PRO-001',
                'stock_quantity' => 100,
                'is_active' => true,
                'is_featured' => true,
            ]);

            ProductImage::create([
                'product_id' => $smartphone->id,
                'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductPriceTier::create([
                'product_id' => $smartphone->id,
                'min_quantity' => 3,
                'max_quantity' => 9,
                'price' => 849.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $smartphone->id,
                'min_quantity' => 10,
                'max_quantity' => null,
                'price' => 799.99,
            ]);
        }

        // Clothing Products
        if ($clothingCategory) {
            $tshirt = Product::create([
                'category_id' => $clothingCategory->id,
                'name' => 'Classic Cotton T-Shirt',
                'slug' => 'classic-cotton-tshirt',
                'description' => 'Comfortable 100% cotton t-shirt available in multiple colors and sizes. Perfect for everyday wear.',
                'short_description' => 'Comfortable 100% cotton t-shirt in multiple colors',
                'retail_price' => 24.99,
                'sku' => 'TSHIRT-COTTON-001',
                'stock_quantity' => 200,
                'is_active' => true,
                'is_featured' => false,
            ]);

            ProductImage::create([
                'product_id' => $tshirt->id,
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductPriceTier::create([
                'product_id' => $tshirt->id,
                'min_quantity' => 10,
                'max_quantity' => 49,
                'price' => 22.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $tshirt->id,
                'min_quantity' => 50,
                'max_quantity' => null,
                'price' => 19.99,
            ]);

            $jeans = Product::create([
                'category_id' => $clothingCategory->id,
                'name' => 'Slim Fit Denim Jeans',
                'slug' => 'slim-fit-denim-jeans',
                'description' => 'Premium denim jeans with stretch fabric for comfort. Available in multiple washes and sizes.',
                'short_description' => 'Premium stretch denim jeans in multiple washes',
                'retail_price' => 79.99,
                'sku' => 'JEANS-SLIM-001',
                'stock_quantity' => 150,
                'is_active' => true,
                'is_featured' => true,
            ]);

            ProductImage::create([
                'product_id' => $jeans->id,
                'image_url' => 'https://images.unsplash.com/photo-1542272604-787c3833835b?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductPriceTier::create([
                'product_id' => $jeans->id,
                'min_quantity' => 5,
                'max_quantity' => 19,
                'price' => 74.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $jeans->id,
                'min_quantity' => 20,
                'max_quantity' => null,
                'price' => 69.99,
            ]);
        }

        // Home & Kitchen Products
        if ($homeKitchenCategory) {
            $coffeeMaker = Product::create([
                'category_id' => $homeKitchenCategory->id,
                'name' => 'Premium Coffee Maker',
                'slug' => 'premium-coffee-maker',
                'description' => 'Programmable coffee maker with thermal carafe, 12-cup capacity, and auto-shutoff feature. Perfect for your morning brew.',
                'short_description' => '12-cup programmable coffee maker with thermal carafe',
                'retail_price' => 89.99,
                'sku' => 'COFFEE-MAKER-001',
                'stock_quantity' => 75,
                'is_active' => true,
                'is_featured' => true,
            ]);

            ProductImage::create([
                'product_id' => $coffeeMaker->id,
                'image_url' => 'https://images.unsplash.com/photo-1517668808823-f8f85e0e79fa?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductImage::create([
                'product_id' => $coffeeMaker->id,
                'image_url' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55c?w=800',
                'sort_order' => 1,
                'is_primary' => false,
            ]);

            ProductPriceTier::create([
                'product_id' => $coffeeMaker->id,
                'min_quantity' => 3,
                'max_quantity' => 9,
                'price' => 84.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $coffeeMaker->id,
                'min_quantity' => 10,
                'max_quantity' => null,
                'price' => 79.99,
            ]);

            $blender = Product::create([
                'category_id' => $homeKitchenCategory->id,
                'name' => 'Professional Blender',
                'slug' => 'professional-blender',
                'description' => 'High-powered blender with 1500W motor, 64oz capacity, and multiple speed settings. Perfect for smoothies and more.',
                'short_description' => '1500W professional blender with 64oz capacity',
                'retail_price' => 149.99,
                'sku' => 'BLENDER-PRO-001',
                'stock_quantity' => 60,
                'is_active' => true,
                'is_featured' => false,
            ]);

            ProductImage::create([
                'product_id' => $blender->id,
                'image_url' => 'https://images.unsplash.com/photo-1571167530759-4b9e7c4e4c8b?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductPriceTier::create([
                'product_id' => $blender->id,
                'min_quantity' => 2,
                'max_quantity' => 4,
                'price' => 139.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $blender->id,
                'min_quantity' => 5,
                'max_quantity' => null,
                'price' => 129.99,
            ]);
        }

        // Sports & Outdoors Products
        if ($sportsCategory) {
            $yogaMat = Product::create([
                'category_id' => $sportsCategory->id,
                'name' => 'Premium Yoga Mat',
                'slug' => 'premium-yoga-mat',
                'description' => 'Eco-friendly yoga mat with superior grip and cushioning. Non-slip surface and easy to clean.',
                'short_description' => 'Eco-friendly yoga mat with superior grip',
                'retail_price' => 39.99,
                'sku' => 'YOGA-MAT-001',
                'stock_quantity' => 120,
                'is_active' => true,
                'is_featured' => false,
            ]);

            ProductImage::create([
                'product_id' => $yogaMat->id,
                'image_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductPriceTier::create([
                'product_id' => $yogaMat->id,
                'min_quantity' => 10,
                'max_quantity' => 24,
                'price' => 36.99,
            ]);

            ProductPriceTier::create([
                'product_id' => $yogaMat->id,
                'min_quantity' => 25,
                'max_quantity' => null,
                'price' => 34.99,
            ]);

            $dumbbells = Product::create([
                'category_id' => $sportsCategory->id,
                'name' => 'Adjustable Dumbbell Set',
                'slug' => 'adjustable-dumbbell-set',
                'description' => 'Space-saving adjustable dumbbell set. Adjust from 5 to 50 pounds per dumbbell. Perfect for home gyms.',
                'short_description' => 'Adjustable dumbbells from 5-50 lbs per dumbbell',
                'retail_price' => 299.99,
                'sku' => 'DUMBBELL-ADJ-001',
                'stock_quantity' => 40,
                'is_active' => true,
                'is_featured' => true,
            ]);

            ProductImage::create([
                'product_id' => $dumbbells->id,
                'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800',
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            ProductImage::create([
                'product_id' => $dumbbells->id,
                'image_url' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800',
                'sort_order' => 1,
                'is_primary' => false,
            ]);

            ProductPriceTier::create([
                'product_id' => $dumbbells->id,
                'min_quantity' => 2,
                'max_quantity' => null,
                'price' => 279.99,
            ]);
        }
    }
}
