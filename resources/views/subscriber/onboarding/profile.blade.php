@extends('layouts.auth')

@section('title', 'إكمال التسجيل' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'ابدأ إعداد مساحتك في خطوات واضحة')
@section('visual_subtitle', 'أدخل بيانات الجهة، النطاق الفرعي، والخطة المفضلة لتجهيز مساحة فعّالة ومتناسقة مع المنصة.')

@section('auth_title', 'إكمال التسجيل')
@section('auth_subtitle', 'خطوة أخيرة قبل فتح لوحة التحكم وإنشاء مساحتك.')

@push('styles')
<style>
    .auth-panel-visual {
        background:
            linear-gradient(165deg, rgba(15, 143, 131, 0.98) 0%, rgba(10, 92, 99, 0.98) 64%, rgba(13, 72, 78, 0.97) 100%);
    }

    .auth-visual-top {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 26px;
        padding: 1.4rem;
        backdrop-filter: blur(12px);
        box-shadow: 0 18px 38px rgba(0, 0, 0, 0.08);
    }

    .auth-heading {
        font-size: clamp(1.45rem, 1.9vw, 2rem);
        line-height: 1.35;
        margin-bottom: 0.7rem;
    }

    .auth-subheading {
        max-width: 100%;
        font-size: 0.95rem;
        line-height: 1.85;
    }

    .auth-feature-cards {
        margin-top: 1.3rem;
    }

    .auth-feat-card {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
    }

    .auth-stat-card {
        display: grid;
        gap: 0.2rem;
        padding: 0.85rem;
        text-align: center;
    }

    .auth-stat-number {
        font-size: 1.02rem;
    }

    @media (max-width: 991px) {
        .auth-visual-top {
            padding: 1.2rem;
        }
    }
</style>
@endpush

@section('auth-content')
    <form action="{{ route('onboarding.profile.save') }}" method="POST" class="auth-form">
        @csrf

        <h3 class="auth-form-section-title">بيانات المساحة</h3>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">الاسم</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="phone">رقم الجوال</label>
                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-8">
                <label class="form-label" for="company_name">اسم الجهة / الشركة</label>
                <input type="text" id="company_name" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', optional($company)->name) }}" required>
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label" for="annual_events_estimate">عدد الفعاليات سنويًا</label>
                <input type="number" id="annual_events_estimate" name="annual_events_estimate" min="1" class="form-control @error('annual_events_estimate') is-invalid @enderror" value="{{ old('annual_events_estimate', optional($company)->annual_events_estimate) }}" required>
                @error('annual_events_estimate')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-7">
                <label class="form-label" for="subdomain">النطاق الفرعي</label>
                <div class="input-group">
                    <input type="text" id="subdomain" name="subdomain" class="form-control @error('subdomain') is-invalid @enderror" value="{{ old('subdomain', optional($company)->subdomain) }}" required>
                    <span class="input-group-text">.maaninvite.com</span>
                </div>
                @error('subdomain')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-5">
                <label class="form-label" for="timezone">المنطقة الزمنية</label>
                <input type="text" id="timezone" name="timezone" class="form-control @error('timezone') is-invalid @enderror" value="{{ old('timezone', optional($company)->timezone ?: 'Asia/Riyadh') }}">
                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-4">
            <label class="form-label fw-semibold d-block mb-3">الخطة المفضلة بعد التجربة</label>
            <div class="row g-3">
                @foreach($plans as $plan)
                    <div class="col-md-6">
                        <label class="auth-plan-option p-3 d-block h-100">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <input type="radio" class="form-check-input me-2" name="preferred_plan_code" value="{{ $plan->code }}" {{ old('preferred_plan_code', optional($company)->settings['preferred_plan_code'] ?? 'starter') === $plan->code ? 'checked' : '' }}>
                                    <span class="fw-bold">{{ $plan->name }}</span>
                                    <div class="text-muted small mt-2">{{ $plan->description }}</div>
                                </div>
                                <span class="badge bg-dark-subtle text-dark text-uppercase">{{ $plan->code }}</span>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
            @error('preferred_plan_code')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        </div>

        <div class="alert alert-info auth-alert mt-4 mb-0">
            <strong>معلومة مهمة:</strong> بعد إكمال التسجيل يتم تفعيل تجربة مجانية لمدة {{ $trialDays }} يومًا. تبدأ المساحة بحد أعلى {{ $trialInviteLimit }} مدعو لكل فعالية تجريبية، ويمكن تعديل الصلاحيات لاحقًا من إعدادات الفريق.
        </div>

        <button class="auth-btn mt-3" type="submit">إكمال التسجيل وإنشاء المساحة</button>
    </form>
@endsection
