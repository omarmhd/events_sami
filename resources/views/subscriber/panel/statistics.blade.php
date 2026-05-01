@extends('layouts.app')

@section('title', __('ui.analytics.title'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   Analytics Page — متوافق مع نظام التصميم (saas-ui.css)
══════════════════════════════════════════════════════════════ */

/* ── Filter Card ──────────────────────────────────────────── */
.filter-card {
    background: var(--surface, #fff);
    border: 1px solid var(--line, #dce8e4);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.75rem;
    box-shadow: var(--shadow-soft);
}
.filter-card .form-label {
    font-size: .78rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .35rem;
}
.filter-card .form-control,
.filter-card .form-select {
    border-color: var(--line);
    border-radius: var(--radius-sm);
    background: var(--surface-soft, #f5faf8);
    color: var(--text-main);
    font-size: .88rem;
    min-height: 40px;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 .18rem rgba(15,143,131,.16);
    background: #fff;
}

/* ── KPI Cards ────────────────────────────────────────────── */
.an-kpi {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 1.3rem 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
    height: 100%;
}
.an-kpi:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.an-kpi::after {
    content:''; position:absolute; top:0; inset-inline-end:0;
    width:4px; height:100%;
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
}
.an-kpi.kpi-blue::after   { background: var(--grad-primary); }
.an-kpi.kpi-green::after  { background: var(--grad-success); }
.an-kpi.kpi-purple::after { background: linear-gradient(135deg,#6366f1,#8b5cf6); }
.an-kpi.kpi-amber::after  { background: var(--grad-accent); }

.an-kpi-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; margin-bottom: .6rem;
}
.kpi-blue  .an-kpi-icon { background: var(--primary-soft); color: var(--primary-color); }
.kpi-green .an-kpi-icon { background: rgba(16,185,129,.1);  color: var(--success-color); }
.kpi-purple .an-kpi-icon{ background: rgba(99,102,241,.1);  color: #6366f1; }
.kpi-amber .an-kpi-icon { background: rgba(245,158,11,.1);  color: var(--warning-color); }

.an-kpi-label { font-size:.74rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.07em; margin-bottom:.2rem; }
.an-kpi-value { font-size: clamp(1.7rem,2.3vw,2.1rem); font-weight:800; color:var(--text-main); line-height:1; }
.an-kpi-sub   { font-size:.76rem; color:var(--text-soft); margin-top:.35rem; }

/* ── Progress bars ────────────────────────────────────────── */
.an-progress {
    height: 8px; border-radius: 99px;
    background: var(--surface-muted, #e9f3ef);
    overflow: hidden; margin-top: .5rem;
}
.an-progress-fill {
    height: 100%; border-radius: 99px;
    transition: width .8s cubic-bezier(.4,0,.2,1);
}

/* ── Section Cards ────────────────────────────────────────── */
.an-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
    height: 100%;
}
.an-card-header {
    padding: 1.1rem 1.5rem .9rem;
    border-bottom: 1px solid var(--line);
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
}
.an-card-title {
    font-size: .95rem; font-weight:700; color:var(--text-main);
    display:flex; align-items:center; gap:.5rem; margin:0;
}
.an-card-title i { color:var(--primary-color); }
.an-card-subtitle { font-size:.76rem; color:var(--text-soft); margin:.15rem 0 0; }
.an-card-body { padding: 1.25rem 1.5rem; }

/* ── Response rows ────────────────────────────────────────── */
.resp-row { margin-bottom:.9rem; }
.resp-row:last-child { margin-bottom: 0; }
.resp-label { font-size:.84rem; font-weight:600; color:var(--text-main); }
.resp-count  { font-size:.82rem; color:var(--text-soft); }
.resp-pct    { font-size:.78rem; font-weight:700; }

/* ── Ticket pill ──────────────────────────────────────────── */
.ticket-pill {
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 1rem 1.25rem;
    background: var(--surface-soft);
    display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    margin-bottom: .75rem;
    transition: background .2s;
}
.ticket-pill:last-child { margin-bottom: 0; }
.ticket-pill:hover { background: #fff; box-shadow: var(--shadow-soft); }
.ticket-pill-num { font-size: 1.45rem; font-weight:800; color:var(--text-main); line-height:1; }
.ticket-pill-sub { font-size: .75rem; color:var(--text-soft); margin-top:.1rem; }
.ticket-pill-pct {
    font-size:.82rem; font-weight:700; padding:.2rem .7rem;
    border-radius:99px;
}
.pct-good { background:rgba(16,185,129,.12); color:var(--success-color); }
.pct-warn { background:rgba(245,158,11,.12);  color:var(--warning-color); }
.pct-zero { background:var(--surface-muted);  color:var(--text-soft); }

/* ── Charts ───────────────────────────────────────────────── */
.chart-wrap {
    position: relative;
    width: 100%;
    min-height: 220px;
}
.chart-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding: 2.5rem 1rem; color:var(--text-soft); gap:.6rem; text-align:center;
}
.chart-empty i { font-size: 2rem; color:var(--primary-soft); opacity:.8; }

/* ── Active filter badge ──────────────────────────────────── */
.filter-badge {
    display:inline-flex; align-items:center; gap:.4rem;
    background:var(--primary-soft); color:var(--primary-color);
    border-radius:99px; font-size:.76rem; font-weight:700;
    padding:.2rem .75rem;
}

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width:767.98px) {
    .app-page-hero { padding:1.35rem 1.25rem; border-radius:var(--radius-lg); }
    .filter-card    { padding:1rem; }
    .an-card-body   { padding:1rem; }
    .an-kpi         { padding:1rem 1.1rem; }
}
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════════════════ --}}
<div class="app-page-hero animate__animated animate__fadeInDown">
    <div class="app-page-hero-content">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <div class="app-page-hero-kicker">
                    <i class="fas fa-chart-mixed me-2"></i>{{ __('ui.sidebar.analytics') }}
                </div>
                <h1 class="app-page-hero-title">
                    {{ __('ui.analytics.title') }}
                </h1>
                <p class="app-page-hero-subtitle">
                    {{ __('ui.analytics.subtitle') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($selectedEvent)
                    <span class="filter-badge" style="background:rgba(255,255,255,.18);color:#fff;border:1px solid rgba(255,255,255,.3);">
                        <i class="fas fa-calendar-check fa-xs"></i>
                        {{ Str::limit($selectedEvent->name, 28) }}
                    </span>
                @endif
                <span style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:99px;padding:.3rem 1rem;font-size:.78rem;font-weight:600;">
                    <i class="fas fa-clock me-1"></i>{{ __('ui.analytics.last_updated') }}: {{ now()->format('h:i A') }}
                </span>
                <a href="{{ route('statistics') }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:99px;padding:.3rem 1rem;font-size:.78rem;font-weight:600;">
                    <i class="fas fa-rotate-right me-1"></i>{{ __('ui.analytics.refresh') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     FILTER CARD
══════════════════════════════════════════════════════════ --}}
<div class="filter-card animate__animated animate__fadeInUp" style="animation-delay:.05s">
    <form method="GET" action="{{ route('statistics') }}" id="filter-form">

        <div class="row g-3 align-items-end">

            {{-- اختيار الفعالية --}}
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label" for="event_id">
                    <i class="fas fa-calendar-star me-1" style="color:var(--primary-color)"></i>
                    {{ __('ui.analytics.filter_event') }}
                </label>
                <select name="event_id" id="event_id" class="form-select">
                    <option value="">{{ __('ui.analytics.filter_event_all') }}</option>
                    @foreach($events as $evt)
                        <option value="{{ $evt->id }}" {{ (string)$selectedEventId === (string)$evt->id ? 'selected' : '' }}>
                            {{ $evt->name }}
                            @if($evt->start_datetime)
                                ({{ \Carbon\Carbon::parse($evt->start_datetime)->format('d/m/Y') }})
                            @elseif($evt->date)
                                ({{ \Carbon\Carbon::parse($evt->date)->format('d/m/Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- من تاريخ --}}
            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" for="date_from">
                    <i class="fas fa-calendar-arrow-down me-1" style="color:var(--primary-color)"></i>
                    {{ __('ui.analytics.filter_date_from') }}
                </label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                    value="{{ $dateFrom }}"
                    max="{{ now()->toDateString() }}">
            </div>

            {{-- إلى تاريخ --}}
            <div class="col-6 col-sm-3 col-lg-2">
                <label class="form-label" for="date_to">
                    <i class="fas fa-calendar-arrow-up me-1" style="color:var(--primary-color)"></i>
                    {{ __('ui.analytics.filter_date_to') }}
                </label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                    value="{{ $dateTo }}"
                    max="{{ now()->toDateString() }}">
            </div>

            {{-- أزرار --}}
            <div class="col-12 col-lg-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="min-height:40px;">
                    <i class="fas fa-filter me-2"></i>{{ __('ui.analytics.filter_apply') }}
                </button>
                @if($selectedEventId || $dateFrom || $dateTo)
                    <a href="{{ route('statistics') }}" class="btn btn-outline-secondary rounded-pill px-3" style="min-height:40px;">
                        <i class="fas fa-xmark me-1"></i>{{ __('ui.analytics.filter_reset') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Active filter summary --}}
        @if($selectedEventId || $dateFrom || $dateTo)
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center" style="font-size:.8rem;color:var(--text-soft);">
            <i class="fas fa-circle-info" style="color:var(--primary-color)"></i>
            @if($selectedEvent)
                <span class="filter-badge">{{ $selectedEvent->name }}</span>
            @endif
            @if($dateFrom || $dateTo)
                <span class="filter-badge">
                    {{ $dateFrom ?: '...' }} — {{ $dateTo ?: now()->toDateString() }}
                </span>
            @endif
        </div>
        @endif

    </form>
</div>

{{-- ══════════════════════════════════════════════════════════
     KPI CARDS
══════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4 animate__animated animate__fadeInUp" style="animation-delay:.1s">

    {{-- إجمالي الدعوات --}}
    <div class="col-6 col-lg-3">
        <div class="an-kpi kpi-blue">
            <div class="an-kpi-icon"><i class="fas fa-paper-plane"></i></div>
            <div class="an-kpi-label">{{ __('ui.analytics.kpi_total_invitations') }}</div>
            <div class="an-kpi-value" data-count="{{ $invitationStats->total ?? 0 }}">
                {{ number_format($invitationStats->total ?? 0) }}
            </div>
            <div class="an-kpi-sub">
                <i class="fas fa-circle-check fa-xs me-1" style="color:var(--success-color)"></i>
                {{ __('ui.analytics.kpi_sent_successfully') }}
            </div>
        </div>
    </div>

    {{-- المقاعد المؤكدة --}}
    <div class="col-6 col-lg-3">
        <div class="an-kpi kpi-green">
            <div class="an-kpi-icon"><i class="fas fa-user-check"></i></div>
            <div class="an-kpi-label">{{ __('ui.analytics.kpi_confirmed') }}</div>
            <div class="an-kpi-value" style="color:var(--success-color)"
                data-count="{{ ($invitationStats->accepted ?? 0) + ($invitationStats->total_guests_confirmed ?? 0) }}">
                {{ number_format(($invitationStats->accepted ?? 0) + ($invitationStats->total_guests_confirmed ?? 0)) }}
            </div>
            <div class="an-progress">
                <div class="an-progress-fill" style="background:var(--grad-success);width:{{ $acceptanceRate }}%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2" style="font-size:.74rem;">
                <span style="color:var(--text-soft)">{{ __('ui.analytics.kpi_acceptance_rate') }}</span>
                <span style="font-weight:700;color:var(--text-main)">{{ $acceptanceRate }}%</span>
            </div>
        </div>
    </div>

    {{-- الحضور الفعلي --}}
    <div class="col-6 col-lg-3">
        <div class="an-kpi kpi-purple">
            <div class="an-kpi-icon"><i class="fas fa-qrcode"></i></div>
            <div class="an-kpi-label">{{ __('ui.analytics.kpi_attendance') }}</div>
            <div class="an-kpi-value" style="color:#6366f1" data-count="{{ $ticketStats->total_checked_in ?? 0 }}">
                {{ number_format($ticketStats->total_checked_in ?? 0) }}
                <span style="font-size:.95rem;font-weight:500;color:var(--text-soft)">
                    / {{ number_format($ticketStats->total_issued ?? 0) }}
                </span>
            </div>
            <div class="an-progress">
                <div class="an-progress-fill" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);width:{{ $attendanceRate }}%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2" style="font-size:.74rem;">
                <span style="color:var(--text-soft)">{{ __('ui.analytics.kpi_checkin_rate') }}</span>
                <span style="font-weight:700;color:var(--text-main)">{{ $attendanceRate }}%</span>
            </div>
        </div>
    </div>

    {{-- في انتظار الرد --}}
    <div class="col-6 col-lg-3">
        <div class="an-kpi kpi-amber">
            <div class="an-kpi-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="an-kpi-label">{{ __('ui.analytics.kpi_pending') }}</div>
            <div class="an-kpi-value" style="color:var(--warning-color)" data-count="{{ $invitationStats->pending ?? 0 }}">
                {{ number_format($invitationStats->pending ?? 0) }}
            </div>
            <div class="an-kpi-sub">
                <i class="fas fa-clock fa-xs me-1"></i>{{ __('ui.analytics.kpi_awaiting_response') }}
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 2: Response Breakdown + Ticket Analysis
══════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4 animate__animated animate__fadeInUp" style="animation-delay:.15s">

    {{-- توزيع الاستجابات --}}
    <div class="col-md-6">
        <div class="an-card h-100">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">
                        <i class="fas fa-chart-pie"></i>
                        {{ __('ui.analytics.response_breakdown') }}
                    </div>
                    <div class="an-card-subtitle">
                        {{ __('ui.analytics.kpi_response_rate') }}: <strong>{{ $responseRate }}%</strong>
                    </div>
                </div>
            </div>
            <div class="an-card-body">
                @php $total = $invitationStats->total ?: 1; @endphp

                @foreach([
                    ['label' => __('ui.analytics.response_accepted'), 'count' => $invitationStats->accepted ?? 0, 'color' => 'var(--success-color)', 'bg' => 'var(--grad-success)'],
                    ['label' => __('ui.analytics.response_declined'), 'count' => $invitationStats->declined ?? 0, 'color' => '#ef4444',              'bg' => 'linear-gradient(135deg,#ef4444,#dc2626)'],
                    ['label' => __('ui.analytics.response_maybe'),   'count' => $invitationStats->maybe   ?? 0, 'color' => '#0ea5e9',              'bg' => 'linear-gradient(135deg,#0ea5e9,#0284c7)'],
                    ['label' => __('ui.analytics.response_pending'), 'count' => $invitationStats->pending  ?? 0, 'color' => 'var(--warning-color)', 'bg' => 'var(--grad-accent)'],
                ] as $row)
                @php $pct = round(($row['count'] / $total) * 100, 1); @endphp
                <div class="resp-row">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="resp-label" style="color:{{ $row['color'] }}">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $row['color'] }};display:inline-block;margin-inline-end:.45rem;"></span>
                            {{ $row['label'] }}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="resp-count">{{ number_format($row['count']) }}</span>
                            <span class="resp-pct" style="color:{{ $row['color'] }}">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="an-progress">
                        <div class="an-progress-fill" style="background:{{ $row['bg'] }};width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- تحليل التذاكر --}}
    <div class="col-md-6">
        <div class="an-card h-100">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">
                        <i class="fas fa-ticket"></i>
                        {{ __('ui.analytics.ticket_analysis') }}
                    </div>
                    <div class="an-card-subtitle">
                        {{ __('ui.analytics.kpi_checkin_rate') }}: <strong>{{ $attendanceRate }}%</strong>
                    </div>
                </div>
            </div>
            <div class="an-card-body d-flex flex-column justify-content-center gap-2">

                @php
                    $mainPct  = $ticketStats->main_issued  > 0 ? round(($ticketStats->main_checked_in  / $ticketStats->main_issued)  * 100) : 0;
                    $guestPct = $ticketStats->guest_issued > 0 ? round(($ticketStats->guest_checked_in / $ticketStats->guest_issued) * 100) : 0;
                @endphp

                {{-- المدعوون الرئيسيون --}}
                <div class="ticket-pill">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:38px;height:38px;border-radius:12px;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-user-tie" style="color:var(--primary-color)"></i>
                        </div>
                        <div>
                            <div style="font-size:.84rem;font-weight:700;color:var(--text-main)">{{ __('ui.analytics.ticket_main') }}</div>
                            <div class="ticket-pill-sub">
                                {{ __('ui.analytics.ticket_checked_in') }}:
                                <strong style="color:var(--text-main)">{{ number_format($ticketStats->main_checked_in ?? 0) }}</strong>
                                {{ __('ui.analytics.ticket_of') }} {{ number_format($ticketStats->main_issued ?? 0) }}
                            </div>
                        </div>
                    </div>
                    <span class="ticket-pill-pct {{ $mainPct >= 50 ? 'pct-good' : ($mainPct > 0 ? 'pct-warn' : 'pct-zero') }}">
                        {{ $mainPct }}%
                    </span>
                </div>

                {{-- المرافقون --}}
                <div class="ticket-pill">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:38px;height:38px;border-radius:12px;background:rgba(14,165,233,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-users" style="color:#0ea5e9"></i>
                        </div>
                        <div>
                            <div style="font-size:.84rem;font-weight:700;color:var(--text-main)">{{ __('ui.analytics.ticket_guests') }}</div>
                            <div class="ticket-pill-sub">
                                {{ __('ui.analytics.ticket_checked_in') }}:
                                <strong style="color:var(--text-main)">{{ number_format($ticketStats->guest_checked_in ?? 0) }}</strong>
                                {{ __('ui.analytics.ticket_of') }} {{ number_format($ticketStats->guest_issued ?? 0) }}
                            </div>
                        </div>
                    </div>
                    <span class="ticket-pill-pct {{ $guestPct >= 50 ? 'pct-good' : ($guestPct > 0 ? 'pct-warn' : 'pct-zero') }}">
                        {{ $guestPct }}%
                    </span>
                </div>

                {{-- إجمالي شريط الحضور --}}
                <div style="border-top:1px solid var(--line);padding-top:1rem;margin-top:.25rem;">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                        <span style="color:var(--text-soft)">{{ __('ui.analytics.kpi_attendance') }}</span>
                        <span style="font-weight:700;color:var(--text-main)">{{ $attendanceRate }}%</span>
                    </div>
                    <div class="an-progress" style="height:10px;">
                        <div class="an-progress-fill" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);width:{{ $attendanceRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 3: Arrival Timeline + Daily Trend
══════════════════════════════════════════════════════════ --}}
<div class="row g-3 animate__animated animate__fadeInUp" style="animation-delay:.2s">

    {{-- منحنى الوصول --}}
    <div class="col-lg-6">
        <div class="an-card">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">
                        <i class="fas fa-chart-bar"></i>
                        {{ __('ui.analytics.arrival_timeline') }}
                    </div>
                    <div class="an-card-subtitle">{{ __('ui.analytics.arrival_subtitle') }}</div>
                </div>
            </div>
            <div class="an-card-body">
                @if($arrivalTimeline->isEmpty())
                    <div class="chart-empty">
                        <i class="fas fa-chart-simple"></i>
                        <span>{{ __('ui.analytics.arrival_no_data') }}</span>
                    </div>
                @else
                    <div class="chart-wrap">
                        <canvas id="arrivalChart" height="220"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- اتجاه الدعوات اليومي --}}
    <div class="col-lg-6">
        <div class="an-card">
            <div class="an-card-header">
                <div>
                    <div class="an-card-title">
                        <i class="fas fa-chart-line"></i>
                        {{ __('ui.analytics.daily_trend') }}
                    </div>
                    <div class="an-card-subtitle">{{ __('ui.analytics.daily_subtitle') }}</div>
                </div>
            </div>
            <div class="an-card-body">
                @if($dailyInvitations->isEmpty())
                    <div class="chart-empty">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ __('ui.analytics.daily_no_data') }}</span>
                    </div>
                @else
                    <div class="chart-wrap">
                        <canvas id="dailyChart" height="220"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

{{-- ══════════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════ --}}
@push('scripts')
{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
(function () {
    'use strict';

    // ── تحديد اتجاه RTL/LTR ──────────────────────────────
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

    // ── ألوان النظام ─────────────────────────────────────
    const primary  = '#0f8f83';
    const accent   = '#6366f1';
    const gridLine = 'rgba(220,232,228,.6)';

    // ── خيارات مشتركة ─────────────────────────────────────
    Chart.defaults.font.family = isRtl
        ? "'Cairo', 'Outfit', sans-serif"
        : "'Outfit', 'Cairo', sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#6a8a87';

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                rtl: isRtl,
                backgroundColor: '#102a2a',
                titleColor: '#e5f3f1',
                bodyColor: '#a6c4c0',
                padding: 10,
                cornerRadius: 10,
            },
        },
        scales: {
            x: {
                grid: { color: gridLine },
                ticks: { color: '#6a8a87' },
            },
            y: {
                beginAtZero: true,
                grid: { color: gridLine },
                ticks: { color: '#6a8a87', stepSize: 1, precision: 0 },
            },
        },
    };

    // ── Arrival Timeline Chart ─────────────────────────────
    @if(!$arrivalTimeline->isEmpty())
    const arrivalCtx = document.getElementById('arrivalChart');
    if (arrivalCtx) {
        const arrivalLabels = {!! $arrivalTimeline->pluck('hour')->map(fn($h) => sprintf('%02d:00', $h))->toJson() !!};
        const arrivalData   = {!! $arrivalTimeline->pluck('count')->toJson() !!};

        new Chart(arrivalCtx, {
            type: 'bar',
            data: {
                labels: arrivalLabels,
                datasets: [{
                    label: '{{ __("ui.analytics.axis_count") }}',
                    data: arrivalData,
                    backgroundColor: 'rgba(15,143,131,.22)',
                    borderColor: primary,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }],
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            title: (ctx) => '{{ __("ui.analytics.axis_hour") }}: ' + ctx[0].label,
                            label: (ctx) => ' ' + ctx.raw + ' {{ __("ui.analytics.axis_count") }}',
                        },
                    },
                },
            },
        });
    }
    @endif

    // ── Daily Invitations Chart ────────────────────────────
    @if(!$dailyInvitations->isEmpty())
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        const dailyLabels = {!! $dailyInvitations->pluck('day')->toJson() !!};
        const dailyData   = {!! $dailyInvitations->pluck('count')->toJson() !!};

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: '{{ __("ui.analytics.kpi_total_invitations") }}',
                    data: dailyData,
                    borderColor: accent,
                    backgroundColor: 'rgba(99,102,241,.1)',
                    borderWidth: 2.5,
                    pointBackgroundColor: accent,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: commonOptions,
        });
    }
    @endif

    // ── KPI Counter Animation ─────────────────────────────
    document.querySelectorAll('.an-kpi-value[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count, 10);
        if (isNaN(target) || target === 0) return;
        let start = 0;
        const dur = 1000, step = 14;
        const inc = target / (dur / step);
        const timer = setInterval(() => {
            start += inc;
            if (start >= target) {
                el.textContent = target.toLocaleString() + (el.textContent.includes('/') ? '' : '');
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(start).toLocaleString();
            }
        }, step);
    });

    // ── Progress bars animation on load ──────────────────
    requestAnimationFrame(() => {
        document.querySelectorAll('.an-progress-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = w; }, 100);
        });
    });

    // ── Auto-submit filter on event change ───────────────
    document.getElementById('event_id')?.addEventListener('change', function () {
        document.getElementById('filter-form')?.submit();
    });

})();
</script>
@endpush
