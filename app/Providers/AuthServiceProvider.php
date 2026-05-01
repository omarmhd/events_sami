<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\PublicEventRegistration;
use App\Models\Ticket;
use App\Policies\EventInvitationPolicy;
use App\Policies\EventPolicy;
use App\Policies\PublicEventRegistrationPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        EventInvitation::class => EventInvitationPolicy::class,
        PublicEventRegistration::class => PublicEventRegistrationPolicy::class,
        Ticket::class => TicketPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->role === 'super_admin' || $user->role === 'saas_admin') {
                return true;
            }

            return null;
        });
    }
}
