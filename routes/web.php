<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Home page with featured deals
Route::get('/', function () {
    return view('home');
})->name('home');

// Deals routes
Route::prefix('deals')->name('deals.')->group(function () {
    // List all deals
    Volt::route('/', 'deals.deal-list')->name('index');
    
    // Submit a new deal form
    Volt::route('/submit', 'deals.deal-form')
        ->middleware(['auth'])
        ->name('submit');
    
    // Show a specific deal
    Volt::route('/{deal:slug}', 'deals.deal-detail')
        ->name('show');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // User deals management
    Volt::route('dashboard/deals', 'dashboard.deals.index')->name('dashboard.deals.index');
    Volt::route('dashboard/deals/{deal}/edit', 'dashboard.deals.edit')->name('dashboard.deals.edit');
});

// Settings routes
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
