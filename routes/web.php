<?php

use App\Livewire\AdminDashboard;
use App\Livewire\BrowsePlans;
use App\Livewire\SubscriptionManager;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    // User routes
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
    
    Route::get('/user/dashboard', UserDashboard::class)->name('user.dashboard');
    Route::get('/plans', BrowsePlans::class)->name('browse-plans');
    Route::get('/subscriptions', SubscriptionManager::class)->name('manage-subscriptions');
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    });
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
