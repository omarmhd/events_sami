@extends('layouts.auth')

@section('title', 'استعادة كلمة المرور' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'أمان حسابك يبدأ من هنا.')
@section('visual_subtitle', 'سنرسل رابطاً آمناً إلى بريدك لإعادة تعيين كلمة المرور خلال دقائق.')

@section('auth_title', 'نسيت كلمة المرور؟')
@section('auth_subtitle', 'أدخل بريدك الإلكتروني وسنرسل لك رابط الاستعادة.')

@section('auth-content')

    @if(session('status'))
        {{-- حالة النجاح: تم إرسال الرابط --}}
        <div class="auth-note mt-3" style="border-color:#bbf7d0; background:#f0fdf4;">
            <p class="auth-note-title" style="color:#166534;">
                <i class="fas fa-circle-check me-1" style="color:#22c55e;"></i>
                تم إرسال رابط الاستعادة
            </p>
            <ul class="auth-note-list" style="color:#166534;">
                <li>تحقق من صندوق الوارد في بريدك الإلكتروني.</li>
                <li>إن لم تجده، تفقّد مجلد البريد المزعج (Spam).</li>
                <li>الرابط صالح لمدة 60 دقيقة فقط.</li>
            </ul>
        </div>
        <div class="text-center mt-4 small" style="color:#5f7a76;">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="fas fa-arrow-right me-1"></i> العودة لتسجيل الدخول
            </a>
        </div>
    @else
        {{-- النموذج --}}
        @if($errors->any())
            <div class="alert alert-danger auth-alert mt-3">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <div class="mb-4">
                <label class="form-label" for="email">البريد الإلكتروني</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="you@company.com"
                    required
                    autofocus
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="auth-btn">
                <i class="fas fa-paper-plane me-2"></i>
                إرسال رابط الاستعادة
            </button>

            <div class="text-center mt-3 small" style="color:#5f7a76;">
                تذكّرت كلمة المرور؟
                <a href="{{ route('login') }}" class="auth-link">تسجيل الدخول</a>
            </div>

            <div class="text-center mt-2 small" style="color:#5f7a76;">
                أو استخدم
                <a href="{{ route('onboarding.otp.form') }}" class="auth-link">الدخول برمز OTP</a>
                بدون كلمة مرور
            </div>
        </form>
    @endif

@endsection
