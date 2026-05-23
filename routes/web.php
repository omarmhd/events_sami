<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Subscriber\AccountController;
use App\Http\Controllers\Subscriber\BillingController;
use App\Http\Controllers\Subscriber\EventManagementController;
use App\Http\Controllers\Subscriber\EmailSettingsController;
use App\Http\Controllers\Subscriber\OnboardingController;
use App\Http\Controllers\Subscriber\PublicRegistrationController;
use App\Http\Controllers\Subscriber\QrScanController;
use App\Http\Controllers\Subscriber\RegistrationFormController;
use App\Http\Controllers\Subscriber\SystemAdminController;
use App\Http\Controllers\Subscriber\TeamController;
use App\Http\Controllers\Subscriber\AdminController;
use App\Http\Controllers\Subscriber\AttendanceManagement;
use App\Http\Controllers\Subscriber\EventInvitationController;
use App\Http\Controllers\Subscriber\FeatureController;
use App\Http\Controllers\Subscriber\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function (string $locale) {
    $supportedLocales = ['ar', 'en'];

    if (!in_array($locale, $supportedLocales, true)) {
        $locale = config('app.locale', 'en');
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return ($user->is_system_admin || in_array($user->role, ['system_admin', 'super_admin', 'saas_admin'], true))
            ? redirect()->route('system.dashboard')
            : redirect()->route('dashboard.index');
    }

    return redirect()->route('onboarding.otp.form');
});

Route::view('/platform/maana', 'home.platform-about')->name('platform.about');

Route::post('save', [HomeController::class, 'save'])->name('save');
Route::get('check-email', [HomeController::class, 'check_email'])->name('check_email');

Route::get('/onboarding/login', [OnboardingController::class, 'showOtpForm'])->name('onboarding.otp.form');
Route::post('/onboarding/send-otp', [OnboardingController::class, 'sendOtp'])
    ->middleware('throttle:otp-send')
    ->name('onboarding.otp.send');
Route::get('/onboarding/verify', [OnboardingController::class, 'showVerifyForm'])->name('onboarding.verify.form');
Route::post('/onboarding/verify', [OnboardingController::class, 'verifyOtp'])
    ->middleware('throttle:otp-verify')
    ->name('onboarding.verify.submit');

Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding/profile', [OnboardingController::class, 'showProfileForm'])->name('onboarding.profile');
    Route::post('/onboarding/profile', [OnboardingController::class, 'saveProfile'])->name('onboarding.profile.save');

    // Plans step — shown after profile setup, before dashboard.
    // Accessible while on trial (subscription.status middleware exempts 'onboarding.' prefix).
    Route::get('/onboarding/plans', [OnboardingController::class, 'showPlansStep'])->name('onboarding.plans');

    // ── Subscription expired / suspended page ─────────────────────────────────
    // Defined here (web.php, global domain) so the CheckSubscriptionStatus
    // middleware can always resolve this route regardless of which subdomain
    // the subscriber is on. The organizer.php copy under the tenant prefix is
    // a duplicate for intra-tenant navigation; this one is the canonical target.
    Route::get('/subscription/expired', [\App\Http\Controllers\Subscriber\OrganizerSubscriptionController::class, 'showExpiredPage'])
        ->name('subscription.expired');

    // ── Account Settings ──────────────────────────────────────────────────────
    // Editing account / security / company info must remain accessible even
    // when the subscription is expired or suspended, so we register these
    // outside the `subscription.status` middleware block.
    Route::get('account', [AccountController::class, 'index'])->name('account.index');
    Route::patch('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('account/email', [AccountController::class, 'updateEmail'])->name('account.email.update');
    Route::patch('account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::patch('account/company', [AccountController::class, 'updateCompany'])->name('account.company.update');
});


