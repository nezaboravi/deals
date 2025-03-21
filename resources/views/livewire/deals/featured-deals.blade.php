<?php

use App\Models\Deal;
use Livewire\Volt\Component;

new class extends Component {
    public function getFeaturedDealsProperty()
    {
        return Deal::with(['category', 'user'])
            ->featured()
            ->published()
            ->active()
            ->take(4)
            ->get();
    }
}; ?>

<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Featured Deals
            </h2>
            <a href="{{ route('deals.index') }}" class="text-sm font-medium text-orange-500 hover:text-orange-600 dark:text-orange-400 dark:hover:text-orange-300 flex items-center">
                View all
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($this->featuredDeals as $deal)
                <div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                    <!-- Featured Badge -->
                    <div class="absolute left-0 top-0 z-10 bg-orange-500 px-2 py-1 text-xs font-medium text-white">
                        Featured
                    </div>
                    
                    <!-- Upvote Button (Absolute Positioned) -->
                    <div class="absolute right-3 top-3 z-10">
                        <button class="flex flex-col items-center justify-center w-10 h-10 rounded-full bg-white/90 hover:bg-white shadow-md dark:bg-gray-800/90 dark:hover:bg-gray-800 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $deal->vote_count }}</span>
                        </button>
                    </div>
                    
                    <!-- Deal Image -->
                    <div class="aspect-h-1 aspect-w-1 bg-gray-200 group-hover:opacity-90 dark:bg-gray-700">
                        <img 
                            src="https://picsum.photos/seed/{{ $deal->id }}/300/300" 
                            alt="{{ $deal->title }}" 
                            class="h-full w-full object-cover object-center"
                        >
                    </div>
                    
                    <!-- Deal Content -->
                    <div class="flex flex-1 flex-col p-4">
                        <div class="flex items-start justify-between">
                            <span class="inline-flex items-center rounded-full bg-{{ str_replace('#', '', $deal->category_color) }}-50 px-2 py-1 text-xs font-medium text-{{ str_replace('#', '', $deal->category_color) }}-700 ring-1 ring-inset ring-{{ str_replace('#', '', $deal->category_color) }}-600/20 dark:bg-{{ str_replace('#', '', $deal->category_color) }}-900 dark:text-{{ str_replace('#', '', $deal->category_color) }}-300 dark:ring-{{ str_replace('#', '', $deal->category_color) }}-800">
                                {{ $deal->category_name }}
                            </span>
                        </div>
                        
                        <h3 class="mt-3 text-base font-semibold text-gray-900 dark:text-white line-clamp-2 hover:text-orange-500 dark:hover:text-orange-400 transition-colors duration-200">
                            <a href="{{ route('deals.show', $deal) }}">
                                {{ $deal->title }}
                            </a>
                        </h3>
                        
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                            {{ $deal->description }}
                        </p>
                        
                        <div class="mt-auto pt-4 flex items-center justify-between">
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Expires: {{ $deal->expiry_date->format('M d, Y') }}
                            </div>
                            
                            <a 
                                href="{{ $deal->deal_link }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="inline-flex items-center rounded-md bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700 hover:bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 dark:hover:bg-orange-900/50"
                            >
                                Get Deal
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 py-8 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No featured deals available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
