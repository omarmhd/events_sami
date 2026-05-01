<!-- resources/views/onboarding/profile-setup.blade.php -->
@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-600 to-blue-600 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl w-full">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إكمال ملفك الشخصي</h1>
        <p class="text-gray-600 mb-6">أخبرنا عن نفسك وجهتك</p>

        <form id="profileForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">اسمك</label>
                    <input
                        type="text"
                        name="name"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="أحمد محمد">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">رقم الجوال</label>
                    <input
                        type="tel"
                        name="phone"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="+966512345678">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">اسم الجهة/الشركة</label>
                    <input
                        type="text"
                        name="company_name"
                        required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="شركة ABC">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">النطاق الفرعي (Subdomain)</label>
                    <div class="flex items-center mt-1">
                        <input
                            type="text"
                            name="subdomain"
                            required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            placeholder="mycompany">
                        <span class="ml-2 text-gray-500">.maaninvite.com</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">مثال: http://mycompany.maaninvite.com</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">عدد الفعاليات السنوية المتوقعة</label>
                    <input
                        type="number"
                        name="annual_events_estimate"
                        required
                        min="1"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="5">
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <strong>الفترة التجريبية المجانية:</strong> ستحصل على 15 يوماً مجاني لاختبار المنصة بإنشاء فعالية واحدة من كل نوع بحد أقصى 10 دعوات.
                </p>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                إكمال الإعداد
            </button>
        </form>
    </div>
</div>

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
@endsection