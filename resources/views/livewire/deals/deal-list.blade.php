<?php

use App\Models\Deal;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public.index')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $sort = 'popular';
    public string $filter = 'active';
    public int $perPage = 12;

    public function mount(): void
    {
        // Initialize from query parameters if available
        $this->category = request()->get('category', '');
        $this->sort = request()->get('sort', 'popular');
        $this->filter = request()->get('filter', 'active');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function getDealsProperty()
    {
        $query = Deal::query()->with(['category', 'user']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter
        if ($this->category) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        // Apply status filter
        if ($this->filter === 'active') {
            $query->active();
        } elseif ($this->filter === 'expired') {
            $query->where('expiry_date', '<', now()->format('Y-m-d'));
        }

        // Only show published deals
        $query->published();

        // Apply sorting
        if ($this->sort === 'popular') {
            $query->popular();
        } elseif ($this->sort === 'recent') {
            $query->recent();
        } elseif ($this->sort === 'ending-soon') {
            $query->where('expiry_date', '>=', now()->format('Y-m-d'))
                ->orderBy('expiry_date', 'asc');
        }

        // Featured deals always appear first
        $query->orderByDesc('is_featured');

        return $query->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return Category::all();
    }
}; ?>

<div>
    <div class="bg-white dark:bg-gray-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6 py-10 md:flex-row md:gap-4">
                <div class="w-full md:w-1/3">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Laravel Deals
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Discover the best tools and resources for Laravel developers</p>
                </div>
                <div class="w-full md:w-2/3">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                 aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="search"
                            class="block w-full rounded-full border-0 bg-white py-3 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-500 dark:focus:ring-orange-500 sm:text-sm sm:leading-6"
                            placeholder="Search for deals..."
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row">
            <!-- Filters sidebar -->
            <div class="w-full lg:w-1/4">
                <div
                    class="sticky top-6 space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:shadow-md transition-shadow duration-200 dark:border-gray-700 dark:bg-gray-800">
                    <!-- Categories filter -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Categories
                        </h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center">
                                <input
                                    id="category-all"
                                    name="category"
                                    type="radio"
                                    value=""
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="category-all"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">All
                                    Categories</label>
                            </div>

                            @foreach ($this->categories as $cat)
                                <div class="flex items-center">
                                    <input
                                        id="category-{{ $cat->id }}"
                                        name="category"
                                        type="radio"
                                        value="{{ $cat->slug }}"
                                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                    >
                                    <label for="category-{{ $cat->id }}"
                                           class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cat->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sort filter -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                            </svg>
                            Sort By
                        </h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center">
                                <input
                                    id="sort-popular"
                                    wire:model.live="sort"
                                    type="radio"
                                    value="popular"
                                    class="h-4 w-4 border-gray-300 text-orange-600 focus:ring-orange-600 dark:border-gray-600 dark:text-orange-500 dark:focus:ring-orange-500"
                                >
                                <label for="sort-popular"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Most
                                    Popular</label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="sort-recent"
                                    wire:model.live="sort"
                                    type="radio"
                                    value="recent"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="sort-recent"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Most
                                    Recent</label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="sort-ending"
                                    wire:model.live="sort"
                                    type="radio"
                                    value="ending-soon"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="sort-ending"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Ending
                                    Soon</label>
                            </div>
                        </div>
                    </div>

                    <!-- Status filter -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Status
                        </h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center">
                                <input
                                    id="filter-active"
                                    wire:model.live="filter"
                                    type="radio"
                                    value="active"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="filter-active"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Active
                                    Deals</label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="filter-expired"
                                    wire:model.live="filter"
                                    type="radio"
                                    value="expired"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="filter-expired"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Expired
                                    Deals</label>
                            </div>
                            <div class="flex items-center">
                                <input
                                    id="filter-all"
                                    wire:model.live="filter"
                                    type="radio"
                                    value="all"
                                    class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                >
                                <label for="filter-all"
                                       class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">All
                                    Deals</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deals grid -->
            <div class="w-full lg:w-3/4">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($this->deals as $deal)
                        <div
                            class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            @if ($deal->is_featured)
                                <div
                                    class="absolute left-0 top-0 z-10 bg-amber-500 px-3 py-1.5 text-xs font-medium text-white">
                                    Featured
                                </div>
                            @endif

                            <div class="aspect-h-1 aspect-w-1 bg-gray-200 group-hover:opacity-75 dark:bg-gray-700">
                                <img
                                    src="https://picsum.photos/seed/{{ $deal->id }}/300/300"
                                    alt="{{ $deal->title }}"
                                    class="h-full w-full object-cover object-center"
                                >
                            </div>

                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="inline-flex items-center rounded-md bg-{{ str_replace('#', '', $deal->category_color) }}-50 px-2 py-1 text-xs font-medium text-{{ str_replace('#', '', $deal->category_color) }}-700 ring-1 ring-inset ring-{{ str_replace('#', '', $deal->category_color) }}-600/20 dark:bg-{{ str_replace('#', '', $deal->category_color) }}-900 dark:text-{{ str_replace('#', '', $deal->category_color) }}-300 dark:ring-{{ str_replace('#', '', $deal->category_color) }}-800">
                                        {{ $deal->category_name }}
                                    </span>


                                    <livewire:deals.vote-button :deal="$deal" :key="'vote-'.$deal->id"/>
                                </div>

                                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('deals.show', $deal) }}">
                                        {{ $deal->title }}
                                    </a>
                                </h3>

                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-3">
                                    {{ $deal->description }}
                                </p>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Expires: {{ $deal->expiry_date->format('M d, Y') }}
                                    </div>

                                    <a
                                        href="{{ $deal->deal_link }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                                    >
                                        Get Deal
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No deals found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter
                                criteria.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $this->deals->links(data: ['scrollTo' => false]) }}
                </div>
            </div>
        </div>
    </div>
</div>
