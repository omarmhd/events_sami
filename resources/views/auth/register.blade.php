@extends('layouts.auth')

@section('title', 'إنشاء حساب ومساحة عمل' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('register_layout', '1')
@section('visual_title', 'ابدأ مساحة عملك من شاشة تسجيل متوازنة.')
@section('visual_subtitle', 'نفس الإيقاع البصري مع بداية واضحة لحسابك الجديد.')

@section('auth_title', 'إنشاء حساب جديد')
@section('auth_subtitle', 'أدخل البيانات التالية لإتمام التسجيل')

@section('auth-content')
    @if($errors->any())
        <div class="alert alert-danger auth-alert mb-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="auth-form" id="register-form">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="name">الاسم الكامل</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="أحمد محمد" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>


            <div class="row g-2">
                <div class="col-12 col-md-7">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 col-md-5">
                    <label class="form-label">رقم الجوال</label>
                    <div class="d-flex gap-2">

                        <div style="flex:1;">
                            <input type="tel" id="phone_local" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="5XXXXXXXX" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

        <div class="mb-3">
            <label class="form-label" for="password">كلمة المرور</label>
            <div class="auth-password-field">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="8 أحرف على الأقل" required>
                <button type="button" class="auth-toggle-pw toggle-pw" data-target="password">
                    <i class="fas fa-eye-slash auth-toggle-pw-icon"></i>
                </button>
            </div>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div id="pw-strength" class="mt-1" style="font-size:0.75rem; color:#a0b8b4;"></div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
            <div class="auth-password-field">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="أعد كتابة كلمة المرور" required>
                <button type="button" class="auth-toggle-pw toggle-pw" data-target="password_confirmation">
                    <i class="fas fa-eye-slash auth-toggle-pw-icon"></i>
                </button>
            </div>
            <div id="pw-match" class="mt-1" style="font-size:0.75rem;"></div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="company_name">اسم الجهة أو الشركة</label>
            <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="شركة ABC للفعاليات" required>
            @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
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

        <input type="hidden" name="phone" id="phone" value="">
        <input type="hidden" name="annual_events_estimate" value="1">
        <input type="hidden" name="terms" value="1">

        <button type="submit" class="auth-btn w-100" id="submit-btn">
            إنشاء الحساب
        </button>

        <div class="text-center mt-3 small" style="color:#5f7a76;">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}" class="auth-link">تسجيل الدخول</a>
        </div>
    </form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/css/intlTelInput.min.css" />

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js"></script>

<script>
    // Password toggle
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

    // Password strength
    const pw = document.getElementById('password');
    const pwStrengthEl = document.getElementById('pw-strength');
    const pwMatchEl = document.getElementById('pw-match');

    if (pw) {
        pw.addEventListener('input', function () {
            const val = this.value;
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
            pwStrengthEl.textContent = val.length ? 'قوة كلمة المرور: ' + lvl.label : '';
            pwStrengthEl.style.color = lvl.color;
            checkMatch();
        });
    }

    const pwConf = document.getElementById('password_confirmation');
    if (pwConf) {
        pwConf.addEventListener('input', checkMatch);
    }

    function checkMatch() {
        const a = document.getElementById('password')?.value || '';
        const b = document.getElementById('password_confirmation')?.value || '';
        if (!b) { pwMatchEl.textContent = ''; return; }
        if (a === b) { pwMatchEl.textContent = 'كلمتا المرور متطابقتان ✓'; pwMatchEl.style.color = '#0f8f83'; }
        else { pwMatchEl.textContent = 'كلمتا المرور غير متطابقتان'; pwMatchEl.style.color = '#ef4444'; }
    }

    // Initialize intl-tel-input for phone input and set hidden phone value on submit
    (function () {
        var phoneInput = document.querySelector('#phone_local');
        if (!phoneInput) return;

        var iti = window.intlTelInput(phoneInput, {
            separateDialCode: false,
            initialCountry: 'sa',
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js'
        });

        document.getElementById('register-form').addEventListener('submit', function () {
            try {
                var number = iti.getNumber(); // E.164
                document.getElementById('phone').value = number || phoneInput.value.replace(/\s+/g, '');
            } catch (e) {
                document.getElementById('phone').value = phoneInput.value.replace(/\s+/g, '');
            }
        });
    })();
</script>
@endpush
