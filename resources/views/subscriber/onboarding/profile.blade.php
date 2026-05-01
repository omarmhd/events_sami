@extends('layouts.auth')

@section('title', 'إكمال التسجيل' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'حوّل بياناتك إلى مساحة عمل جاهزة')
@section('visual_subtitle', 'وحّد هوية فريقك من البداية: اسم الجهة، النطاق الفرعي، والخطة المناسبة بعد الفترة التجريبية.')

@section('auth_title', 'إكمال التسجيل وإعداد المساحة')
@section('auth_subtitle', 'هذه خطوة التسجيل النهائية قبل فتح لوحة التحكم.')

@section('auth-content')
    <form action="{{ route('onboarding.profile.save') }}" method="POST" class="auth-form">
        @csrf

        <h3 class="auth-form-section-title">البيانات الأساسية</h3>

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
