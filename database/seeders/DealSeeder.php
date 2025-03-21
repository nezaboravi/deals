<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users for the deals
        User::factory(5)->create();
        
        // Get all categories
        $categories = Category::all();
        
        // Create 5 featured deals (1 for each category if possible)
        foreach ($categories->take(5) as $category) {
            Deal::factory()
                ->featured()
                ->verified()
                ->published()
                ->create([
                    'category_id' => $category->id,
                    'user_id' => User::inRandomOrder()->first()->id,
                ]);
        }
        
        // Create 20 regular deals distributed across categories
        foreach ($categories as $category) {
            $dealsPerCategory = ceil(20 / $categories->count());
            
            Deal::factory($dealsPerCategory)
                ->published()
                ->create([
                    'category_id' => $category->id,
                ]);
        }
        
        // Create 5 unpublished deals
        Deal::factory(5)
            ->unpublished()
            ->create([
                'category_id' => $categories->random()->id,
            ]);
    }
}