// ─── تسجيل الدخول / الخروج ───────────────────────────────────────────────────
Route::get('/login',        [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/admin/login',  [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/login',       [LoginController::class, 'login']);
Route::post('/logout',      [LoginController::class, 'logout'])->name('logout');

// ─── إنشاء حساب جديد ─────────────────────────────────────────────────────────
Route::get('/register',     [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register',    [RegisterController::class, 'register']);

// ─── استعادة كلمة المرور ──────────────────────────────────────────────────────
Route::get('/forgot-password',        [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth', 'company', 'organizer.only', 'subscription.status'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard.index');

    Route::view('qr-code', 'subscriber.panel.qr')->name('qr');
    Route::post('scan/check-in', [QrScanController::class, 'checkin'])->name('scan.checkin');
    Route::get('checked_in/{id?}', [AdminController::class, 'checked_in'])->name('checked_in');
    Route::view('register-attendance', 'subscriber.panel.register_attendance')->name('register_attendance');
    Route::get('search-on-ticket', [AdminController::class, 'search_on_ticket'])->name('search_on_ticket');
    Route::get('statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('invitations/export', [AdminController::class, 'export'])->name('invitations.export');

    Route::get('emps', [AdminController::class, 'all_emps'])->name('emps');
    Route::post('resendTickets', [HomeController::class, 'resendTickets'])->name('resendTickets');

    Route::resource('events', EventManagementController::class)->except(['show']);

    // ── Feature: Registration Forms ───────────────────────────────────────────
    // index() is always accessible (shows lock UI + upgrade CTA when disabled).
    // create/store are gated at middleware level; edit/update/destroy only work
    // on forms the company already owns, so no extra middleware needed there.
    Route::get('registration-forms', [RegistrationFormController::class, 'index'])->name('registration-forms.index');
    Route::middleware('feature:registration_forms')->group(function () {
        Route::get('registration-forms/create', [RegistrationFormController::class, 'create'])->name('registration-forms.create');
        Route::post('registration-forms', [RegistrationFormController::class, 'store'])->name('registration-forms.store');
    });
    Route::get('registration-forms/{registrationForm}/edit', [RegistrationFormController::class, 'edit'])->name('registration-forms.edit');
    Route::put('registration-forms/{registrationForm}', [RegistrationFormController::class, 'update'])->name('registration-forms.update');
    Route::patch('registration-forms/{registrationForm}', [RegistrationFormController::class, 'update']);
    Route::delete('registration-forms/{registrationForm}', [RegistrationFormController::class, 'destroy'])->name('registration-forms.destroy');

    Route::get('events/{event}/invitations', [InvitationController::class, 'list'])
        ->name('events.invitations.index');
    Route::get('events/{event}/invitations/export-csv', [InvitationController::class, 'exportCsv'])
        ->name('events.invitations.export_csv');
    // bulk-import and bulk-resend moved to feature middleware groups below
    Route::post('events/invitations/{invitation}/resend', [InvitationController::class, 'resend'])
        ->name('events.invitations.resend');
    Route::post('events/invitations/{invitation}/copy-link', [InvitationController::class, 'copyDirectLink'])
        ->name('events.invitations.copy_link');

    Route::resource('invitations', EventInvitationController::class)->except(['show']);
    Route::post('/invitations/resend', [EventInvitationController::class, 'resend'])->name('invitations.resend');
    Route::post('/invitations/bulk-delete-selected', [EventInvitationController::class, 'bulkDestroySelected'])->name('invitations.bulk_destroy_selected');

    // ── Feature: Bulk Resend ──────────────────────────────────────────────────
    Route::middleware('feature:bulk_resend')->group(function () {
        Route::post('/invitations/resend-all', [EventInvitationController::class, 'resendAll'])->name('invitations.resend_all');
        Route::post('/invitations/bulk-resend-selected', [EventInvitationController::class, 'bulkResendSelected'])->name('invitations.bulk_resend_selected');
        Route::post('events/{event}/invitations/bulk-resend', [InvitationController::class, 'bulkResend'])->name('events.invitations.bulk_resend');
    });

    // ── Feature: CSV / Excel Import ──────────────────────────────────────────
    Route::middleware('feature:csv_import')->group(function () {
        Route::post('/invitations/import-csv',   [EventInvitationController::class, 'importCsv'])->name('invitations.import_csv');
        Route::post('/invitations/import-excel', [EventInvitationController::class, 'importExcel'])->name('invitations.import_excel');
        Route::post('events/{event}/invitations/bulk-import', [InvitationController::class, 'bulkImport'])->name('events.invitations.bulk_import');
    });

    // Template download is always accessible (no feature gate — it's just a blank file).
    Route::get('/invitations/excel-template', [EventInvitationController::class, 'downloadExcelTemplate'])->name('invitations.excel_template');

    Route::get('attendance-list', [AttendanceManagement::class, 'index'])->name('attendance_list');
    Route::post('attendance/checked_in', [AttendanceManagement::class, 'checked_in'])->name('attendance.checked_in');

    Route::get('events/{event}/registrations', [PublicRegistrationController::class, 'reviewQueue'])->name('events.registrations.index');
    Route::post('events/{event}/registrations/{registration}/review', [PublicRegistrationController::class, 'reviewDecision'])->name('events.registrations.review');
    Route::delete('events/{event}/registrations/{registration}', [PublicRegistrationController::class, 'destroy'])->name('events.registrations.destroy');

    Route::get('billing/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
    Route::post('billing/assess-needs', [BillingController::class, 'assessNeeds'])->name('billing.assess');
    Route::post('billing/switch-plan', [BillingController::class, 'switchPlan'])->name('billing.switch');
    Route::post('billing/contact-request', [BillingController::class, 'submitContactRequest'])->name('billing.contact-request');
    Route::post('billing/renewal-request', [BillingController::class, 'requestRenewal'])->name('billing.renewal-request');

    // ── Feature: Visual Identity / Branding ───────────────────────────────────
    // The index page is always accessible (shows read-only platform branding
    // when feature is off). Only the save endpoints are hard-blocked by middleware
    // to prevent form-POST bypasses; the controller also guards them in code.
    Route::get('email-settings', [EmailSettingsController::class, 'index'])->name('email-settings.index');
    Route::middleware('feature:visual_identity')->group(function () {
        Route::post('email-settings/branding', [EmailSettingsController::class, 'saveBranding'])->name('email-settings.branding');
        Route::post('email-settings/send-test', [EmailSettingsController::class, 'sendTest'])->name('email-settings.send_test');
    });
    // Clear-image routes are always registered — the controller guards access internally.
    Route::post('email-settings/branding/clear-logo', [EmailSettingsController::class, 'clearLogo'])->name('email-settings.clearLogo');
    Route::post('email-settings/branding/clear-header', [EmailSettingsController::class, 'clearHeaderImage'])->name('email-settings.clearHeader');
    Route::post('email-settings/template', [EmailSettingsController::class, 'saveTemplate'])->name('email-settings.template');
    Route::post('email-settings/preview', [EmailSettingsController::class, 'preview'])->name('email-settings.preview');

    // ── Feature: Teams ────────────────────────────────────────────────────────
    Route::middleware('feature:teams')->group(function () {
        Route::get('team', [TeamController::class, 'index'])->name('team.index');
        Route::post('team', [TeamController::class, 'store'])->name('team.store');
        Route::patch('team/{member}/role', [TeamController::class, 'updateRole'])->name('team.role.update');
        Route::delete('team/{member}', [TeamController::class, 'destroy'])->name('team.destroy');
    });

    // ── Feature unavailable landing page ──────────────────────────────────────
    Route::get('feature/unavailable', [FeatureController::class, 'unavailable'])->name('feature.unavailable');

    Route::post('impersonation/leave', [SystemAdminController::class, 'leaveImpersonation'])
        ->name('system.impersonation.leave');
});

Route::middleware(['auth', 'system.admin'])->prefix('admin')->name('system.')->group(function () {
    Route::get('/', [SystemAdminController::class, 'dashboard'])->name('dashboard');

    // ── Companies ──────────────────────────────────────────────────────────────
    Route::get('/companies', [SystemAdminController::class, 'companies'])->name('companies');
    Route::post('/companies', [SystemAdminController::class, 'storeCompany'])->name('companies.store');
    Route::patch('/companies/{company}', [SystemAdminController::class, 'updateCompany'])->name('companies.update');
    Route::patch('/companies/{company}/status', [SystemAdminController::class, 'updateCompanyStatus'])->name('companies.status');
    Route::patch('/companies/{company}/subscription', [SystemAdminController::class, 'forceSubscriptionPlan'])->name('companies.subscription');
    Route::patch('/companies/{company}/terminate-subscription', [SystemAdminController::class, 'terminateSubscription'])->name('companies.terminate-subscription');
    Route::post('/companies/{company}/impersonate', [SystemAdminController::class, 'impersonate'])->name('companies.impersonate');

    // ── Subscriptions ──────────────────────────────────────────────────────────
    Route::get('/subscriptions', [SystemAdminController::class, 'subscriptions'])->name('subscriptions');

    // ── System Users ───────────────────────────────────────────────────────────
    Route::get('/users', [SystemAdminController::class, 'users'])->name('users');
    Route::post('/users', [SystemAdminController::class, 'storeSystemUser'])->name('users.store');
    Route::patch('/users/{user}', [SystemAdminController::class, 'updateSystemUser'])->name('users.update');

    // ── Plan Management ────────────────────────────────────────────────────────
    Route::get('/plans', [SystemAdminController::class, 'plans'])->name('plans');
    Route::post('/plans', [SystemAdminController::class, 'storePlan'])->name('plans.store');
    Route::patch('/plans/{plan}', [SystemAdminController::class, 'updatePlan'])->name('plans.update');
    Route::delete('/plans/{plan}', [SystemAdminController::class, 'destroyPlan'])->name('plans.destroy');

    // ── Renewal / Contact Requests ────────────────────────────────────────────
    Route::get('/renewal-requests', [SystemAdminController::class, 'renewalRequests'])->name('renewal-requests');
    Route::patch('/renewal-requests/{billingRequest}/contacted', [SystemAdminController::class, 'markRenewalContacted'])->name('renewal-requests.contacted');
    Route::patch('/renewal-requests/{billingRequest}/dismiss', [SystemAdminController::class, 'dismissRenewalRequest'])->name('renewal-requests.dismiss');

    // ── Invoice Management ─────────────────────────────────────────────────────
    Route::get('/invoices', [SystemAdminController::class, 'invoices'])->name('invoices');
    Route::post('/invoices', [SystemAdminController::class, 'storeInvoice'])->name('invoices.store');
    Route::patch('/invoices/{invoice}/mark-paid', [SystemAdminController::class, 'markInvoicePaid'])->name('invoices.mark_paid');
    Route::delete('/invoices/{invoice}', [SystemAdminController::class, 'destroyInvoice'])->name('invoices.destroy');

    // ── System Settings ────────────────────────────────────────────────────────
    Route::get('/settings', [SystemAdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SystemAdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/clear-logo', [SystemAdminController::class, 'clearLogo'])->name('settings.clear-logo');
});

Route::middleware(['auth', 'system.admin'])->prefix('system-admin')->group(function () {
    Route::get('/', fn () => redirect()->route('system.dashboard'));
    Route::get('/{any}', fn () => redirect()->route('system.dashboard'))->where('any', '.*');
});

require __DIR__ . '/front.php';
require __DIR__ . '/subscriber.php';
require __DIR__ . '/admin-dashboard.php';

