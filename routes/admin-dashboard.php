<?php

use App\Http\Controllers\Subscriber\AdminDashboardPageController;
use App\Http\Controllers\Subscriber\AdminProjectsPageController;
use App\Http\Controllers\Subscriber\AdminUsersPageController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardPageController::class)->name('dashboard');
    // Keep legacy admin users page under a distinct URI to avoid colliding with system admin /admin/users
    Route::get('/legacy-users', AdminUsersPageController::class)->name('users.index');
    Route::get('/projects', AdminProjectsPageController::class)->name('projects.index');
});
