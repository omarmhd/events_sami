@extends('layouts.auth')

@section('title', 'إكمال الإعداد' . ' - ' . \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')))
@section('visual_title', 'أكمل إعداد مساحتك بخطوات مرتبة')
@section('visual_subtitle', 'أدخل بيانات الجهة والنطاق الفرعي والخطة المفضلة لتصبح مساحة العمل جاهزة للاستخدام.')

@section('auth_title', 'إكمال الإعداد')
@section('auth_subtitle', 'أخبرنا عنك وعن مساحة العمل لنكمل التفعيل.')

@section('auth-content')
    <form id="profileForm" class="auth-form">
        @csrf

        <h3 class="auth-form-section-title">البيانات الأساسية</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">اسمك</label>
                <input type="text" name="name" required class="form-control" placeholder="أحمد محمد">
            </div>

            <div class="col-md-6">
                <label class="form-label">رقم الجوال</label>
                <input type="tel" name="phone" required class="form-control" placeholder="+966512345678">
            </div>

            <div class="col-md-8">
                <label class="form-label">اسم الجهة/الشركة</label>
                <input type="text" name="company_name" required class="form-control" placeholder="شركة ABC">
            </div>

            <div class="col-md-4">
                <label class="form-label">عدد الفعاليات سنويًا</label>
                <input type="number" name="annual_events_estimate" required min="1" class="form-control" placeholder="5">
            </div>

            <div class="col-12">
                <label class="form-label">النطاق الفرعي</label>
                <div class="input-group">
                    <input type="text" name="subdomain" required class="form-control" placeholder="mycompany">
                    <span class="input-group-text">.maaninvite.com</span>
                </div>
                <div class="form-text">مثال: mycompany.maaninvite.com</div>
            </div>
        </div>

        <div class="alert alert-info auth-alert mt-4 mb-0">
            <strong>معلومة مهمة:</strong> بعد الإكمال تحصل على فترة تجربة مجانية لمدة {{ config('subscription.trial.days', 15) }} يومًا.
        </div>

        <button type="submit" class="auth-btn mt-3">إكمال الإعداد</button>
    </form>
@endsection

@push('scripts')
<script>
    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('{{ route("onboarding.store-profile") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert(result.message || 'حدث خطأ');
            }
        } catch (error) {
            console.error(error);
            alert('فشل الحفظ');
        }
    });
</script>
@endpush
