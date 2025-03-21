<?php

use App\Models\Deal;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use Livewire\Volt\Component;

new class extends Component
{
    public Deal $deal;
    public bool $hasVoted = false;
    public bool $isVoting = false;
    
    public function mount(Deal $deal)
    {
        $this->deal = $deal;
        $this->checkIfUserHasVoted();
    }
    
    // Check if the user has already voted
    public function checkIfUserHasVoted()
    {
        $user = Auth::user();
        $ipAddress = request()->ip();
        
        if ($user) {
            $this->hasVoted = Vote::where('deal_id', $this->deal->id)
                ->where('user_id', $user->id)
                ->exists();
        } else {
            $this->hasVoted = Vote::where('deal_id', $this->deal->id)
                ->where('ip_address', $ipAddress)
                ->exists();
        }
    }
    
    // Vote for the deal
    public function vote()
    {
        $this->isVoting = true;
        
        $user = Auth::user();
        $ipAddress = request()->ip();
        
        // Check if the user has already voted
        if ($user) {
            $existingVote = Vote::where('deal_id', $this->deal->id)
                ->where('user_id', $user->id)
                ->first();
        } else {
            $existingVote = Vote::where('deal_id', $this->deal->id)
                ->where('ip_address', $ipAddress)
                ->first();
        }
        
        // If the user has already voted, return
        if ($existingVote) {
            $this->hasVoted = true;
            $this->isVoting = false;
            return;
        }
        
        // Determine the vote weight based on user status
        $weight = 1; // Anonymous vote
        
        if ($user) {
            $weight = 2; // Logged-in user vote
            
            if ($user->isVerifiedDeveloper()) {
                $weight = 3; // Verified developer vote
            }
        }
        
        // Create the vote
        Vote::create([
            'deal_id' => $this->deal->id,
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'weight' => $weight,
        ]);
        
        // Update the deal's vote count
        $this->deal->increment('vote_count', $weight);
        
        $this->hasVoted = true;
        $this->isVoting = false;
        
        $this->dispatch('vote-updated', dealId: $this->deal->id);
    }
};

?>

<div class="flex flex-col items-center">
    <button 
        wire:click="vote"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-75"
        class="group relative flex flex-col items-center justify-center"
        @class([
            'cursor-not-allowed' => $hasVoted,
            'cursor-pointer' => !$hasVoted,
        ])
        @disabled($hasVoted)
    >
        <div class="flex h-12 w-12 items-center justify-center rounded-full border-2 shadow-sm transition-all duration-200 
            @if($hasVoted) 
                border-orange-600 bg-orange-50 dark:border-orange-500 dark:bg-orange-900/30 
            @else 
                border-gray-300 bg-white hover:border-orange-600 hover:bg-orange-50 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-orange-500 dark:hover:bg-orange-900/30 
            @endif"
        >
            <svg 
                class="h-6 w-6 transition-all duration-200 
                @if($hasVoted) 
                    text-orange-600 dark:text-orange-500 
                @else 
                    text-gray-500 group-hover:text-orange-600 dark:text-gray-400 dark:group-hover:text-orange-500 
                @endif" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24" 
                xmlns="http://www.w3.org/2000/svg"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M5 15l7-7 7 7"
                ></path>
            </svg>
            
            <div wire:loading wire:target="vote" class="absolute inset-0 flex items-center justify-center rounded-full bg-white/80 dark:bg-gray-800/80">
                <svg class="h-5 w-5 animate-spin text-orange-600 dark:text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <span class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $deal->vote_count }}</span>
    </button>
    
    @if($hasVoted)
        <span class="mt-1 text-xs text-orange-600 font-medium dark:text-orange-400">Upvoted!</span>
    @else
        <span class="mt-1 text-xs text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-200">Upvote</span>
    @endif
</div>
