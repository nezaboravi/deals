<?php

namespace Database\Seeders\Production;

use App\Models\Category;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds for production.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::all();
        
        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@laraveldeals.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );
        
        // Featured deals data
        $featuredDeals = [
            [
                'title' => 'Laravel Forge - 20% Off Annual Plans',
                'description' => 'Laravel Forge is offering 20% off all annual plans. Forge simplifies server management for Laravel applications.',
                'url' => 'https://forge.laravel.com',
                'price' => 19.99,
                'discount_percentage' => 20,
                'expires_at' => now()->addMonths(1),
                'is_featured' => true,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'hosting')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'PhpStorm - 30% Discount for Laravel Developers',
                'description' => 'JetBrains is offering a special 30% discount on PhpStorm for Laravel developers. Use code LARAVELDEALS30.',
                'url' => 'https://www.jetbrains.com/phpstorm/',
                'price' => 89.00,
                'discount_percentage' => 30,
                'expires_at' => now()->addMonths(2),
                'is_featured' => true,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'development-tools')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Laracasts Annual Subscription - 25% Off',
                'description' => 'Get 25% off an annual subscription to Laracasts, the best Laravel learning platform.',
                'url' => 'https://laracasts.com',
                'price' => 99.00,
                'discount_percentage' => 25,
                'expires_at' => now()->addMonths(1),
                'is_featured' => true,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'learning-resources')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Tailwind UI - 15% Discount for New Users',
                'description' => 'Tailwind UI is offering 15% off for new users. Build beautiful UI components with Tailwind CSS.',
                'url' => 'https://tailwindui.com',
                'price' => 149.00,
                'discount_percentage' => 15,
                'expires_at' => now()->addMonths(3),
                'is_featured' => true,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'design-resources')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Laravel Spark - 20% Off Lifetime License',
                'description' => 'Laravel Spark is offering 20% off lifetime licenses. Quickly create SaaS applications with Laravel.',
                'url' => 'https://spark.laravel.com',
                'price' => 199.00,
                'discount_percentage' => 20,
                'expires_at' => now()->addMonths(1),
                'is_featured' => true,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'saas-products')->first()->id ?? $categories->first()->id,
            ],
        ];
        
        // Regular deals data
        $regularDeals = [
            [
                'title' => 'Digital Ocean - $100 Credit for New Users',
                'description' => 'Get $100 in credit over 60 days when you sign up for Digital Ocean.',
                'url' => 'https://digitalocean.com',
                'price' => 0,
                'discount_percentage' => 100,
                'expires_at' => now()->addMonths(6),
                'is_featured' => false,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'hosting')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Laravel News Pro - 10% Off Annual Subscription',
                'description' => 'Stay up to date with Laravel News Pro and save 10% on your annual subscription.',
                'url' => 'https://laravel-news.com',
                'price' => 49.00,
                'discount_percentage' => 10,
                'expires_at' => now()->addMonths(2),
                'is_featured' => false,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'learning-resources')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Tinkerwell - 15% Discount',
                'description' => 'Tinkerwell is offering 15% off. The perfect tool for Laravel developers to tinker with their code.',
                'url' => 'https://tinkerwell.app',
                'price' => 15.00,
                'discount_percentage' => 15,
                'expires_at' => now()->addMonths(1),
                'is_featured' => false,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'development-tools')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Laravel Shift - 20% Off Bundle',
                'description' => 'Get 20% off the Laravel Shift bundle. Automate your Laravel upgrades.',
                'url' => 'https://laravelshift.com',
                'price' => 99.00,
                'discount_percentage' => 20,
                'expires_at' => now()->addMonths(3),
                'is_featured' => false,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'development-tools')->first()->id ?? $categories->first()->id,
            ],
            [
                'title' => 'Mailcoach - 25% Off First Year',
                'description' => 'Mailcoach is offering 25% off your first year. Send newsletters without breaking the bank.',
                'url' => 'https://mailcoach.app',
                'price' => 199.00,
                'discount_percentage' => 25,
                'expires_at' => now()->addMonths(2),
                'is_featured' => false,
                'is_verified' => true,
                'is_published' => true,
                'category_id' => $categories->where('slug', 'saas-products')->first()->id ?? $categories->first()->id,
            ],
        ];
        
        // Insert featured deals
        foreach ($featuredDeals as $dealData) {
            $this->createDeal($dealData, $adminUser);
        }
        
        // Insert regular deals
        foreach ($regularDeals as $dealData) {
            $this->createDeal($dealData, $adminUser);
        }
    }
    
    /**
     * Create a deal with the given data
     */
    private function createDeal(array $dealData, User $user): void
    {
        $slug = Str::slug($dealData['title']);
        
        Deal::create([
            'title' => $dealData['title'],
            'slug' => $slug,
            'description' => $dealData['description'],
            'url' => $dealData['url'],
            'price' => $dealData['price'],
            'discount_percentage' => $dealData['discount_percentage'],
            'expires_at' => $dealData['expires_at'],
            'is_featured' => $dealData['is_featured'],
            'is_verified' => $dealData['is_verified'],
            'is_published' => $dealData['is_published'],
            'category_id' => $dealData['category_id'],
            'user_id' => $user->id,
            'vote_count' => 0, // Will be updated by VoteSeeder
        ]);
    }
}
