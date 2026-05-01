<!-- resources/views/subscription/upgrade.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">اختر الخطة المناسبة لك</h1>
            <p class="text-xl text-gray-600">
                @if($remainingTrialDays > 0)
                لديك <strong>{{ $remainingTrialDays }}</strong> يوماً متبقياً من الفترة التجريبية المجانية
                @else
                تمت الفترة التجريبية. يرجى اختيار خطة الاشتراك الخاصة بك
                @endif
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach(['starter', 'professional', 'enterprise'] as $key => $plan)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition {{ isset($plans[$key]['best_value']) ? 'ring-2 ring-indigo-600' : '' }}">
                @isset($plans[$key]['best_value'])
                <div class="bg-indigo-600 text-white py-2 text-center font-semibold">الخيار الأكثر شيوعاً</div>
                @endisset

                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $plans[$key]['name'] }}</h3>

                    <div class="mt-4 mb-6">
                        @if($plans[$key]['annual_price'] === 'custom')
                        <p class="text-4xl font-bold text-gray-900">مخصص</p>
                        <p class="text-gray-600">تواصل معنا للمزيد</p>
                        @else
                        <p class="text-4xl font-bold text-gray-900">${{ $plans[$key]['annual_price'] }}</p>
                        <p class="text-gray-600 text-sm">سنوياً</p>
                        @endif
                    </div>

                    <ul class="space-y-3 mb-8">
                        @foreach($plans[$key]['features'] as $feature)
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill-rule="evenodd" />
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    <button
                        onclick="selectPlan('{{ $key }}')"
                        class="w-full {{ $key === 'professional' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900' }} py-3 rounded-lg font-semibold hover:opacity-90 transition">
                        {{ $plans[$key]['cta'] ?? 'اختر الآن' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- المقارنة -->
        <div class="bg-white rounded-lg shadow-lg overflow-x-auto">
            <table class="w-full text-center">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-right">الميزة</th>
                        <th class="px-4 py-3">الخطة الأساسية</th>
                        <th class="px-4 py-3 bg-indigo-50">الخطة المتقدمة</th>
                        <th class="px-4 py-3">خطة المؤسسات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-right">الفعاليات السنوية</td>
                        <td class="px-4 py-3">12</td>
                        <td class="px-4 py-3 bg-indigo-50"><strong>100</strong></td>
                        <td class="px-4 py-3">غير محدود</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-right">الدعوات لكل فعالية</td>
                        <td class="px-4 py-3">100</td>
                        <td class="px-4 py-3 bg-indigo-50"><strong>1000</strong></td>
                        <td class="px-4 py-3">غير محدود</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-right">استيراد CSV</td>
                        <td>❌</td>
                        <td class="bg-indigo-50">✅</td>
                        <td>✅</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-right">الإحصائيات المتقدمة</td>
                        <td>❌</td>
                        <td class="bg-indigo-50">✅</td>
                        <td>✅</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-right">التخصيص البرمجي</td>
                        <td>❌</td>
                        <td class="bg-indigo-50">❌</td>
                        <td>✅</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function selectPlan(plan) {
        if (plan === 'enterprise') {
            // Redirect to contact sales
            window.location.href = '{{ route("contact.sales") }}';
        } else {
            // Show checkout
            showCheckout(plan);
        }
    }

    function showCheckout(plan) {
        // Placeholder for payment processing
        console.log('Proceeding to checkout for plan:', plan);
        // Integrate with payment gateway here
    }
</script>
@endsection