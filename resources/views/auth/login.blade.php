@extends('layouts.auth')

@php
    $adminMode = $adminMode ?? false;
@endphp

@section('title', ($adminMode ? 'دخول المشرف العام' : 'تسجيل الدخول') . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', $adminMode ? 'وصول إداري كامل لعمليات النظام.' : 'أدر كل فعالية من غرفة تحكم واحدة.')
@section('visual_subtitle', $adminMode ? 'راقب المؤسسات والخطط والمستخدمين من المركز الإداري.' : 'تتبع الدعوات وعمليات الدخول والفوترة وجودة التواصل بمعايير SaaS عالمية.')

@section('auth_title', $adminMode ? 'دخول المشرف العام' : 'أهلاً بعودتك')
@section('auth_subtitle', $adminMode ? 'استخدم بيانات اعتماد المشرف.' : 'سجّل الدخول إلى مساحة عملك.')

@section('auth-content')
    @if(session('success'))
        <div class="alert alert-success auth-alert mt-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger auth-alert mt-3">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="mb-3">
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

        <div class="mb-1">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="password">كلمة المرور</label>
                @if(!$adminMode)
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size:0.8rem;">نسيت كلمة المرور؟</a>
                @endif
            </div>
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                >
                <button type="button" class="input-group-text" id="toggle-password" style="cursor:pointer; background:#f5faf8; border-color:#d8e7e3;">
                    <i class="fas fa-eye-slash" id="toggle-icon" style="color:#5f7a76; font-size:0.85rem;"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3 mt-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember" style="font-size:0.84rem; color:#5f7a76;">
                    تذكّرني على هذا الجهاز
                </label>
            </div>
        </div>

        <button type="submit" class="auth-btn mt-1">
            {{ $adminMode ? 'دخول لوحة الإدارة' : 'دخول لوحة التحكم' }}
        </button>

        @if(!$adminMode)
            <div class="auth-divider my-3 text-center" style="position:relative;">
                <hr style="border-color:#dce8e4;">
                <span style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:0 0.6rem; font-size:0.78rem; color:#a0b8b4;">أو</span>
            </div>

            <a href="{{ route('onboarding.otp.form') }}" class="auth-btn-outline d-flex align-items-center justify-content-center gap-2" style="text-decoration:none; font-size:0.9rem;">
                <i class="fas fa-envelope" style="color:#0f8f83; font-size:0.85rem;"></i>
                الدخول برمز OTP بدون كلمة مرور
            </a>

            <div class="text-center mt-3 small" style="color:#5f7a76;">
                ليس لديك حساب؟
                <a href="{{ route('register') }}" class="auth-link">أنشئ مساحة عمل مجانية</a>
            </div>
        @else
            <div class="text-center mt-3 small">
                <a href="{{ route('login') }}" class="auth-link">العودة لتسجيل الدخول العادي</a>
            </div>
        @endif
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('toggle-password').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon  = document.getElementById('toggle-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    });
</script>
@endpush
