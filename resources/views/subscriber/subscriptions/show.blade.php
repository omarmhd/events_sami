<!-- resources/views/subscription/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">إدارة الاشتراك</h1>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- الخطة الحالية -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">الخطة الحالية</h2>

                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-8 text-white mb-6">
                        <p class="text-lg opacity-90">الخطة المتقدمة</p>
                        <p class="text-4xl font-bold">$299</p>
                        <p class="text-sm opacity-75 mt-2">سنة واحدة</p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-700">تاريخ البدء</span>
                            <span class="font-semibold text-gray-900">{{ $subscription->created_at->format('Y-m-d') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b">
                            <span class="text-gray-700">تاريخ التجديد</span>
                            <span class="font-semibold text-gray-900">{{ $subscription->ends_at?->format('Y-m-d') ?? 'قريباً' }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-700">الحالة</span>
                            <span class="px-4 py-1 bg-green-100 text-green-800 rounded-full font-semibold">نشطة</span>
                        </div>
                    </div>

                    <button onclick="alert('قريباً')" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700">
                        ترقية الخطة
                    </button>
                </div>

                <!-- الاستخدام -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">الاستخدام</h3>

                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">الفعاليات</span>
                                <span class="font-semibold">5 / 100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 5%;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">الدعوات</span>
                                <span class="font-semibold">320 / 1000</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 32%;"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">التخزين</span>
                                <span class="font-semibold">12.5 GB / 100 GB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 12.5%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الفواتير -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">الفواتير والدفع</h3>

                <div class="space-y-4">
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-900">INV-001</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">مدفوع</span>
                        </div>
                        <p class="text-sm text-gray-600">7 مارس 2026</p>
                        <p class="text-lg font-bold text-gray-900 mt-2">$299</p>
                    </div>
                </div>

                <button class="w-full mt-6 bg-gray-200 text-gray-900 py-2 rounded-lg font-semibold hover:bg-gray-300">
                    تحميل جميع الفواتير
                </button>

                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-900 mb-3">معلومات الفوترة</h4>
                    <p class="text-sm text-gray-600">{{ $company->billing_email ?? $company->contact_email }}</p>
                    <button class="text-indigo-600 text-sm hover:underline mt-2">تحديث البيانات</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection