<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('is_active', true)->get();
        $users = User::all();

        if ($products->isEmpty()) {
            $this->command->warn('No active products found. Please seed products first.');
            return;
        }

        // Sample review comments
        $reviewComments = [
            // 5-star reviews
            [
                'Excellent product! Exceeded my expectations. High quality and fast shipping.',
                'Amazing value for money. Will definitely order again!',
                'Perfect quality, exactly as described. Highly recommended!',
                'Outstanding product! My customers love it. Great wholesale pricing.',
                'Top-notch quality and excellent customer service. Very satisfied!',
                'Best purchase I\'ve made. Quality is exceptional and delivery was quick.',
                'Fantastic product! Great value and the quality is outstanding.',
                'Love this product! It\'s exactly what I needed for my business.',
            ],
            // 4-star reviews
            [
                'Good product overall. Quality is solid, though shipping took a bit longer than expected.',
                'Nice product, good value. Minor packaging issue but product itself is great.',
                'Satisfied with the purchase. Quality is good, would recommend.',
                'Decent product for the price. Works well for my needs.',
                'Good quality product. Minor improvements could be made but overall happy.',
            ],
            // 3-star reviews
            [
                'Average product. Does the job but nothing special.',
                'Okay quality. Expected a bit more for the price.',
                'Product is fine, but could be better. Meets basic requirements.',
            ],
            // 2-star reviews
            [
                'Not as described. Quality is lower than expected.',
                'Disappointed with the product. Expected better quality.',
            ],
            // 1-star reviews
            [
                'Poor quality. Product arrived damaged and not as described.',
                'Very disappointed. Product does not meet expectations at all.',
            ],
        ];

        // Guest review names
        $guestNames = [
            'Alex Thompson', 'Sarah Martinez', 'Michael Brown', 'Emily Davis',
            'David Wilson', 'Jessica Taylor', 'Chris Anderson', 'Amanda White',
            'Ryan Johnson', 'Nicole Garcia', 'Kevin Lee', 'Rachel Moore',
        ];

        $guestEmails = [
            'alex.t@example.com', 'sarah.m@example.com', 'michael.b@example.com',
            'emily.d@example.com', 'david.w@example.com', 'jessica.t@example.com',
        ];

        $reviewCount = 0;

        // Create reviews for each product
        foreach ($products as $product) {
            // Determine how many reviews per product (3-8 reviews)
            $reviewsPerProduct = rand(3, 8);

            for ($i = 0; $i < $reviewsPerProduct; $i++) {
                $rating = $this->getRandomRating();
                $isAuthenticated = rand(0, 1) === 1 && !$users->isEmpty();
                
                $reviewData = [
                    'product_id' => $product->id,
                    'rating' => $rating,
                ];

                if ($isAuthenticated) {
                    // Authenticated user review
                    $user = $users->random();
                    $reviewData['user_id'] = $user->id;
                    $reviewData['name'] = $user->name;
                    $reviewData['email'] = $user->email;

                    // Check if user has purchased this product (for verified purchase)
                    $hasPurchased = Order::where('user_id', $user->id)
                        ->whereHas('items', function ($query) use ($product) {
                            $query->where('product_id', $product->id);
                        })
                        ->whereIn('status', ['delivered', 'shipped', 'processing'])
                        ->exists();

                    $reviewData['is_verified_purchase'] = $hasPurchased;
                } else {
                    // Guest review
                    $reviewData['user_id'] = null;
                    $reviewData['name'] = $guestNames[array_rand($guestNames)];
                    $reviewData['email'] = rand(0, 1) === 1 ? $guestEmails[array_rand($guestEmails)] : null;
                    $reviewData['is_verified_purchase'] = false;
                }

                // Add comment (80% chance)
                if (rand(0, 9) < 8) {
                    $ratingIndex = $rating - 1;
                    if (isset($reviewComments[$ratingIndex]) && !empty($reviewComments[$ratingIndex])) {
                        $reviewData['comment'] = $reviewComments[$ratingIndex][array_rand($reviewComments[$ratingIndex])];
                    }
                }

                // Set created_at to random date within last 6 months
                $reviewData['created_at'] = now()->subDays(rand(0, 180));
                $reviewData['updated_at'] = $reviewData['created_at'];

                Review::create($reviewData);
                $reviewCount++;
            }
        }

        $this->command->info("Created {$reviewCount} reviews for {$products->count()} products.");
    }

    /**
     * Get a random rating with weighted distribution (more 4-5 star reviews)
     */
    private function getRandomRating(): int
    {
        $weights = [
            1 => 5,   // 5% chance
            2 => 5,   // 5% chance
            3 => 10,  // 10% chance
            4 => 30,  // 30% chance
            5 => 50,  // 50% chance
        ];

        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $current = 0;

        foreach ($weights as $rating => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $rating;
            }
        }

        return 5; // Default to 5 stars
    }
}
