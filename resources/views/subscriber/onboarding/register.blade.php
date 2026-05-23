@extends('layouts.auth')

@section('title', 'إنشاء حساب سريع' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'ابدأ الدخول بدون تعقيد')
@section('visual_subtitle', 'أدخل بريدك الإلكتروني، واستقبل رمز تحقق يفتح لك طريق الإعداد الكامل لمساحة العمل.')

@section('auth_title', 'إنشاء حساب سريع')
@section('auth_subtitle', 'أرسل رمز التحقق إلى بريدك ثم أكمل الإعداد.')

@section('auth-content')
<div id="otp-step">
    <form id="registerForm" class="auth-form">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" required class="form-control" placeholder="your@email.com">
            <span class="text-danger mt-1 d-block" id="emailError"></span>
        </div>

        <button type="submit" class="auth-btn" id="submitBtn">إرسال رمز التحقق</button>

        <div class="auth-note mt-3">
            <p class="auth-note-title">كيف تتم العملية؟</p>
            <ul class="auth-note-list">
                <li>نرسل رمزًا مكونًا من 6 أرقام إلى بريدك.</li>
                <li>بعد التأكيد تنتقل مباشرة إلى إعداد مساحة العمل.</li>
                <li>لا تحتاج إلى كلمة مرور في أول دخول.</li>
            </ul>
        </div>

        <div class="text-center mt-3 small">
            <a href="{{ route('login') }}" class="auth-link">الدخول بكلمة المرور</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'جاري الإرسال...';

        try {
            const response = await fetch('{{ route("onboarding.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ email }),
            });

            const data = await response.json();

            if (data.success) {
                showOtpForm(email);
            } else {
                document.getElementById('emailError').textContent = data.message || 'حدث خطأ';
            }
        } catch (error) {
            document.getElementById('emailError').textContent = 'فشل الإرسال. حاول مجدداً';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'إرسال رمز التحقق';
        }
    });

    function showOtpForm(email) {
        document.getElementById('registerForm').innerHTML = `
            <div>
                <p class="text-center text-muted mb-3">تحقق من بريدك الإلكتروني</p>
                <p dir="ltr" class="text-center small text-muted mb-4">${email}</p>
                <label for="otp" class="form-label">رمز التحقق (6 أرقام)</label>
                <input type="text" id="otp" maxlength="6" pattern="[0-9]{6}" class="form-control text-center fs-4 tracking-widest" placeholder="000000">
                <button type="button" onclick="verifyOtp('${email}')" class="auth-btn mt-3">تحقق</button>
            </div>
        `;
    }

    async function verifyOtp(email) {
        const otp = document.getElementById('otp').value;

        if (otp.length !== 6) {
            alert('أدخل رمز يحتوي على 6 أرقام');
            return;
        }

        try {
            const response = await fetch('{{ route("onboarding.verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ email, otp }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect || '{{ route("onboarding.profile-setup") }}';
            } else {
                alert(data.message || 'فشل التحقق');
            }
        } catch (error) {
            alert('فشل التحقق. حاول مجدداً');
        }
    }
</script>
@endpush
