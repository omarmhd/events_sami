@extends('layouts.auth')

@section('title', 'إنشاء حساب ومساحة عمل' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_mode', 'register')
@section('visual_title', 'اصنع مساحة عمل تبدو احترافية من أول لحظة')
@section('visual_subtitle', 'واجهة التسجيل هنا تكمل الهوية البصرية للمنصة وتمنحك بداية واضحة، مرتبة، ومبهرة.')

@section('visual_badges')
    <div class="register-visual-badges">
        <span class="register-visual-badge"><i class="fas fa-circle-check"></i> تفعيل فوري</span>
        <span class="register-visual-badge"><i class="fas fa-shield-halved"></i> دخول آمن</span>
        <span class="register-visual-badge"><i class="fas fa-layer-group"></i> مساحة عمل منظمة</span>
    </div>
@endsection

@section('visual_footer')
    <div class="register-visual-card">
        <div class="register-visual-card-kicker">من أول خطوة</div>
        <div class="register-visual-card-title">تجربة تسجيل لا تبدو كصفحة عادية</div>
        <div class="register-visual-card-text">كل شيء هنا مصمم ليشعر المستخدم أن المنصة جاهزة للاستخدام الجاد، لا مجرد نموذج تسجيل.</div>
    </div>
@endsection

@section('auth_title', 'إنشاء حساب جديد')
@section('auth_subtitle', 'أدخل بياناتك لبدء التجربة المجانية وتفعيل مساحة العمل.')

@section('auth-content')
    <div class="register-intro">
        <div class="register-intro-badge">
            <i class="fas fa-sparkles"></i>
            <span>إنشاء حساب · تفعيل مساحة · بدء التجربة</span>
        </div>
        <p class="register-intro-text">نظم التسجيل في خطوة واحدة مع واجهة أوضح، وابدأ بإعداد مساحة عمل تحمل هوية المنصة نفسها.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger auth-alert mt-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="register-highlights mb-3">
        <span class="register-highlight-pill">تسجيل مجاني</span>
        <span class="register-highlight-pill">لا يلزم بطاقة ائتمان</span>
        <span class="register-highlight-pill">تفعيل فوري</span>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form" id="register-form">
        @csrf

        <div class="register-section-card">
            <div class="register-section-head">
                <h3 class="auth-form-section-title mb-0">بيانات الحساب</h3>
                <span class="register-section-note">معلوماتك الأساسية للدخول الآمن</span>
            </div>

            <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="أحمد محمد" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="phone">رقم الجوال</label>
                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+966 5X XXX XXXX" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="email">البريد الإلكتروني المهني</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="password">كلمة المرور</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="8 أحرف على الأقل" required>
                    <button type="button" class="input-group-text toggle-pw" data-target="password" style="cursor:pointer; background:#f5faf8; border-color:#d8e7e3;">
                        <i class="fas fa-eye-slash" style="color:#5f7a76; font-size:0.85rem;"></i>
                    </button>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div id="pw-strength" class="mt-1" style="font-size:0.75rem; color:#a0b8b4;"></div>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                <div class="input-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
                    <button type="button" class="input-group-text toggle-pw" data-target="password_confirmation" style="cursor:pointer; background:#f5faf8; border-color:#d8e7e3;">
                        <i class="fas fa-eye-slash" style="color:#5f7a76; font-size:0.85rem;"></i>
                    </button>
                </div>
                <div id="pw-match" class="mt-1" style="font-size:0.75rem;"></div>
            </div>
            </div>
        </div>

        <div class="register-section-card mt-3">
            <div class="register-section-head">
                <h3 class="auth-form-section-title mb-0">بيانات مساحة العمل</h3>
                <span class="register-section-note">تظهر هذه البيانات داخل لوحة التحكم وفي هوية الحساب</span>
            </div>

            <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label" for="company_name">اسم الجهة أو الشركة</label>
                <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="شركة ABC للفعاليات" required>
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-5">
                <label class="form-label" for="annual_events_estimate">الفعاليات المتوقعة سنويًا</label>
                <input type="number" id="annual_events_estimate" name="annual_events_estimate" class="form-control @error('annual_events_estimate') is-invalid @enderror" value="{{ old('annual_events_estimate', 5) }}" min="1" max="10000" required>
                @error('annual_events_estimate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label" for="timezone">المنطقة الزمنية <span class="text-muted fw-normal">(اختياري)</span></label>
                <select id="timezone" name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                    <option value="Asia/Riyadh" {{ old('timezone','Asia/Riyadh') === 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)</option>
                    <option value="Asia/Dubai" {{ old('timezone') === 'Asia/Dubai' ? 'selected' : '' }}>دبي / أبوظبي (GMT+4)</option>
                    <option value="Asia/Kuwait" {{ old('timezone') === 'Asia/Kuwait' ? 'selected' : '' }}>الكويت (GMT+3)</option>
                    <option value="Asia/Qatar" {{ old('timezone') === 'Asia/Qatar' ? 'selected' : '' }}>قطر (GMT+3)</option>
                    <option value="Asia/Bahrain" {{ old('timezone') === 'Asia/Bahrain' ? 'selected' : '' }}>البحرين (GMT+3)</option>
                    <option value="Asia/Muscat" {{ old('timezone') === 'Asia/Muscat' ? 'selected' : '' }}>مسقط (GMT+4)</option>
                    <option value="Africa/Cairo" {{ old('timezone') === 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)</option>
                    <option value="Africa/Casablanca" {{ old('timezone') === 'Africa/Casablanca' ? 'selected' : '' }}>الدار البيضاء (GMT+1)</option>
                    <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>لندن (GMT+0)</option>
                </select>
                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            </div>
        </div>

        <div class="register-consent mb-3 mt-3">
            <div class="form-check">
                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required>
                <label class="form-check-label" for="terms" style="font-size:0.83rem; color:#5f7a76;">
                    أوافق على <a href="#" class="auth-link">شروط الاستخدام</a> و<a href="#" class="auth-link">سياسة الخصوصية</a>
                </label>
                @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="alert alert-info auth-alert mb-3 register-trial-card">
            <i class="fas fa-circle-info me-1"></i>
            بعد التسجيل تبدأ تجربة مجانية لمدة <strong>{{ config('subscription.trial.days', 15) }} يومًا</strong> بحد أقصى <strong>{{ config('subscription.trial.invites_limit', 10) }} مدعوين</strong> لكل فعالية.
        </div>

        <button type="submit" class="auth-btn" id="submit-btn">
            <i class="fas fa-rocket me-2"></i>
            إنشاء الحساب وتفعيل مساحة العمل
        </button>

        <div class="auth-divider my-3 text-center register-divider" style="position:relative;">
            <hr style="border-color:#dce8e4;">
            <span style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:0 0.6rem; font-size:0.78rem; color:#a0b8b4;">أو</span>
        </div>

        <a href="{{ route('onboarding.otp.form') }}" class="auth-btn-outline d-flex align-items-center justify-content-center gap-2" style="text-decoration:none; font-size:0.9rem;">
            <i class="fas fa-bolt" style="color:#f59e0b; font-size:0.85rem;"></i>
            تسجيل سريع برمز OTP بدون كلمة مرور
        </a>

        <div class="text-center mt-3 small" style="color:#5f7a76;">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="auth-link">تسجيل الدخول</a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    });

    document.getElementById('password').addEventListener('input', function () {
        const val = this.value;
        const el = document.getElementById('pw-strength');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { label: '', color: '#a0b8b4' },
            { label: 'ضعيفة', color: '#ef4444' },
            { label: 'مقبولة', color: '#f59e0b' },
            { label: 'جيدة', color: '#10b981' },
            { label: 'قوية جداً ✓', color: '#0f8f83' },
        ];
        const lvl = levels[score] ?? levels[0];
        el.textContent = val.length ? 'قوة كلمة المرور: ' + lvl.label : '';
        el.style.color = lvl.color;
        checkMatch();
    });

    function checkMatch() {
        const pw = document.getElementById('password').value;
        const conf = document.getElementById('password_confirmation').value;
        const el = document.getElementById('pw-match');
        if (!conf) { el.textContent = ''; return; }
        if (pw === conf) {
            el.textContent = 'كلمتا المرور متطابقتان ✓';
            el.style.color = '#0f8f83';
        } else {
            el.textContent = 'كلمتا المرور غير متطابقتان';
            el.style.color = '#ef4444';
        }
    }
    document.getElementById('password_confirmation').addEventListener('input', checkMatch);

</script>
@endpush

@push('styles')
<style>
    .register-intro {
        margin-bottom: 1rem;
        padding: 1rem 1rem 0.15rem;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(15,143,131,0.08) 0%, rgba(15,143,131,0.03) 100%);
        border: 1px solid rgba(15,143,131,0.12);
    }

    .register-intro-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid rgba(15,143,131,0.12);
        color: #0f766e;
        font-size: 0.76rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .register-intro-text {
        margin: 0 0 0.9rem;
        color: #4f6b69;
        line-height: 1.7;
        font-size: 0.92rem;
    }

    .register-highlights {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .register-highlight-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.44rem 0.8rem;
        border-radius: 999px;
        background: #f5faf8;
        border: 1px solid #d8e7e3;
        color: #456866;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .register-section-card {
        padding: 1rem;
        border: 1px solid #dce8e4;
        border-radius: 18px;
        background: linear-gradient(180deg, #fff 0%, #f9fcfb 100%);
    }

    .register-section-head {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: baseline;
        margin-bottom: 0.9rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(220,232,228,0.9);
    }

    .register-section-note {
        font-size: 0.78rem;
        color: #7b9490;
    }

    .register-consent {
        padding: 0.9rem 1rem;
        border: 1px solid #dce8e4;
        border-radius: 14px;
        background: #f8fbfa;
    }

    .register-trial-card {
        font-size: 0.84rem;
        border-radius: 14px;
    }

    .register-divider hr {
        opacity: 1;
    }

    @media (max-width: 576px) {
        .register-section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .register-intro {
            padding: 0.9rem;
        }

        .register-section-card,
        .register-consent {
            padding: 0.85rem;
        }
    }
</style>
@endpush
