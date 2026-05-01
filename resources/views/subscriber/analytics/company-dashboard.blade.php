{{-- resources/views/analytics/company-dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@push('styles')
<style>
/* ── KPI strip ─────────────────────────────────── */
.an-kpi-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;margin-bottom:2rem; }
.an-kpi {
    background:#fff;border-radius:var(--radius-lg);border:1px solid var(--line);
    padding:1.25rem 1.4rem;position:relative;overflow:hidden;transition:all .25s;
}
.an-kpi:hover { transform:translateY(-3px);box-shadow:var(--shadow-hover); }
.an-kpi-bar {
    position:absolute;bottom:0;left:0;height:3px;border-radius:0 0 var(--radius-lg) var(--radius-lg);
}
.an-icon {
    width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;
    font-size:.95rem;margin-bottom:.6rem;
}
.an-label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-bottom:.15rem; }
.an-val { font-size:1.9rem;font-weight:800;color:var(--text-main);line-height:1; }
.an-sub  { font-size:.77rem;color:var(--text-soft);margin-top:.25rem; }

/* ── Response Funnel ───────────────────────────── */
.funnel-bar {
    margin-bottom:.9rem;
}
.funnel-label { display:flex;justify-content:space-between;font-size:.8rem;font-weight:600;color:var(--text-main);margin-bottom:.3rem; }
.funnel-track { height:10px;border-radius:99px;background:var(--surface-soft);overflow:hidden; }
.funnel-fill  { height:100%;border-radius:99px;transition:width 1.2s cubic-bezier(.4,0,.2,1); }

/* ── Chart Canvas ──────────────────────────────── */
.chart-wrap { position:relative;height:260px; }

/* ── Event Table ───────────────────────────────── */
.ev-table { width:100%;border-collapse:separate;border-spacing:0; }
.ev-table thead th {
    font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
    color:var(--text-muted);padding:.6rem 1rem;background:var(--surface-soft);border-bottom:1px solid var(--line);
}
.ev-table thead th:first-child { border-radius:var(--radius-sm) 0 0 0; }
.ev-table thead th:last-child  { border-radius:0 var(--radius-sm) 0 0; }
.ev-table tbody tr { transition:background .15s; }
.ev-table tbody tr:hover { background:var(--surface-soft); }
.ev-table tbody td {
    padding:.7rem 1rem;border-bottom:1px solid var(--line);
    font-size:.84rem;color:var(--text-main);vertical-align:middle;
}
.ev-table tbody tr:last-child td { border-bottom:none; }
.ev-rate-bar { display:inline-flex;align-items:center;gap:.5rem;width:100%;max-width:140px; }
.ev-rate-track { flex:1;height:6px;border-radius:99px;background:var(--surface-soft);overflow:hidden; }
.ev-rate-fill  { height:100%;border-radius:99px;background:var(--grad-primary); }

/* ── Timeline Chart override ───────────────────── */
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Analytics Dashboard</h4>
        <p class="text-muted small mb-0">Overview of invitations, attendance, and event performance</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-calendar-days me-1"></i>Events
        </a>
        <a href="{{ route('invitations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-envelope me-1"></i>Invitations
        </a>
    </div>
</div>

