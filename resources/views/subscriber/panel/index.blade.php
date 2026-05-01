@extends('layouts.app')

@section('title', __('ui.dashboard.title'))

@push('styles')
<style>
/* ─── Hero Banner ─────────────────────────────────────────── */
.dash-hero {
    background: var(--grad-primary);
    border-radius: var(--radius-xl);
    padding: 2.25rem 2.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 20px 50px -15px rgba(15,143,131,0.45);
}
.dash-hero::before {
    content:'';position:absolute;top:-80px;right:-80px;
    width:320px;height:320px;border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.15) 0%,transparent 70%);
}
.dash-hero::after {
    content:'';position:absolute;bottom:-60px;left:-40px;
    width:220px;height:220px;border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);
}
.dash-hero-content { position:relative;z-index:1; }
.dash-kicker {
    display:inline-flex;align-items:center;gap:.45rem;
    background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);
    border-radius:999px;padding:.35rem 1rem;
    font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:.75rem;
}
.dash-title { font-size:clamp(1.5rem,2.5vw,2.1rem);font-weight:800;margin:.1rem 0 .4rem;letter-spacing:-.02em; }
.dash-sub { font-size:.88rem;opacity:.85; }
.dash-badge {
    display:inline-flex;align-items:center;gap:.5rem;
    background:rgba(255,255,255,.15);backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.25);border-radius:999px;
    padding:.38rem 1rem;font-size:.8rem;font-weight:700;
}
.dash-badge.trial  { background:rgba(251,191,36,.22);border-color:rgba(251,191,36,.4); }
.dash-badge.active { background:rgba(16,185,129,.22);border-color:rgba(16,185,129,.4); }

/* ─── Trial Progress Bar ─────────────────────────────────── */
.trial-bar  { height:6px;border-radius:99px;background:rgba(255,255,255,.2);overflow:hidden; }
.trial-fill { height:100%;border-radius:99px;background:rgba(255,255,255,.85);transition:width .9s ease; }

