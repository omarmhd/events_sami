<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختر خطتك — {{ \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
    /* ══════════════════════════════════════════════════════════════
       Onboarding — Plans Step
       Full-page standalone layout (no auth panel split needed here).
    ══════════════════════════════════════════════════════════════ */

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --primary:       #0f8f83;
        --primary-dark:  #0a6b62;
        --primary-soft:  rgba(15,143,131,.09);
        --primary-grad:  linear-gradient(135deg, #0f8f83, #0a6b62);
        --surface:       #fff;
        --bg:            #f1f5f9;
        --line:          #e2e8f0;
        --text-main:     #0f172a;
        --text-soft:     #64748b;
        --text-muted:    #94a3b8;
        --radius-lg:     16px;
        --radius-xl:     22px;
        --shadow-card:   0 8px 32px -8px rgba(0,0,0,.10);
        --shadow-float:  0 20px 60px -15px rgba(0,0,0,.13);
    }

    body {
        font-family: 'Cairo', sans-serif;
        background: var(--bg);
        min-height: 100vh;
        color: var(--text-main);
    }

    /* ── Top bar ── */
    .ob-topbar {
        background: var(--surface);
        border-bottom: 1px solid var(--line);
        padding: .85rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .ob-brand {
        display: flex;
        align-items: center;
        gap: .55rem;
        font-size: 1.15rem;
        font-weight: 900;
        color: var(--primary);
        text-decoration: none;
    }
    .ob-brand span { color: var(--text-main); }

    /* ── Progress steps ── */
    .ob-steps {
        display: flex;
        align-items: center;
        gap: 0;
    }
    .ob-step {
        display: flex;
        align-items: center;
        gap: .45rem;
        font-size: .78rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .ob-step-dot {
        width: 28px; height: 28px;
        border-radius: 50%;
        border: 2px solid var(--line);
        background: var(--surface);
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
        flex-shrink: 0;
        transition: all .25s;
    }
    .ob-step.done .ob-step-dot   { background: var(--primary); border-color: var(--primary); color: #fff; }
    .ob-step.active .ob-step-dot { background: var(--primary); border-color: var(--primary); color: #fff; }
    .ob-step.active { color: var(--primary); }
    .ob-step-line {
        width: 32px; height: 2px;
        background: var(--line);
        margin: 0 4px;
    }
    .ob-step.done + .ob-step-line { background: var(--primary); }

    /* ── Page wrapper ── */
    .ob-page {
        max-width: 1080px;
        margin: 0 auto;
        padding: 3rem 1.5rem 5rem;
    }

    /* ── Hero text ── */
    .ob-hero {
        text-align: center;
        margin-bottom: 2.75rem;
    }
    .ob-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--primary-soft);
        color: var(--primary);
        border-radius: 999px;
        padding: .3rem 1rem;
        font-size: .78rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .ob-hero-title {
        font-size: clamp(1.6rem, 3.5vw, 2.3rem);
        font-weight: 900;
        color: var(--text-main);
        letter-spacing: -.025em;
        line-height: 1.2;
        margin-bottom: .75rem;
    }
    .ob-hero-sub {
        font-size: .95rem;
        color: var(--text-soft);
        max-width: 58ch;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* ── Trial banner ── */
    .ob-trial-banner {
        background: linear-gradient(135deg, #1e293b 0%, #0f8f83 100%);
        border-radius: var(--radius-xl);
        padding: 1.75rem 2rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 48px -12px rgba(15,143,131,.4);
    }
    .ob-trial-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 60% 80% at 100% 50%, rgba(255,255,255,.08), transparent);
    }
    .ob-trial-icon {
        width: 58px; height: 58px;
        border-radius: 50%;
        background: rgba(255,255,255,.15);
        border: 1.5px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        position: relative; z-index: 1;
    }
    .ob-trial-body { position: relative; z-index: 1; flex: 1; }
    .ob-trial-title { font-size: 1.05rem; font-weight: 800; margin-bottom: .25rem; }
    .ob-trial-meta {
        font-size: .82rem;
        opacity: .88;
        line-height: 1.6;
    }
    .ob-trial-pills {
        display: flex; flex-wrap: wrap; gap: .5rem;
        margin-top: .65rem;
    }
    .ob-trial-pill {
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 999px;
        padding: .22rem .75rem;
        font-size: .73rem;
        font-weight: 600;
    }
    .ob-trial-cta {
        position: relative; z-index: 1;
        flex-shrink: 0;
    }
    .ob-skip-btn {
        background: rgba(255,255,255,.15);
        border: 1.5px solid rgba(255,255,255,.3);
        color: #fff;
        border-radius: 10px;
        padding: .65rem 1.4rem;
        font-size: .88rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        transition: background .2s;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
    }
    .ob-skip-btn:hover { background: rgba(255,255,255,.25); color: #fff; }

    /* ── Plans grid ── */
    .ob-plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    /* ── Plan card ── */
    .ob-plan-card {
        background: var(--surface);
        border: 2px solid var(--line);
        border-radius: var(--radius-xl);
        overflow: hidden;
        box-shadow: var(--shadow-card);
        transition: border-color .22s, box-shadow .22s, transform .22s;
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .ob-plan-card:hover {
        border-color: var(--primary);
        box-shadow: 0 12px 40px -10px rgba(15,143,131,.22);
        transform: translateY(-3px);
    }
    .ob-plan-card.is-selected {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(15,143,131,.14), 0 12px 40px -10px rgba(15,143,131,.22);
    }
    .ob-plan-card.is-trial {
        border-color: #6366f1;
    }
    .ob-plan-card.is-trial:hover {
        border-color: #4f46e5;
        box-shadow: 0 12px 40px -10px rgba(99,102,241,.22);
    }

    /* Badge */
    .ob-plan-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: var(--primary-grad);
        color: #fff;
        border-radius: 999px;
        padding: .2rem .75rem;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .04em;
    }
    .ob-plan-badge.badge-trial {
        background: linear-gradient(135deg,#6366f1,#4f46e5);
    }
    .ob-plan-badge.badge-popular {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    /* Card header */
    .ob-plan-head {
        padding: 1.75rem 1.75rem 1.25rem;
        border-bottom: 1px solid var(--line);
    }
    .ob-plan-name {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: .35rem;
    }
    .ob-plan-price-row {
        display: flex;
        align-items: baseline;
        gap: .35rem;
    }
    .ob-plan-price {
        font-size: 2rem;
        font-weight: 900;
        color: var(--primary);
        letter-spacing: -.03em;
    }
    .ob-plan-price.is-trial { color: #6366f1; }
    .ob-plan-currency { font-size: .95rem; font-weight: 600; color: var(--primary); }
    .ob-plan-currency.is-trial { color: #6366f1; }
    .ob-plan-period { font-size: .78rem; color: var(--text-muted); margin-top: .15rem; }

    /* Card body */
    .ob-plan-body { padding: 1.25rem 1.75rem 1.5rem; flex: 1; }
    .ob-plan-desc {
        font-size: .83rem;
        color: var(--text-soft);
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    .ob-feature-list {
        list-style: none;
        padding: 0; margin: 0;
        display: flex;
        flex-direction: column;
        gap: .42rem;
    }
    .ob-feature-list li {
        display: flex;
        align-items: flex-start;
        gap: .55rem;
        font-size: .82rem;
        color: var(--text-soft);
        line-height: 1.4;
    }
    .ob-feature-list li .fi {
        color: var(--primary);
        font-size: .7rem;
        margin-top: .22rem;
        flex-shrink: 0;
    }
    .ob-feature-list li .fi.fi-trial { color: #6366f1; }

    /* Card footer */
    .ob-plan-foot { padding: 0 1.75rem 1.75rem; }
    .ob-select-btn {
        width: 100%;
        padding: .75rem 1rem;
        border-radius: 12px;
        font-size: .9rem;
        font-weight: 800;
        border: 2px solid var(--line);
        background: transparent;
        color: var(--text-main);
        cursor: pointer;
        transition: all .2s;
        font-family: 'Cairo', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }
    .ob-select-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-soft);
    }
    .ob-plan-card.is-selected .ob-select-btn,
    .is-selected .ob-select-btn {
        background: var(--primary-grad);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 6px 18px -6px rgba(15,143,131,.5);
    }
    .ob-plan-card.is-trial .ob-select-btn:hover {
        border-color: #6366f1;
        color: #6366f1;
        background: rgba(99,102,241,.07);
    }

    /* ── Enterprise card ── */
    .ob-enterprise-card {
        background: var(--surface);
        border: 2px dashed var(--line);
        border-radius: var(--radius-xl);
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        cursor: default;
    }
    .ob-enterprise-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: rgba(15,143,131,.08);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .ob-enterprise-title { font-size: .98rem; font-weight: 800; color: var(--text-main); margin-bottom: .2rem; }
    .ob-enterprise-sub   { font-size: .82rem; color: var(--text-soft); }
    .ob-enterprise-link {
        margin-right: auto;
        flex-shrink: 0;
        background: var(--surface);
        border: 1.5px solid var(--line);
        color: var(--text-main);
        border-radius: 10px;
        padding: .6rem 1.2rem;
        font-size: .85rem;
        font-weight: 700;
        text-decoration: none;
        transition: border-color .2s, color .2s;
    }
    .ob-enterprise-link:hover { border-color: var(--primary); color: var(--primary); }

    /* ── Bottom actions ── */
    .ob-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        background: var(--surface);
        border-top: 1px solid var(--line);
        padding: 1.25rem 2rem;
        position: sticky;
        bottom: 0;
        z-index: 90;
    }
    .ob-action-info {
        font-size: .82rem;
        color: var(--text-soft);
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .ob-action-info i { color: var(--primary); }
    .ob-action-btns { display: flex; gap: .75rem; }

    .ob-btn-next {
        padding: .75rem 2rem;
        background: var(--primary-grad);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: .95rem;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .5rem;
        box-shadow: 0 6px 20px -6px rgba(15,143,131,.45);
        transition: all .2s;
        font-family: 'Cairo', sans-serif;
        text-decoration: none;
    }
    .ob-btn-next:hover { transform: translateY(-1px); box-shadow: 0 10px 28px -6px rgba(15,143,131,.55); color: #fff; }

    .ob-btn-skip {
        padding: .75rem 1.5rem;
        background: transparent;
        color: var(--text-soft);
        border: 1.5px solid var(--line);
        border-radius: 12px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .5rem;
        transition: border-color .2s, color .2s;
        font-family: 'Cairo', sans-serif;
        text-decoration: none;
    }
    .ob-btn-skip:hover { border-color: var(--primary); color: var(--primary); }

    /* ── Mobile ── */
    @media (max-width: 640px) {
        .ob-topbar { padding: .75rem 1rem; }
        .ob-step-label { display: none; }
        .ob-trial-banner { flex-direction: column; gap: 1rem; }
        .ob-trial-cta { width: 100%; }
        .ob-skip-btn { width: 100%; justify-content: center; }
        .ob-actions { flex-direction: column-reverse; }
        .ob-action-btns { width: 100%; flex-direction: column; }
        .ob-btn-next, .ob-btn-skip { width: 100%; justify-content: center; }
    }
    </style>
</head>
<body>

{{-- ══ Top bar ══ --}}
<header class="ob-topbar">
    <a href="{{ route('login') }}" class="ob-brand">
        <span style="width:32px;height:32px;border-radius:9px;background:var(--primary-grad);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;flex-shrink:0;">
            <i class="fas fa-calendar-star"></i>
        </span>
        <x-platform-logo size="sm" theme="light" />
    </a>

    {{-- Step progress --}}
    <div class="ob-steps d-none d-md-flex">
        <div class="ob-step done">
            <div class="ob-step-dot"><i class="fas fa-check" style="font-size:.6rem;"></i></div>
            <span class="ob-step-label">بيانات الحساب</span>
        </div>
        <div class="ob-step-line"></div>
        <div class="ob-step active">
            <div class="ob-step-dot">2</div>
            <span class="ob-step-label">اختيار الخطة</span>
        </div>
        <div class="ob-step-line"></div>
        <div class="ob-step">
            <div class="ob-step-dot">3</div>
            <span class="ob-step-label">لوحة التحكم</span>
        </div>
    </div>

    <div style="width:140px;"></div>{{-- spacer --}}
</header>

{{-- ══ Main content ══ --}}
<main class="ob-page">

    {{-- Hero --}}
    <div class="ob-hero">
        <div class="ob-hero-eyebrow">
            <i class="fas fa-layer-group"></i>
            خطط الاشتراك
        </div>
        <h1 class="ob-hero-title">اختر خطتك — أو ابدأ بالتجربة المجانية</h1>
        <p class="ob-hero-sub">
            حسابك فعّال الآن على الخطة التجريبية. يمكنك الترقية الآن أو المتابعة للوحة التحكم والترقية لاحقاً.
        </p>
    </div>

    {{-- Trial active banner --}}
    <div class="ob-trial-banner">
        <div class="ob-trial-icon"><i class="fas fa-rocket"></i></div>
        <div class="ob-trial-body">
            <div class="ob-trial-title">
                أنت الآن على الخطة التجريبية المجانية
                <span style="opacity:.75;font-weight:600;">لمدة {{ $trialDays }} يوماً</span>
            </div>
            <div class="ob-trial-meta">
                تبدأ تجربتك اليوم وتنتهي في
                <strong>{{ $trialEndsAt->format('d/m/Y') }}</strong>.
                خلال هذه المدة يمكنك إنشاء فعاليتين واستقبال ما يصل إلى {{ $trialInviteLimit }} مدعواً لكل فعالية.
            </div>
            <div class="ob-trial-pills">
                <span class="ob-trial-pill"><i class="fas fa-calendar-check me-1"></i>{{ $trialDays }} يوم مجاناً</span>
                <span class="ob-trial-pill"><i class="fas fa-users me-1"></i>{{ $trialInviteLimit }} مدعو / فعالية</span>
                <span class="ob-trial-pill"><i class="fas fa-calendar me-1"></i>2 فعاليات تجريبية</span>
                <span class="ob-trial-pill"><i class="fas fa-shield-halved me-1"></i>لا يُخصم أي مبلغ</span>
            </div>
        </div>
        <div class="ob-trial-cta">
            <a href="{{ route('dashboard.index') }}" class="ob-skip-btn">
                <i class="fas fa-arrow-left"></i>
                متابعة بالتجربة المجانية
            </a>
        </div>
    </div>

    {{-- Plans grid --}}
    <div class="ob-plans-grid" id="plansGrid">

        {{-- Trial plan card (always first) --}}
        <div class="ob-plan-card is-trial" data-plan="trial" onclick="selectPlan(this)">
            <span class="ob-plan-badge badge-trial"><i class="fas fa-gift me-1"></i> خطتك الحالية</span>
            <div class="ob-plan-head">
                <div class="ob-plan-name">الخطة التجريبية</div>
                <div class="ob-plan-price-row">
                    <span class="ob-plan-price is-trial">مجاني</span>
                </div>
                <div class="ob-plan-period">{{ $trialDays }} يوماً — لا يُطلب بطاقة</div>
            </div>
            <div class="ob-plan-body">
                <p class="ob-plan-desc">ابدأ بدون أي التزام. جرّب المنصة بالكامل قبل أن تقرر.</p>
                <ul class="ob-feature-list">
                    <li>
                        <i class="fas fa-check-circle fi fi-trial"></i>
                        حتى فعاليتين تجريبيتين (خاصة + عامة)
                    </li>
                    <li>
                        <i class="fas fa-check-circle fi fi-trial"></i>
                        {{ $trialInviteLimit }} مدعو كحد أقصى لكل فعالية
                    </li>
                    <li>
                        <i class="fas fa-check-circle fi fi-trial"></i>
                        تسجيل الحضور بـ QR
                    </li>
                    <li>
                        <i class="fas fa-check-circle fi fi-trial"></i>
                        جميع لوحات التحليلات
                    </li>
                </ul>
            </div>
            <div class="ob-plan-foot">
                <button class="ob-select-btn" type="button">
                    <i class="fas fa-check"></i>
                    خطتي الحالية
                </button>
            </div>
        </div>

        {{-- Paid plans from DB --}}
        @foreach($plans as $plan)
        @php
            $features = $plan->featureList();
            $isPopular = ($plan->highlight_label && str_contains(strtolower($plan->highlight_label ?? ''), 'popular'))
                       || $plan->sort_order === 1;
        @endphp
        <div class="ob-plan-card {{ $isPopular ? 'is-selected' : '' }}"
             data-plan="{{ $plan->code }}"
             onclick="selectPlan(this)">

            @if($plan->highlight_label)
            <span class="ob-plan-badge {{ $isPopular ? 'badge-popular' : '' }}">
                {{ $plan->highlight_label }}
            </span>
            @endif

            <div class="ob-plan-head">
                <div class="ob-plan-name">{{ $plan->name }}</div>
                <div class="ob-plan-price-row">
                    @if($plan->annual_price > 0)
                    <span class="ob-plan-price">{{ number_format($plan->annual_price, 0) }}</span>
                    <span class="ob-plan-currency">ر.س</span>
                    @else
                    <span class="ob-plan-price" style="font-size:1.5rem;">مجاني</span>
                    @endif
                </div>
                <div class="ob-plan-period">
                    @if($plan->annual_price > 0) سنوياً (+ ضريبة 15%)
                    @else دائماً
                    @endif
                </div>
            </div>

            <div class="ob-plan-body">
                @if($plan->description)
                <p class="ob-plan-desc">{{ $plan->description }}</p>
                @endif
                <ul class="ob-feature-list">
                    @foreach(array_slice($features, 0, 6) as $f)
                    @if($f['enabled'])
                    <li>
                        <i class="{{ $f['icon'] ?? 'fas fa-check-circle' }} fi"></i>
                        {{ $f['text'] }}
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>

            <div class="ob-plan-foot">
                @if($plan->code === 'enterprise')
                <a href="mailto:sales@maaninvite.com" class="ob-select-btn" style="text-decoration:none;">
                    <i class="fas fa-phone-volume"></i>
                    تواصل مع المبيعات
                </a>
                @else
                <button class="ob-select-btn" type="button">
                    <i class="fas fa-arrow-circle-up"></i>
                    اختر هذه الخطة
                </button>
                @endif
            </div>
        </div>
        @endforeach

    </div>

    {{-- Note about no immediate charge --}}
    <div style="text-align:center;font-size:.8rem;color:var(--text-muted);margin-bottom:3rem;">
        <i class="fas fa-shield-halved me-1" style="color:var(--primary);"></i>
        اختيار خطة الآن لا يتطلب دفعاً فورياً — فريقنا سيتواصل معك لإتمام الاشتراك.
    </div>

</main>

{{-- ══ Sticky bottom actions ══ --}}
<div class="ob-actions">
    <div class="ob-action-info d-none d-sm-flex">
        <i class="fas fa-circle-info"></i>
        <span>لا يُخصم أي مبلغ الآن — يمكنك الترقية في أي وقت من لوحة التحكم.</span>
    </div>
    <div class="ob-action-btns">
        <a href="{{ route('dashboard.index') }}" class="ob-btn-skip">
            <i class="fas fa-forward-step"></i>
            تخطّي — متابعة بالتجربة المجانية
        </a>
        <button type="button" class="ob-btn-next" id="upgradeBtn" onclick="submitUpgrade()" style="display:none;">
            <i class="fas fa-arrow-circle-up"></i>
            طلب الترقية للخطة المختارة
        </button>
        <a href="{{ route('dashboard.index') }}" class="ob-btn-next" id="nextBtn">
            <i class="fas fa-arrow-left"></i>
            التالي — الدخول للوحة التحكم
        </a>
    </div>
</div>

{{-- Hidden form for upgrade request --}}
<form id="upgradeForm" action="{{ route('billing.contact-request') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="plan_code" id="upgradeFormPlan" value="">
    <input type="hidden" name="contact_name" value="{{ $user->name }}">
    <input type="hidden" name="contact_email" value="{{ $user->email }}">
    <input type="hidden" name="contact_phone" value="{{ $user->phone ?? '' }}">
    <input type="hidden" name="message" value="طلب ترقية خلال عملية التسجيل">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let selectedPlan = null;

function selectPlan(card) {
    // Deselect all
    document.querySelectorAll('.ob-plan-card').forEach(c => c.classList.remove('is-selected'));

    // Select clicked
    card.classList.add('is-selected');
    selectedPlan = card.dataset.plan;

    const upgradeBtn = document.getElementById('upgradeBtn');
    const nextBtn    = document.getElementById('nextBtn');

    if (selectedPlan === 'trial') {
        // Trial selected — just go to dashboard
        upgradeBtn.style.display = 'none';
        nextBtn.style.display    = 'inline-flex';
        nextBtn.innerHTML        = '<i class="fas fa-arrow-left"></i> متابعة بالتجربة المجانية';
    } else if (selectedPlan === 'enterprise') {
        // Enterprise — mailto link already on card; hide upgrade btn
        upgradeBtn.style.display = 'none';
        nextBtn.style.display    = 'inline-flex';
        nextBtn.innerHTML        = '<i class="fas fa-arrow-left"></i> التالي — الدخول للوحة التحكم';
    } else {
        // Paid plan selected
        upgradeBtn.style.display = 'inline-flex';
        nextBtn.style.display    = 'none';
    }
}

function submitUpgrade() {
    if (!selectedPlan || selectedPlan === 'trial') return;
    document.getElementById('upgradeFormPlan').value = selectedPlan;
    document.getElementById('upgradeBtn').disabled   = true;
    document.getElementById('upgradeBtn').innerHTML  =
        '<i class="fas fa-spinner fa-spin me-1"></i> جارٍ الإرسال...';
    document.getElementById('upgradeForm').submit();
}
</script>
</body>
</html>
