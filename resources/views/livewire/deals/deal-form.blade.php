<?php

use App\Models\Category;
use App\Models\Deal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use function Livewire\Volt\computed;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

// Form fields with validation rules
state([
    'title' => '',
    'description' => '',
    'deal_link' => '',
    'category_id' => '',
    'expiry_date' => '',
    'image' => null,
    'submitter_email' => '',
]);

// Component state
state(['formSubmitted' => false, 'submitting' => false]);

// Get all categories for the dropdown
$categories = computed(function () {
    return Category::orderBy('name')->get();
});

// Submit the deal
$submitDeal = function()
{
    $submitting = true;

    $this->validate();

    // Generate a slug from the title
    $slug = Str::slug($this->title);

    // Check if the slug already exists, if so, append a random string
    if (Deal::where('slug', $slug)->exists()) {
        $slug = $slug . '-' . Str::random(5);
    }

    // Process the image if uploaded
    $imagePath = null;
    if ($this->image) {
        $imagePath = $this->image->store('deals', 'public');
    }

    // Create the deal
    $deal = Deal::create([
        'title' => $this->title,
        'slug' => $slug,
        'description' => $this->description,
        'deal_link' => $this->deal_link,
        'category_id' => $this->category_id,
        'expiry_date' => $this->expiry_date,
        'image_path' => $imagePath,
        'user_id' => Auth::id(),
        'submitter_email' => $this->submitter_email,
        'verification_token' => Str::random(32),
        'published_at' => null, // Will be published after review
    ]);

    // Reset the form
    $this->reset(['title', 'description', 'deal_link', 'category_id', 'expiry_date', 'image', 'submitter_email']);

    $this->formSubmitted = true;
    $this->submitting = false;
}

?>

<div>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-6 border-b border-gray-200 pb-4 dark:border-gray-700">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Submit a Deal</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Share a great Laravel deal with the community. All submissions are reviewed before publishing.
                </p>
            </div>

            @if ($formSubmitted)
                <div class="rounded-md bg-green-50 p-4 dark:bg-green-900/30">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400 dark:text-green-500" viewBox="0 0 20 20"
                                 fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-400">Deal submitted
                                successfully</h3>
                            <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                <p>Thank you for your submission! Our team will review your deal and publish it
                                    soon.</p>
                            </div>
                            <div class="mt-4">
                                <div class="-mx-2 -my-1.5 flex">
                                    <button
                                        wire:click="$set('formSubmitted', false)"
                                        type="button"
                                        class="rounded-md bg-green-50 px-2 py-1.5 text-sm font-medium text-green-800 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 dark:bg-green-900/40 dark:text-green-400 dark:hover:bg-green-900/60 dark:focus:ring-green-500 dark:focus:ring-offset-gray-800"
                                    >
                                        Submit another deal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <form wire:submit="submitDeal">
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal
                                Title</label>
                            <div class="mt-1">
                                <input
                                    type="text"
                                    id="title"
                                    wire:model="title"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                    placeholder="e.g., 50% off Laravel Forge Annual Plan"
                                >
                            </div>
                            @error('title') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <div class="mt-1">
                                <textarea
                                    id="description"
                                    wire:model="description"
                                    rows="5"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                    placeholder="Provide details about the deal, including what it offers and why it's valuable."
                                ></textarea>
                            </div>
                            @error('description') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Deal Link -->
                        <div>
                            <label for="deal_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal
                                Link</label>
                            <div class="mt-1">
                                <input
                                    type="url"
                                    id="deal_link"
                                    wire:model="deal_link"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                    placeholder="https://example.com/deal"
                                >
                            </div>
                            @error('deal_link') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <div class="mt-1">
                                <select
                                    id="category_id"
                                    wire:model="category_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                >
                                    <option value="">Select a category</option>
                                    @foreach ($this->categories as $category)
                                        @if($category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry
                                Date</label>
                            <div class="mt-1">
                                <input
                                    type="date"
                                    id="expiry_date"
                                    wire:model="expiry_date"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                >
                            </div>
                            @error('expiry_date') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal
                                Image (Optional)</label>
                            <div class="mt-1">
                                <input
                                    type="file"
                                    id="image"
                                    wire:model="image"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:py-2 file:px-4 file:text-sm file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/20 dark:file:text-indigo-500 dark:hover:file:bg-indigo-900/30"
                                >
                            </div>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Recommended size: 1200x675px (16:9 ratio). Max 1MB.
                            </div>
                            @error('image') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror

                            @if ($image)
                                <div class="mt-3">
                                    <div
                                        class="aspect-h-9 aspect-w-16 overflow-hidden rounded-md bg-gray-200 dark:bg-gray-700">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                             class="h-full w-full object-cover object-center">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Submitter Email -->
                        <div>
                            <label for="submitter_email"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Email</label>
                            <div class="mt-1">
                                <input
                                    type="email"
                                    id="submitter_email"
                                    wire:model="submitter_email"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                                    placeholder="you@example.com"
                                    value="{{ Auth::user()?->email ?? '' }}"
                                >
                            </div>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                We'll notify you when your deal is published.
                            </div>
                            @error('submitter_email') <p
                                class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-indigo-500 dark:hover:bg-indigo-400"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="submitDeal">Submit Deal</span>
                                <span wire:loading wire:target="submitDeal">
                                    <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
