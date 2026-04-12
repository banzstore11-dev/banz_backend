<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Users
        User::factory()->create([
            'name' => 'Banz Admin',
            'email' => 'admin@banz.com',
            'password' => bcrypt('password'),
        ]);

        // Seed Categories (must be seeded first)
        $this->call([
            CategorySeeder::class,
        ]);

        // Seed Products (depends on Categories)
        $this->call([
            ProductSeeder::class,
        ]);

        // Seed Cart (depends on Products and Users)
        $this->call([
            CartSeeder::class,
        ]);

        // Seed Orders (depends on Products and Users)
        $this->call([
            OrderSeeder::class,
        ]);

        // Seed Testimonials
        $this->call([
            TestimonialSeeder::class,
        ]);

        // Seed Reviews (depends on Products and Users/Orders)
        $this->call([
            ReviewSeeder::class,
        ]);
    }
}
