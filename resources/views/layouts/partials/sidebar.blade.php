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
            @php
                $logoUrl   = \App\Models\SystemSetting::get('platform_logo_url', '');
                $brandName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
            @endphp

            @if(!empty($logoUrl))
                {{-- Image logo from platform admin settings --}}
                <div class="sidebar-logo-img-wrap">
                    <img
                        src="{{ $logoUrl }}"
                        alt="{{ $brandName }}"
                        class="sidebar-logo-img"
                        loading="lazy"
                    >
                </div>
            @else
                {{-- Text fallback using platform name --}}
                <div class="sidebar-brand-text-wrap">
                    <div class="sidebar-brand-text">{{ $brandName }}</div>
                    <div class="sidebar-brand-badge">{{ __('ui.workspace') }}</div>
                </div>
            @endif
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
                    <div class="sidebar-trial-bar-fill" style="width: {{ $pct }}%;"></div>
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

    @yield('infoCard')

    {{-- ── Footer / Logout ──────────────────────────────────────── --}}
    <div class="sidebar-footer">
        <div class="sidebar-user-row">
            <div class="sidebar-user-avatar" aria-hidden="true">
                {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info min-w-0">
                <div class="sidebar-user-name text-truncate">{{ $user?->name ?? '' }}</div>
                <div class="sidebar-user-email text-truncate">{{ $user?->email ?? '' }}</div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout" aria-label="{{ __('ui.sidebar.sign_out') }}">
                <i class="fas fa-power-off" aria-hidden="true"></i>
                <span>{{ __('ui.sidebar.sign_out') }}</span>
            </button>
        </form>
    </div>

</div>{{-- /sidebar-wrapper --}}
