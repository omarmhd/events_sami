<!-- resources/views/onboarding/register.blade.php -->
@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-blue-600 px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">مرحباً بك</h1>
            <p class="text-gray-600 mt-2">منصة Maan Invite - إرسل دعوات احترافية</p>
        </div>

        <form id="registerForm" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    البريد الإلكتروني
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="your@email.com">
                <span class="text-sm text-red-600 mt-1" id="emailError"></span>
            </div>

            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700 transition"
                id="submitBtn">
                إرسال رمز التحقق
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-6">
            لديك حساب؟ <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">تسجيل الدخول</a>
        </p>
    </div>
</div>

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
                body: JSON.stringify({
                    email
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Show OTP verification form
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
            <p class="text-center text-gray-600 mb-4">تحقق من بريدك الإلكتروني</p>
            <p dir="ltr" class="text-center text-sm text-gray-500 mb-4">${email}</p>
            <label for="otp" class="block text-sm font-medium text-gray-700">رمز التحقق (6 أرقام)</label>
            <input 
                type="text" 
                id="otp" 
                maxlength="6" 
                pattern="[0-9]{6}"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-center text-2xl tracking-widest"
                placeholder="000000"
            >
            <button 
                type="button" 
                onclick="verifyOtp('${email}')"
                class="w-full mt-4 bg-indigo-600 text-white py-2 px-4 rounded-md font-medium hover:bg-indigo-700"
            >
                تحقق
            </button>
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
                body: JSON.stringify({
                    email,
                    otp
                }),
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
@endsection