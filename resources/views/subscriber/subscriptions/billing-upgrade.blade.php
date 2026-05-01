@extends('layouts.app')

@section('title', 'الترقية والفوترة')

@push('styles')
<style>
/* ─── Page Header (standardized with dashboard hero) ───────────────── */
.billing-hero {
    background: var(--grad-primary);
    border-radius: var(--radius-xl);
    padding: 2.25rem 2.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 20px 50px -15px rgba(15,143,131,.45);
}
.billing-hero::before {
    content:'';
    position:absolute;
    top:-80px;
    right:-80px;
    width:320px;
    height:320px;
    border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.15) 0%,transparent 70%);
}
.billing-hero::after {
    content:'';
    position:absolute;
    bottom:-60px;
    left:-40px;
    width:220px;
    height:220px;
    border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);
}
.billing-hero-content {
    position: relative;
    z-index: 1;
}
.billing-greeting {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px;
    padding: .35rem 1rem;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: .75rem;
}
.billing-title {
    font-size: clamp(1.5rem,2.5vw,2.1rem);
    font-weight: 800;
    color: #fff;
    margin: .1rem 0 .5rem;
    letter-spacing: -.02em;
}
.billing-sub {
    color: rgba(255,255,255,.85);
    font-size: .9rem;
    margin: 0;
    max-width: 48ch;
}
.billing-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px;
    padding: .4rem 1rem;
    font-size: .8rem;
    font-weight: 700;
}

/* ─── Status Bar ──────────────────────────────────────────── */
.status-bar {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 1rem 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    align-items: center;
    margin-bottom: 2rem;
}
.status-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.status-item .label {
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-soft);
}
.status-item .value {
    font-size: .92rem;
    font-weight: 700;
    color: var(--text-main);
}
.status-divider {
    width: 1px;
    height: 32px;
    background: var(--line);
}
.plan-badge-inline {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--primary-soft);
    color: var(--primary-color);
    border-radius: 100px;
    padding: 3px 10px;
    font-size: .78rem;
    font-weight: 700;
}

/* ─── Section Title ───────────────────────────────────────── */
.section-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--primary-color);
    margin-bottom: .35rem;
}
.section-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -.02em;
    margin-bottom: .35rem;
}
.section-subtitle {
    font-size: .875rem;
    color: var(--text-muted);
}

/* ─── Plan Cards ──────────────────────────────────────────── */
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
}
.plan-card {
    background: var(--surface);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0;
    transition: border-color .25s, box-shadow .25s, transform .25s;
    position: relative;
    overflow: hidden;
}
.plan-card:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-hover);
    transform: translateY(-3px);
}
.plan-card.popular {
    border-color: var(--primary-color);
    box-shadow: 0 8px 30px -8px rgba(15,143,131,.25);
}
.plan-card.enterprise-card {
    background: var(--grad-dark);
    border-color: transparent;
}
.plan-popular-tag {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: var(--grad-primary);
    text-align: center;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .06em;
    color: #fff;
    padding: 5px 0;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.plan-card.popular .plan-body { padding-top: .75rem; }

.plan-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-bottom: .9rem;
    flex-shrink: 0;
}
.plan-icon-wrap.icon-starter   { background: rgba(245,158,11,.12); color: #d97706; }
.plan-icon-wrap.icon-professional { background: var(--primary-soft); color: var(--primary-color); }
.plan-icon-wrap.icon-enterprise { background: rgba(255,255,255,.12); color: rgba(255,255,255,.9); }

.plan-name {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-soft);
    margin-bottom: .2rem;
}
.plan-card.enterprise-card .plan-name { color: rgba(255,255,255,.6); }

