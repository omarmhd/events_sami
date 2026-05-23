@extends('layouts.front')

@section('title', 'تعرف على منصة معا')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap');

    :root {
        --about-dark: #173c39;
        --about-accent: #dabc9a;
        --about-soft: #f4f6f8;
    }

    html[dir="rtl"] body,
    body {
        font-family: 'Cairo', 'Manrope', sans-serif;
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(218, 188, 154, 0.22), transparent 30%),
            radial-gradient(circle at top right, rgba(15, 143, 131, 0.18), transparent 26%),
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
        background:
            linear-gradient(135deg, rgba(13,59,55,.94) 0%, rgba(15,107,98,.92) 52%, rgba(218,188,154,.95) 100%),
            radial-gradient(circle at 20% 20%, rgba(255,255,255,.16), transparent 22%),
            radial-gradient(circle at 80% 30%, rgba(255,255,255,.1), transparent 18%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(15, 59, 55, 0.18);
        border: 1px solid rgba(255,255,255,.08);
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

    .about-visual-wrap {
        position: relative;
        isolation: isolate;
    }

    .about-glow {
        position: absolute;
        inset: auto -1rem -1rem auto;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.18) 0%, rgba(255,255,255,.04) 54%, transparent 72%);
        filter: blur(2px);
        z-index: -1;
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
        backdrop-filter: blur(10px);
    }

    .about-hero h1 {
        font-size: clamp(2rem, 4vw, 3.6rem);
        font-weight: 800;
        line-height: 1.1;
        margin: 1rem 0 .9rem;
        text-wrap: balance;
    }

    .about-lead {
        max-width: 52ch;
        font-size: 1.08rem;
        line-height: 1.9;
        color: rgba(255,255,255,.92);
        margin-bottom: 1.5rem;
    }

    .about-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
        margin-bottom: 1.25rem;
    }

    .about-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .7rem;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.95);
        font-size: .82rem;
        font-weight: 600;
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

    .about-btn--outline {
        background: transparent;
        color: #fff;
        border: 1px solid rgba(255,255,255,.34);
    }

    .about-btn--outline:hover {
        color: #fff;
        border-color: rgba(255,255,255,.52);
        background: rgba(255,255,255,.08);
    }

    .about-card {
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(15, 143, 131, 0.08);
        border-radius: 28px;
        padding: 1.55rem;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(16px);
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
        font-size: 1.08rem;
        color: var(--about-dark);
    }

    .about-stat span {
        color: #5b6472;
        font-size: .94rem;
        line-height: 1.7;
    }

    .about-section {
        padding: 1.75rem 0 0;
    }

    .section-head {
        margin-top: 1.2rem;
        text-align: center;
    }

    .section-head .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .48rem .85rem;
        border-radius: 999px;
        background: rgba(15, 143, 131, 0.08);
        color: var(--about-dark);
        font-size: .8rem;
        font-weight: 700;
        margin-bottom: .8rem;
    }

    .section-head h2 {
        margin: 0;
        font-size: clamp(1.45rem, 2vw, 2rem);
        font-weight: 800;
        color: var(--about-dark);
    }

    .section-head p {
        margin: .65rem auto 0;
        max-width: 60ch;
        color: #5b6472;
        line-height: 1.8;
    }

    .about-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.25rem;
    }

    .feature-card {
        background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.92));
        border: 1px solid rgba(34,34,34,.08);
        border-radius: 24px;
        padding: 1.35rem;
        box-shadow: 0 14px 30px rgba(0,0,0,.06);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 38px rgba(0,0,0,.08);
        border-color: rgba(15, 143, 131, 0.14);
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
        background: linear-gradient(135deg, rgba(15,143,131,.08), rgba(218,188,154,.12));
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
        .about-contact .about-btn {
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
                    انضم لنا وابدأ التجربة المجانية في تنظيم الفعاليات مع معا: إنشاء الدعوات، متابعة الردود، توليد رموز QR، وتنظيم الحضور من مكان واحد.
                </p>

                <div class="about-badges">
                    <span class="about-badge"><i class="fas fa-calendar-check"></i> إدارة الفعاليات</span>
                    <span class="about-badge"><i class="fas fa-envelope-open-text"></i> دعوات احترافية</span>
                    <span class="about-badge"><i class="fas fa-qrcode"></i> حضور عبر QR</span>
                </div>

                <div class="about-actions">
                    <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--light">
                        <i class="fas fa-bolt"></i>
                        <span>ابدأ التجربة المجانية</span>
                    </a>
                    <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--outline">
                        <i class="fas fa-user-plus"></i>
                        <span>انضم الآن</span>
                    </a>
                </div>
            </div>

            <div class="about-visual-wrap">
                <div class="about-glow"></div>

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
        </div>
    </section>

    <section class="about-section">
        <div class="section-head">
            <span class="eyebrow"><i class="fas fa-layer-group"></i> لماذا معا؟</span>
            <h2>صورة أوضح للمنصة وتجربة أكثر فخامة للمستخدم</h2>
            <p>هذه الصفحة تعرّف الزائر بسرعة على قيمة المنصة وتدفعه بخطوة واحدة للبدء، بدون تشتيت أو ازدحام بصري.</p>
        </div>

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
                <h2>انضم الآن وابدأ التجربة المجانية</h2>
                <p>جرب معا في تنظيم فعالياتك، وشاهد كيف تصبح الإدارة أبسط، أسرع، وأكثر أناقة.</p>
            </div>

            <div class="about-actions" style="margin:0;">
                <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--light">
                    <i class="fas fa-bolt"></i>
                    <span>ابدأ التجربة المجانية</span>
                </a>
                <a href="{{ route('onboarding.otp.form') }}" class="about-btn about-btn--outline">
                    <i class="fas fa-arrow-right"></i>
                    <span>انضم الآن</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection