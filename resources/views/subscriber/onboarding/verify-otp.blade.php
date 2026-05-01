@extends('layouts.auth')

@section('title', 'تأكيد رمز التحقق' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))

@php
    // Context: 'new' | 'incomplete' | 'returning'
    $ctx = $userContext ?? 'new';

    $visualTitle = match($ctx) {
        'returning'  => 'مرحباً بعودتك — أكد هويتك للدخول',
        'incomplete' => 'أكمل تسجيل مساحتك',
        default      => 'تحقق الهوية قبل الدخول للمساحة',
    };
    $visualSub = match($ctx) {
        'returning'  => 'رمز التحقق المؤقت يضمن أن الدخول آمن في كل مرة.',
        'incomplete' => 'بريدك موجود لدينا لكن بيانات مساحتك لم تكتمل بعد — سنكملها معاً.',
        default      => 'جميع محاولات الدخول تُراجع برمز مؤقت قصير العمر لضمان حماية الوصول.',
    };
    $cardTitle = match($ctx) {
        'returning'  => 'تأكيد رمز التحقق — الدخول',
        'incomplete' => 'تأكيد رمز التحقق — استكمال التسجيل',
        default      => 'تأكيد رمز التحقق',
    };
    $cardSub = 'تم إرسال الرمز إلى: ' . $email;
@endphp

@section('visual_title', $visualTitle)
@section('visual_subtitle', $visualSub)
@section('auth_title', $cardTitle)
@section('auth_subtitle', $cardSub)

@section('auth-content')

    {{-- Contextual info banner --}}
    @if($ctx === 'incomplete')
    <div class="alert auth-alert mb-3"
         style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);color:#92400e;border-radius:10px;font-size:.84rem;">
        <i class="fas fa-circle-info me-1"></i>
        بريدك الإلكتروني مسجّل لكن بيانات منظمتك لم تكتمل بعد.
        بعد التحقق سنأخذك لإكمال بيانات المساحة.
    </div>
    @elseif($ctx === 'returning')
    <div class="alert auth-alert mb-3"
         style="background:rgba(15,143,131,.07);border:1px solid rgba(15,143,131,.25);color:#065f46;border-radius:10px;font-size:.84rem;">
        <i class="fas fa-shield-halved me-1"></i>
        حساب موجود — أدخل الرمز للدخول إلى لوحة تحكمك.
    </div>
    @endif

    <form action="{{ route('onboarding.verify.submit') }}" method="POST" class="auth-form">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        <div class="mb-3">
            <label class="form-label" for="otp">رمز مكون من 6 أرقام</label>
            <input
                type="text"
                id="otp"
                name="otp"
                maxlength="6"
                inputmode="numeric"
                autocomplete="one-time-code"
                class="form-control @error('otp') is-invalid @enderror"
                value="{{ old('otp') }}"
                autofocus
                required
            >
            @error('otp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="auth-btn mt-2" type="submit">تحقق ومتابعة</button>
    </form>

    <form action="{{ route('onboarding.otp.send') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button class="auth-btn-outline" type="submit">إعادة إرسال الرمز</button>
    </form>

    <div class="text-center mt-3 small">
        <a href="{{ route('onboarding.otp.form') }}" class="auth-link">
            <i class="fas fa-arrow-right me-1"></i> تغيير البريد الإلكتروني
        </a>
    </div>

@endsection
