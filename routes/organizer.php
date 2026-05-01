<?php

use App\Http\Controllers\Subscriber\OrganizerOnboardingController;
use App\Http\Controllers\Subscriber\InvitationController;
use App\Http\Controllers\Subscriber\CheckinController;
use App\Http\Controllers\Subscriber\AnalyticsController;
use App\Http\Controllers\Subscriber\OrganizerSubscriptionController;
use Illuminate\Support\Facades\Route;

// Onboarding Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [OrganizerOnboardingController::class, 'showRegistrationForm'])
        ->name('onboarding.register');
    Route::post('/register/send-otp', [OrganizerOnboardingController::class, 'sendOtp'])
        ->name('onboarding.send-otp');
    Route::post('/register/verify-otp', [OrganizerOnboardingController::class, 'verifyOtp'])
        ->name('onboarding.verify-otp');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Setup
    Route::get('/onboarding/profile-setup', [OrganizerOnboardingController::class, 'showProfileSetup'])
        ->name('onboarding.profile-setup');
    Route::post('/onboarding/profile-setup', [OrganizerOnboardingController::class, 'storeProfile'])
        ->name('onboarding.store-profile');

    // Needs Assessment
    Route::get('/onboarding/needs-assessment', [OrganizerOnboardingController::class, 'showNeedsAssessment'])
        ->name('onboarding.needs-assessment');
    Route::post('/onboarding/needs-assessment', [OrganizerOnboardingController::class, 'storeNeedsAssessment'])
        ->name('onboarding.store-needs-assessment');

    // Organizer Dashboard Routes
    Route::middleware(['subscriptions:check_limits'])->group(function () {
        // Invitations
        Route::prefix('invitations')->group(function () {
            Route::get('/{event}', [InvitationController::class, 'list'])
                ->name('invitations.list');
            Route::post('/{event}/bulk-import', [InvitationController::class, 'bulkImport'])
                ->name('invitations.bulk-import');
            Route::post('/{invitation}/resend', [InvitationController::class, 'resend'])
                ->name('invitations.resend');
            Route::post('/{event}/bulk-resend', [InvitationController::class, 'bulkResend'])
                ->name('invitations.bulk-resend');
            Route::get('/{event}/export-csv', [InvitationController::class, 'exportCsv'])
                ->name('invitations.export-csv');
            Route::post('/{invitation}/copy-link', [InvitationController::class, 'copyDirectLink'])
                ->name('invitations.copy-link');
        });

        // Check-in Routes
        Route::prefix('checkin')->group(function () {
            Route::get('/{eventSlug}', [CheckinController::class, 'showCheckinPage'])
                ->name('checkin.page');
            Route::post('/process-qr', [CheckinController::class, 'processQrScan'])
                ->name('checkin.process-qr');
            Route::get('/{eventId}/stats', [CheckinController::class, 'getCheckinStats'])
                ->name('checkin.stats');
            Route::get('/{eventId}/recent', [CheckinController::class, 'getRecentCheckins'])
                ->name('checkin.recent');
        });

        // Analytics Routes
        Route::prefix('analytics')->group(function () {
            Route::get('/company', [AnalyticsController::class, 'companyDashboard'])
                ->name('analytics.company');
            Route::get('/event/{event}', [AnalyticsController::class, 'eventDashboard'])
                ->name('analytics.event');
            Route::get('/event/{event}/report', [AnalyticsController::class, 'getAttendanceReport'])
                ->name('analytics.attendance-report');
            Route::get('/event/{event}/export-attendance', [AnalyticsController::class, 'exportAttendanceReport'])
                ->name('analytics.export-attendance');
            Route::get('/event/{event}/realtime', [AnalyticsController::class, 'getRealTimeStats'])
                ->name('analytics.realtime');
            Route::get('/event/{event}/invitations-chart', [AnalyticsController::class, 'getInvitationChartData'])
                ->name('analytics.invitations-chart');
            Route::get('/event/{event}/attendance-chart', [AnalyticsController::class, 'getAttendanceChartData'])
                ->name('analytics.attendance-chart');
        });
    });

    // Subscription Routes
    Route::prefix('subscription')->group(function () {
        Route::get('/', [OrganizerSubscriptionController::class, 'show'])
            ->name('subscription.show');
        Route::get('/upgrade', [OrganizerSubscriptionController::class, 'showUpgradePage'])
            ->name('subscription.show-upgrade');
        Route::post('/upgrade', [OrganizerSubscriptionController::class, 'upgradeToProPlan'])
            ->name('subscription.upgrade');
        Route::post('/process-payment', [OrganizerSubscriptionController::class, 'processPayment'])
            ->name('subscription.process-payment');
        Route::get('/paywall', [OrganizerSubscriptionController::class, 'showPaywall'])
            ->name('subscription.show-paywall');
        Route::get('/usage', [OrganizerSubscriptionController::class, 'getUsage'])
            ->name('subscription.usage');
    });

    // Organizer Dashboard
    Route::get('/', fn() => redirect()->route('filament.organizer.pages.dashboard'))
        ->name('organizer.dashboard');
});