/* ─── KPI Cards ──────────────────────────────────────────── */
.kpi-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(190px,1fr));
    gap:1rem;margin-bottom:2rem;
}
.kpi-card {
    background:var(--surface);border-radius:var(--radius-lg);
    padding:1.4rem 1.5rem;border:1px solid var(--line);
    transition:all .25s cubic-bezier(.4,0,.2,1);
    position:relative;overflow:hidden;cursor:default;
}
.kpi-card:hover { transform:translateY(-4px);box-shadow:var(--shadow-hover); }
.kpi-card::before {
    content:'';position:absolute;top:0;right:0;
    width:4px;height:100%;
    border-radius:0 var(--radius-lg) var(--radius-lg) 0;
}
.kpi-card.c-events::before  { background:var(--grad-primary); }
.kpi-card.c-invites::before { background:linear-gradient(135deg,#0ea5e9,#6366f1); }
.kpi-card.c-accept::before  { background:var(--grad-success); }
.kpi-card.c-checkin::before { background:linear-gradient(135deg,#f59e0b,#ef4444); }

.kpi-icon {
    width:42px;height:42px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.05rem;margin-bottom:.75rem;
}
.kpi-card.c-events  .kpi-icon { background:var(--primary-soft);color:var(--primary-color); }
.kpi-card.c-invites .kpi-icon { background:rgba(14,165,233,.1);color:#0ea5e9; }
.kpi-card.c-accept  .kpi-icon { background:rgba(16,185,129,.1);color:var(--success-color); }
.kpi-card.c-checkin .kpi-icon { background:rgba(245,158,11,.1);color:var(--warning-color); }
.kpi-card.c-accept::before  { background:var(--grad-success); }
.kpi-card.c-checkin::before { background:linear-gradient(135deg,#f59e0b,#ef4444); }

.kpi-label { font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.25rem; }
.kpi-value { font-size:clamp(1.8rem,2.5vw,2.3rem);font-weight:900;color:var(--text-main);line-height:1;letter-spacing:-.02em; }
.kpi-sub   { font-size:.76rem;color:var(--text-soft);margin-top:.35rem;display:flex;align-items:center;gap:.35rem; }
.kpi-sub a { color:inherit;text-decoration:none; }
.kpi-sub a:hover { color:var(--primary-color); }

/* ─── Quick Actions ──────────────────────────────────────── */
.qa-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
    gap:.85rem;
}
.qa-btn {
    display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.55rem;
    background:var(--surface);border:1.5px solid var(--line);border-radius:var(--radius-md);
    padding:1.3rem .75rem;text-decoration:none;color:var(--text-main);
    transition:all .22s ease;
}
.qa-btn:hover {
    transform:translateY(-3px);box-shadow:var(--shadow-hover);
    border-color:var(--primary-color);color:var(--primary-color);
}
.qa-icon {
    width:46px;height:46px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    font-size:1.15rem;transition:transform .22s;
}
.qa-btn:hover .qa-icon { transform:scale(1.1); }
.qa-label { font-size:.76rem;font-weight:600;text-align:center;line-height:1.3; }

/* ─── Section Headers ────────────────────────────────────── */
.sec-head  { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; }
.sec-title { font-size:.95rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem; }
.sec-title i { color:var(--primary-color); }

/* ─── Event Rows ─────────────────────────────────────────── */
.event-row {
    display:flex;align-items:center;gap:1rem;
    padding:.9rem 1rem;border-radius:var(--radius-md);transition:background .18s;
}
.event-row:hover { background:var(--surface-soft); }
.event-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }
.event-dot.public  { background:var(--success-color);box-shadow:0 0 0 3px rgba(16,185,129,.18); }
.event-dot.private { background:var(--primary-color);box-shadow:0 0 0 3px rgba(15,143,131,.18); }
.event-dot.pending { background:var(--warning-color);box-shadow:0 0 0 3px rgba(245,158,11,.18); }
.event-name { font-weight:600;font-size:.88rem;color:var(--text-main);flex:1; }
.event-meta { font-size:.76rem;color:var(--text-soft);margin-top:.15rem;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap; }

/* ─── Subscription Card ──────────────────────────────────── */
.sub-card {
    background:var(--grad-primary);color:#fff;border:none;border-radius:var(--radius-lg);
    padding:1.4rem 1.5rem;display:flex;flex-direction:column;gap:1rem;
    box-shadow:0 12px 28px -10px rgba(15,143,131,.4);
}
.sub-icon {
    width:44px;height:44px;border-radius:12px;
    background:rgba(255,255,255,.16);
    display:flex;align-items:center;justify-content:center;font-size:1.1rem;
}
.sub-plan-label { font-size:.72rem;font-weight:700;opacity:.8;text-transform:uppercase;letter-spacing:.07em; }
.sub-plan-value { font-size:1.15rem;font-weight:800;margin-top:.1rem; }
.sub-note { font-size:.84rem;opacity:.9;display:flex;align-items:center;gap:.4rem; }

/* ─── Checklist ──────────────────────────────────────────── */
.checklist-item {
    display:flex;align-items:center;gap:.85rem;
    padding:.75rem .85rem;border-radius:12px;
    border:1px solid var(--line);background:var(--surface-soft);
    transition:background .18s;
}
.checklist-item:hover { background:var(--surface); }
.chk-dot {
    width:32px;height:32px;border-radius:50%;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:.72rem;
}
.chk-text { font-size:.84rem;font-weight:500;color:var(--text-main);line-height:1.35; }
.chk-status { font-size:.72rem;color:var(--text-soft);margin-top:.1rem; }

/* ─── Stats Sidebar ──────────────────────────────────────── */
.stats-card { background:var(--surface);border-radius:20px;border:1px solid var(--line);box-shadow:var(--shadow-card);padding:1.25rem; }
.stat-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;font-size:.875rem; }
.stat-label { color:var(--text-soft); }
.stat-value { font-weight:700;color:var(--text-main); }

/* ─── Responsive ─────────────────────────────────────────── */
@media (max-width:768px) {
    .dash-hero { padding:1.5rem; }
    .kpi-grid  { grid-template-columns:repeat(2,1fr); }
    .qa-grid   { grid-template-columns:repeat(3,1fr); }
}
@media (max-width:480px) {
    .kpi-grid { grid-template-columns:1fr 1fr; }
    .qa-grid  { grid-template-columns:repeat(2,1fr); }
}
</style>
@endpush

@section('content')

{{-- ════════ HERO ════════ --}}
<div class="dash-hero animate__animated animate__fadeInDown">
    <div class="dash-hero-content">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <div class="dash-kicker">
                    <i class="fas fa-gauge-high"></i>
                    {{ __('ui.dashboard.welcome_back') }}
                </div>
                <h1 class="dash-title">{{ $company->name ?? __('ui.dashboard.workspace_fallback') }}</h1>
                <p class="dash-sub mb-0">
                    <i class="fas fa-calendar-day me-1"></i>
                    {{ now()->locale(app()->getLocale())->translatedFormat('l، j F Y') }}
                </p>
            </div>

            <div class="d-flex flex-column align-items-end gap-2">
                @php
                    $subStatus   = optional($subscription)->status ?? 'trial';
                    $subPlanCode = strtoupper(optional(optional($subscription)->plan)->code ?? $company->current_plan_code ?? 'trial');
                    $planCodeLabel = $subPlanCode === 'TRIAL' ? __('ui.dashboard.trial_short') : $subPlanCode;
                    $isTrial  = $subStatus === 'trial';
                    $isActive = $subStatus === 'active';
                    $daysLeft = null;
                    $pct      = 0;
                    if ($isTrial && optional($subscription)->trial_ends_at) {
                        $daysLeft  = max(0, (int) now()->diffInDays($subscription->trial_ends_at, false));
                        $totalDays = max(1, (int) config('subscription.trial.days', 15));
                        $pct       = max(0, min(100, round(($daysLeft / $totalDays) * 100)));
                    }
                    $acceptRate = $invitationsCount > 0 ? round(($acceptedCount / $invitationsCount) * 100) : 0;
                @endphp

                @if($isActive && $subPlanCode !== 'TRIAL')
                    <span class="dash-badge active">
                        <i class="fas fa-circle-check"></i> {{ $planCodeLabel }} · {{ __('ui.dashboard.active') }}
                    </span>
                @elseif($isTrial)
                    <span class="dash-badge trial">
                        <i class="fas fa-hourglass-half"></i> {{ __('ui.dashboard.trial') }}
                        @if($daysLeft !== null) · {{ __('ui.dashboard.days_left', ['count' => $daysLeft]) }} @endif
                    </span>
                @else
                    <span class="dash-badge trial">
                        <i class="fas fa-exclamation-circle"></i> {{ $planCodeLabel }}
                    </span>
                @endif

                @if($isTrial && $daysLeft !== null)
                    <div style="width:170px;">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.7rem;opacity:.8;">
                            <span>{{ __('ui.dashboard.trial_progress') }}</span>
                            <span>{{ __('ui.dashboard.days_left', ['count' => $daysLeft]) }}</span>
                        </div>
                        <div class="trial-bar">
                            <div class="trial-fill" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ════════ KPI CARDS ════════ --}}
<div class="kpi-grid animate__animated animate__fadeInUp" style="animation-delay:.08s">

    <div class="kpi-card c-events">
        <div class="kpi-icon"><i class="fas fa-calendar-days"></i></div>
        <div class="kpi-label">{{ __('ui.dashboard.kpi_total_events') }}</div>
        <div class="kpi-value" data-count="{{ $eventsCount }}">{{ number_format($eventsCount) }}</div>
        <div class="kpi-sub">
            <i class="fas fa-arrow-left fa-xs"></i>
            <a href="{{ route('events.index') }}">{{ __('ui.dashboard.manage_events') }}</a>
        </div>
    </div>

    <div class="kpi-card c-invites">
        <div class="kpi-icon"><i class="fas fa-envelope-open-text"></i></div>
        <div class="kpi-label">{{ __('ui.dashboard.kpi_invitations') }}</div>
        <div class="kpi-value" data-count="{{ $invitationsCount }}">{{ number_format($invitationsCount) }}</div>
        <div class="kpi-sub">
            <i class="fas fa-arrow-left fa-xs"></i>
            <a href="{{ route('invitations.index') }}">{{ __('ui.dashboard.view_all') }}</a>
        </div>
    </div>

    <div class="kpi-card c-accept">
        <div class="kpi-icon"><i class="fas fa-qrcode"></i></div>
        <div class="kpi-label">{{ __('ui.dashboard.kpi_qr_checkin') }}</div>
        <div class="kpi-value">{{ __('ui.dashboard.kpi_live') }}</div>
        <div class="kpi-sub">
            <i class="fas fa-arrow-left fa-xs"></i>
            <a href="{{ route('scan.checkin') }}">{{ __('ui.dashboard.open_scanner') }}</a>
        </div>
    </div>

    <div class="kpi-card c-checkin">
        <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
        <div class="kpi-label">{{ __('ui.dashboard.kpi_plan') }}</div>
        <div class="kpi-value" style="font-size:1.3rem;">{{ $planCodeLabel }}</div>
        <div class="kpi-sub">
            <i class="fas fa-arrow-left fa-xs"></i>
            <a href="{{ route('billing.upgrade') }}">{{ __('ui.dashboard.manage_billing') }}</a>
        </div>
    </div>

</div>

{{-- ════════ QUICK ACTIONS ════════ --}}
<div class="card-surface mb-4 animate__animated animate__fadeInUp" style="animation-delay:.14s">
    <div class="sec-head">
        <div class="sec-title"><i class="fas fa-bolt"></i> {{ __('ui.dashboard.quick_actions') }}</div>
    </div>
    <div class="qa-grid">

        <a href="{{ route('events.create') }}" class="qa-btn">
            <div class="qa-icon" style="background:var(--primary-soft);color:var(--primary-color)">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <span class="qa-label">{{ __('ui.dashboard.new_event') }}</span>
        </a>

        <a href="{{ route('invitations.index') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <span class="qa-label">الدعوات</span>
        </a>

        <a href="{{ route('scan.checkin') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(16,185,129,.1);color:var(--success-color)">
                <i class="fas fa-qrcode"></i>
            </div>
            <span class="qa-label">{{ __('ui.sidebar.qr_scanner') }}</span>
        </a>

        <a href="{{ route('attendance_list') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(99,102,241,.1);color:#6366f1">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <span class="qa-label">قائمة الحضور</span>
        </a>

        <a href="{{ route('statistics') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="qa-label">{{ __('ui.dashboard.analytics') }}</span>
        </a>

        <a href="{{ route('team.index') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(245,158,11,.1);color:var(--warning-color)">
                <i class="fas fa-user-group"></i>
            </div>
            <span class="qa-label">{{ __('ui.dashboard.team') }}</span>
        </a>

        <a href="{{ route('email-settings.index') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(244,63,94,.1);color:var(--danger-color)">
                <i class="fas fa-palette"></i>
            </div>
            <span class="qa-label">{{ __('ui.dashboard.branding') }}</span>
        </a>

        <a href="{{ route('billing.upgrade') }}" class="qa-btn">
            <div class="qa-icon" style="background:rgba(16,185,129,.1);color:var(--success-color)">
                <i class="fas fa-gem"></i>
            </div>
            <span class="qa-label">{{ __('ui.dashboard.billing') }}</span>
        </a>

    </div>
</div>

{{-- ════════ RECENT EVENTS + SIDE ════════ --}}
<div class="row g-3 animate__animated animate__fadeInUp" style="animation-delay:.2s">

    {{-- Recent Events --}}
    <div class="col-lg-7">
        <div class="card-surface h-100">
            <div class="sec-head">
                <div class="sec-title"><i class="fas fa-calendar-days"></i> {{ __('ui.dashboard.recent_events') }}</div>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:.78rem;">
                    {{ __('ui.dashboard.view_all') }} <i class="fas fa-arrow-left ms-1 fa-xs"></i>
                </a>
            </div>

            @forelse($recentEvents ?? [] as $evt)
                @php
                    $dotClass = match($evt->type ?? 'private') {
                        'public'  => 'public',
                        'private' => 'private',
                        default   => 'pending',
                    };
                    $badgeBg  = $dotClass === 'public' ? 'rgba(16,185,129,.1)' : ($dotClass === 'private' ? 'var(--primary-soft)' : 'rgba(245,158,11,.1)');
                    $badgeClr = $dotClass === 'public' ? 'var(--success-color)' : ($dotClass === 'private' ? 'var(--primary-color)' : 'var(--warning-color)');
                @endphp
                <div class="event-row">
                    <div class="event-dot {{ $dotClass }}"></div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="event-name text-truncate">{{ $evt->name }}</div>
                        <div class="event-meta">
                            @if($evt->event_date)
                                <span><i class="fas fa-calendar fa-xs"></i> {{ \Carbon\Carbon::parse($evt->event_date)->format('j M Y') }}</span>
                            @endif
                            @if($evt->location)
                                <span><i class="fas fa-location-dot fa-xs"></i> {{ Str::limit($evt->location, 22) }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="badge rounded-pill" style="background:{{ $badgeBg }};color:{{ $badgeClr }};font-size:.68rem;font-weight:700;padding:.35rem .75rem;">
                        {{ $dotClass === 'public' ? __('ui.dashboard.type_public') : ($dotClass === 'private' ? __('ui.dashboard.type_private') : __('ui.dashboard.type_pending')) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-5">
                    <div style="width:60px;height:60px;border-radius:50%;background:var(--primary-soft);margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-calendar-plus fa-lg" style="color:var(--primary-color)"></i>
                    </div>
                    <p class="fw-semibold mb-1" style="color:var(--text-main);font-size:.9rem">{{ __('ui.dashboard.no_events_yet') }}</p>
                    <p class="small mb-3" style="color:var(--text-soft)">{{ __('ui.dashboard.create_first_event_hint') }}</p>
                    <a href="{{ route('events.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i>{{ __('ui.dashboard.create_event') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Side Stack --}}
    <div class="col-lg-5 d-flex flex-column gap-3">

        {{-- Subscription Card --}}
        <div class="sub-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sub-icon"><i class="fas fa-gem"></i></div>
                <div>
                    <div class="sub-plan-label">{{ __('ui.dashboard.subscription') }}</div>
                    <div class="sub-plan-value">{{ __('ui.dashboard.plan_label', ['plan' => $planCodeLabel]) }}</div>
                </div>
            </div>

            @if($isTrial && $daysLeft !== null)
                <div class="sub-note"><i class="fas fa-hourglass-half"></i>{{ __('ui.dashboard.trial_remaining', ['count' => $daysLeft]) }}</div>
                <div class="trial-bar"><div class="trial-fill" style="width:{{ $pct }}%"></div></div>
                <a href="{{ route('billing.upgrade') }}" class="btn btn-light btn-sm rounded-pill px-4 fw-bold align-self-start">
                    <i class="fas fa-rocket me-2"></i>{{ __('ui.dashboard.upgrade_now') }}
                </a>
            @elseif($isActive)
                <div class="sub-note">
                    <i class="fas fa-circle-check"></i>{{ __('ui.dashboard.plan_active') }}
                    @if(optional($subscription)->renews_at)
                        · {{ __('ui.dashboard.renewal', ['date' => $subscription->renews_at->format('j M Y')]) }}
                    @elseif(optional($subscription)->ends_at)
                        · {{ __('ui.dashboard.renewal', ['date' => $subscription->ends_at->format('j M Y')]) }}
                    @endif
                </div>
                <a href="{{ route('billing.upgrade') }}" class="btn btn-light btn-sm rounded-pill px-4 fw-bold align-self-start">
                    <i class="fas fa-gear me-2"></i>{{ __('ui.dashboard.manage_billing') }}
                </a>
            @else
                <a href="{{ route('billing.upgrade') }}" class="btn btn-light btn-sm rounded-pill px-4 fw-bold align-self-start">
                    <i class="fas fa-rocket me-2"></i>{{ __('ui.dashboard.choose_plan') }}
                </a>
            @endif
        </div>

        {{-- Getting Started Checklist --}}
        <div class="card-surface flex-grow-1">
            <div class="sec-title mb-3"><i class="fas fa-list-check"></i> {{ __('ui.dashboard.getting_started') }}</div>
            @php
                $checks = [
                    ['done' => $eventsCount > 0,        'icon' => 'fas fa-calendar-days', 'text' => __('ui.dashboard.check_create_event')],
                    ['done' => $invitationsCount > 0,   'icon' => 'fas fa-envelope',       'text' => __('ui.dashboard.check_send_invites')],
                    ['done' => true,                    'icon' => 'fas fa-palette',         'text' => __('ui.dashboard.check_setup_branding')],
                    ['done' => $subStatus === 'active', 'icon' => 'fas fa-gem',             'text' => __('ui.dashboard.check_activate_subscription')],
                ];
            @endphp
            <div class="d-flex flex-column gap-2">
                @foreach($checks as $chk)
                    <div class="checklist-item">
                        <div class="chk-dot" style="background:{{ $chk['done'] ? 'rgba(16,185,129,.12)' : 'var(--primary-soft)' }};color:{{ $chk['done'] ? 'var(--success-color)' : 'var(--primary-color)' }}">
                            <i class="{{ $chk['done'] ? 'fas fa-check' : $chk['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="chk-text {{ $chk['done'] ? 'text-decoration-line-through' : '' }}" style="{{ $chk['done'] ? 'color:var(--text-soft)' : '' }}">
                                {{ $chk['text'] }}
                            </div>
                            <div class="chk-status">{{ $chk['done'] ? __('ui.dashboard.status_completed') : __('ui.dashboard.status_pending') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection

@section('infoCard')
@if($company)
<div class="stats-card">
    <h6 class="fw-bold mb-3" style="font-size:.85rem;color:var(--text-main)">
        <i class="fas fa-building me-2" style="color:var(--primary-color)"></i>{{ __('ui.dashboard.workspace') }}
    </h6>
    <div class="stat-row">
        <span class="stat-label">{{ __('ui.dashboard.company') }}</span>
        <span class="stat-value" style="font-size:.85rem">{{ Str::limit($company->name, 18) }}</span>
    </div>
    <div class="stat-row">
        <span class="stat-label">{{ __('ui.dashboard.plan') }}</span>
        <span class="stat-value">
            <span class="badge rounded-pill" style="background:var(--primary-soft);color:var(--primary-color);font-size:.68rem;font-weight:700;">{{ $planCodeLabel }}</span>
        </span>
    </div>
    <div class="stat-row">
        <span class="stat-label">{{ __('ui.dashboard.events') }}</span>
        <span class="stat-value fw-bold">{{ number_format($eventsCount) }}</span>
    </div>
    <div class="stat-row">
        <span class="stat-label">{{ __('ui.dashboard.invitations') }}</span>
        <span class="stat-value fw-bold">{{ number_format($invitationsCount) }}</span>
    </div>
    <div class="stat-row">
        <span class="stat-label">الموافقون</span>
        <span class="stat-value fw-bold" style="color:var(--success-color)">{{ number_format($acceptedCount) }}</span>
    </div>
    <div class="stat-row">
        <span class="stat-label">الحاضرون</span>
        <span class="stat-value fw-bold" style="color:var(--warning-color)">{{ number_format($checkedInCount) }}</span>
    </div>
    @if($isTrial && $daysLeft !== null)
    <div class="mt-3 pt-2" style="border-top:1px solid var(--line)">
        <div class="d-flex justify-content-between mb-1" style="font-size:.72rem;color:var(--text-soft)">
            <span>{{ __('ui.dashboard.trial_short') }}</span>
            <span>{{ __('ui.dashboard.days_left', ['count' => $daysLeft]) }}</span>
        </div>
        <div style="height:5px;border-radius:99px;background:var(--line);overflow:hidden">
            <div style="height:100%;width:{{ $pct }}%;background:var(--grad-primary);border-radius:99px;transition:width .8s ease"></div>
        </div>
    </div>
    @elseif($isActive)
    <div class="mt-3 pt-2" style="border-top:1px solid var(--line)">
        <div class="d-flex align-items-center gap-2" style="font-size:.75rem;color:var(--success-color);font-weight:600;">
            <i class="fas fa-circle-check"></i> {{ __('ui.dashboard.plan_active') }}
        </div>
        @if(optional($subscription)->renews_at)
            <div style="font-size:.72rem;color:var(--text-soft);margin-top:.25rem">
                تجديد: {{ $subscription->renews_at->format('j M Y') }}
            </div>
        @endif
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script>
// Animate KPI counters on page load
document.querySelectorAll('.kpi-value[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count);
    if (isNaN(target) || target === 0) return;
    let current = 0;
    const duration = 1200;
    const step = 16;
    const increment = target / (duration / step);
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current).toLocaleString();
        }
    }, step);
});
</script>
@endpush
