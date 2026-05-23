@extends('layouts.front')

@section('title', 'تعرف على منصة معا')

@push('styles')
<style>
    :root {
        --about-dark: #173c39;
        --about-accent: #dabc9a;
        --about-soft: #f4f6f8;
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(218, 188, 154, 0.18), transparent 34%),
            radial-gradient(circle at top right, rgba(15, 143, 131, 0.16), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #f7fafb 100%);
    }

    .about-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .about-hero {
        position: relative;
        overflow: hidden;
        border-radius: 32px;
        padding: 3rem;
        background: linear-gradient(135deg, #0d3b37 0%, #0f6b62 48%, #dabc9a 100%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(15, 59, 55, 0.18);
    }

    .about-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255,255,255,.16), transparent 28%),
                    radial-gradient(circle at 80% 30%, rgba(255,255,255,.12), transparent 24%);
        pointer-events: none;
    }

    .about-hero-inner {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 1.5rem;
        grid-template-columns: 1.15fr .85fr;
        align-items: center;
    }

    .about-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .9rem;
        border-radius: 999px;
        background: rgba(255,255,255,.16);
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .04em;
    }

    .about-hero h1 {
        font-size: clamp(2rem, 4vw, 3.6rem);
        font-weight: 800;
        line-height: 1.1;
        margin: 1rem 0 .9rem;
    }

    .about-lead {
        max-width: 52ch;
        font-size: 1.08rem;
        line-height: 1.9;
        color: rgba(255,255,255,.92);
        margin-bottom: 1.5rem;
    }

    .about-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
    }

    .about-btn {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .85rem 1.2rem;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .about-btn:hover {
        transform: translateY(-1px);
    }

    .about-btn--light {
        background: #fff;
        color: var(--about-dark);
        box-shadow: 0 14px 28px rgba(0,0,0,.12);
    }

    .about-btn--soft {
        background: rgba(255,255,255,.14);
        color: #fff;
        border: 1px solid rgba(255,255,255,.2);
    }

    .about-card {
        background: #fff;
        border: 1px solid rgba(15, 143, 131, 0.08);
        border-radius: 24px;
        padding: 1.4rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .about-stat {
        display: grid;
        gap: .35rem;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(15, 143, 131, 0.08);
    }

    .about-stat:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .about-stat strong {
        font-size: 1.1rem;
        color: var(--about-dark);
    }

    .about-stat span {
        color: #5b6472;
        font-size: .94rem;
        line-height: 1.7;
    }

    .about-section {
        padding: 1.5rem 0 0;
    }

    .about-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.25rem;
    }

    .feature-card {
        background: #fff;
        border: 1px solid rgba(34,34,34,.08);
        border-radius: 22px;
        padding: 1.3rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
    }

    .feature-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 143, 131, 0.1);
        color: var(--about-dark);
        margin-bottom: .9rem;
    }

    .feature-card h3 {
        font-size: 1.05rem;
        margin-bottom: .55rem;
        color: var(--about-dark);
    }

    .feature-card p {
        margin: 0;
        color: #5b6472;
        line-height: 1.8;
        font-size: .95rem;
    }

    .about-contact {
        margin: 1.8rem 0 0;
        background: linear-gradient(135deg, rgba(15,143,131,.08), rgba(218,188,154,.1));
        border: 1px solid rgba(15,143,131,.12);
        border-radius: 24px;
        padding: 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .about-contact h2 {
        font-size: 1.2rem;
        margin: 0 0 .35rem;
        color: var(--about-dark);
    }

    .about-contact p {
        margin: 0;
        color: #5b6472;
    }

    .about-contact a {
        text-decoration: none;
    }

    @media (max-width: 992px) {
        .about-hero-inner,
        .about-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .about-hero {
            padding: 1.5rem;
            border-radius: 24px;
        }

        .about-actions,
        .about-contact {
            flex-direction: column;
            align-items: stretch;
        }

        .about-btn,
        .about-contact a {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
@php
    $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
    $supportEmail = \App\Models\SystemSetting::get('support_email', '');
    $platformLogo = \App\Models\SystemSetting::get('platform_logo_url', '');
@endphp

<div class="container py-4 py-lg-5 about-page">
    <section class="about-hero">
        <div class="about-hero-inner">
            <div>
                <span class="about-kicker">
                    <i class="fas fa-stars"></i>
                    منصة {{ $platformName }}
                </span>

                <h1>حل عملي لإدارة الدعوات، التذاكر، والحضور من مكان واحد</h1>
                <p class="about-lead">
                    معا تساعدك على إنشاء الفعاليات، إرسال الدعوات، متابعة الردود، توليد رموز QR، وتقديم تجربة منظمة وواضحة للمستخدمين من أول دعوة إلى آخر تسجيل.
                </p>

                <div class="about-actions">
                    <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--light">
                        <i class="fas fa-user-plus"></i>
                        <span>ابدأ الاشتراك</span>
                    </a>
                    @if(!empty($supportEmail))
                        <a href="mailto:{{ $supportEmail }}" class="about-btn about-btn--soft">
                            <i class="fas fa-envelope"></i>
                            <span>تواصل معنا</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="about-card">
                @if(!empty($platformLogo))
                    <div class="mb-3">
                        <x-platform-logo size="lg" theme="dark" />
                    </div>
                @endif

                <div class="about-stat">
                    <strong>إدارة ذكية للفعاليات</strong>
                    <span>إنشاء فعالية، تخصيص الهوية، وجدولة المحتوى في واجهة واحدة.</span>
                </div>
                <div class="about-stat">
                    <strong>دعوات وتأكيد حضور</strong>
                    <span>إرسال الدعوات ومتابعة التفاعل والردود بشكل واضح وسريع.</span>
                </div>
                <div class="about-stat">
                    <strong>QR وحضور مباشر</strong>
                    <span>رموز QR وتسجيل دخول يساعدان فريقك على تنظيم التجربة عند الباب.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="about-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-envelope-open-text"></i></div>
                <h3>دعوات احترافية</h3>
                <p>قوالب دعوات واضحة، وعلامة تجارية موحدة تجعل الحدث يبدو مرتبًا من أول رسالة.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-qrcode"></i></div>
                <h3>حضور أسرع</h3>
                <p>تأكيدات QR وتسجيل دخول يختصر وقت الفريق ويحسن تجربة الضيوف.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>رؤية أوضح</h3>
                <p>متابعة الاشتراك، التسجيل، والدعوات من لوحة واحدة تسهّل اتخاذ القرار.</p>
            </div>
        </div>

        <div class="about-contact">
            <div>
                <h2>هل تريد التعرف أكثر على معا؟</h2>
                <p>ابدأ من هنا أو تواصل معنا لنوضح لك كيف تناسب المنصة احتياجك.</p>
            </div>

            <div class="about-actions" style="margin:0;">
                <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--light">
                    <i class="fas fa-arrow-right"></i>
                    <span>الانضمام الآن</span>
                </a>
                @if(!empty($supportEmail))
                    <a href="mailto:{{ $supportEmail }}" class="about-btn about-btn--soft" style="color:var(--about-dark);border-color:rgba(34,34,34,.08);background:#fff;">
                        <i class="fas fa-paper-plane"></i>
                        <span>{{ $supportEmail }}</span>
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection