<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'phone',
        'subdomain',
        'status',
        'custom_domain',
        'annual_events_estimate',
        'trial_started_at',
        'trial_ends_at',
        'onboarding_completed_at',
        'billing_email',
        'timezone',
        'current_plan_code',
        'settings',
        'owner_user_id',
    ];

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
        'settings' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organizationUsers()
    {
        return $this->hasMany(User::class, 'organization_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function registrationForms()
    {
        return $this->hasMany(RegistrationForm::class);
    }

    public function organizationEvents()
    {
        return $this->hasMany(Event::class, 'organization_id');
    }

    public function invitations()
    {
        return $this->hasMany(EventInvitation::class);
    }

    public function organizationInvitations()
    {
        return $this->hasMany(EventInvitation::class, 'organization_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'organization_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function branding()
    {
        return $this->hasOne(CompanyBranding::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function latestSubscription()
    {
        return $this->hasOne(CompanySubscription::class)->latestOfMany();
    }

    public function activeSubscription()
    {
        // Prefer paid (active/past_due) over trial — a paid plan should always win,
        // even if a stale trial row exists from before the upgrade.
        $paid = $this->subscriptions()
            ->whereIn('status', ['active', 'past_due'])
            ->latest('id')
            ->first();

        return $paid ?? $this->subscriptions()
            ->where('status', 'trial')
            ->latest('id')
            ->first();
    }

    public function needsAssessment()
    {
        return $this->hasOne(SubscriptionNeedsAssessment::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function featureAccess()
    {
        return $this->hasMany(\App\Models\FeatureAccess::class);
    }
}
