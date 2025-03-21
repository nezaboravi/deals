<?php

use App\Models\Deal;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use function Livewire\Volt\mount;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

// Props
state(['deal' => null]);

// Form fields
state([
    'title' => '',
    'description' => '',
    'deal_link' => '',
    'category_id' => '',
    'expiry_date' => '',
    'image' => null,
    'currentImage' => null,
]);

// Component state
state(['formSubmitted' => false, 'submitting' => false]);

// Mount the component
mount(function (Deal $deal) {
    // Ensure the user can only edit their own deals
    if ($deal->user_id !== Auth::id()) {
        return redirect()->route('dashboard.deals.index')
            ->with('message', 'You can only edit your own deals.');
    }
    
    $this->deal = $deal;
    $this->title = $deal->title;
    $this->description = $deal->description;
    $this->deal_link = $deal->deal_link;
    $this->category_id = $deal->category_id;
    $this->expiry_date = $deal->expiry_date->format('Y-m-d');
    $this->currentImage = $deal->image_path;
});

// Validation rules
#[Rule('required|min:5|max:100')]
function title() {}

#[Rule('required|min:20|max:1000')]
function description() {}

#[Rule('required|url')]
function deal_link() {}

#[Rule('required|exists:categories,id')]
function category_id() {}

#[Rule('required|date')]
function expiry_date() {}

#[Rule('nullable|image|max:1024')]
function image() {}

// Get all categories for the dropdown
#[Computed]
function categories() {
    return Category::orderBy('name')->get();
}

// Update the deal
function updateDeal() {
    $this->submitting = true;
    
    $this->validate();
    
    // Process the image if a new one was uploaded
    $imagePath = $this->currentImage;
    if ($this->image) {
        // Delete the old image if it exists
        if ($this->currentImage) {
            Storage::disk('public')->delete($this->currentImage);
        }
        
        $imagePath = $this->image->store('deals', 'public');
    }
    
    // Update the deal
    $this->deal->update([
        'title' => $this->title,
        'description' => $this->description,
        'deal_link' => $this->deal_link,
        'category_id' => $this->category_id,
        'expiry_date' => $this->expiry_date,
        'image_path' => $imagePath,
    ]);
    
    $this->formSubmitted = true;
    $this->submitting = false;
    
    // Redirect back to the deals index
    return redirect()->route('dashboard.deals.index')
        ->with('message', 'Deal updated successfully.');
}

?>

<div>
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('dashboard.deals.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-500 dark:hover:text-indigo-400">
                &larr; Back to My Deals
            </a>
        </div>
        
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-6 border-b border-gray-200 pb-4 dark:border-gray-700">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Deal</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Update your deal information
                </p>
            </div>
            
            <form wire:submit="updateDeal">
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal Title</label>
                        <div class="mt-1">
                            <input 
                                type="text" 
                                id="title" 
                                wire:model="title" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm" 
                                placeholder="e.g., 50% off Laravel Forge Annual Plan"
                            >
                        </div>
                        @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
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
                        @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Deal Link -->
                    <div>
                        <label for="deal_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal Link</label>
                        <div class="mt-1">
                            <input 
                                type="url" 
                                id="deal_link" 
                                wire:model="deal_link" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm" 
                                placeholder="https://example.com/deal"
                            >
                        </div>
                        @error('deal_link') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
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
                        @error('category_id') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Expiry Date -->
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                        <div class="mt-1">
                            <input 
                                type="date" 
                                id="expiry_date" 
                                wire:model="expiry_date" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-500 sm:text-sm"
                            >
                        </div>
                        @error('expiry_date') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deal Image</label>
                        
                        @if ($currentImage)
                            <div class="mt-2 mb-4">
                                <div class="aspect-h-9 aspect-w-16 overflow-hidden rounded-md bg-gray-200 dark:bg-gray-700">
                                    <img src="{{ asset('storage/' . $currentImage) }}" alt="Current Image" class="h-full w-full object-cover object-center">
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Current image</p>
                            </div>
                        @endif
                        
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
                            Upload a new image to replace the current one. Recommended size: 1200x675px (16:9 ratio). Max 1MB.
                        </div>
                        @error('image') <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        
                        @if ($image)
                            <div class="mt-3">
                                <div class="aspect-h-9 aspect-w-16 overflow-hidden rounded-md bg-gray-200 dark:bg-gray-700">
                                    <img src="{{ $image->temporaryUrl() }}" alt="New Image Preview" class="h-full w-full object-cover object-center">
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">New image preview</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Status Information -->
                    <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/30">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400 dark:text-blue-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    @if ($deal->published_at)
                                        This deal is currently published and visible to users.
                                    @else
                                        This deal is pending review and not yet visible to users.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-indigo-500 dark:hover:bg-indigo-400"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="updateDeal">Update Deal</span>
                            <span wire:loading wire:target="updateDeal">
                                <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
