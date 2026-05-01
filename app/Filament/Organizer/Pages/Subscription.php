<?php

namespace App\Filament\Organizer\Pages;

use App\Models\Company;
use App\Models\Event;
use App\Services\SubscriptionManagementService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Subscription extends Page
{
    protected static ?string $title = 'الاشتراك';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'الاشتراك';
    protected static string $view = 'filament.pages.subscription';

    public ?Company $company = null;
    public ?string $remainingTrialDays = null;
    public array $currentLimits = [];
    public array $currentUsage = [];

    public function mount(): void
    {
        $this->company = Auth::user()->company;
        $subscriptionService = app(SubscriptionManagementService::class);

        $subscription = $this->company->activeSubscription;

        if ($subscription) {
            $this->remainingTrialDays = $subscriptionService->getRemainingTrialDays($subscription);
            $this->currentLimits = $subscriptionService->getUsageLimits($subscription);

            // Calculate usage
            $year = date('Y');
            $this->currentUsage = [
                'events' => Event::where('company_id', $this->company->id)
                    ->whereYear('created_at', $year)
                    ->count(),
                'invites' => $this->company->invitations()->count(),
            ];
        }
    }

    public function getHeading(): string
    {
        return 'إدارة الاشتراك والترقية';
    }

    public function upgradeToProPlan(): void
    {
        // Redirect to upgrade page
        redirect()->route('subscription.upgrade', ['plan' => 'professional']);
    }
}
