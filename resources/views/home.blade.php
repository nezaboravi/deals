@php
try {
    // This will help us identify where the error is occurring
    $debugCategories = \App\Models\Category::all();
    foreach ($debugCategories as $cat) {
        // Force access to properties to see if any are null
        $name = $cat->name;
        $slug = $cat->slug;
        $color = $cat->color;
    }
} catch (\Exception $e) {
    // Log the error
    \Illuminate\Support\Facades\Log::error('Debug error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
}
@endphp
<x-layouts.public.index :title="__('Home')">
    <flux:main>
    <!-- Hero Section with ProductHunt-inspired design -->
    <div class="bg-white dark:bg-gray-900 pt-8 pb-4 relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6 md:flex-row md:gap-4">
                <div class="w-full md:w-1/2">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                        Discover the Best <span class="text-orange-500">Laravel Deals</span> Today
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Your daily feed of the best tools, courses, and resources for Laravel developers at unbeatable prices.
                        Upvote your favorites and help great deals rise to the top.
                    </p>
                    <div class="mt-8 flex gap-4">
                        <a href="{{ route('deals.submit') }}" class="rounded-md bg-orange-500 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500">
                            Submit a Deal
                        </a>
                        <a href="{{ route('deals.index') }}" class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-700">
                            Browse All Deals
                        </a>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <img src="{{ asset('images/hero-illustration.svg') }}" alt="Laravel Deals" class="h-auto w-full">
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Deals Section (ProductHunt style) -->
    <div class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Today's Deals
                </h2>
                <div class="flex space-x-2">
                    <a href="{{ route('deals.index', ['sort' => 'popular']) }}" class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Popular
                    </a>
                    <a href="{{ route('deals.index', ['sort' => 'newest']) }}" class="rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Newest
                    </a>
                </div>
            </div>

            <!-- Deal List in ProductHunt Style -->
            @php
            $todayDeals = \App\Models\Deal::with(['category', 'user'])
                ->published()
                ->active()
                ->latest()
                ->take(5)
                ->get();
            @endphp

            <div class="space-y-4">
                @foreach($todayDeals as $deal)
                <div class="flex items-start space-x-4 p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                    <!-- Upvote Button -->
                    <div class="flex flex-col items-center space-y-1 pt-1">
                        <button class="flex flex-col items-center justify-center w-12 h-12 rounded-full bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $deal->vote_count }}</span>
                        </button>
                    </div>

                    <!-- Deal Image -->
                    <div class="flex-shrink-0">
                        <img src="https://picsum.photos/seed/{{ $deal->id }}/80/80" alt="{{ $deal->title }}" class="w-20 h-20 object-cover rounded-lg">
                    </div>

                    <!-- Deal Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-orange-500 dark:hover:text-orange-400 transition-colors duration-200">
                                <a href="{{ route('deals.show', $deal) }}">{{ $deal->title }}</a>
                            </h3>
                            <span class="inline-flex items-center rounded-full bg-{{ str_replace('#', '', $deal->category->color) }}-50 px-2 py-1 text-xs font-medium text-{{ str_replace('#', '', $deal->category->color) }}-700 ring-1 ring-inset ring-{{ str_replace('#', '', $deal->category->color) }}-600/20 dark:bg-{{ str_replace('#', '', $deal->category->color) }}-900 dark:text-{{ str_replace('#', '', $deal->category->color) }}-300 dark:ring-{{ str_replace('#', '', $deal->category->color) }}-800">
                                {{ $deal->category->name }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $deal->description }}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Expires: {{ $deal->expiry_date->format('M d, Y') }}
                                </div>
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ $deal->user->name }}
                                </div>
                            </div>
                            <a href="{{ $deal->deal_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-md bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700 hover:bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 dark:hover:bg-orange-900/50">
                                Get Deal
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-6">
                    <a href="{{ route('deals.index') }}" class="inline-flex items-center text-sm font-medium text-orange-500 hover:text-orange-600 dark:text-orange-400 dark:hover:text-orange-300">
                        View all deals
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section with ProductHunt-inspired design -->
    <div class="bg-gray-50 dark:bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Browse by Category
                </h2>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @php
                $categories = \App\Models\Category::all();
                @endphp

                @foreach ($categories as $category)
                    @if($category)
                    <a
                        href="{{ route('deals.index', ['category' => $category->slug]) }}"
                        class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:border-orange-500 hover:shadow-md transition-all duration-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-orange-500"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-{{ str_replace('#', '', $category->color ?? 'gray') }}-100 dark:bg-{{ str_replace('#', '', $category->color ?? 'gray') }}-900/30">
                            <span class="text-xl font-bold text-{{ str_replace('#', '', $category->color ?? 'gray') }}-600 dark:text-{{ str_replace('#', '', $category->color ?? 'gray') }}-500">
                                {{ substr($category->name, 0, 1) }}
                            </span>
                        </div>
                        <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $category->deals()->published()->count() }} deals
                        </p>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA Section with ProductHunt-inspired design -->
    <div class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-orange-500 px-6 py-10 sm:px-12 sm:py-16 dark:bg-orange-900">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Have a deal to share?
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-orange-50">
                        Help the Laravel community discover great deals by submitting one today.
                        Your contribution could save developers time and money!
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a
                            href="{{ route('deals.submit') }}"
                            class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-orange-600 shadow-sm hover:bg-orange-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-colors duration-200"
                        >
                            Submit a Deal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
    </flux:main>
</x-layouts.public.index>
