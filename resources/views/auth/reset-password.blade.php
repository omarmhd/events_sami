@extends('layouts.auth')

@section('title', 'تعيين كلمة مرور جديدة' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'أعد ضبط كلمة مرورك بثقة.')
@section('visual_subtitle', 'اختر كلمة مرور قوية لحماية مساحة عملك وبيانات فعالياتك.')

@section('auth_title', 'تعيين كلمة مرور جديدة')
@section('auth_subtitle', 'أدخل كلمة المرور الجديدة لحسابك.')

@section('auth-content')

    @if($errors->any())
        <div class="alert alert-danger auth-alert mt-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
        @csrf

        {{-- رموز مخفية --}}
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        {{-- ─── البريد الإلكتروني (للتأكيد) ─── --}}
        @if(!isset($email))
        <div class="mb-3">
            <label class="form-label" for="email_visible">البريد الإلكتروني</label>
            <input
                type="email"
                id="email_visible"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="you@company.com"
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">البريد الإلكتروني</label>
            <div class="form-control" style="background:#f5faf8; color:#5f7a76;">{{ $email }}</div>
        </div>
        @endif

        {{-- ─── كلمة المرور الجديدة ─── --}}
        <div class="mb-3">
            <label class="form-label" for="password">كلمة المرور الجديدة</label>
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="8 أحرف على الأقل"
                    required
                    autofocus
                >
                <button type="button" class="input-group-text toggle-pw" data-target="password" style="cursor:pointer; background:#f5faf8; border-color:#d8e7e3;">
                    <i class="fas fa-eye-slash" style="color:#5f7a76; font-size:0.85rem;"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div id="pw-strength" class="mt-1" style="font-size:0.76rem; color:#a0b8b4;"></div>
        </div>

        {{-- ─── تأكيد كلمة المرور ─── --}}
        <div class="mb-4">
            <label class="form-label" for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
            <div class="input-group">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="أعد كتابة كلمة المرور"
                    required
                >
                <button type="button" class="input-group-text toggle-pw" data-target="password_confirmation" style="cursor:pointer; background:#f5faf8; border-color:#d8e7e3;">
                    <i class="fas fa-eye-slash" style="color:#5f7a76; font-size:0.85rem;"></i>
                </button>
            </div>
            <div id="pw-match" class="mt-1" style="font-size:0.76rem;"></div>
        </div>

        <button type="submit" class="auth-btn">
            <i class="fas fa-lock me-2"></i>
            تحديث كلمة المرور
        </button>

        <div class="text-center mt-3 small" style="color:#5f7a76;">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="fas fa-arrow-right me-1"></i> العودة لتسجيل الدخول
            </a>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    // إظهار / إخفاء كلمة المرور
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    });

    // مؤشر قوة كلمة المرور
    document.getElementById('password').addEventListener('input', function () {
        const val = this.value;
        const el  = document.getElementById('pw-strength');
        let score = 0;
        if (val.length >= 8)           score++;
        if (/[A-Z]/.test(val))         score++;
        if (/[0-9]/.test(val))         score++;
        if (/[^A-Za-z0-9]/.test(val))  score++;

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

        // تحقق من التطابق أيضاً
        checkMatch();
    });

    // مطابقة كلمة المرور
    function checkMatch() {
        const pw   = document.getElementById('password').value;
        const conf = document.getElementById('password_confirmation').value;
        const el   = document.getElementById('pw-match');
        if (!conf) { el.textContent = ''; return; }
        if (pw === conf) {
            el.textContent = 'كلمتا المرور متطابقتان ✓';
            el.style.color = '#0f8f83';
        } else {
            el.textContent = 'كلمتا المرور غير متطابقتين';
            el.style.color = '#ef4444';
        }
    }

    document.getElementById('password_confirmation').addEventListener('input', checkMatch);
</script>
@endpush
