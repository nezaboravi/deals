<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $isPublished = fake()->boolean(80); // 80% chance of being published
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'deal_link' => fake()->url(),
            'image_path' => 'deals/' . fake()->word() . '.jpg',
            'category_id' => Category::factory(),
            'user_id' => fake()->boolean(70) ? User::factory() : null, // 70% chance of having a user
            'expiry_date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
            'is_verified' => fake()->boolean(60), // 60% chance of being verified
            'vote_count' => fake()->numberBetween(0, 100),
            'published_at' => $isPublished ? fake()->dateTimeBetween('-3 months', 'now') : null,
            'submitter_email' => $isPublished && !fake()->boolean(70) ? fake()->safeEmail() : null, // For anonymous submissions
            'verification_token' => !$isPublished ? Str::random(32) : null,
        ];
    }
    
    /**
     * Indicate that the deal is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
    
    /**
     * Indicate that the deal is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }
    
    /**
     * Indicate that the deal is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'verification_token' => null,
        ]);
    }
    
    /**
     * Indicate that the deal is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
            'verification_token' => Str::random(32),
        ]);
    }
}
