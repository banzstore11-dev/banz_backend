<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'role' => 'Retail Store Owner',
                'content' => 'The wholesale prices here are unbeatable. My profit margins have increased significantly since switching to this platform. The product quality is consistently excellent.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'role' => 'E-commerce Entrepreneur',
                'content' => 'Fast shipping and amazing customer service. I\'ve been sourcing products from this platform for my online store for over a year now. Highly recommended!',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@example.com',
                'role' => 'Boutique Manager',
                'content' => 'The variety of products available is outstanding. I can find everything I need for my boutique in one place. Great wholesale rates and reliable delivery.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'David Park',
                'email' => 'david.park@example.com',
                'role' => 'Distribution Center Manager',
                'content' => 'Solid wholesale platform with competitive pricing. The bulk ordering system works smoothly. Would love to see more electronics options in the future.',
                'rating' => 4,
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Lisa Thompson',
                'email' => 'lisa.thompson@example.com',
                'role' => 'Online Reseller',
                'content' => 'Best wholesale experience I\'ve had! The product images are accurate, and items arrive exactly as described. My customers are always satisfied.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@example.com',
                'role' => 'Store Chain Owner',
                'content' => 'We\'ve been ordering for our chain of 5 stores for over 2 years. Consistent quality, fair prices, and excellent support team. A truly reliable partner.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@example.com',
                'role' => 'Fashion Retailer',
                'content' => 'Excellent product selection and fast turnaround times. The customer support team is always helpful and responsive. Highly satisfied with the service!',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Robert Kim',
                'email' => 'robert.kim@example.com',
                'role' => 'Tech Store Owner',
                'content' => 'Great platform for sourcing electronics. The pricing is competitive and the quality is top-notch. My customers love the products I get from here.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.lee@example.com',
                'role' => 'Home Goods Retailer',
                'content' => 'The home and kitchen products are fantastic. Good quality, fair prices, and reliable shipping. My store has seen increased sales since partnering with this platform.',
                'rating' => 4,
                'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            [
                'name' => 'Thomas Anderson',
                'email' => 'thomas.anderson@example.com',
                'role' => 'Sports Equipment Distributor',
                'content' => 'Perfect for bulk orders. The sports equipment I\'ve purchased has been high quality and well-priced. The ordering process is straightforward and efficient.',
                'rating' => 5,
                'avatar' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=150&h=150&fit=crop&crop=faces',
                'is_approved' => true,
            ],
            // Add some pending testimonials (not approved)
            [
                'name' => 'Pending User',
                'email' => 'pending@example.com',
                'role' => 'New Customer',
                'content' => 'This is a pending testimonial that needs admin approval before being displayed.',
                'rating' => 5,
                'avatar' => null,
                'is_approved' => false,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
