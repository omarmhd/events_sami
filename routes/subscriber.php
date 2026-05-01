<?php

use App\Http\Controllers\Subscriber\DashboardController;
use App\Http\Controllers\Subscriber\ProfileController;
use App\Http\Controllers\Subscriber\SettingsController;
use App\Http\Controllers\Subscriber\SubscriptionController;
use Illuminate\Support\Facades\Route;

// NOTE: Event CRUD routes (events.index, events.create, events.store, events.edit,
// events.update, events.destroy) are defined in web.php via Route::resource().
// This file only contains routes for the legacy /app prefix group.

Route::prefix('app')->middleware(['auth', 'subscriber'])->name('subscriber.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'details'])->name('subscriptions.details');

    Route::get('/settings', SettingsController::class)->name('settings');
});
