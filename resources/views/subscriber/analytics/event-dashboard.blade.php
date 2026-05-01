<!-- resources/views/analytics/event-dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <!-- الرأس -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            <p class="text-gray-600 mt-2">{{ $event->start_datetime->format('Y-m-d H:i') }} | {{ $event->location_name }}</p>
        </div>

        <!-- الإحصائيات الرئيسية -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm">إجمالي الدعوات</p>
                <p class="text-4xl font-bold text-gray-900">{{ $stats['total_invitations'] }}</p>
                <p class="text-sm text-gray-500 mt-2">معدل القبول: {{ $stats['acceptance_rate'] }}%</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm">التذاكر المصدرة</p>
                <p class="text-4xl font-bold text-green-600">{{ $stats['total_tickets'] }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm">الحاضرون الفعليون</p>
                <p class="text-4xl font-bold text-indigo-600">{{ $stats['checked_in'] }}</p>
                <p class="text-sm text-gray-500 mt-2">معدل الحضور: {{ $stats['check_in_rate'] }}%</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <p class="text-gray-600 text-sm">المقبولون</p>
                <p class="text-4xl font-bold text-yellow-600">{{ $stats['accepted_invitations'] }}</p>
            </div>
        </div>

        <!-- جدول التفاصيل -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- الدعوات -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">توزيع الدعوات</h3>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">مقبول</span>
                            <span class="font-semibold">{{ $stats['accepted_invitations'] }}</span>
                        </div>
                        <div class="w-full bg-green-200 rounded-full h-2"></div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">معلق</span>
                            <span class="font-semibold">{{ $stats['pending_invitations'] }}</span>
                        </div>
                        <div class="w-full bg-yellow-200 rounded-full h-2"></div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-700">مرفوض</span>
                            <span class="font-semibold">{{ $stats['rejected_invitations'] }}</span>
                        </div>
                        <div class="w-full bg-red-200 rounded-full h-2"></div>
                    </div>
                </div>
            </div>

            <!-- الحضور -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">معدل الحضور</h3>

                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-40 h-40">
                        <canvas id="attendanceChart" data-checked-in="{{ $stats['checked_in'] ?? 0 }}" data-no-show="{{ $stats['no_show'] ?? 0 }}"></canvas>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <p><span class="inline-block w-3 h-3 bg-green-600 rounded-full mr-2"></span>حضروا: {{ $stats['checked_in'] }}</p>
                    <p><span class="inline-block w-3 h-3 bg-red-600 rounded-full mr-2"></span>لم يحضروا: {{ $stats['no_show'] }}</p>
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">الإجراءات</h3>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('checkin.page', $event->event_slug) }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                    📱 فحص QR
                </a>

                <a href="{{ route('analytics.export-attendance', $event->id) }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    📥 تحميل الحاضرين
                </a>

                <a href="{{ route('events.invitations.index', $event) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    📧 إدارة الدعوات
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance chart
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const attendanceData = [
            Number(ctx.dataset.checkedIn || 0),
            Number(ctx.dataset.noShow || 0),
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['حضروا', 'لم يحضروا'],
                datasets: [{
                    data: attendanceData,
                    backgroundColor: ['#10b981', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
            }
        });
    }
</script>
@endsection