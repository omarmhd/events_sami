@extends('layouts.app')

@section('title', 'الحضور')

@push('styles')
    <style>
        /* --- Page Header --- */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }

        /* --- Buttons & Inputs --- */
        .btn-action { background: var(--grad-primary); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(15, 143, 131, 0.3); transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15, 143, 131, 0.4); color: white; }

        /* --- Card & Table --- */
        .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: visible; min-height: 400px; }
        .custom-card .table-responsive { overflow: visible; }

        /* Guest dropdown — rendered via Bootstrap Popper so it escapes any overflow context */
        .guest-dropdown-menu {
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 28px -6px rgba(0,0,0,.14);
            border-radius: 12px;
            padding: 6px;
            min-width: 250px;
            max-height: 260px;
            overflow-y: auto;
            z-index: 1055;
        }
        .table-custom thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .table-custom tbody tr:last-child td { border-bottom: none; }
        .table-custom tbody tr:hover td { background: #fafcfb; }

        /* --- Avatar --- */
        .avatar-initials { width: 40px; height: 40px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; color: var(--primary-color); background: rgba(15,143,131,0.1); margin-right: 12px; flex-shrink: 0; }

        /* --- Badges --- */
        .badge-soft { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }

        /* --- Guest Dropdown --- */
        .dropdown-item { border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; font-weight: 500; color: #475569; }
        .dropdown-item:hover { background: #f8fafc; color: var(--primary-color); }

        /* --- Stats Card (sidebar infoCard) --- */
        .stats-card { background: white; border-radius: 20px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); padding: 1.25rem; }
        .stat-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 0.875rem; }
        .stat-label { color: var(--text-soft); }
        .stat-value { font-weight: 700; color: var(--text-main); }

        /* --- Filter bar --- */
        .attendance-filter-group .form-select,
        .attendance-filter-group .form-control { border-color: #e2e8f0; background: #f8fafc; }
        .attendance-filter-group .form-select:focus,
        .attendance-filter-group .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(15,143,131,.1); background: #fff; }

        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">الحضور</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">الحضور</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ── Main Card ── --}}
    <div class="custom-card">
        <div class="card-body p-0">

            {{-- Filter & Search Bar --}}
            <div class="p-4 border-bottom bg-white">
                <form action="{{ route('attendance_list') }}" method="get" class="w-100">
                    <div class="input-group attendance-filter-group flex-wrap gap-2">

                        {{-- Event Filter --}}
                        <select name="event_id" class="form-select" style="max-width: 230px; border-radius: 10px;">
                            <option value="">كل الفعاليات</option>
                            @foreach(($events ?? collect()) as $eventItem)
                                <option value="{{ $eventItem->id }}"
                                    {{ (string) request('event_id') === (string) $eventItem->id ? 'selected' : '' }}>
                                    {{ $eventItem->title ?: $eventItem->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Search Icon --}}
                        <span class="input-group-text bg-white border-end-0 border-start-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>

                        {{-- Search Input --}}
                        <input type="search"
                               name="searchInput"
                               class="form-control border-start-0 ps-0"
                               placeholder="بحث بالاسم، البريد، المسمى الوظيفي..."
                               value="{{ request('searchInput') }}">

                        {{-- Search Button --}}
                        <button class="btn btn-primary px-4" type="submit" style="border-radius: 10px;">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>

                        {{-- Clear Filters --}}
                        @if(request('searchInput') || request('event_id'))
                            <a href="{{ route('attendance_list') }}"
                               class="btn btn-outline-secondary px-3"
                               style="border-radius: 10px;">
                                <i class="fas fa-times me-1"></i> مسح
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Active Event Filter Badge --}}
            @if(request('event_id') && ($selectedEvent = ($events ?? collect())->firstWhere('id', request('event_id'))))
                <div class="px-4 py-2 border-bottom bg-light d-flex align-items-center gap-2 small">
                    <i class="fas fa-filter text-muted"></i>
                    <span class="text-muted">مصفّى بـ:</span>
                    <span class="badge-soft badge-neutral">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $selectedEvent->title ?: $selectedEvent->name }}
                    </span>
                </div>
            @endif

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-custom mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">المدعو</th>
                            <th>الفعالية</th>
                            <th>المسمى الوظيفي</th>
                            <th class="text-center">تسجيل الدخول الرئيسي</th>
                            <th class="text-center">المرافقون</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $mainTicket = $row->InvitationQrs->where('type','main')->first();
                                $guests     = $row->InvitationQrs->where('type','guest');
                            @endphp
                            <tr>
                                {{-- Invitee --}}
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials">
                                            {{ strtoupper(substr($row->invitee_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: var(--text-main);">{{ $row->invitee_name }}</div>
                                            <div class="small" style="color: var(--text-soft); font-size: 0.8rem;">{{ $row->invitee_email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Event --}}
                                <td>
                                    @if($row->event)
                                        <span class="fw-bold" style="color: var(--text-main); font-size: 0.875rem;">
                                            {{ $row->event->title ?: $row->event->name }}
                                        </span>
                                    @else
                                        <span class="text-muted opacity-50">—</span>
                                    @endif
                                </td>

                                {{-- Job Title --}}
                                <td>
                                    <span class="small fw-bold" style="color: var(--text-soft);">
                                        {{ $row->invitee_position ?: '—' }}
                                    </span>
                                </td>

                                {{-- Main Check-in --}}
                                <td class="text-center">
                                    @if($mainTicket && $mainTicket->is_used)
                                        <span class="badge-soft badge-success">
                                            <i class="fas fa-check-circle me-1"></i> تم الدخول
                                        </span>
                                    @elseif($mainTicket)
                                        <form action="{{ route('attendance.checked_in') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $mainTicket->id }}">
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-primary"
                                                    style="border-radius: 8px; font-size: 0.8rem;">
                                                <i class="fas fa-user-check me-1"></i> تسجيل الدخول
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted opacity-40 small">لا يوجد تذكرة</span>
                                    @endif
                                </td>

                                {{-- Guests --}}
                                <td class="text-center">
                                    @if($guests->count())
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm border dropdown-toggle"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    data-bs-auto-close="outside"
                                                    aria-expanded="false"
                                                    style="border-radius: 8px; font-size: 0.8rem; white-space: nowrap;">
                                                <i class="fas fa-users me-1" style="font-size:.7rem;"></i>
                                                المرافقون ({{ $guests->where('is_used', true)->count() }}/{{ $guests->count() }})
                                            </button>
                                            <ul class="dropdown-menu guest-dropdown-menu"
                                                @foreach($guests as $guest)
                                                    <li class="d-flex justify-content-between align-items-center px-2 py-2 border-bottom">
                                                        <span class="small fw-bold" style="color: var(--text-main);">
                                                            مرافق {{ $loop->iteration }}
                                                        </span>
                                                        @if($guest->is_used)
                                                            <span class="badge-soft badge-success">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @else
                                                            <form action="{{ route('attendance.checked_in') }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $guest->id }}">
                                                                <button class="btn btn-sm btn-outline-primary py-0 px-2"
                                                                        style="font-size: 0.75rem; border-radius: 6px;">
                                                                    دخول
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted small opacity-50">لا يوجد</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center gap-2" style="color: var(--text-soft);">
                                        <i class="fas fa-users fa-2x opacity-25"></i>
                                        <span class="small">لا توجد نتائج مطابقة للبحث</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($rows->hasPages())
                <div class="d-flex justify-content-center py-4 border-top">
                    {{ $rows->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection

@section('infoCard')
    {{-- Stats Sidebar --}}
    <div class="stats-card d-none d-md-block">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-chart-bar" style="color: var(--primary-color);"></i>
            <h6 class="fw-bold mb-0" style="color: var(--text-main);">إحصائيات الحضور</h6>
        </div>

        <div class="stat-row">
            <span class="stat-label">إجمالي التذاكر</span>
            <span class="stat-value">{{ $stats->total ?? '0' }}</span>
        </div>
        <hr class="my-2 opacity-25">
        <div class="stat-row">
            <span class="stat-label">
                <i class="fas fa-circle small me-1" style="color: var(--primary-color);"></i>
                تم الدخول
            </span>
            <span class="stat-value" style="color: var(--primary-color);">{{ $stats->checked ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">
                <i class="fas fa-circle small me-1 text-warning"></i>
                لم يدخل بعد
            </span>
            <span class="stat-value text-warning">{{ $stats->not_checked ?? '0' }}</span>
        </div>

        @if(($stats->total ?? 0) > 0)
            <hr class="my-2 opacity-25">
            <div class="mt-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span style="color: var(--text-soft);">نسبة الحضور</span>
                    <span class="fw-bold" style="color: var(--primary-color);">
                        {{ round(($stats->checked / $stats->total) * 100) }}%
                    </span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar"
                         style="width: {{ round(($stats->checked / $stats->total) * 100) }}%; background: var(--grad-primary); border-radius: 10px;">
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
