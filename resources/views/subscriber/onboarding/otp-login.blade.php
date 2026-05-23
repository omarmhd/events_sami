@extends('layouts.auth')

@section('title', 'تسجيل الدخول' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'دخول سريع وآمن لمساحة العمل')
@section('visual_subtitle', 'ابدأ من بريدك الإلكتروني عبر رمز تحقق مؤقت، ثم أكمل بيانات مساحتك لتفعيل منصة معا بالكامل.')

@section('auth_title', 'الدخول برمز تحقق')
@section('auth_subtitle', 'أدخل بريد العمل لإرسال رمز تحقق صالح لدقائق محدودة.')

@section('auth-content')
    @if(session('success'))
        <div class="alert alert-success auth-alert mt-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger auth-alert mt-3">{{ session('error') }}</div>
    @endif

    <p class="auth-kicker">تسجيل بدون كلمة مرور</p>

    <form action="{{ route('onboarding.otp.send') }}" method="POST" class="auth-form">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">البريد الإلكتروني المهني</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="auth-btn mt-2" type="submit">إرسال رمز التحقق</button>

        <div class="auth-note">
            <p class="auth-note-title">كيف تتم العملية؟</p>
            <ul class="auth-note-list">
                <li>نرسل رمزًا مكونًا من 6 أرقام إلى بريدك.</li>
                <li>تأكيد الرمز ينقلك مباشرة إلى إعداد مساحة العمل.</li>
                <li>لا تحتاج كلمة مرور في أول دخول.</li>
            </ul>
        </div>

        <div class="text-center mt-3 small">
            <a href="{{ route('login') }}" class="auth-link">الدخول بكلمة المرور</a>
        </div>
    </form>
@endsection