{{-- ════ KPI STRIP ════ --}}
<div class="an-kpi-grid animate__animated animate__fadeInUp">

    <div class="an-kpi">
        <div class="an-icon" style="background:var(--primary-soft);color:var(--primary-color)"><i class="fas fa-calendar-star"></i></div>
        <div class="an-label">Total Events</div>
        <div class="an-val">{{ $stats['total_events'] ?? 0 }}</div>
        <div class="an-sub">{{ $stats['upcoming_events'] ?? 0 }} upcoming</div>
        <div class="an-kpi-bar" style="background:var(--grad-primary);width:70%"></div>
    </div>

    <div class="an-kpi">
        <div class="an-icon" style="background:rgba(14,165,233,.1);color:var(--accent-color)"><i class="fas fa-envelope-open-text"></i></div>
        <div class="an-label">Invitations</div>
        <div class="an-val">{{ $stats['total_invitations'] ?? 0 }}</div>
        <div class="an-sub">{{ $stats['accepted_invitations'] ?? 0 }} accepted</div>
        <div class="an-kpi-bar" style="background:var(--grad-accent);width:55%"></div>
    </div>

    <div class="an-kpi">
        <div class="an-icon" style="background:rgba(16,185,129,.1);color:var(--success-color)"><i class="fas fa-user-check"></i></div>
        <div class="an-label">Checked In</div>
        <div class="an-val">{{ $stats['checked_in_count'] ?? 0 }}</div>
        <div class="an-sub">of {{ $stats['total_tickets'] ?? 0 }} tickets</div>
        <div class="an-kpi-bar" style="background:var(--grad-success);width:{{ $stats['total_tickets'] > 0 ? min(100, round(($stats['checked_in_count']/$stats['total_tickets'])*100)) : 0 }}%"></div>
    </div>

    <div class="an-kpi">
        <div class="an-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="fas fa-percent"></i></div>
        <div class="an-label">Attendance Rate</div>
        <div class="an-val">{{ $stats['overall_attendance_rate'] ?? 0 }}<small style="font-size:.9rem">%</small></div>
        <div class="an-sub">overall average</div>
        <div class="an-kpi-bar" style="background:linear-gradient(90deg,#8b5cf6,#d946ef);width:{{ $stats['overall_attendance_rate'] ?? 0 }}%"></div>
    </div>

</div>

{{-- ════ FUNNEL + ATTENDANCE ════ --}}
<div class="row g-3 mb-3 animate__animated animate__fadeInUp" style="animation-delay:.1s">

    {{-- Response Funnel --}}
    <div class="col-lg-6">
        <div class="card-surface h-100">
            <div class="section-title mb-4" style="font-size:.95rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem">
                <i class="fas fa-filter" style="color:var(--primary-color)"></i> Invitation Response Funnel
            </div>
            @php
                $total   = $stats['total_invitations'] ?? 0;
                $accepted = $stats['accepted_invitations'] ?? 0;
                $pctAcc  = $total > 0 ? round($accepted/$total*100) : 0;
                $declined = $stats['declined_invitations'] ?? 0;
                $pctDec  = $total > 0 ? round($declined/$total*100) : 0;
                $pending  = $stats['pending_invitations'] ?? ($total - $accepted - $declined);
                $pctPend = $total > 0 ? round($pending/$total*100) : 0;
                $checked = $stats['checked_in_count'] ?? 0;
                $pctChk  = $total > 0 ? round($checked/$total*100) : 0;
            @endphp

            <div class="funnel-bar">
                <div class="funnel-label"><span><i class="fas fa-paper-plane fa-xs me-1"></i>Sent</span><span>{{ $total }}</span></div>
                <div class="funnel-track"><div class="funnel-fill" style="width:100%;background:var(--grad-accent)"></div></div>
            </div>
            <div class="funnel-bar">
                <div class="funnel-label">
                    <span><i class="fas fa-circle-check fa-xs me-1" style="color:var(--success-color)"></i>Accepted</span>
                    <span>{{ $accepted }} <small class="text-muted">({{ $pctAcc }}%)</small></span>
                </div>
                <div class="funnel-track"><div class="funnel-fill" style="width:{{ $pctAcc }}%;background:var(--grad-success)"></div></div>
            </div>
            <div class="funnel-bar">
                <div class="funnel-label">
                    <span><i class="fas fa-circle-xmark fa-xs me-1" style="color:var(--danger-color)"></i>Declined</span>
                    <span>{{ $declined }} <small class="text-muted">({{ $pctDec }}%)</small></span>
                </div>
                <div class="funnel-track"><div class="funnel-fill" style="width:{{ $pctDec }}%;background:linear-gradient(90deg,#f43f5e,#fb7185)"></div></div>
            </div>
            <div class="funnel-bar">
                <div class="funnel-label">
                    <span><i class="fas fa-clock fa-xs me-1" style="color:var(--warning-color)"></i>Pending</span>
                    <span>{{ $pending }} <small class="text-muted">({{ $pctPend }}%)</small></span>
                </div>
                <div class="funnel-track"><div class="funnel-fill" style="width:{{ $pctPend }}%;background:linear-gradient(90deg,#f59e0b,#fbbf24)"></div></div>
            </div>
            <div class="funnel-bar mb-0">
                <div class="funnel-label">
                    <span><i class="fas fa-qrcode fa-xs me-1" style="color:var(--primary-color)"></i>Checked In</span>
                    <span>{{ $checked }} <small class="text-muted">({{ $pctChk }}%)</small></span>
                </div>
                <div class="funnel-track"><div class="funnel-fill" style="width:{{ $pctChk }}%;background:var(--grad-primary)"></div></div>
            </div>
        </div>
    </div>

    {{-- Attendance Donut (Chart.js) --}}
    <div class="col-lg-6">
        <div class="card-surface h-100">
            <div class="section-title mb-3" style="font-size:.95rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;justify-content:space-between">
                <span><i class="fas fa-chart-pie" style="color:var(--primary-color)"></i> Attendance Breakdown</span>
            </div>
            <div class="chart-wrap d-flex align-items-center justify-content-center">
                <canvas id="attendanceDonut"></canvas>
            </div>
        </div>
    </div>

