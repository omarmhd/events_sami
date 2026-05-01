<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Services\EventAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(
        private EventAnalyticsService $analyticsService,
    ) {}

    /**
     * Get comprehensive event dashboard
     */
    public function eventDashboard(Event $event)
    {
        $this->authorize('view', $event);

        $stats = $this->analyticsService->getEventStats($event);

        return view('subscriber.analytics.event-dashboard', [
            'event' => $event,
            'stats' => $stats,
        ]);
    }

    /**
     * Get company-wide analytics
     */
    public function companyDashboard()
    {
        $company = Auth::user()->company;
        $stats = $this->analyticsService->getCompanyDashboardStats($company);

        return view('subscriber.analytics.company-dashboard', [
            'stats' => $stats,
        ]);
    }

    /**
     * Get attendance report API
     */
    public function getAttendanceReport(Event $event)
    {
        $this->authorize('view', $event);

        $report = $this->analyticsService->getAttendanceReport($event);

        return response()->json($report);
    }

    /**
     * Export attendance report
     */
    public function exportAttendanceReport(Event $event)
    {
        $this->authorize('view', $event);

        $report = $this->analyticsService->getAttendanceReport($event);

        $csv = "اسم الحدث,التاريخ,إجمالي الحاضرين\n";
        $csv .= "\"" . $report['event_name'] . "\",\"" . $report['event_date'] . "\",\"" . $report['total_checked_in'] . "\"\n\n";
        $csv .= "الاسم,البريد الإلكتروني,وقت الدخول,ملاحظات\n";

        foreach ($report['checkins'] as $checkin) {
            $csv .= "\"" . $checkin['name'] . "\",\"" . $checkin['email'] . "\",\"" . $checkin['checked_in_at'] . "\",\"" . ($checkin['notes'] ?? '') . "\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="attendance_' . $event->id . '.csv"',
        ]);
    }

    /**
     * Get real-time statistics (for websocket/polling)
     */
    public function getRealTimeStats(Event $event)
    {
        $this->authorize('view', $event);

        $stats = $this->analyticsService->getEventStats($event);

        return response()->json($stats);
    }

    /**
     * Chart data for event invitations
     */
    public function getInvitationChartData(Event $event)
    {
        $this->authorize('view', $event);

        $invitations = $event->invitations()->get();

        $data = [
            'pending' => $invitations->where('status', 'pending')->count(),
            'accepted' => $invitations->where('status', 'accepted')->count(),
            'rejected' => $invitations->where('status', 'rejected')->count(),
            'cancelled' => $invitations->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'labels' => ['معلقة', 'مقبولة', 'مرفوضة', 'ملغاة'],
            'data' => array_values($data),
            'colors' => ['#fbbf24', '#10b981', '#ef4444', '#6b7280'],
        ]);
    }

    /**
     * Chart data for attendance
     */
    public function getAttendanceChartData(Event $event)
    {
        $this->authorize('view', $event);

        $tickets = $event->tickets()->get();

        $checkedIn = $tickets->where('status', 'checked_in')->count();
        $noShow = $tickets->where('status', '!=', 'checked_in')->count();

        return response()->json([
            'labels' => ['حضروا', 'لم يحضروا'],
            'data' => [$checkedIn, $noShow],
            'colors' => ['#10b981', '#ef4444'],
        ]);
    }
}
