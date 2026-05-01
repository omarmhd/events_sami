@extends('layouts.app')

@section('title', 'انتهى اشتراكك')

@push('styles')
<style>
/* ── Paywall Hero ──────────────────────────────────────────────── */
.pw-hero {
    border-radius: var(--radius-xl);
    padding: 3rem 2.5rem;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.pw-hero.reason-suspended {
    background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%);
    box-shadow: 0 24px 60px -15px rgba(109,40,217,.5);
}
.pw-hero.reason-trial_expired {
    background: linear-gradient(135deg, #78350f 0%, #d97706 100%);
    box-shadow: 0 24px 60px -15px rgba(217,119,6,.4);
}
.pw-hero.reason-subscription_ended,
.pw-hero.reason-no_subscription {
    background: linear-gradient(135deg, #1e293b 0%, #0f8f83 100%);
    box-shadow: 0 24px 60px -15px rgba(15,143,131,.45);
}
.pw-hero.reason-event_limit {
    background: linear-gradient(135deg, #7f1d1d 0%, #ef4444 100%);
    box-shadow: 0 24px 60px -15px rgba(239,68,68,.4);
}
.pw-hero::before {
    content:'';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 50% at 50% -10%, rgba(255,255,255,.18), transparent);
}
.pw-hero-content { position: relative; z-index: 1; }

.pw-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255,255,255,.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.25rem;
}
.pw-title {
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 900;
    margin-bottom: .5rem;
    letter-spacing: -.02em;
}
.pw-sub {
    font-size: .95rem;
    opacity: .88;
    max-width: 500px;
    margin: 0 auto 1.5rem;
    line-height: 1.6;
}
.pw-badges {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: .6rem;
    margin-top: 1rem;
}
.pw-badge {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 999px;
    padding: .3rem .9rem;
    font-size: .78rem;
    font-weight: 600;
}

/* ── Renewal Card ──────────────────────────────────────────────── */
.renewal-card {
    background: var(--surface);
    border: 2px solid var(--primary-color);
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    text-align: center;
    box-shadow: 0 20px 50px -15px rgba(15,143,131,.2);
    max-width: 600px;
    margin: 0 auto 2rem;
    position: relative;
    overflow: hidden;
}
.renewal-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--grad-primary);
}
.renewal-icon-wrap {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 1.25rem;
}
.renewal-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: .5rem;
    letter-spacing: -.01em;
}
.renewal-sub {
    font-size: .9rem;
    color: var(--text-soft);
    margin-bottom: 1.5rem;
    line-height: 1.6;
    max-width: 42ch;
    margin-left: auto;
    margin-right: auto;
}
.renewal-info-strip {
    background: var(--surface-soft);
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: .85rem 1.25rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: .83rem;
}
.renewal-info-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    color: var(--text-muted);
}
.renewal-info-item i { color: var(--primary-color); }
.renewal-info-item strong { color: var(--text-main); }

