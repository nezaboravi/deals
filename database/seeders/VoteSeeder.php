<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all published deals
        $deals = Deal::published()->get();
        $users = User::all();
        
        // Create votes for each deal
        foreach ($deals as $deal) {
            // Generate a random number of votes for each deal (5-30)
            $voteCount = rand(5, 30);
            
            // Create anonymous votes (weight = 1)
            $anonymousVoteCount = ceil($voteCount * 0.6); // 60% anonymous votes
            for ($i = 0; $i < $anonymousVoteCount; $i++) {
                Vote::factory()
                    ->anonymous()
                    ->create([
                        'deal_id' => $deal->id,
                        'ip_address' => fake()->unique()->ipv4(),
                    ]);
            }
            
            // Create logged-in user votes (weight = 2)
            $loggedInVoteCount = ceil($voteCount * 0.3); // 30% logged-in user votes
            $loggedInUsers = $users->random(min($loggedInVoteCount, $users->count()));
            foreach ($loggedInUsers as $user) {
                // Check if this user has already voted on this deal
                if (!Vote::where('deal_id', $deal->id)->where('user_id', $user->id)->exists()) {
                    Vote::factory()
                        ->loggedIn()
                        ->create([
                            'deal_id' => $deal->id,
                            'user_id' => $user->id,
                            'ip_address' => fake()->unique()->ipv4(),
                        ]);
                }
            }
            
            // Create verified developer votes (weight = 3)
            $verifiedVoteCount = ceil($voteCount * 0.1); // 10% verified developer votes
            // Get users who haven't voted yet
            $votedUserIds = Vote::where('deal_id', $deal->id)->pluck('user_id')->toArray();
            $availableUsers = $users->whereNotIn('id', $votedUserIds);
            
            foreach ($availableUsers->random(min($verifiedVoteCount, $availableUsers->count())) as $user) {
                Vote::factory()
                    ->verifiedDeveloper()
                    ->create([
                        'deal_id' => $deal->id,
                        'user_id' => $user->id,
                        'ip_address' => fake()->unique()->ipv4(),
                    ]);
            }
            
            // Update the deal's vote_count based on the votes' weights
            $totalWeight = Vote::where('deal_id', $deal->id)->sum('weight');
            $deal->update(['vote_count' => $totalWeight]);
        }
    }
}
