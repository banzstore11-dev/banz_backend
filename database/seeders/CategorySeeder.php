<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Latest electronic devices and gadgets including smartphones, laptops, tablets, and accessories.',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Fashionable clothing for men, women, and children. From casual wear to formal attire.',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Kitchen',
                'slug' => 'home-kitchen',
                'description' => 'Everything you need for your home and kitchen. Furniture, appliances, and decor items.',
                'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'description' => 'Sports equipment, outdoor gear, fitness accessories, and athletic wear.',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Books',
                'slug' => 'books',
                'description' => 'Books for all ages and interests. Fiction, non-fiction, educational, and more.',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
                'description' => 'Fun toys and games for children of all ages. Educational toys, board games, and more.',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'slug' => 'beauty-personal-care',
                'description' => 'Beauty products, skincare, cosmetics, and personal care items.',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Car accessories, parts, tools, and automotive supplies.',
                'image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