.btn-renew {
    background: var(--grad-primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    padding: .85rem 2.5rem;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 8px 24px -6px rgba(15,143,131,.5);
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    letter-spacing: -.01em;
    width: 100%;
    justify-content: center;
}
.btn-renew:hover {
    box-shadow: 0 12px 30px -6px rgba(15,143,131,.65);
    transform: translateY(-2px);
}
.btn-renew:disabled {
    opacity: .7;
    cursor: not-allowed;
    transform: none;
}

/* ── Success State ─────────────────────────────────────────────── */
.renewal-success {
    background: linear-gradient(135deg,rgba(16,185,129,.06),rgba(255,255,255,0));
    border: 2px solid #bbf7d0;
    border-radius: var(--radius-xl);
    padding: 2.5rem;
    text-align: center;
    max-width: 600px;
    margin: 0 auto 2rem;
}
.success-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: rgba(16,185,129,.12);
    color: #059669;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

/* ── Plans Strip ───────────────────────────────────────────────── */
.plans-strip {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.ps-card {
    background: var(--surface);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    transition: border-color .2s, box-shadow .2s, transform .2s;
    cursor: pointer;
}
.ps-card:hover, .ps-card.selected {
    border-color: var(--primary-color);
    box-shadow: 0 6px 20px -6px rgba(15,143,131,.2);
    transform: translateY(-2px);
}
.ps-name { font-weight: 800; font-size: .9rem; color: var(--text-main); margin-bottom: .2rem; }
.ps-price {
    font-size: 1.5rem; font-weight: 900; color: var(--primary-color);
    letter-spacing: -.02em; margin-bottom: .1rem;
}
.ps-period { font-size: .72rem; color: var(--text-soft); margin-bottom: .6rem; }
.ps-features { list-style: none; padding: 0; margin: 0; }
.ps-features li {
    font-size: .77rem; color: var(--text-muted);
    display: flex; align-items: center; gap: .4rem;
    padding: .18rem 0;
}
.ps-features li i { color: var(--primary-color); font-size: .65rem; flex-shrink: 0; }
</style>
@endpush

@section('content')

@php
    $reason   = request('reason', 'subscription_ended');
    $company  = auth()->user()?->company;
    $hasRenewed = session('renewal_sent');

    $config = match($reason) {
        'suspended'          => ['icon' => 'fas fa-ban',                  'title' => 'الحساب موقوف مؤقتاً',       'sub'  => 'تم تعليق حسابك من قِبل الإدارة. تواصل مع فريقنا لإعادة التفعيل.'],
        'trial_expired'      => ['icon' => 'fas fa-hourglass-end',         'title' => 'انتهت الفترة التجريبية',    'sub'  => 'استمتعت بالتجربة المجانية — الآن حان وقت الانطلاق باشتراك رسمي.'],
        'event_limit'        => ['icon' => 'fas fa-calendar-xmark',        'title' => 'وصلت لحد الفعاليات',       'sub'  => 'استنفذت عدد الفعاليات المتاحة في خطتك. قيّم خطة أوسع لمواصلة العمل.'],
        'no_subscription'    => ['icon' => 'fas fa-credit-card',           'title' => 'لا يوجد اشتراك نشط',       'sub'  => 'حسابك يحتاج خطة اشتراك لتفعيل جميع الإمكانيات.'],
        default              => ['icon' => 'fas fa-rotate-left',           'title' => 'انتهت مدة اشتراكك',        'sub'  => 'اشتراكك الحالي منتهي. أرسل طلب التجديد بنقرة واحدة وسنتواصل معك خلال ساعات.'],
    };
@endphp

{{-- ── Hero ── --}}
<div class="pw-hero reason-{{ $reason }} animate__animated animate__fadeInDown">
    <div class="pw-hero-content">
        <div class="pw-icon">
            <i class="{{ $config['icon'] }}"></i>
        </div>
        <div class="pw-title">{{ $config['title'] }}</div>
        <div class="pw-sub">{{ $config['sub'] }}</div>
        <div class="pw-badges">
            @if($company)
            <span class="pw-badge"><i class="fas fa-building me-1"></i>{{ $company->name }}</span>
            @endif
            <span class="pw-badge"><i class="fas fa-clock me-1"></i>{{ now()->format('Y/m/d') }}</span>
            @if($reason === 'event_limit' && $company?->activeSubscription)
            @php $sub = $company->activeSubscription; @endphp
            <span class="pw-badge">
                <i class="fas fa-calendar-check me-1"></i>
                {{ $sub->annual_events_used ?? 0 }} / {{ $sub->annual_event_quota ?? '—' }} فعالية
            </span>
            @endif
        </div>
    </div>
</div>

{{-- ── Renewal Card / Success State ── --}}
@if($hasRenewed)
{{-- ── Already sent — show success ── --}}
<div class="renewal-success animate__animated animate__fadeInUp">
    <div class="success-icon"><i class="fas fa-circle-check"></i></div>
    <h3 style="font-size:1.3rem;font-weight:800;color:#065f46;margin-bottom:.5rem;">تم إرسال طلبك بنجاح!</h3>
    <p style="font-size:.9rem;color:#047857;margin-bottom:1.5rem;line-height:1.6;">
        سيتواصل معك فريقنا على <strong>{{ session('renewal_email') ?? auth()->user()?->email }}</strong>
        خلال ساعات عمل قصيرة لإتمام التجديد وتفعيل حسابك.
    </p>
    <div style="display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap;">
        <a href="{{ route('dashboard.index') }}"
           class="btn btn-light rounded-pill px-4"
           style="font-weight:600;font-size:.88rem;">
            <i class="fas fa-house me-1"></i> لوحة التحكم
        </a>
    </div>
</div>
@else
{{-- ── One-Click Renewal Card ── --}}
<div class="renewal-card animate__animated animate__fadeInUp">
    <div class="renewal-icon-wrap">
        <i class="fas fa-rotate-right"></i>
    </div>
    <div class="renewal-title">
        @if($reason === 'suspended') طلب رفع التعليق
        @elseif($reason === 'event_limit') طلب توسعة الاشتراك
        @else طلب تجديد الاشتراك
        @endif
    </div>
    <p class="renewal-sub">
        @if($reason === 'suspended')
            أرسل لنا طلب إعادة تفعيل وسيتواصل معك فريقنا لمراجعة الوضع وحله فوراً.
        @elseif($reason === 'event_limit')
            خطتك الحالية وصلت حدها. أرسل طلب الترقية وسنقترح عليك الخطة المناسبة.
        @else
            بنقرة واحدة سنستلم طلبك ونتواصل معك لإتمام التجديد — لا حاجة لملء أي استمارة.
        @endif
    </p>

    {{-- Pre-filled info strip --}}
    @if($company || auth()->user())
    <div class="renewal-info-strip">
        @if(auth()->user()?->name)
        <div class="renewal-info-item">
            <i class="fas fa-user"></i>
            <strong>{{ auth()->user()->name }}</strong>
        </div>
        @endif
        @if(auth()->user()?->email)
        <div class="renewal-info-item">
            <i class="fas fa-envelope"></i>
            <strong>{{ auth()->user()->email }}</strong>
        </div>
        @endif
        @if($company?->phone)
        <div class="renewal-info-item">
            <i class="fas fa-phone"></i>
            <strong>{{ $company->phone }}</strong>
        </div>
        @endif
        @if($company?->current_plan_code)
        <div class="renewal-info-item">
            <i class="fas fa-layer-group"></i>
            <span>الخطة: <strong>{{ strtoupper($company->current_plan_code) }}</strong></span>
        </div>
        @endif
    </div>
    @endif

    <form action="{{ route('billing.renewal-request') }}" method="POST" id="renewalForm">
        @csrf
        <input type="hidden" name="reason" value="{{ $reason }}">
        <button type="submit" class="btn-renew" id="renewBtn">
            <i class="fas fa-paper-plane"></i>
            @if($reason === 'suspended') إرسال طلب رفع التعليق
            @elseif($reason === 'event_limit') إرسال طلب الترقية
            @else إرسال طلب التجديد الآن
            @endif
        </button>
    </form>

    <p style="font-size:.75rem;color:var(--text-soft);margin-top:.85rem;">
        <i class="fas fa-shield-halved me-1" style="color:var(--primary-color);"></i>
        لن يتم خصم أي مبلغ الآن — فريقنا سيتواصل معك لإتمام الدفع.
    </p>
</div>
@endif

{{-- ── Available Plans Preview ── --}}
@if(!empty($plans) && !$hasRenewed)
<div class="mb-2 mt-3">
    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--primary-color);margin-bottom:.35rem;">
        <i class="fas fa-layer-group me-1"></i> خطط الاشتراك
    </div>
    <div style="font-size:1.1rem;font-weight:800;color:var(--text-main);">استكشف خطط Maan Invite</div>
</div>

<div class="plans-strip">
    @foreach($plans as $plan)
    @php
        $pFeatures = $plan->featureList();
    @endphp
    <div class="ps-card">
        <div class="ps-name">{{ $plan->name }}</div>
        @if($plan->annual_price > 0)
        <div class="ps-price">{{ number_format($plan->annual_price, 0) }} <span style="font-size:.9rem;font-weight:600;">ر.س</span></div>
        <div class="ps-period">سنوياً</div>
        @else
        <div class="ps-price" style="font-size:1.1rem;">مجاني</div>
        <div class="ps-period">دائماً</div>
        @endif
        <ul class="ps-features">
            @foreach(array_slice($pFeatures, 0, 4) as $f)
            <li><i class="fas fa-check-circle"></i>{{ $f['text'] }}</li>
            @endforeach
        </ul>
    </div>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
document.getElementById('renewalForm')?.addEventListener('submit', function() {
    var btn = document.getElementById('renewBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جارٍ الإرسال...';
    }
});
</script>
@endpush
