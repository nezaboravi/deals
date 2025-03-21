<?php

use App\Models\Deal;
use Livewire\Attributes\Computed;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use function Livewire\Volt\method;

// Props
state(['deal' => null, 'relatedDeals' => []]);

// Mount the component
mount(function (Deal $deal) {
    $this->deal = $deal->load(['category', 'user']);
    $this->loadRelatedDeals();
});

// Load related deals from the same category
$loadRelatedDeals =  function () {
    $this->relatedDeals = Deal::where('category_id', $this->deal->category_id)
        ->where('id', '!=', $this->deal->id)
        ->published()
        ->active()
        ->popular()
        ->take(3)
        ->get();
};

// Listen for vote updates
$updatedVoteCount =  function ($dealId) {
    if ($dealId == $this->deal->id) {
        $this->deal->refresh();
    }
};

?>

<div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="py-8">
            <!-- Breadcrumbs -->
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-orange-500 dark:text-gray-400 dark:hover:text-orange-400 transition-colors duration-200">Home</a>
                    </li>
                    <li>
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                    </li>
                    <li>
                        @if($deal->category)
                        @if($deal->category_slug)
                        <a href="{{ route('deals.index', ['category' => $deal->category_slug]) }}" class="text-gray-500 hover:text-orange-500 dark:text-gray-400 dark:hover:text-orange-400 transition-colors duration-200">{{ $deal->category_name }}</a>
                        @else
                        <span class="text-gray-500 dark:text-gray-400">{{ $deal->category_name }}</span>
                        @endif
                        @else
                        <span class="text-gray-500 dark:text-gray-400">Uncategorized</span>
                        @endif
                    </li>
                    <li>
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-700 dark:text-gray-300">{{ $deal->title }}</span>
                    </li>
                </ol>
            </nav>

            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Deal details -->
                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="relative">
                            @if ($deal->is_featured)
                                <div class="absolute left-0 top-0 z-10 bg-orange-500 px-3 py-1.5 text-xs font-medium text-white">
                                    Featured
                                </div>
                            @endif

                            <div class="aspect-h-9 aspect-w-16 bg-gray-200 dark:bg-gray-700">
                                <img
                                    src="https://picsum.photos/seed/{{ $deal->id }}/1200/675"
                                    alt="{{ $deal->title }}"
                                    class="h-full w-full object-cover object-center"
                                >
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full bg-{{ str_replace('#', '', $deal->category_color) }}-50 px-2.5 py-1 text-xs font-medium text-{{ str_replace('#', '', $deal->category_color) }}-700 ring-1 ring-inset ring-{{ str_replace('#', '', $deal->category_color) }}-600/20 dark:bg-{{ str_replace('#', '', $deal->category_color) }}-900 dark:text-{{ str_replace('#', '', $deal->category_color) }}-300 dark:ring-{{ str_replace('#', '', $deal->category_color) }}-800">
                                    {{ $deal->category_name }}
                                </span>

                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $deal->published_at->format('M d, Y') }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Expires: {{ $deal->expiry_date->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ $deal->title }}</h1>

                            <div class="mt-6 prose prose-orange max-w-none dark:prose-invert">
                                {{ $deal->description }}
                            </div>

                            <div class="mt-8 flex items-center justify-between border-t border-gray-100 pt-6 dark:border-gray-700">
                                <div class="flex items-center">
                                    <img
                                        src="{{ $deal->user->profile_photo_url }}"
                                        alt="{{ $deal->user->name }}"
                                        class="h-10 w-10 rounded-full border border-gray-200 dark:border-gray-600"
                                    >
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $deal->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Submitted by {{ $deal->user->isVerifiedDeveloper() ? 'Verified Developer' : 'Member' }}</p>
                                    </div>
                                </div>

                                <a
                                    href="{{ $deal->deal_link }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 dark:bg-orange-500 dark:hover:bg-orange-400"
                                >
                                    Get This Deal
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Comments section (placeholder for future implementation) -->
                    <div class="mt-8 overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Comments
                        </h2>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Comments feature coming soon!</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Vote card -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex flex-col items-center">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                Vote for this deal
                            </h2>
                            <p class="mt-1 text-sm text-center text-gray-500 dark:text-gray-400">
                                Show your support by upvoting this deal
                            </p>

                            <div class="mt-4">
                                <livewire:deals.vote-button :deal="$deal" :key="'vote-detail-'.$deal->id" />
                            </div>

                            <div class="mt-4 w-full border-t border-gray-200 pt-4 dark:border-gray-700">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Total votes:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $deal->vote_count }}</span>
                                </div>

                                @auth
                                    <div class="mt-2 flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Your vote weight:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ auth()->user()->isVerifiedDeveloper() ? '3' : '2' }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2 flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Anonymous vote weight:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">1</span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400">Sign in</a> for a higher vote weight!
                                    </p>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- Related deals -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Related Deals</h2>

                        <div class="mt-4 space-y-4">
                            @forelse ($relatedDeals as $relatedDeal)
                                <div class="flex items-start space-x-4">
                                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md bg-gray-200 dark:bg-gray-700">
                                        <img
                                            src="https://picsum.photos/seed/{{ $relatedDeal->id }}/300/300"
                                            alt="{{ $relatedDeal->title }}"
                                            class="h-full w-full object-cover object-center"
                                        >
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('deals.show', $relatedDeal) }}" class="hover:underline">
                                                {{ $relatedDeal->title }}
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                            {{ $relatedDeal->description }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">No related deals found</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Share card -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Share this deal
                        </h2>

                        <div class="mt-4 flex space-x-4">
                            <a
                                href="https://twitter.com/intent/tweet?url={{ urlencode(route('deals.show', $deal)) }}&text={{ urlencode($deal->title) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#1DA1F2] text-white hover:bg-opacity-90"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>

                            <a
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('deals.show', $deal)) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#1877F2] text-white hover:bg-opacity-90"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a
                                href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('deals.show', $deal)) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#0A66C2] text-white hover:bg-opacity-90"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M19.7 3H4.3A1.3 1.3 0 003 4.3v15.4A1.3 1.3 0 004.3 21h15.4a1.3 1.3 0 001.3-1.3V4.3A1.3 1.3 0 0019.7 3zM8.339 18.338H5.667v-8.59h2.672v8.59zM7.004 8.574a1.548 1.548 0 11-.002-3.096 1.548 1.548 0 01.002 3.096zm11.335 9.764H15.67v-4.177c0-.996-.017-2.278-1.387-2.278-1.389 0-1.601 1.086-1.601 2.206v4.249h-2.667v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.779 3.203 4.092v4.711z" clip-rule="evenodd" />
                                </svg>
                            </a>

                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText('{{ route('deals.show', $deal) }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 2000);"
                                class="flex h-8 items-center justify-center rounded-md bg-gray-100 px-3 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
