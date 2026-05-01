@extends('layouts.app')

@section('title', 'تجديد الاشتراك')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   Subscription Expired — Renewal Middleware Page
   The subscriber is authenticated (session preserved) but all
   platform features are blocked. This page lets them see the
   expiry state, update their contact info, and submit a renewal
   request to the admin team.
═══════════════════════════════════════════════════════════════ */

:root {
    --exp-primary: #0f8f83;
    --exp-primary-soft: rgba(15,143,131,.08);
    --exp-danger: #ef4444;
    --exp-danger-soft: rgba(239,68,68,.08);
    --exp-warning: #f59e0b;
    --exp-radius: 18px;
    --exp-shadow: 0 20px 60px -15px rgba(0,0,0,.12);
}

body { background: #f1f5f9; }

.exp-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}

/* ── Top brand bar ── */
.exp-brand {
    display: flex;
    align-items: center;
    gap: .65rem;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--exp-primary);
}
.exp-brand img { height: 36px; }

/* ── Hero status card ── */
.exp-hero {
    width: 100%;
    max-width: 680px;
    border-radius: var(--exp-radius);
    overflow: hidden;
    box-shadow: var(--exp-shadow);
    margin-bottom: 1.5rem;
}

.exp-hero-banner {
    padding: 2.5rem 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.exp-hero-banner.status-expired {
    background: linear-gradient(135deg, #1e293b 0%, #0f8f83 100%);
}
.exp-hero-banner.status-suspended {
    background: linear-gradient(135deg, #1e1b4b 0%, #6d28d9 100%);
}
.exp-hero-banner.status-terminated {
    background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%);
}
.exp-hero-banner.status-trial_expired {
    background: linear-gradient(135deg, #78350f 0%, #d97706 100%);
}

.exp-hero-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(255,255,255,.15), transparent);
    pointer-events: none;
}

.exp-hero-content { position: relative; z-index: 1; }

.exp-icon-ring {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #fff;
    margin: 0 auto 1.25rem;
    backdrop-filter: blur(8px);
}

.exp-hero-title {
    font-size: clamp(1.5rem, 3.5vw, 2rem);
    font-weight: 900;
    color: #fff;
    margin-bottom: .5rem;
    letter-spacing: -.02em;
}
.exp-hero-sub {
    font-size: .92rem;
    color: rgba(255,255,255,.88);
    max-width: 46ch;
    margin: 0 auto;
    line-height: 1.65;
}

.exp-meta-pills {
    display: flex; flex-wrap: wrap;
    justify-content: center; gap: .5rem;
    margin-top: 1.25rem;
}
.exp-pill {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: rgba(255,255,255,.92);
    border-radius: 999px;
    padding: .3rem .9rem;
    font-size: .77rem; font-weight: 600;
}

/* ── Body of hero card ── */
.exp-hero-body {
    background: #fff;
    padding: 2rem 2.5rem;
}

/* ── Steps indicator ── */
.exp-steps {
    display: flex; gap: 0;
    margin-bottom: 1.75rem;
}
.exp-step {
    flex: 1;
    display: flex; flex-direction: column; align-items: center;
    position: relative;
    font-size: .75rem; color: #94a3b8;
}
.exp-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 16px; left: calc(50% + 14px);
    right: calc(-50% + 14px);
    height: 2px;
    background: #e2e8f0;
}
.exp-step.done::after  { background: var(--exp-primary); }
.exp-step.active .exp-step-dot { background: var(--exp-primary); color: #fff; border-color: var(--exp-primary); }
.exp-step.done .exp-step-dot   { background: var(--exp-primary); color: #fff; border-color: var(--exp-primary); }
.exp-step-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    background: #f8fafc; color: #94a3b8;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700;
    margin-bottom: .45rem;
    z-index: 1; position: relative;
    transition: all .25s;
}
.exp-step-label { text-align: center; line-height: 1.3; }

/* ── Form cards ── */
.exp-section {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.25rem;
}
.exp-section-title {
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    color: var(--exp-primary);
    margin-bottom: .9rem;
    display: flex; align-items: center; gap: .4rem;
}

.exp-field label {
    font-size: .82rem; font-weight: 600; color: #374151;
    display: block; margin-bottom: .3rem;
}
.exp-field input,
.exp-field select,
.exp-field textarea {
    width: 100%;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: .65rem 1rem;
    font-size: .88rem; color: #1e293b;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.exp-field input:focus,
.exp-field select:focus,
.exp-field textarea:focus {
    border-color: var(--exp-primary);
    box-shadow: 0 0 0 3px rgba(15,143,131,.12);
}
.exp-field input[readonly] {
    background: #f1f5f9; color: #64748b; cursor: not-allowed;
}

/* ── Submit button ── */
.exp-submit {
    width: 100%;
    padding: .9rem 2rem;
    background: linear-gradient(135deg, #0f8f83, #0a6b62);
    color: #fff; border: none;
    border-radius: 12px;
    font-size: 1rem; font-weight: 800;
    cursor: pointer;
    box-shadow: 0 8px 24px -6px rgba(15,143,131,.45);
    transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: .6rem;
}
.exp-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -6px rgba(15,143,131,.6); }
.exp-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; }

/* ── Success state ── */
.exp-success {
    background: linear-gradient(135deg, rgba(16,185,129,.06), rgba(255,255,255,0));
    border: 2px solid #bbf7d0;
    border-radius: var(--exp-radius);
    padding: 2.5rem;
    text-align: center;
    max-width: 540px;
    margin: 0 auto;
    box-shadow: var(--exp-shadow);
}
.exp-success-icon {
    width: 72px; height: 72px; border-radius: 50%;
    background: rgba(16,185,129,.12); color: #059669;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; margin: 0 auto 1.25rem;
}

/* ── Logout link ── */
.exp-logout {
    font-size: .8rem; color: #94a3b8;
    text-decoration: none; margin-top: 1rem;
    display: inline-flex; align-items: center; gap: .35rem;
}
.exp-logout:hover { color: var(--exp-danger); }
</style>
@endpush

@section('content')
@php
    $user    = auth()->user();
    $company = $user?->company;
    $reason  = request('reason', 'subscription_ended');
    $sent    = session('renewal_sent');

    /* ─ Hero config per reason ─ */
    $heroConfig = match($reason) {
        'suspended'          => ['status' => 'suspended',     'icon' => 'fas fa-ban',              'title' => 'الحساب موقوف مؤقتاً',    'sub' => 'تم تعليق حسابك من قِبل الإدارة. أرسل لنا طلب إعادة التفعيل وسنتواصل معك فوراً.'],
        'trial_expired'      => ['status' => 'trial_expired', 'icon' => 'fas fa-hourglass-end',    'title' => 'انتهت الفترة التجريبية', 'sub' => 'استمتعت بفترة تجريبية مجانية — الآن حان وقت الانطلاق باشتراك رسمي.'],
        'terminated'         => ['status' => 'terminated',    'icon' => 'fas fa-circle-xmark',     'title' => 'تم إنهاء اشتراكك',       'sub' => 'أُنهي اشتراكك من قِبل الإدارة. تواصل معنا لمعرفة التفاصيل أو طلب إعادة التفعيل.'],
        default              => ['status' => 'expired',       'icon' => 'fas fa-rotate-left',      'title' => 'انتهت مدة اشتراكك',      'sub' => 'اشتراكك الحالي منتهٍ. أكمل النموذج أدناه وسيتواصل معك فريقنا خلال ساعات لإتمام التجديد.'],
    };

    $sub = $company?->latestSubscription;
@endphp

<div class="exp-wrapper">

    {{-- Brand --}}
    <div class="exp-brand">
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
        <span>{{ config('app.name', 'Maan Invite') }}</span>
    </div>

    @if($sent)
    {{-- ════════════════════════════════════════
         SUCCESS STATE
    ════════════════════════════════════════ --}}
    <div class="exp-success animate__animated animate__fadeInUp">
        <div class="exp-success-icon">
            <i class="fas fa-circle-check"></i>
        </div>
        <h3 style="font-size:1.4rem;font-weight:900;color:#065f46;margin-bottom:.5rem;">
            تم إرسال طلبك بنجاح!
        </h3>
        <p style="font-size:.9rem;color:#047857;line-height:1.65;margin-bottom:1.5rem;">
            استلمنا طلبك وسيتواصل معك فريقنا على
            <strong>{{ session('renewal_email') ?? $user?->email }}</strong>
            خلال ساعات عمل قصيرة لإتمام
            @if($reason === 'suspended') رفع التعليق
            @elseif($reason === 'trial_expired') الترقية إلى خطة مدفوعة
            @else التجديد وتفعيل حسابك
            @endif.
        </p>
        <p style="font-size:.8rem;color:#6b7280;margin-bottom:1.5rem;">
            <i class="fas fa-shield-halved me-1" style="color:var(--exp-primary);"></i>
            لن يتم خصم أي مبلغ الآن — فريقنا سيتواصل معك لإتمام الدفع وتفعيل حسابك.
        </p>
        <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;">
            {{-- While suspended they can't go to the dashboard —
                 just show an info note that the team will contact them. --}}
            <p style="font-size:.8rem;color:#94a3b8;margin:0;">
                <i class="fas fa-clock me-1"></i>
                ستتلقى بريداً إلكترونياً فور إعادة تفعيل حسابك.
            </p>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="exp-logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </div>

    @else
    {{-- ════════════════════════════════════════
         RENEWAL FORM STATE
    ════════════════════════════════════════ --}}
    <div class="exp-hero animate__animated animate__fadeInDown" style="max-width:680px;width:100%;">

        {{-- ── Hero Banner ── --}}
        <div class="exp-hero-banner status-{{ $heroConfig['status'] }}">
            <div class="exp-hero-content">
                <div class="exp-icon-ring">
                    <i class="{{ $heroConfig['icon'] }}"></i>
                </div>
                <div class="exp-hero-title">{{ $heroConfig['title'] }}</div>
                <div class="exp-hero-sub">{{ $heroConfig['sub'] }}</div>
                <div class="exp-meta-pills">
                    @if($company)
                    <span class="exp-pill"><i class="fas fa-building me-1"></i>{{ $company->name }}</span>
                    @endif
                    @if($sub && $sub->ends_at)
                    <span class="exp-pill"><i class="fas fa-calendar-xmark me-1"></i>انتهى: {{ $sub->ends_at->format('Y/m/d') }}</span>
                    @endif
                    <span class="exp-pill"><i class="fas fa-clock me-1"></i>{{ now()->format('Y/m/d') }}</span>
                </div>
            </div>
        </div>

        {{-- ── Body ── --}}
        <div class="exp-hero-body">

            {{-- Steps indicator --}}
            <div class="exp-steps mb-4">
                <div class="exp-step done">
                    <div class="exp-step-dot"><i class="fas fa-check" style="font-size:.65rem;"></i></div>
                    <div class="exp-step-label">تسجيل الدخول</div>
                </div>
                <div class="exp-step active">
                    <div class="exp-step-dot">2</div>
                    <div class="exp-step-label">طلب التجديد</div>
                </div>
                <div class="exp-step">
                    <div class="exp-step-dot">3</div>
                    <div class="exp-step-label">التواصل وإتمام الدفع</div>
                </div>
                <div class="exp-step">
                    <div class="exp-step-dot">4</div>
                    <div class="exp-step-label">تفعيل الحساب</div>
                </div>
            </div>

            <form action="{{ route('billing.renewal-request') }}" method="POST" id="expiredRenewalForm">
                @csrf
                <input type="hidden" name="reason" value="{{ $reason }}">

                {{-- ── Section 1: Account Info (pre-filled, readonly) ── --}}
                <div class="exp-section">
                    <div class="exp-section-title">
                        <i class="fas fa-user-circle"></i> معلومات الحساب
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 exp-field">
                            <label>الاسم</label>
                            <input type="text" value="{{ $user?->name }}" readonly>
                        </div>
                        <div class="col-md-6 exp-field">
                            <label>البريد الإلكتروني</label>
                            <input type="email" value="{{ $user?->email }}" readonly>
                        </div>
                        @if($company)
                        <div class="col-md-6 exp-field">
                            <label>اسم المنظمة</label>
                            <input type="text" value="{{ $company->name }}" readonly>
                        </div>
                        <div class="col-md-6 exp-field">
                            <label>الخطة الحالية</label>
                            <input type="text" value="{{ strtoupper($company->current_plan_code ?? '—') }}" readonly>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- ── Section 2: Contact details (editable — subscriber may update) ── --}}
                <div class="exp-section">
                    <div class="exp-section-title">
                        <i class="fas fa-phone-volume"></i> بيانات التواصل
                        <span style="font-weight:400;font-size:.72rem;color:#64748b;text-transform:none;letter-spacing:0;">
                            (يمكنك تعديلها قبل الإرسال)
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 exp-field">
                            <label>رقم الهاتف / الجوال <span style="color:var(--exp-danger);">*</span></label>
                            <input type="tel" name="phone"
                                   value="{{ $company?->phone ?? $user?->phone }}"
                                   placeholder="05xxxxxxxx" required>
                        </div>
                        <div class="col-md-6 exp-field">
                            <label>أفضل وقت للتواصل</label>
                            <select name="preferred_contact_time">
                                <option value="">— اختياري —</option>
                                <option value="morning">صباحاً (8ص – 12م)</option>
                                <option value="afternoon">ظهراً (12م – 4م)</option>
                                <option value="evening">مساءً (4م – 8م)</option>
                            </select>
                        </div>
                        <div class="col-12 exp-field">
                            <label>هل لديك رسالة أو طلب خاص؟</label>
                            <textarea name="message" rows="3"
                                      placeholder="مثال: أريد ترقية الخطة، أو لدي سؤال عن الأسعار..."
                                      style="resize:vertical;"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ── Section 3: Renewal preference ── --}}
                <div class="exp-section">
                    <div class="exp-section-title">
                        <i class="fas fa-layer-group"></i> تفضيلات التجديد
                    </div>
                    <div class="row g-3">
                        <div class="col-12 exp-field">
                            <label>الخطة المرغوبة</label>
                            <select name="desired_plan">
                                <option value="same">نفس الخطة الحالية</option>
                                <option value="upgrade">ترقية إلى خطة أعلى</option>
                                <option value="downgrade">خطة أقل تكلفة</option>
                                <option value="unsure">لست متأكداً — أنصحوني</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── Privacy note ── --}}
                <p style="font-size:.75rem;color:#94a3b8;text-align:center;margin-bottom:1.25rem;line-height:1.6;">
                    <i class="fas fa-lock me-1" style="color:var(--exp-primary);"></i>
                    بياناتك محفوظة وآمنة. لن يتم مشاركتها مع أي طرف خارجي.
                    لن يُخصم أي مبلغ الآن — فريقنا سيتواصل معك أولاً.
                </p>

                {{-- ── Submit ── --}}
                <button type="submit" class="exp-submit" id="expiredSubmitBtn">
                    <i class="fas fa-paper-plane"></i>
                    @if($reason === 'suspended') إرسال طلب رفع التعليق
                    @elseif($reason === 'trial_expired') إرسال طلب الاشتراك المدفوع
                    @elseif($reason === 'terminated') إرسال طلب إعادة التفعيل
                    @else إرسال طلب تجديد الاشتراك
                    @endif
                </button>

            </form>

            {{-- Logout option --}}
            <div style="text-align:center;margin-top:1.25rem;">
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="exp-logout">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                        تسجيل الخروج
                    </button>
                </form>
            </div>

        </div>{{-- /.exp-hero-body --}}
    </div>{{-- /.exp-hero --}}
    @endif

</div>{{-- /.exp-wrapper --}}
@endsection

@push('scripts')
<script>
document.getElementById('expiredRenewalForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('expiredSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جارٍ الإرسال...';
    }
});
</script>
@endpush
