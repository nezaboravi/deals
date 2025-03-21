<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasUser = fake()->boolean(60); // 60% chance of having a user
        
        return [
            'deal_id' => Deal::factory(),
            'user_id' => $hasUser ? User::factory() : null,
            'ip_address' => fake()->ipv4(),
            'weight' => $hasUser ? fake()->randomElement([2, 3]) : 1, // 1 for anonymous, 2 for logged-in, 3 for verified developers
        ];
    }
    
    /**
     * Indicate that the vote is from an anonymous user.
     */
    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'weight' => 1,
        ]);
    }
    
    /**
     * Indicate that the vote is from a logged-in user.
     */
    public function loggedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->state(['email_verified_at' => null]),
            'weight' => 2,
        ]);
    }
    
    /**
     * Indicate that the vote is from a verified developer.
     */
    public function verifiedDeveloper(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'weight' => 3,
        ]);
    }
}
