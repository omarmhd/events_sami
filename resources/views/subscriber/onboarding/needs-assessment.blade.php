<!-- resources/views/onboarding/needs-assessment.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">تقييم احتياجاتك</h1>
            <p class="text-gray-600 mb-8">ساعدنا لننصح لك بأفضل خطة مناسبة لاحتياجاتك</p>

            <form id="assessmentForm" class="space-y-8">
                @csrf

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <p class="text-sm text-blue-900">
                        هذا الاستبيان سريع ويساعدنا في فهم احتياجاتك بشكل أفضل لتقديم الخطة الأنسب لك.
                    </p>
                </div>

                <!-- السؤال الأول -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-4">
                        كم عدد الفعاليات التي تتوقع إقامتها سنوياً؟
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="annual_events_estimate" value="1-5" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>1 - 5</strong> فعاليات
                                <p class="text-sm text-gray-500">مناسب للشركات الناشئة</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="annual_events_estimate" value="6-15" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>6 - 15</strong> فعالية
                                <p class="text-sm text-gray-500">مناسب للشركات المتوسطة</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="annual_events_estimate" value="16-50" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>16 - 50</strong> فعالية
                                <p class="text-sm text-gray-500">مناسب للشركات الكبيرة</p>
                            </span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="annual_events_estimate" value="50+" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>50+</strong> فعالية
                                <p class="text-sm text-gray-500">مناسب للمؤسسات والتجمعات الكبيرة</p>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- السؤال الثاني -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-4">
                        كم متوسط عدد الحاضرين في الفعالية الواحدة؟
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="average_attendance" value="1-50" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700"><strong>1 - 50</strong> شخص</span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="average_attendance" value="51-200" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700"><strong>51 - 200</strong> شخص</span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="average_attendance" value="201-500" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700"><strong>201 - 500</strong> شخص</span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="average_attendance" value="500+" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700"><strong>500+</strong> شخص</span>
                        </label>
                    </div>
                </div>

                <!-- السؤال الثالث -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-4">
                        هل تحتاج لتخصيصات برمجية خاصة (API, تكاملات، إلخ)؟
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="requires_custom_development" value="no" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>لا</strong> - المنصة الأساسية تكفيني
                            </span>
                        </label>

                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 transition">
                            <input type="radio" name="requires_custom_development" value="yes" class="w-4 h-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">
                                <strong>نعم</strong> - أحتاج تخصيصات ودعم خاص
                            </span>
                        </label>
                    </div>
                </div>

                <!-- ملاحظات إضافية -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-3">
                        ملاحظات إضافية (اختياري)
                    </label>
                    <textarea
                        name="notes"
                        rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="أخبرنا عن احتياجاتك الخاصة..."></textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition">
                    احسب الخطة المناسبة لي
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('assessmentForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validate all required fields
        const formData = new FormData(e.target);

        if (!formData.get('annual_events_estimate')) {
            alert('يرجى اختيار عدد الفعاليات');
            return;
        }
        if (!formData.get('average_attendance')) {
            alert('يرجى اختيار متوسط الحاضرين');
            return;
        }
        if (!formData.get('requires_custom_development')) {
            alert('يرجى الإجابة على سؤال التخصيصات');
            return;
        }

        try {
            const response = await fetch('{{ route("onboarding.store-needs-assessment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: JSON.stringify({
                    annual_events_estimate: parseInt(formData.get('annual_events_estimate').split('-')[0]),
                    average_attendance: parseInt(formData.get('average_attendance').split('-')[0]),
                    requires_custom_development: formData.get('requires_custom_development') === 'yes',
                    notes: formData.get('notes'),
                }),
            });

            const result = await response.json();

            if (result.success) {
                // Show recommended plan modal
                showRecommendation(result.recommended_plan);
            } else {
                alert(result.message || 'حدث خطأ');
            }
        } catch (error) {
            console.error(error);
            alert('فشل إرسال التقييم');
        }
    });

    function showRecommendation(plan) {
        const recommendations = {
            enterprise: {
                title: '✨ نقترح عليك خطة المؤسسات',
                description: 'نظراً لاحتياجاتك للتخصيصات البرمجية، فإن خطة المؤسسات هي الأنسب لك',
                color: 'purple',
                cta: 'تواصل مع فريق المبيعات',
            },
            professional: {
                title: '⭐ نقترح عليك الخطة المتقدمة',
                description: 'بناءً على حجم فعالياتك، الخطة المتقدمة توفر أفضل قيمة مقابل السعر',
                color: 'indigo',
                cta: 'اختر الخطة المتقدمة الآن',
            },
            starter: {
                title: '🚀 نقترح عليك الخطة الأساسية',
                description: 'الخطة الأساسية توفر كل ما تحتاجه للبدء والنمو',
                color: 'blue',
                cta: 'ابدأ بالخطة الأساسية',
            },
        };

        const rec = recommendations[plan];

        alert(rec.title + '\n\n' + rec.description + '\n\n' + rec.cta);
        window.location.href = '{{ route("subscription.show-upgrade") }}?plan=' + plan;
    }
</script>
@endsection