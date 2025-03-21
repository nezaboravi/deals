<?php

use App\Models\Deal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use function Livewire\Volt\computed;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses([WithPagination::class]);

// Component state
state(['search' => '', 'status' => 'all']);

// Reset pagination when filters change
function updatedSearch()
{
    //  $reset;
}

$count = computed(function () {
    return Deal::count();
});
function updatedStatus()
{
    //  reset();
}

// Get user's deals with filtering
$deals = \Livewire\Volt\computed( function() {
    $user = Auth::user();

    $query = Deal::where('user_id', $user->id)
        ->with('category')
        ->latest();

// Apply search filter
if ($this->search) {
    $query->where(function ($q) {
        $q->where('title', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%');
    });
}

// Apply status filter
if ($this->status === 'published') {
    $query->whereNotNull('published_at');
} elseif ($this->status === 'pending') {
    $query->whereNull('published_at');
} elseif ($this->status === 'expired') {
    $query->where('expiry_date', '<', now()->format('Y-m-d'));
}

return $query->paginate(10);
}) ;

// Delete a deal
function deleteDeal($dealId)
{
    $deal = Deal::where('id', $dealId)
        ->where('user_id', Auth::id())
        ->first();

    if ($deal) {
        $deal->delete();
        session()->flash('message', 'Deal deleted successfully.');
    }
}

?>

<div>
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">My Deals</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                Manage your submitted deals {{ $this->count }}
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a
                href="{{ route('deals.submit') }}"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
            >
                Submit a new deal
            </a>
        </div>
    </div>

    <!-- Session Message -->
    @if (session('message'))
        <div class="mt-4 rounded-md bg-green-50 p-4 dark:bg-green-900/30">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20" fill="currentColor"
                         aria-hidden="true">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6">
        <div class="sm:flex sm:items-center">
            <!-- Search -->
            <div class="w-full sm:max-w-xs">
                <label for="search" class="sr-only">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        id="search"
                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-500 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                        placeholder="Search deals..."
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div class="mt-4 sm:ml-4 sm:mt-0">
                <label for="status" class="sr-only">Status</label>
                <select
                    wire:model.live="status"
                    id="status"
                    class="block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                >
                    <option value="all">All Deals</option>
                    <option value="published">Published</option>
                    <option value="pending">Pending Review</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <!-- Deals Table -->
        <div class="mt-6 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">
                                    Title
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                    Category
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                    Votes
                                </th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                    Expiry Date
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                            @forelse ($this->deals as $deal)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img
                                                    src="https://picsum.photos/seed/{{ $deal->id }}/40/40"
                                                    alt="{{ $deal->title }}"
                                                    class="h-10 w-10 rounded-md object-cover"
                                                >
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('deals.show', $deal) }}"
                                                   class="hover:text-indigo-600 dark:hover:text-indigo-500">
                                                    {{ $deal->title }}
                                                </a>
                                                @if ($deal->is_featured)
                                                    <span
                                                        class="ml-2 inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-500 dark:ring-amber-800">
                                                            Featured
                                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span
                                                class="inline-flex items-center rounded-md bg-{{ str_replace('#', '', $deal->category_color) }}-50 px-2 py-1 text-xs font-medium text-{{ str_replace('#', '', $deal->category_color) }}-700 ring-1 ring-inset ring-{{ str_replace('#', '', $deal->category_color) }}-600/20 dark:bg-{{ str_replace('#', '', $deal->category_color) }}-900/30 dark:text-{{ str_replace('#', '', $deal->category_color) }}-500 dark:ring-{{ str_replace('#', '', $deal->category_color) }}-800">
                                                {{ $deal->category_name }}
                                            </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if ($deal->published_at)
                                            <span
                                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/30 dark:text-green-500 dark:ring-green-800">
                                                    Published
                                                </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-900/30 dark:text-yellow-500 dark:ring-yellow-800">
                                                    Pending Review
                                                </span>
                                        @endif

                                        @if ($deal->expiry_date < now())
                                            <span
                                                class="ml-1 inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-500 dark:ring-red-800">
                                                    Expired
                                                </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $deal->vote_count }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $deal->expiry_date->format('M d, Y') }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-end space-x-2">
                                            <a
                                                href="{{ route('dashboard.deals.edit', $deal) }}"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-500 dark:hover:text-indigo-400"
                                            >
                                                Edit
                                            </a>
                                            <button
                                                wire:click="deleteDeal({{ $deal->id }})"
                                                wire:confirm="Are you sure you want to delete this deal? This action cannot be undone."
                                                class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No deals
                                                found</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by
                                                submitting a new deal.</p>
                                            <div class="mt-6">
                                                <a
                                                    href="{{ route('deals.submit') }}"
                                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                                                >
                                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20"
                                                         fill="currentColor" aria-hidden="true">
                                                        <path
                                                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                                                    </svg>
                                                    Submit a deal
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $this->deals()->links() }}
        </div>
    </div>
</div>
