@extends('layouts.auth')

@section('title', 'إنشاء حساب ومساحة عمل' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('register_layout', '1')
@section('visual_title', 'ابدأ مساحة عملك من شاشة تسجيل متوازنة.')
@section('visual_subtitle', 'نفس الارتفاع ونفس الإيقاع البصري مثل شاشة الدخول، مع بداية أوضح لحسابك الجديد.')

@section('auth_title', 'إنشاء حساب جديد')
@section('auth_subtitle', 'أدخل بياناتك لبدء التجربة المجانية وتفعيل مساحة العمل.')

@section('auth-content')
    @if($errors->any())
        <div class="alert alert-danger auth-alert mb-3">
            @foreach($errors->all() as $error)
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

                        <div class="mb-3">
                            <label class="form-label" for="email">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">رقم الجوال</label>
                            <div class="d-flex gap-2">
                                <select id="phone_code" class="form-select" style="max-width:110px;" aria-label="رمز الدولة">
                                    <option value="+966" selected>+966</option>
                                    <option value="+971">+971</option>
                                    <option value="+20">+20</option>
                                    <option value="+974">+974</option>
                                    <option value="+962">+962</option>
                                    <option value="+965">+965</option>
                                </select>
                                <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="5X XXX XXXX" required>
                            </div>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

                        <button type="submit" class="auth-btn w-100" id="submit-btn">
                            إنشاء الحساب
                        </button>

                        <div class="text-center mt-3 small" style="color:#5f7a76;">
                            لديك حساب بالفعل؟
                            <a href="{{ route('login') }}" class="auth-link">تسجيل الدخول</a>
                        </div>
                    </form>
                @endsection
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
