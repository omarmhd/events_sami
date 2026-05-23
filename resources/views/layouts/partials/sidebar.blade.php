@php
    $user    = auth()->user();
    $company = $user?->company;

    // Always derive plan state from the active CompanySubscription — NOT from company columns,
    // which can become stale (e.g. trial_ends_at remains set even after upgrading to a paid plan).
    $activeSubscription = $company ? $company->activeSubscription() : null;
    $subStatus          = optional($activeSubscription)->status ?? 'trial';
    $subPlanCode        = strtoupper(optional(optional($activeSubscription)->plan)->code ?? $company?->current_plan_code ?? 'trial');
    $currentPlan        = $subPlanCode;
    $currentPlanLabel   = $currentPlan === 'TRIAL' ? __('ui.dashboard.trial_short') : $currentPlan;

    $isTrial  = $subStatus === 'trial';
    $isActive = $subStatus === 'active';

    // Trial countdown only when subscription is genuinely on trial.
    $trialEndsAt = null;
    $daysLeft    = null;
    $pct         = 0;

    if ($isTrial && optional($activeSubscription)->trial_ends_at) {
        $trialEndsAt = $activeSubscription->trial_ends_at->format('Y-m-d');
        $daysLeft    = max(0, now()->diffInDays($activeSubscription->trial_ends_at, false));
        $totalDays   = max(1, (int) config('subscription.trial.days', 15));
        $pct         = max(0, min(100, round(($daysLeft / $totalDays) * 100)));
    }
@endphp