.plan-price-block {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    margin-bottom: .15rem;
}
.plan-price {
    font-size: 2.25rem;
    font-weight: 900;
    line-height: 1;
    color: var(--text-main);
    letter-spacing: -.03em;
}
.plan-card.enterprise-card .plan-price { color: #fff; }
.plan-currency {
    font-size: .95rem;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.plan-card.enterprise-card .plan-currency { color: rgba(255,255,255,.6); }
.plan-period {
    font-size: .78rem;
    color: var(--text-soft);
    margin-bottom: 1rem;
}
.plan-card.enterprise-card .plan-period { color: rgba(255,255,255,.5); }

.plan-desc {
    font-size: .83rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
    line-height: 1.55;
}
.plan-card.enterprise-card .plan-desc { color: rgba(255,255,255,.6); }

.plan-divider {
    border: none;
    border-top: 1px solid var(--line);
    margin-bottom: 1rem;
}
.plan-card.enterprise-card .plan-divider { border-color: rgba(255,255,255,.12); }

.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem;
    display: flex;
    flex-direction: column;
    gap: .55rem;
    flex: 1;
}
.plan-features li {
    display: flex;
    align-items: center;
    gap: .6rem;
    font-size: .82rem;
    color: var(--text-muted);
}
.plan-card.enterprise-card .plan-features li { color: rgba(255,255,255,.65); }
.plan-features li .fi {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(15,143,131,.12);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .6rem;
    flex-shrink: 0;
}
.plan-card.enterprise-card .plan-features li .fi {
    background: rgba(255,255,255,.12);
    color: rgba(255,255,255,.8);
}

.btn-select-plan {
    width: 100%;
    padding: .7rem 1rem;
    border-radius: var(--radius-md);
    font-size: .88rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.btn-select-plan.primary {
    background: var(--grad-primary);
    color: #fff;
    box-shadow: 0 6px 18px -4px rgba(15,143,131,.45);
}
.btn-select-plan.primary:hover {
    box-shadow: 0 10px 24px -4px rgba(15,143,131,.6);
    transform: translateY(-1px);
}
.btn-select-plan.outline {
    background: transparent;
    color: var(--primary-color);
    border: 1.5px solid var(--primary-color);
}
.btn-select-plan.outline:hover {
    background: var(--primary-soft);
}
.btn-select-plan.enterprise {
    background: rgba(255,255,255,.14);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.3);
    backdrop-filter: blur(6px);
}
.btn-select-plan.enterprise:hover {
    background: rgba(255,255,255,.22);
    border-color: rgba(255,255,255,.5);
}


/* ── Contact request modal — specific styles ──────────────── */
.selected-plan-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-soft);
    border: 1px solid rgba(15,143,131,.25);
    color: var(--primary-color);
    border-radius: 100px;
    padding: 4px 12px;
    font-size: .8rem;
    font-weight: 700;
}
.btn-send {
    background: var(--grad-primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    font-size: .9rem;
    font-weight: 700;
    padding: .7rem 1.75rem;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 6px 18px -4px rgba(15,143,131,.4);
}
.btn-send:hover {
    box-shadow: 0 10px 24px -4px rgba(15,143,131,.55);
    transform: translateY(-1px);
}
</style>
@endpush

@section('content')

{{-- ─── Hero ──────────────────────────────────────────────────────────── --}}
<div class="billing-hero animate__animated animate__fadeInDown">
    <div class="billing-hero-content d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div>
            <div class="billing-greeting">
                <i class="fas fa-wallet"></i>
                {{ __('ui.billing.hero_kicker') }}
            </div>
            <h1 class="billing-title">{{ __('ui.billing.hero_title') }}</h1>
            <p class="billing-sub">{{ __('ui.billing.hero_subtitle') }}</p>
        </div>
        <div class="d-flex flex-column align-items-end gap-2">
            <span class="billing-hero-badge">
                <i class="fas fa-circle-check"></i>
                {{ __('ui.billing.current_plan') }}: {{ strtoupper($company->current_plan_code ?? optional($subscription->plan)->code ?? 'trial') }}
            </span>
        </div>
    </div>
</div>

{{-- ─── Current Status Bar ────────────────────────────────────────────── --}}
<div class="status-bar">
    <div class="status-item">
        <span class="label">الشركة</span>
        <span class="value">{{ $company->name }}</span>
    </div>
    <div class="status-divider d-none d-sm-block"></div>
    <div class="status-item">
        <span class="label">الخطة الحالية</span>
        <span class="value">
            <span class="plan-badge-inline">
                <i class="fas fa-circle-check" style="font-size:.65rem;"></i>
                {{ strtoupper($company->current_plan_code ?? optional($subscription->plan)->code ?? 'trial') }}
            </span>
        </span>
    </div>
    <div class="status-divider d-none d-sm-block"></div>
    <div class="status-item">
        <span class="label">حالة الاشتراك</span>
        <span class="value">{{ strtoupper($subscription->status) }}</span>
    </div>
    @if(!is_null($trialDaysLeft))
    <div class="status-divider d-none d-sm-block"></div>
    <div class="status-item">
        <span class="label">أيام التجربة المتبقية</span>
        <span class="value" style="color:var(--accent-color);">{{ $trialDaysLeft }} يوم</span>
    </div>
    @endif
    @if($recommendedPlanCode)
    <div class="ms-auto">
        <span class="plan-badge-inline" style="background:rgba(245,158,11,.12);color:#d97706;border:1px solid rgba(245,158,11,.25);">
            <i class="fas fa-lightbulb" style="font-size:.65rem;"></i>
            موصى بـ: {{ strtoupper($recommendedPlanCode) }}
        </span>
    </div>
    @endif
</div>

{{-- ─── Plans Grid ────────────────────────────────────────────────────── --}}
<div class="mb-2">
    <div class="section-label"><i class="fas fa-layer-group me-1"></i> خطط الاشتراك</div>
    <div class="section-title">قارن الخطط وابدأ الآن</div>
    <p class="section-subtitle">اختر خطتك وسيتواصل فريقنا معك لإتمام الدفع والتفعيل.</p>
</div>

<div class="plans-grid mb-4">
    @foreach($plans as $plan)
    @php
        $isPopular    = $plan->highlight_label && str_contains(strtolower($plan->highlight_label), 'شعبي');
        $isEnterprise = $plan->code === 'enterprise';
        $features     = $plan->featureList();
    @endphp

    <div class="plan-card {{ $isPopular ? 'popular' : '' }} {{ $isEnterprise ? 'enterprise-card' : '' }}">

        @if($isPopular)
        <div class="plan-popular-tag">⭐ {{ $plan->highlight_label }}</div>
        @endif

        {{-- icon --}}
        <div class="plan-icon-wrap icon-{{ $plan->code }}">
            @if($plan->code === 'starter')       <i class="fas fa-seedling"></i>
            @elseif($plan->code === 'professional') <i class="fas fa-rocket"></i>
            @elseif($plan->code === 'enterprise')   <i class="fas fa-building-columns"></i>
            @else                                    <i class="fas fa-box"></i>
            @endif
        </div>

        {{-- name + label --}}
        <div class="plan-name">{{ $plan->name }}</div>
        @if($plan->highlight_label && !$isPopular)
        <span style="font-size:.72rem;font-weight:700;color:var(--accent-color);display:block;margin-bottom:.25rem;">
            {{ $plan->highlight_label }}
        </span>
        @endif

        {{-- price --}}
        <div class="plan-price-block">
            @if($isEnterprise)
                <span class="plan-price" style="font-size:1.6rem;">مخصص</span>
            @else
                <span class="plan-price">{{ number_format($plan->annual_price, 0) }}</span>
                <span class="plan-currency">ر.س</span>
            @endif
        </div>
        <div class="plan-period">{{ $isEnterprise ? 'سعر حسب الاحتياج' : 'سنوياً — شامل الضريبة' }}</div>

        {{-- Description: if it contains commas, show as feature bullets; otherwise as plain text --}}
        @if($plan->description && str_contains($plan->description, ','))
        @php
            $descFeatures = array_filter(array_map('trim', explode(',', $plan->description)));
        @endphp
        @else
        @php $descFeatures = []; @endphp
        @if($plan->description)
        <p class="plan-desc">{{ $plan->description }}</p>
        @endif
        @endif

        <hr class="plan-divider">

        <ul class="plan-features">
            @foreach($features as $f)
            <li @if(!($f['enabled'] ?? true)) style="opacity:0.45;" @endif>
                <span class="fi" style="{{ !($f['enabled'] ?? true) ? 'background:rgba(148,163,184,.15);color:#94a3b8;' : '' }}">
                    <i class="{{ $f['icon'] }}"></i>
                </span>
                {{ $f['text'] }}
                @if(!empty($f['limit']))
                    <span style="font-size:.72rem;color:var(--text-soft);margin-right:2px;">({{ $f['limit'] }})</span>
                @endif
            </li>
            @endforeach

            {{-- Extra features from comma-separated description --}}
            @foreach($descFeatures as $df)
            <li>
                <span class="fi"><i class="fas fa-circle-check"></i></span>
                {{ $df }}
            </li>
            @endforeach
        </ul>

        {{-- CTA --}}
        <button type="button"
            class="btn-select-plan {{ $isPopular ? 'primary' : ($isEnterprise ? 'enterprise' : 'outline') }}"
            onclick="openContactModal('{{ $plan->code }}', '{{ $plan->name }}')">
            <i class="fas {{ $isEnterprise ? 'fa-handshake' : 'fa-arrow-up-right-from-square' }}"></i>
            {{ $isEnterprise ? 'تواصل مع المبيعات' : 'اختر هذه الخطة' }}
        </button>
    </div>
    @endforeach
</div>


{{-- ─── FAQ / Needs Assessment ────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Needs Assessment --}}
    <div class="col-lg-6">
        <div style="background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);padding:1.5rem;">
            <div class="section-label"><i class="fas fa-compass me-1"></i> مساعد الاختيار</div>
            <div class="section-title" style="font-size:1.1rem;">لا تعرف أي خطة تناسبك؟</div>
            <p class="section-subtitle mb-3">أجب على ثلاثة أسئلة سريعة وسنوصي لك بالخطة الأنسب.</p>
            <form action="{{ route('billing.assess') }}" method="POST">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-12 form-field-group">
                        <label>عدد الفعاليات السنوية</label>
                        <input type="number" name="annual_events" min="1" max="5000"
                            class="form-control @error('annual_events') is-invalid @enderror"
                            placeholder="مثال: 12" required>
                        @error('annual_events')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 form-field-group">
                        <label>متوسط الحضور في كل فعالية</label>
                        <input type="number" name="average_attendance" min="1" max="100000"
                            class="form-control @error('average_attendance') is-invalid @enderror"
                            placeholder="مثال: 150" required>
                        @error('average_attendance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 form-field-group">
                        <label>هل تحتاج تخصيصًا للتصميم؟</label>
                        <select name="needs_customization" class="form-select @error('needs_customization') is-invalid @enderror" required>
                            <option value="no">لا</option>
                            <option value="yes">نعم</option>
                        </select>
                        @error('needs_customization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn-select-plan primary" style="width:auto;padding:.6rem 1.5rem;">
                    <i class="fas fa-wand-sparkles"></i>
                    احصل على التوصية
                </button>
            </form>
        </div>
    </div>

    {{-- Trust signals --}}
    <div class="col-lg-6">
        <div style="background:var(--surface);border:1px solid var(--line);border-radius:var(--radius-lg);padding:1.5rem;height:100%;display:flex;flex-direction:column;gap:1rem;">
            <div class="section-label"><i class="fas fa-shield-halved me-1"></i> ضمانات المنصة</div>
            <div class="section-title" style="font-size:1.1rem;">لماذا Maan Invite؟</div>
            @foreach([
                ['fas fa-lock','أمان على مستوى المؤسسات','تشفير كامل للبيانات مع نسخ احتياطية يومية.'],
                ['fas fa-headset','دعم فني متخصص','فريق دعم عربي على مدار ساعات العمل.'],
                ['fas fa-rotate','ترقية بدون انقطاع','الترقية والتخفيض تتم فوراً بدون توقف.'],
                ['fas fa-receipt','فواتير تلقائية','فواتير رسمية بالريال السعودي شاملة الضريبة.'],
            ] as [$icon,$title,$desc])
            <div style="display:flex;align-items:flex-start;gap:.85rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:var(--primary-soft);color:var(--primary-color);display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;">
                    <i class="{{ $icon }}"></i>
                </div>
                <div>
                    <div style="font-size:.83rem;font-weight:700;color:var(--text-main);margin-bottom:2px;">{{ $title }}</div>
                    <div style="font-size:.78rem;color:var(--text-soft);">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

{{--
    ┌──────────────────────────────────────────────────────────────────────┐
    │  @push('modals')  — خارج .page-wrap تماماً                          │
    │  يتم إدراجه عبر @stack('modals') في layouts/app.blade.php          │
    │  وذلك لتجنب مشكلة backdrop-filter stacking context                 │
    └──────────────────────────────────────────────────────────────────────┘
--}}
@push('modals')

{{-- ── Contact Request Modal ─── يستخدم <x-modal> الموحد ────────────── --}}
<x-modal id="contactModal"
         title="أرسل طلب الترقية"
         subtitle="سيتواصل فريقنا معك خلال 24 ساعة لإتمام الدفع وتفعيل خطتك."
         static="true">

    <div class="modal-body">

        {{-- Info banner --}}
        <div class="app-modal-info-banner">
            <i class="fas fa-circle-info" style="font-size:1rem;flex-shrink:0;margin-top:1px;"></i>
            <span>لن يتم خصم أي مبلغ الآن — فريقنا سيتواصل معك ويرشدك خلال عملية الدفع بالكامل.</span>
        </div>

        {{-- Plan chip --}}
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
    document.getElementById('planCodeInput').value = planCode;
    document.getElementById('selectedPlanDisplay').textContent = planName;
    new bootstrap.Modal(document.getElementById('contactModal')).show();
}

// Auto-open if validation errors exist on contact fields
@if($errors->hasAny(['contact_name','contact_email','contact_phone','message','plan_code']))
document.addEventListener('DOMContentLoaded', function () {
    const planCode = @json(old('plan_code', ''));
    const planName = @json($plans->firstWhere('code', old('plan_code', ''))?->name ?? '');
    if (planCode) {
        document.getElementById('planCodeInput').value = planCode;
        document.getElementById('selectedPlanDisplay').textContent = planName;
    }
    new bootstrap.Modal(document.getElementById('contactModal')).show();
});
@endif
</script>
@endpush
