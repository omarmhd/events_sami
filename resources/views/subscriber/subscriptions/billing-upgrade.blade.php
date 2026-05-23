@extends('layouts.app')

@section('title', 'الترقية والفوترة')

@php
    $currentPlanCode = strtoupper($company->current_plan_code ?? optional($subscription->plan)->code ?? 'trial');
    $currentPlanLabel = optional($subscription->plan)->name ?? 'Trial';
    $recommendedPlan = $recommendedPlanCode ? $plans->firstWhere('code', $recommendedPlanCode) : null;
    $showTrialChip = !is_null($trialDaysLeft);
@endphp

@push('styles')
<style>
    .billing-shell {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .billing-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-xl);
        background:
            radial-gradient(circle at 12% 18%, rgba(255,255,255,.16), transparent 28%),
            radial-gradient(circle at 88% 0%, rgba(255,255,255,.14), transparent 24%),
            linear-gradient(135deg, #0f8f83 0%, #0c6f66 52%, #0b4f49 100%);
        box-shadow: 0 20px 50px -15px rgba(15,143,131,.38);
        color: #fff;
        padding: 2rem;
    }

    .billing-hero::after {
        content: '';
        position: absolute;
        inset-inline-end: -70px;
        inset-block-end: -70px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.13), transparent 68%);
        pointer-events: none;
    }

    .billing-hero-inner {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, .9fr);
        gap: 1.25rem;
        align-items: end;
    }

    .billing-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .4rem .85rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.2);
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .04em;
        margin-bottom: .85rem;
    }

    .billing-title {
        font-size: clamp(1.65rem, 2.6vw, 2.4rem);
        font-weight: 900;
        line-height: 1.15;
        margin: 0 0 .65rem;
        letter-spacing: -.03em;
    }

    .billing-subtitle {
        margin: 0;
        max-width: 58ch;
        color: rgba(255,255,255,.85);
        font-size: .96rem;
        line-height: 1.7;
    }

    .billing-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        margin-top: 1.15rem;
    }

    .billing-hero-btn {
        border-radius: 14px;
        padding: .75rem 1.15rem;
        font-weight: 700;
        font-size: .92rem;
        border: 1px solid transparent;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s, background .2s, color .2s, border-color .2s;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }

    .billing-hero-btn.primary {
        background: #fff;
        color: var(--primary-color);
        box-shadow: 0 10px 22px rgba(0,0,0,.12);
    }

    .billing-hero-btn.primary:hover {
        transform: translateY(-1px);
        color: var(--primary-color);
    }

    .billing-hero-btn.ghost {
        background: rgba(255,255,255,.12);
        color: #fff;
        border-color: rgba(255,255,255,.22);
    }

    .billing-hero-btn.ghost:hover {
        transform: translateY(-1px);
        background: rgba(255,255,255,.18);
        color: #fff;
    }

    .billing-hero-panel {
        justify-self: stretch;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 22px;
        padding: 1rem;
        backdrop-filter: blur(8px);
    }

    .billing-hero-panel .panel-title {
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .85rem;
        color: rgba(255,255,255,.75);
    }

    .billing-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
    }

    .billing-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .8rem;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 700;
        background: rgba(255,255,255,.12);
        color: #fff;
        border: 1px solid rgba(255,255,255,.18);
    }

    .billing-chip strong {
        font-weight: 900;
    }

    .billing-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 20px;
        box-shadow: 0 12px 26px rgba(15,23,42,.05);
        padding: 1rem 1.05rem;
        display: flex;
        align-items: flex-start;
        gap: .85rem;
        min-height: 100%;
    }

    .metric-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--primary-soft);
        color: var(--primary-color);
        font-size: 1rem;
    }

    .metric-label {
        font-size: .75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: var(--text-soft);
        margin-bottom: .2rem;
    }

    .metric-value {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.45;
    }

    .metric-sub {
        font-size: .8rem;
        color: var(--text-soft);
        line-height: 1.55;
        margin-top: .25rem;
    }

    .billing-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(290px, .78fr);
        gap: 1.25rem;
        align-items: start;
    }

    .billing-stack {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .billing-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 24px;
        box-shadow: 0 16px 36px rgba(15,23,42,.06);
        overflow: hidden;
    }

    .billing-card-head {
        padding: 1.25rem 1.35rem 1rem;
        border-bottom: 1px solid #eef2f7;
    }

    .billing-section-kicker {
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--primary-color);
        margin-bottom: .35rem;
    }

    .billing-section-title {
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--text-main);
        margin: 0;
        letter-spacing: -.02em;
    }

    .billing-section-subtitle {
        font-size: .88rem;
        color: var(--text-muted);
        margin: .35rem 0 0;
        line-height: 1.65;
    }

    .billing-plan-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .85rem;
        padding: 1rem 1.35rem 1.2rem;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fff, #fafcfb);
    }

    .billing-summary-pills {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        margin-inline-start: auto;
    }

    .billing-summary-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .46rem .82rem;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--text-main);
        font-size: .82rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .billing-plans-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .plan-card {
        background: linear-gradient(180deg, #fff, #fcfdfd);
        border: 1.5px solid #e8edf3;
        border-radius: 22px;
        padding: 1.15rem;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        transition: transform .22s, box-shadow .22s, border-color .22s;
    }

    .plan-card::before {
        content: '';
        position: absolute;
        inset-inline-end: -55px;
        inset-block-start: -55px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(15,143,131,.09), transparent 70%);
        pointer-events: none;
    }

    .plan-card:hover {
        transform: translateY(-3px);
        border-color: rgba(15,143,131,.24);
        box-shadow: 0 18px 34px rgba(15,23,42,.08);
    }

    .plan-card.is-featured {
        border-color: rgba(15,143,131,.3);
        box-shadow: 0 18px 34px rgba(15,143,131,.12);
    }

    .plan-card.is-selected {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(15,143,131,.11), 0 18px 34px rgba(15,143,131,.12);
    }

    .plan-card.enterprise {
        background: linear-gradient(145deg, #102a2a 0%, #0c1f1f 100%);
        border-color: transparent;
        color: #fff;
    }

    .plan-badge {
        position: absolute;
        top: 0;
        inset-inline-start: 0;
        padding: .35rem .85rem;
        border-radius: 0 0 14px 0;
        font-size: .72rem;
        font-weight: 800;
        color: #fff;
        background: var(--grad-primary);
        letter-spacing: .05em;
    }

    .plan-badge.popular {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .plan-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: .9rem;
        background: var(--primary-soft);
        color: var(--primary-color);
    }

    .plan-card.enterprise .plan-icon {
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.92);
    }

    .plan-name {
        font-size: 1.08rem;
        font-weight: 900;
        margin: 0 0 .35rem;
        color: var(--text-main);
    }

    .plan-card.enterprise .plan-name,
    .plan-card.enterprise .plan-desc,
    .plan-card.enterprise .plan-period,
    .plan-card.enterprise .plan-note,
    .plan-card.enterprise .plan-price-label {
        color: rgba(255,255,255,.78);
    }

    .plan-price-row {
        display: flex;
        align-items: flex-end;
        gap: .35rem;
        margin-bottom: .25rem;
    }

    .plan-price {
        font-size: 2.1rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.04em;
        color: var(--text-main);
    }

    .plan-card.enterprise .plan-price {
        color: #fff;
    }

    .plan-price-label {
        font-size: .92rem;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: .3rem;
    }

    .plan-period {
        font-size: .8rem;
        color: var(--text-soft);
        margin-bottom: .95rem;
    }

    .plan-desc {
        font-size: .85rem;
        line-height: 1.65;
        color: var(--text-muted);
        margin-bottom: .95rem;
        min-height: 54px;
    }

    .plan-divider {
        border: none;
        border-top: 1px solid #eef2f7;
        margin: 0 0 .95rem;
    }

    .plan-card.enterprise .plan-divider {
        border-top-color: rgba(255,255,255,.12);
    }

    .billing-feature-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: .55rem;
        flex: 1;
    }

    .billing-feature-list li {
        display: flex;
        align-items: flex-start;
        gap: .55rem;
        font-size: .83rem;
        line-height: 1.55;
        color: var(--text-muted);
    }

    .plan-card.enterprise .billing-feature-list li {
        color: rgba(255,255,255,.74);
    }

    .billing-feature-icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(15,143,131,.12);
        color: var(--primary-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: .62rem;
        margin-top: .15rem;
    }

    .plan-card.enterprise .billing-feature-icon {
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.82);
    }

    .plan-limit {
        font-size: .72rem;
        color: var(--text-soft);
        font-weight: 700;
    }

    .plan-cta {
        margin-top: 1rem;
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: .78rem 1rem;
        font-weight: 800;
        font-size: .92rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        cursor: pointer;
        transition: transform .2s, box-shadow .2s, background .2s, color .2s;
    }

    .plan-cta.primary {
        background: var(--grad-primary);
        color: #fff;
        box-shadow: 0 10px 22px rgba(15,143,131,.32);
    }

    .plan-cta.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15,143,131,.38);
    }

    .plan-cta.outline {
        background: transparent;
        color: var(--primary-color);
        border: 1.5px solid var(--primary-color);
    }

    .plan-cta.outline:hover {
        background: var(--primary-soft);
    }

    .plan-cta.enterprise {
        background: rgba(255,255,255,.14);
        color: #fff;
        border: 1.5px solid rgba(255,255,255,.26);
    }

    .plan-cta.enterprise:hover {
        background: rgba(255,255,255,.22);
        transform: translateY(-1px);
    }

    .billing-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: sticky;
        top: 1rem;
    }

    .sidebar-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        box-shadow: 0 14px 30px rgba(15,23,42,.05);
        padding: 1.15rem;
    }

    .sidebar-card.dark {
        background: linear-gradient(145deg, #102a2a 0%, #0c1f1f 100%);
        color: #fff;
        border-color: transparent;
    }

    .sidebar-title {
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-soft);
        margin-bottom: .5rem;
    }

    .sidebar-card.dark .sidebar-title {
        color: rgba(255,255,255,.68);
    }

    .sidebar-main {
        font-size: 1rem;
        font-weight: 900;
        color: var(--text-main);
        margin-bottom: .25rem;
    }

    .sidebar-card.dark .sidebar-main,
    .sidebar-card.dark .sidebar-desc,
    .sidebar-card.dark .sidebar-step,
    .sidebar-card.dark .sidebar-note {
        color: rgba(255,255,255,.84);
    }

    .sidebar-desc,
    .sidebar-note {
        font-size: .84rem;
        color: var(--text-muted);
        line-height: 1.65;
    }

    .sidebar-list {
        display: flex;
        flex-direction: column;
        gap: .8rem;
        margin-top: 1rem;
    }

    .sidebar-item {
        display: flex;
        align-items: flex-start;
        gap: .8rem;
    }

    .sidebar-dot {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--primary-soft);
        color: var(--primary-color);
    }

    .sidebar-step {
        font-size: .84rem;
        color: var(--text-muted);
        line-height: 1.65;
    }

    .compare-table-wrap {
        overflow: auto;
        border-radius: 18px;
        border: 1px solid #eef2f7;
    }

    .billing-compare-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
        background: #fff;
    }

    .billing-compare-table th,
    .billing-compare-table td {
        padding: .95rem 1rem;
        border-bottom: 1px solid #eef2f7;
        text-align: center;
        font-size: .86rem;
    }

    .billing-compare-table thead th {
        background: #f8fafc;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .07em;
        font-weight: 800;
        color: var(--text-soft);
    }

    .billing-compare-table tbody td:first-child,
    .billing-compare-table thead th:first-child {
        text-align: right;
        font-weight: 700;
        color: var(--text-main);
    }

    .compare-yes {
        color: #166534;
        font-weight: 800;
    }

    .compare-no {
        color: #94a3b8;
        font-weight: 700;
    }

    .assessment-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(260px, .75fr);
        gap: 1rem;
        align-items: stretch;
    }

    .assessment-panel {
        border: 1px solid #e7eef2;
        border-radius: 20px;
        background: linear-gradient(180deg, #fff, #fbfcfd);
        padding: 1.1rem;
    }

    .app-modal-field label {
        font-size: .82rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: .35rem;
        display: block;
    }

    .app-modal-field .form-control,
    .app-modal-field .form-select {
        background: var(--surface-soft);
        border: 1.5px solid var(--line);
        border-radius: var(--radius-sm);
        color: var(--text-main);
        font-size: .875rem;
        padding: .7rem .9rem;
        transition: border-color .2s, box-shadow .2s;
    }

    .app-modal-field .form-control:focus,
    .app-modal-field .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15,143,131,.12);
        background: #fff;
    }

    .trust-grid {
        display: grid;
        gap: .75rem;
    }

    .trust-item {
        display: flex;
        gap: .75rem;
        align-items: flex-start;
    }

    .trust-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15,143,131,.1);
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .trust-title {
        font-size: .88rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: .1rem;
    }

    .trust-desc {
        font-size: .8rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .selected-plan-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .8rem;
        border-radius: 999px;
        background: var(--primary-soft);
        border: 1px solid rgba(15,143,131,.25);
        color: var(--primary-color);
        font-size: .82rem;
        font-weight: 800;
    }

    .btn-send {
        background: var(--grad-primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-size: .92rem;
        font-weight: 800;
        padding: .75rem 1.6rem;
        cursor: pointer;
        transition: all .2s;
        box-shadow: 0 10px 22px rgba(15,143,131,.28);
    }

    .btn-send:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 26px rgba(15,143,131,.36);
    }

    @media (max-width: 1200px) {
        .billing-hero-inner,
        .billing-layout,
        .assessment-grid {
            grid-template-columns: 1fr;
        }

        .billing-sidebar {
            position: static;
        }

        .billing-plans-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .billing-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .billing-hero,
        .billing-card,
        .sidebar-card,
        .metric-card {
            border-radius: 18px;
        }

        .billing-hero {
            padding: 1.25rem;
        }

        .billing-plans-grid,
        .billing-metrics {
            grid-template-columns: 1fr;
        }

        .billing-plan-toolbar {
            padding: 1rem 1rem .9rem;
        }

        .billing-summary-pills {
            margin-inline-start: 0;
            width: 100%;
        }

        .plan-desc {
            min-height: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="billing-shell">
    <section class="billing-hero animate__animated animate__fadeInDown">
        <div class="billing-hero-inner">
            <div>
                <div class="billing-kicker">
                    <i class="fas fa-wallet"></i>
                    {{ __('ui.billing.hero_kicker') }}
                </div>
                <h1 class="billing-title">{{ __('ui.billing.hero_title') }}</h1>
                <p class="billing-subtitle">{{ __('ui.billing.hero_subtitle') }}</p>

                <div class="billing-hero-actions">
                    <a href="#billing-plans" class="billing-hero-btn primary">
                        <i class="fas fa-layer-group"></i>
                        استعرض الخطط
                    </a>
                    <a href="#billing-assessment" class="billing-hero-btn ghost">
                        <i class="fas fa-wand-sparkles"></i>
                        احصل على توصية
                    </a>
                </div>
            </div>

            <div class="billing-hero-panel">
                <div class="panel-title">ملخص سريع</div>
                <div class="billing-chip-row">
                    <span class="billing-chip">
                        <i class="fas fa-circle-check"></i>
                        الحالية: <strong>{{ $currentPlanCode }}</strong>
                    </span>
                    <span class="billing-chip">
                        <i class="fas fa-user-tie"></i>
                        {{ $company->name }}
                    </span>
                    @if($showTrialChip)
                        <span class="billing-chip">
                            <i class="fas fa-hourglass-half"></i>
                            {{ $trialDaysLeft }} يوم تجربة
                        </span>
                    @endif
                </div>

                @if($recommendedPlan)
                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.18);">
                        <div class="panel-title" style="margin-bottom:.4rem;">الخيار المقترح</div>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div style="width:40px;height:40px;border-radius:14px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <div style="font-size:.95rem;font-weight:900;">{{ $recommendedPlan->name }}</div>
                                <div style="font-size:.8rem;color:rgba(255,255,255,.72);">{{ $recommendedPlan->highlight_label ?? 'خطة موصى بها حسب احتياجك' }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="billing-metrics">
        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <div class="metric-label">الخطة الحالية</div>
                <div class="metric-value">{{ $currentPlanLabel }}</div>
                <div class="metric-sub">رمز الخطة: {{ $currentPlanCode }}</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-shield-halved"></i></div>
            <div>
                <div class="metric-label">حالة الاشتراك</div>
                <div class="metric-value">{{ strtoupper($subscription->status) }}</div>
                <div class="metric-sub">إدارة الاشتراك من لوحة الفوترة</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-building"></i></div>
            <div>
                <div class="metric-label">الشركة</div>
                <div class="metric-value">{{ $company->name }}</div>
                <div class="metric-sub">{{ $company->contact_email ?? auth()->user()->email ?? '—' }}</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-bolt"></i></div>
            <div>
                <div class="metric-label">الحالة الحالية</div>
                <div class="metric-value">{{ $recommendedPlan ? 'موصى بالترقية' : 'مستقر' }}</div>
                <div class="metric-sub">اختر الخطة المناسبة ثم أرسل الطلب</div>
            </div>
        </div>
    </section>

    <div class="billing-layout">
        <div class="billing-stack">
            <section class="billing-card" id="billing-plans">
                <div class="billing-card-head d-flex flex-wrap align-items-start justify-content-between gap-2">
                    <div>
                        <div class="billing-section-kicker"><i class="fas fa-layer-group me-1"></i> خطط الاشتراك</div>
                        <h2 class="billing-section-title">قارن الخطط وابدأ من هنا</h2>
                        <p class="billing-section-subtitle">اختر الخطة الأقرب لاحتياجك، وسيتواصل معك فريقنا لإتمام التفعيل والدفع.</p>
                    </div>
                    <div class="billing-summary-pills">
                        <span class="billing-summary-pill">
                            <i class="fas fa-circle-check text-success"></i>
                            {{ $plans->count() }} خطط متاحة
                        </span>
                        @if($recommendedPlan)
                            <span class="billing-summary-pill">
                                <i class="fas fa-lightbulb text-warning"></i>
                                المقترحة: {{ $recommendedPlan->code }}
                            </span>
                        @endif
                    </div>
                </div>

                 <div id="billing-contact-errors"
                     class="d-none"
                     data-should-open="{{ $errors->hasAny(['contact_name', 'contact_email', 'contact_phone', 'message', 'plan_code']) ? '1' : '0' }}"
                     data-plan-code="{{ old('plan_code', '') }}"
                     data-plan-name="{{ $plans->firstWhere('code', old('plan_code', ''))?->name ?? '' }}"></div>

                 <div class="billing-plans-grid">
                    @forelse($plans as $plan)
                        @php
                            $features = $plan->featureList();
                            $isPopular = !empty($plan->highlight_label) && (str_contains(mb_strtolower($plan->highlight_label), 'popular') || str_contains($plan->highlight_label, 'شعبي') || str_contains($plan->highlight_label, 'الأكثر'));
                            $isEnterprise = $plan->code === 'enterprise';
                            $isSelected = (string) $currentPlanCode === strtoupper((string) $plan->code);
                            $ctaLabel = $isEnterprise ? 'تواصل مع المبيعات' : 'أرسل طلب الترقية';
                            $buttonClass = $isEnterprise ? 'enterprise' : ($isPopular ? 'primary' : 'outline');
                        @endphp
                        <article class="plan-card {{ $isEnterprise ? 'enterprise' : '' }} {{ $isPopular ? 'is-featured' : '' }} {{ $isSelected ? 'is-selected' : '' }}">
                            @if($plan->highlight_label)
                                <span class="plan-badge {{ $isPopular ? 'popular' : '' }}">{{ $plan->highlight_label }}</span>
                            @endif

                            <div class="plan-icon">
                                @if($isEnterprise)
                                    <i class="fas fa-building-columns"></i>
                                @elseif($plan->code === 'professional')
                                    <i class="fas fa-rocket"></i>
                                @elseif($plan->code === 'starter')
                                    <i class="fas fa-seedling"></i>
                                @else
                                    <i class="fas fa-box"></i>
                                @endif
                            </div>

                            <div class="plan-name">{{ $plan->name }}</div>
                            @if($isSelected)
                                <div class="plan-price-label">خطتك الحالية</div>
                            @elseif(!empty($plan->highlight_label))
                                <div class="plan-price-label">{{ $plan->highlight_label }}</div>
                            @endif

                            <div class="plan-price-row">
                                @if((float) $plan->annual_price > 0)
                                    <span class="plan-price">{{ number_format($plan->annual_price, 0) }}</span>
                                    <span class="plan-price-label">ر.س</span>
                                @else
                                    <span class="plan-price" style="font-size:1.6rem;">مجاني</span>
                                @endif
                            </div>
                            <div class="plan-period">{{ (float) $plan->annual_price > 0 ? 'سنوياً - شامل الضريبة' : 'دائماً' }}</div>

                            @if(!empty($plan->description))
                                <p class="plan-desc">{{ $plan->description }}</p>
                            @endif

                            <hr class="plan-divider">

                            <ul class="billing-feature-list">
                                @foreach($features as $feature)
                                    <li>
                                        <span class="billing-feature-icon">
                                            <i class="{{ $feature['icon'] ?? 'fas fa-circle-check' }}"></i>
                                        </span>
                                        <span>
                                            {{ $feature['text'] }}
                                            @if(!empty($feature['limit']))
                                                <span class="plan-limit">({{ $feature['limit'] }})</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                                    <button type="button"
                                        class="plan-cta {{ $buttonClass }}"
                                        data-plan-code="{{ $plan->code }}"
                                        data-plan-name="{{ $plan->name }}">
                                <i class="fas {{ $isEnterprise ? 'fa-handshake' : ($isSelected ? 'fa-circle-check' : 'fa-arrow-up-right-from-square') }}"></i>
                                {{ $ctaLabel }}
                            </button>
                        </article>
                    @empty
                        <div class="col-12">
                            <div class="p-4 text-center text-muted">
                                لا توجد خطط متاحة حالياً.
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="billing-card">
                <div class="billing-card-head">
                    <div class="billing-section-kicker"><i class="fas fa-table-list me-1"></i> مقارنة سريعة</div>
                    <h2 class="billing-section-title">أهم الفروقات الأساسية</h2>
                    <p class="billing-section-subtitle">نظرة سريعة تساعدك على اتخاذ القرار بدون الرجوع للتفاصيل الكاملة في كل بطاقة.</p>
                </div>
                <div class="p-3 p-lg-4 compare-table-wrap">
                    <table class="billing-compare-table">
                        <thead>
                            <tr>
                                <th>الميزة</th>
                                @foreach($plans as $plan)
                                    <th>{{ $plan->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>الفعاليات السنوية</td>
                                @foreach($plans as $plan)
                                    <td>
                                        {{ $plan->annual_event_limit ? $plan->annual_event_limit : 'غير محدود' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>الدعوات لكل فعالية</td>
                                @foreach($plans as $plan)
                                    <td>
                                        {{ $plan->per_event_invitee_limit ? $plan->per_event_invitee_limit : 'غير محدود' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>استيراد CSV</td>
                                @foreach($plans as $plan)
                                    <td class="{{ $plan->includes_csv_import ? 'compare-yes' : 'compare-no' }}">
                                        {{ $plan->includes_csv_import ? 'متاح' : 'غير متاح' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>إعادة الإرسال الجماعي</td>
                                @foreach($plans as $plan)
                                    <td class="{{ $plan->includes_bulk_resend ? 'compare-yes' : 'compare-no' }}">
                                        {{ $plan->includes_bulk_resend ? 'متاح' : 'غير متاح' }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>التخصيص المتقدم</td>
                                @foreach($plans as $plan)
                                    <td class="{{ $plan->includes_customization ? 'compare-yes' : 'compare-no' }}">
                                        {{ $plan->includes_customization ? 'متاح' : 'غير متاح' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="billing-card" id="billing-assessment">
                <div class="billing-card-head">
                    <div class="billing-section-kicker"><i class="fas fa-compass me-1"></i> مساعد الاختيار</div>
                    <h2 class="billing-section-title">أجب على ثلاث نقاط وسنرشدك للخطة الأنسب</h2>
                    <p class="billing-section-subtitle">هذا القسم يختصر عليك التخمين ويعطيك توصية عملية قبل إرسال الطلب.</p>
                </div>
                <div class="p-3 p-lg-4">
                    <div class="assessment-grid">
                        <div class="assessment-panel">
                            <form action="{{ route('billing.assess') }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-12 col-md-6 app-modal-field">
                                    <label>عدد الفعاليات السنوية</label>
                                    <input type="number"
                                           name="annual_events"
                                           min="1"
                                           max="5000"
                                           class="form-control @error('annual_events') is-invalid @enderror"
                                           placeholder="مثال: 12"
                                           required>
                                    @error('annual_events')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 col-md-6 app-modal-field">
                                    <label>متوسط الحضور في كل فعالية</label>
                                    <input type="number"
                                           name="average_attendance"
                                           min="1"
                                           max="100000"
                                           class="form-control @error('average_attendance') is-invalid @enderror"
                                           placeholder="مثال: 150"
                                           required>
                                    @error('average_attendance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 app-modal-field">
                                    <label>هل تحتاج تخصيصًا للتصميم؟</label>
                                    <select name="needs_customization" class="form-select @error('needs_customization') is-invalid @enderror" required>
                                        <option value="no">لا</option>
                                        <option value="yes">نعم</option>
                                    </select>
                                    @error('needs_customization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="plan-cta primary" style="width:auto;">
                                        <i class="fas fa-wand-sparkles"></i>
                                        احصل على التوصية
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="sidebar-card dark" style="margin:0;">
                            <div class="sidebar-title">لماذا هذه الصفحة مختلفة</div>
                            <div class="sidebar-main" style="color:#fff;">ترتيب سريع، قرار أسهل، وخطوة تالية واضحة</div>
                            <div class="sidebar-note" style="color:rgba(255,255,255,.78);">
                                صممت الصفحة لتقودك من فهم الخطة الحالية إلى المقارنة ثم الإجراء النهائي بدون تشتيت.
                            </div>
                            <div class="sidebar-list" style="margin-top:1rem;">
                                <div class="sidebar-item">
                                    <div class="sidebar-dot" style="background:rgba(255,255,255,.12);color:#fff;"><i class="fas fa-bolt"></i></div>
                                    <div class="sidebar-step" style="color:rgba(255,255,255,.8);">بطاقات الخطط تعرض السعر والميزات بشكل مباشر.</div>
                                </div>
                                <div class="sidebar-item">
                                    <div class="sidebar-dot" style="background:rgba(255,255,255,.12);color:#fff;"><i class="fas fa-comments"></i></div>
                                    <div class="sidebar-step" style="color:rgba(255,255,255,.8);">طلب الترقية يفتح نموذجًا واحدًا واضحًا وسريعًا.</div>
                                </div>
                                <div class="sidebar-item">
                                    <div class="sidebar-dot" style="background:rgba(255,255,255,.12);color:#fff;"><i class="fas fa-shield-halved"></i></div>
                                    <div class="sidebar-step" style="color:rgba(255,255,255,.8);">لا يوجد خصم الآن، بل متابعة من الفريق وإكمال آمن.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="billing-sidebar">
            <div class="sidebar-card">
                <div class="sidebar-title">ملخص الاشتراك</div>
                <div class="sidebar-main">{{ $company->name }}</div>
                <div class="sidebar-desc">الخطة الحالية: {{ $currentPlanLabel }} ({{ $currentPlanCode }})</div>

                <div class="sidebar-list">
                    <div class="sidebar-item">
                        <div class="sidebar-dot"><i class="fas fa-circle-check"></i></div>
                        <div>
                            <div class="trust-title">حالة الاشتراك</div>
                            <div class="sidebar-note">{{ strtoupper($subscription->status) }}</div>
                        </div>
                    </div>
                    @if($showTrialChip)
                        <div class="sidebar-item">
                            <div class="sidebar-dot"><i class="fas fa-hourglass-half"></i></div>
                            <div>
                                <div class="trust-title">أيام التجربة المتبقية</div>
                                <div class="sidebar-note">{{ $trialDaysLeft }} يوم</div>
                            </div>
                        </div>
                    @endif
                    @if($recommendedPlan)
                        <div class="sidebar-item">
                            <div class="sidebar-dot"><i class="fas fa-lightbulb"></i></div>
                            <div>
                                <div class="trust-title">الخطة المقترحة</div>
                                <div class="sidebar-note">{{ $recommendedPlan->name }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-title">كيف تتم الترقية</div>
                <div class="sidebar-list">
                    <div class="sidebar-item">
                        <div class="sidebar-dot"><i class="fas fa-1"></i></div>
                        <div class="sidebar-step">اختر الخطة المناسبة من البطاقات أعلاه.</div>
                    </div>
                    <div class="sidebar-item">
                        <div class="sidebar-dot"><i class="fas fa-2"></i></div>
                        <div class="sidebar-step">املأ البيانات الأساسية في نافذة الطلب.</div>
                    </div>
                    <div class="sidebar-item">
                        <div class="sidebar-dot"><i class="fas fa-3"></i></div>
                        <div class="sidebar-step">نتواصل معك خلال 24 ساعة لإتمام التفعيل.</div>
                    </div>
                </div>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-title">ضمانات المنصة</div>
                <div class="trust-grid">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fas fa-lock"></i></div>
                        <div>
                            <div class="trust-title">أمان على مستوى المؤسسات</div>
                            <div class="trust-desc">تشفير للبيانات ونسخ احتياطية منظمة.</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fas fa-headset"></i></div>
                        <div>
                            <div class="trust-title">دعم فني متخصص</div>
                            <div class="trust-desc">متابعة واضحة وفريق يعرف المنتج والاشتراكات.</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fas fa-rotate"></i></div>
                        <div>
                            <div class="trust-title">ترقية بدون انقطاع</div>
                            <div class="trust-desc">لا توقف التشغيل أثناء نقل الخطة أو مراجعة الطلب.</div>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fas fa-receipt"></i></div>
                        <div>
                            <div class="trust-title">فواتير رسمية</div>
                            <div class="trust-desc">فاتورة بالريال السعودي وشاملة الضريبة.</div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('modals')
<x-modal id="contactModal"
         title="أرسل طلب الترقية"
         subtitle="سيتواصل فريقنا معك خلال 24 ساعة لإتمام الدفع وتفعيل خطتك."
         static="true">

    <div class="modal-body">
        <div class="app-modal-info-banner">
            <i class="fas fa-circle-info" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
            <span>لن يتم خصم أي مبلغ الآن — فريقنا سيتواصل معك ويرشدك خلال عملية الدفع بالكامل.</span>
        </div>

        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
            <span style="font-size:.8rem;color:var(--text-soft);font-weight:600;">الخطة المختارة:</span>
            <span class="selected-plan-chip" id="selectedPlanDisplay">—</span>
        </div>

        <form id="contactForm" action="{{ route('billing.contact-request') }}" method="POST">
            @csrf
            <input type="hidden" name="plan_code" id="planCodeInput">

            <div class="row g-3">
                <div class="col-md-6 app-modal-field">
                    <label>الاسم الكامل <span style="color:var(--danger-color);">*</span></label>
                    <input type="text" name="contact_name"
                           class="form-control @error('contact_name') is-invalid @enderror"
                           value="{{ old('contact_name', auth()->user()->name ?? '') }}"
                           placeholder="اسمك الكامل" required>
                    @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 app-modal-field">
                    <label>البريد الإلكتروني <span style="color:var(--danger-color);">*</span></label>
                    <input type="email" name="contact_email"
                           class="form-control @error('contact_email') is-invalid @enderror"
                           value="{{ old('contact_email', auth()->user()->email ?? '') }}"
                           placeholder="email@example.com" required>
                    @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 app-modal-field">
                    <label>رقم الجوال <span style="font-size:.72rem;color:var(--text-soft);font-weight:400;">(اختياري)</span></label>
                    <input type="tel" name="contact_phone"
                           class="form-control @error('contact_phone') is-invalid @enderror"
                           value="{{ old('contact_phone') }}"
                           placeholder="+966 5x xxx xxxx">
                    @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 app-modal-field">
                    <label>رسالة إضافية <span style="font-size:.72rem;color:var(--text-soft);font-weight:400;">(اختياري)</span></label>
                    <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                              rows="3" placeholder="أي تفاصيل تود مشاركتها مع الفريق...">{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" form="contactForm" class="btn-send">
            <i class="fas fa-paper-plane me-1"></i>
            إرسال الطلب
        </button>
    </div>
</x-modal>
@endpush

@push('scripts')
<script>
function openContactModal(planCode, planName) {
    const codeInput = document.getElementById('planCodeInput');
    const labelNode = document.getElementById('selectedPlanDisplay');
    if (codeInput) {
        codeInput.value = planCode;
    }
    if (labelNode) {
        labelNode.textContent = planName;
    }

    const modalNode = document.getElementById('contactModal');
    if (modalNode) {
        new bootstrap.Modal(modalNode).show();
    }
}

document.addEventListener('click', function (event) {
    const trigger = event.target.closest('.plan-cta[data-plan-code]');
    if (!trigger) {
        return;
    }

    event.preventDefault();
    openContactModal(trigger.dataset.planCode || '', trigger.dataset.planName || '');
});

document.addEventListener('DOMContentLoaded', function () {
    const preset = document.getElementById('billing-contact-errors');
    if (!preset || preset.dataset.shouldOpen !== '1') {
        return;
    }

    const planCode = preset.dataset.planCode || '';
    const planName = preset.dataset.planName || '';
    if (planCode) {
        openContactModal(planCode, planName);
    }
});
</script>
@endpush