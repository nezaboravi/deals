<?php

namespace Database\Seeders\Production;

use App\Models\Deal;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds for production.
     */
    public function run(): void
    {
        // Get all published deals
        $deals = Deal::published()->get();
        
        // Predefined IP addresses for anonymous votes
        $ipAddresses = [
            '192.168.1.1', '192.168.1.2', '192.168.1.3', '192.168.1.4', '192.168.1.5',
            '192.168.1.6', '192.168.1.7', '192.168.1.8', '192.168.1.9', '192.168.1.10',
            '192.168.1.11', '192.168.1.12', '192.168.1.13', '192.168.1.14', '192.168.1.15',
            '192.168.1.16', '192.168.1.17', '192.168.1.18', '192.168.1.19', '192.168.1.20',
            '192.168.1.21', '192.168.1.22', '192.168.1.23', '192.168.1.24', '192.168.1.25',
            '192.168.1.26', '192.168.1.27', '192.168.1.28', '192.168.1.29', '192.168.1.30',
        ];
        
        // Vote distribution for each deal
        $voteDistribution = [
            // Featured deals should have more votes
            'Laravel Forge - 20% Off Annual Plans' => 25,
            'PhpStorm - 30% Discount for Laravel Developers' => 28,
            'Laracasts Annual Subscription - 25% Off' => 30,
            'Tailwind UI - 15% Discount for New Users' => 22,
            'Laravel Spark - 20% Off Lifetime License' => 26,
            
            // Regular deals have fewer votes
            'Digital Ocean - $100 Credit for New Users' => 18,
            'Laravel News Pro - 10% Off Annual Subscription' => 15,
            'Tinkerwell - 15% Discount' => 12,
            'Laravel Shift - 20% Off Bundle' => 14,
            'Mailcoach - 25% Off First Year' => 16,
        ];
        
        // Process each deal
        foreach ($deals as $deal) {
            // Get the target vote count for this deal
            $targetVoteCount = $voteDistribution[$deal->title] ?? 10; // Default to 10 votes
            
            // Create anonymous votes (weight = 1) - 60% of votes
            $anonymousVoteCount = ceil($targetVoteCount * 0.6);
            for ($i = 0; $i < $anonymousVoteCount && $i < count($ipAddresses); $i++) {
                Vote::create([
                    'deal_id' => $deal->id,
                    'user_id' => null,
                    'ip_address' => $ipAddresses[$i],
                    'weight' => 1,
                    'created_at' => now()->subHours(rand(1, 72)),
                    'updated_at' => now(),
                ]);
            }
            
            // Get or create some users for authenticated votes
            $users = $this->getOrCreateUsers(5);
            
            // Create logged-in user votes (weight = 2) - 30% of votes
            $loggedInVoteCount = ceil($targetVoteCount * 0.3);
            for ($i = 0; $i < $loggedInVoteCount && $i < count($users); $i++) {
                Vote::create([
                    'deal_id' => $deal->id,
                    'user_id' => $users[$i]->id,
                    'ip_address' => $ipAddresses[$i + $anonymousVoteCount],
                    'weight' => 2,
                    'created_at' => now()->subHours(rand(1, 48)),
                    'updated_at' => now(),
                ]);
            }
            
            // Create verified developer votes (weight = 3) - 10% of votes
            $verifiedVoteCount = ceil($targetVoteCount * 0.1);
            $verifiedUsers = $this->getOrCreateVerifiedUsers(3);
            
            for ($i = 0; $i < $verifiedVoteCount && $i < count($verifiedUsers); $i++) {
                Vote::create([
                    'deal_id' => $deal->id,
                    'user_id' => $verifiedUsers[$i]->id,
                    'ip_address' => $ipAddresses[$i + $anonymousVoteCount + $loggedInVoteCount],
                    'weight' => 3,
                    'created_at' => now()->subHours(rand(1, 24)),
                    'updated_at' => now(),
                ]);
            }
            
            // Update the deal's vote_count based on the votes' weights
            $totalWeight = Vote::where('deal_id', $deal->id)->sum('weight');
            $deal->update(['vote_count' => $totalWeight]);
        }
    }
    
    /**
     * Get or create regular users for votes
     */
    private function getOrCreateUsers(int $count): array
    {
        $users = [];
        $userEmails = [
            'user1@example.com',
            'user2@example.com',
            'user3@example.com',
            'user4@example.com',
            'user5@example.com',
        ];
        
        foreach (array_slice($userEmails, 0, $count) as $email) {
            $users[] = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'User ' . substr($email, 4, 1),
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
        
        return $users;
    }
    
    /**
     * Get or create verified developer users for votes
     */
    private function getOrCreateVerifiedUsers(int $count): array
    {
        $users = [];
        $verifiedEmails = [
            'developer1@example.com',
            'developer2@example.com',
            'developer3@example.com',
        ];
        
        foreach (array_slice($verifiedEmails, 0, $count) as $email) {
            $users[] = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Developer ' . substr($email, 9, 1),
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_verified_developer' => true,
                ]
            );
        }
        
        return $users;
    }
}
