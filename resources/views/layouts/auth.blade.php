<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Platform'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/auth-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="auth-body {{ app()->getLocale() === 'ar' ? 'auth-rtl' : '' }}">
@php
    $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
    $supportEmail = \App\Models\SystemSetting::get('support_email', '');
@endphp

<div class="auth-shell">
    <div class="auth-grid {{ trim($__env->yieldContent('hide_visual_panel')) ? 'auth-grid--compact' : '' }}">

        @if(!trim($__env->yieldContent('hide_visual_panel')))

        {{-- ══════════════════════════════════════════════════════════
             LEFT / TOP PANEL — Visual branding & feature highlights
        ══════════════════════════════════════════════════════════ --}}
        <section class="auth-panel-visual">

            {{-- Decorative floating shapes --}}
            <div class="auth-deco auth-deco--1" aria-hidden="true"></div>
            <div class="auth-deco auth-deco--2" aria-hidden="true"></div>
            <div class="auth-deco auth-deco--3" aria-hidden="true"></div>

            {{-- ── Brand row ── --}}
            <div class="auth-visual-top">
                <div class="auth-brand-row">
                    <x-platform-logo size="md" theme="dark" />
                    <span class="auth-brand-pill">{{ $platformName }}</span>
                </div>

                <h1 class="auth-heading">
                    @yield('visual_title', __('ui.auth.visual_title_default'))
                </h1>
                <p class="auth-subheading">
                    @yield('visual_subtitle', __('ui.auth.visual_subtitle_default'))
                </p>

                @hasSection('visual_badges')
                    @yield('visual_badges')
                @endif

                {{-- ── Illustrated feature cards ── --}}
                <div class="auth-feature-cards" aria-label="Platform features">
                    <div class="auth-feat-card">
                        <div class="auth-feat-icon auth-feat-icon--green">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div class="auth-feat-body">
                            <div class="auth-feat-title">{{ __('ui.auth.feat_invitations') }}</div>
                            <div class="auth-feat-desc">{{ __('ui.auth.feat_invitations_desc') }}</div>
                        </div>
                    </div>
                    <div class="auth-feat-card">
                        <div class="auth-feat-icon auth-feat-icon--gold">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div class="auth-feat-body">
                            <div class="auth-feat-title">{{ __('ui.auth.feat_checkin') }}</div>
                            <div class="auth-feat-desc">{{ __('ui.auth.feat_checkin_desc') }}</div>
                        </div>
                    </div>
                    <div class="auth-feat-card">
                        <div class="auth-feat-icon auth-feat-icon--teal">
                            <i class="fas fa-chart-column"></i>
                        </div>
                        <div class="auth-feat-body">
                            <div class="auth-feat-title">{{ __('ui.auth.feat_analytics') }}</div>
                            <div class="auth-feat-desc">{{ __('ui.auth.feat_analytics_desc') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Stats row ── --}}
            <div class="auth-stats-grid">
                <div class="auth-stat-card">
                    <span class="auth-stat-number">99.9%</span>
                    <span class="auth-stat-label">{{ __('ui.auth.stat_delivery') }}</span>
                </div>
                <div class="auth-stat-card">
                    <span class="auth-stat-number">
                        <i class="fas fa-building-columns fa-sm"></i>
                    </span>
                    <span class="auth-stat-label">{{ __('ui.auth.stat_multitenant') }}</span>
                </div>
                <div class="auth-stat-card">
                    <span class="auth-stat-number">
                        <i class="fas fa-bolt fa-sm"></i>
                    </span>
                    <span class="auth-stat-label">{{ __('ui.auth.stat_realtime') }}</span>
                </div>
            </div>

            @hasSection('visual_footer')
                @yield('visual_footer')
            @endif
        </section>
           @endif

        {{-- ══════════════════════════════════════════════════════════
             RIGHT / BOTTOM PANEL — Auth form
        ══════════════════════════════════════════════════════════ --}}
        <section class="auth-panel-form">

            {{-- Mobile-only logo (visible only on small screens where visual panel is hidden) --}}
            <div class="auth-mobile-brand d-lg-none mb-4 text-center">
                <x-platform-logo size="sm" theme="light" />
            </div>

            <div class="auth-card">
                <h2 class="auth-card-title">@yield('auth_title', __('ui.auth.welcome'))</h2>
                @hasSection('auth_subtitle')
                    <p class="auth-card-subtitle">@yield('auth_subtitle')</p>
                @endif

                @yield('auth-content')
            </div>
        </section>

    </div>
</div>

<footer class="auth-footer">
    <div class="auth-footer-inner">
        <div class="auth-footer-branding">
            <x-platform-logo size="sm" theme="light" />
            <div class="auth-footer-brand-copy">
                <p class="auth-footer-brand-title">مع تحيات منصة {{ $platformName }}</p>
                <p class="auth-footer-brand-subtitle">نقدّم تجربة واضحة ومترابطة في صفحات الزوار كلها.</p>
            </div>
        </div>

        @if(!empty($supportEmail))
            <div class="auth-footer-stack">
                <a class="auth-footer-email" href="mailto:{{ $supportEmail }}">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $supportEmail }}</span>
                </a>
            </div>
        @endif
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