<div class="sidebar-wrapper" role="navigation" aria-label="{{ __('ui.sidebar.core') }}">

    {{-- ── Brand / Logo ─────────────────────────────────────────── --}}
    {{-- The platform logo always comes from the admin panel (SystemSetting).
         Tenant branding (CompanyBranding) is ONLY used for email templates. --}}
    <a href="{{ route('dashboard.index') }}" class="sidebar-brand-link" aria-label="{{ \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')) }}">
        <div class="sidebar-brand-area">
            {{-- Use the centralized platform logo component (handles image + text fallback) --}}
            <x-platform-logo size="md" theme="light" class="sidebar-platform-logo" />
        </div>
    </a>

    {{-- ── Navigation ───────────────────────────────────────────── --}}
    <div class="sidebar-nav" role="list">

        <span class="sidebar-section-label">{{ __('ui.sidebar.core') }}</span>

        <a href="{{ route('dashboard.index') }}"
           class="nav-link-custom {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-chart-pie nav-link-icon"></i>
            <span>{{ __('ui.sidebar.dashboard') }}</span>
        </a>

        <a href="{{ route('events.index') }}"
           class="nav-link-custom {{ request()->routeIs('events.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-calendar-alt nav-link-icon"></i>
            <span>{{ __('ui.sidebar.events') }}</span>
        </a>

        <a href="{{ route('registration-forms.index') }}"
           class="nav-link-custom {{ request()->routeIs('registration-forms.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-rectangle-list nav-link-icon"></i>
            <span>{{ __('ui.sidebar.forms') }}</span>
        </a>

        <a href="{{ route('invitations.index') }}"
           class="nav-link-custom {{ request()->routeIs('invitations.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-paper-plane nav-link-icon"></i>
            <span>{{ __('ui.sidebar.invitations') }}</span>
        </a>

        <span class="sidebar-section-label mt-2">{{ __('ui.sidebar.operations') }}</span>

        <a href="{{ route('qr') }}"
           class="nav-link-custom {{ request()->routeIs('qr') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-qrcode nav-link-icon"></i>
            <span>{{ __('ui.sidebar.qr_scanner') }}</span>
        </a>

        <a href="{{ route('attendance_list') }}"
           class="nav-link-custom {{ request()->routeIs('attendance_list') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-clipboard-user nav-link-icon"></i>
            <span>{{ __('ui.sidebar.attendance') }}</span>
        </a>

        <a href="{{ route('statistics') }}"
           class="nav-link-custom {{ request()->routeIs('statistics') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-chart-column nav-link-icon"></i>
            <span>{{ __('ui.sidebar.analytics') }}</span>
        </a>

        <span class="sidebar-section-label mt-2">{{ __('ui.sidebar.settings') }}</span>

        <a href="{{ route('team.index') }}"
           class="nav-link-custom {{ request()->routeIs('team.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-users-gear nav-link-icon"></i>
            <span>{{ __('ui.sidebar.team') }}</span>
        </a>

        @if($user && $user->company_id)
        <a href="{{ route('email-settings.index') }}"
           class="nav-link-custom {{ request()->routeIs('email-settings.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-wand-magic-sparkles nav-link-icon"></i>
            <span>{{ __('ui.sidebar.branding') }}</span>
        </a>
        @endif

        <a href="{{ route('billing.upgrade') }}"
           class="nav-link-custom {{ request()->routeIs('billing.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-wallet nav-link-icon"></i>
            <span>{{ __('ui.sidebar.billing') }}</span>
        </a>

        @if($user && $user->isSystemAdmin())
        <span class="sidebar-section-label mt-2" style="color: var(--danger-color);">
            <i class="fas fa-lock me-1" style="font-size:.6rem;"></i>
            {{ __('ui.sidebar.system') }}
        </span>
        <a href="{{ route('system.dashboard') }}"
           class="nav-link-custom nav-link-danger {{ request()->routeIs('system.*') ? 'active' : '' }}"
           role="listitem">
            <i class="fas fa-shield-halved nav-link-icon"></i>
            <span>{{ __('ui.sidebar.system_admin') }}</span>
        </a>
        @endif

        @if(session()->has('impersonator_id'))
        <form action="{{ route('system.impersonation.leave') }}" method="POST" class="mt-2 px-1">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100" style="border-radius: 10px; font-size:.8rem;">
                <i class="fas fa-user-shield me-1"></i> {{ __('ui.sidebar.end_impersonation') }}
            </button>
        </form>
        @endif

    </div>{{-- /sidebar-nav --}}

    {{-- ── Spacer pushes plan card + footer to bottom ───────────── --}}
    <div style="flex: 1;"></div>

    {{-- ── Subscription / Plan card ─────────────────────────────── --}}
    @if($company)
    <a href="{{ route('billing.upgrade') }}" class="sidebar-plan-card" aria-label="{{ __('ui.sidebar.plan') }}: {{ $currentPlanLabel }}">
        <div class="sidebar-plan-top">
            <div class="sidebar-plan-icon" aria-hidden="true">
                <i class="fas fa-{{ $isTrial ? 'hourglass-half' : 'crown' }}"></i>
            </div>
            <div class="sidebar-plan-info">
                <div class="sidebar-plan-label">{{ __('ui.sidebar.plan') }}</div>
                <div class="sidebar-plan-value">{{ $currentPlanLabel }}</div>
            </div>
            @if(!$isTrial)
            <i class="fas fa-arrow-up-right-from-square sidebar-plan-arrow" aria-hidden="true"></i>
            @endif
        </div>

        {{-- Show trial countdown ONLY when subscription is genuinely on trial --}}
        @if($isTrial && $trialEndsAt && $daysLeft !== null)
            <div class="sidebar-trial-row">
                <div class="sidebar-trial-days">
                    <i class="fas fa-clock fa-xs"></i>
                    {{ __('ui.sidebar.ends') }} {{ $trialEndsAt }}
                </div>
                <div class="sidebar-trial-bar-wrap">
                    <div class="sidebar-trial-bar-fill" style="{{ 'width:'.$pct.'%;' }}"></div>
                </div>
            </div>
        @elseif($isActive && optional($activeSubscription)->renews_at)
            <div class="sidebar-trial-row">
                <div class="sidebar-trial-days" style="color:rgba(255,255,255,.75);">
                    <i class="fas fa-rotate fa-xs"></i>
                    {{ __('ui.sidebar.renews_at', ['date' => $activeSubscription->renews_at->format('Y-m-d')]) }}
                </div>
            </div>
        @endif
    </a>
    @endif

    {{-- "Workspace" info card removed by user request — pages used to push
         a stats-card here via @section('infoCard'); this slot is intentionally
         left out so nothing renders even if a view still defines that section. --}}

    {{-- ── Footer / Logout ──────────────────────────────────────── --}}
    <div class="sidebar-footer">
        {{-- The user-info box is a clickable link → opens Account Settings page.
             Wrapped in an <a> so the entire block is a single keyboard- and
             screen-reader-accessible target.
             Active styling is applied via .sidebar-user-link.active when the
             user is on the account page. --}}
        <a href="{{ route('account.index') }}"
           class="sidebar-user-link {{ request()->routeIs('account.*') ? 'active' : '' }}"
           aria-label="{{ __('ui.account.title') }}"
           title="{{ __('ui.account.title') }}">
            <div class="sidebar-user-row">
                <div class="sidebar-user-avatar" aria-hidden="true">
                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                </div>
                <div class="sidebar-user-info min-w-0">
                    <div class="sidebar-user-name text-truncate">{{ $user?->name ?? '' }}</div>
                    <div class="sidebar-user-email text-truncate">{{ $user?->email ?? '' }}</div>
                </div>
                <i class="fas fa-gear sidebar-user-cog" aria-hidden="true"></i>
            </div>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout" aria-label="{{ __('ui.sidebar.sign_out') }}">
                <i class="fas fa-power-off" aria-hidden="true"></i>
                <span>{{ __('ui.sidebar.sign_out') }}</span>
            </button>
        </form>
    </div>

    {{-- ── Inline styles for the new clickable user link ──────────
         These are scoped to the sidebar so they don't pollute global CSS.
         Living next to the markup makes future visual tweaks easier. --}}
    <style>
        /* Sidebar logo tweaks: show logo plainly with no border or shadow */
        .sidebar-brand-area { display: flex; align-items: center; gap: .5rem; padding: 10px 6px; background: transparent; border: none; box-shadow: none; }
        .sidebar-brand-link { background: transparent; border: none; box-shadow: none; }
        .sidebar-platform-logo .platform-logo-img, .sidebar-logo-img { border: none !important; box-shadow: none !important; background: transparent !important; }
        .sidebar-platform-logo .platform-logo-text { line-height: 1; }

        .sidebar-user-link {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: 12px;
            padding: 6px 8px;
            margin: 0 -8px 10px;
            transition: background-color .2s ease, transform .2s ease;
        }
        .sidebar-user-link:hover,
        .sidebar-user-link:focus-visible {
            background: rgba(255, 255, 255, .07);
            color: inherit;
            text-decoration: none;
            outline: none;
        }
        .sidebar-user-link.active {
            background: rgba(255, 255, 255, .12);
        }
        .sidebar-user-link .sidebar-user-row {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 0;
        }
        .sidebar-user-cog {
            margin-inline-start: auto;
            font-size: .82rem;
            opacity: .55;
            transition: opacity .2s ease, transform .2s ease;
        }
        .sidebar-user-link:hover .sidebar-user-cog,
        .sidebar-user-link.active .sidebar-user-cog {
            opacity: 1;
            transform: rotate(35deg);
        }
    </style>

</div>{{-- /sidebar-wrapper --}}