</div>

{{-- ════ RECENT EVENTS TABLE ════ --}}
@if(!empty($eventStats) && count($eventStats))
<div class="card-surface animate__animated animate__fadeInUp" style="animation-delay:.15s">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="section-title" style="font-size:.95rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem">
            <i class="fas fa-table-list" style="color:var(--primary-color)"></i> Per-Event Performance
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            All Events <i class="fas fa-arrow-right ms-1 fa-xs"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="ev-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Invitations</th>
                    <th>Accepted</th>
                    <th>Checked In</th>
                    <th>Rate</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventStats as $ev)
                @php
                    $evRate = ($ev['accepted'] ?? 0) > 0 ? min(100, round((($ev['checked_in'] ?? 0) / $ev['accepted']) * 100)) : 0;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $ev['name'] }}</td>
                    <td class="text-muted">{{ $ev['date'] ?? '—' }}</td>
                    <td>{{ $ev['invited'] ?? 0 }}</td>
                    <td><span class="badge rounded-pill" style="background:rgba(16,185,129,.12);color:var(--success-color)">{{ $ev['accepted'] ?? 0 }}</span></td>
                    <td>{{ $ev['checked_in'] ?? 0 }}</td>
                    <td>
                        <div class="ev-rate-bar">
                            <span style="font-size:.78rem;font-weight:700;color:var(--text-main);min-width:30px">{{ $evRate }}%</span>
                            <div class="ev-rate-track"><div class="ev-rate-fill" style="width:{{ $evRate }}%"></div></div>
                        </div>
                    </td>
                    <td>
                        @if(isset($ev['id']))
                        <a href="{{ route('event.statistics', $ev['id']) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-1" style="font-size:.72rem">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function(){
    const ctx = document.getElementById('attendanceDonut');
    if (!ctx) return;
    const checked  = {{ $stats['checked_in_count'] ?? 0 }};
    const notIn    = Math.max(0, ({{ $stats['total_tickets'] ?? 0 }}) - checked);
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Checked In', 'Not Arrived'],
            datasets: [{
                data: [checked, notIn || 1],
                backgroundColor: ['#10b981','#e5e7eb'],
                borderWidth: 0,
                spacing: 2
            }]
        },
        options: {
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font:{ size:12, family:'Outfit' }, padding:16, usePointStyle:true, pointStyle:'circle' }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.raw}`
                    }
                }
            }
        }
    });
})();
</script>
@endpush