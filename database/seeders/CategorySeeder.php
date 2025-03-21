<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Hosting',
                'description' => 'Web hosting, server, and cloud infrastructure deals',
                'color' => '#4f46e5', // Indigo
            ],
            [
                'name' => 'Development Tools',
                'description' => 'IDEs, code editors, and development utilities',
                'color' => '#0ea5e9', // Sky blue
            ],
            [
                'name' => 'Learning Resources',
                'description' => 'Courses, books, and educational content',
                'color' => '#10b981', // Emerald
            ],
            [
                'name' => 'SaaS Products',
                'description' => 'Software as a Service products and subscriptions',
                'color' => '#f59e0b', // Amber
            ],
            [
                'name' => 'Design Resources',
                'description' => 'UI kits, templates, and design tools',
                'color' => '#ef4444', // Red
            ],
            [
                'name' => 'Productivity',
                'description' => 'Tools to improve workflow and productivity',
                'color' => '#8b5cf6', // Violet
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
