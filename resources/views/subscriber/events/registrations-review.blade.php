@extends('layouts.app')

@section('title', 'التسجيلات العامة')

@push('styles')
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .btn-action {
            background: var(--grad-primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(15, 143, 131, 0.3);
            transition: 0.3s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(15, 143, 131, 0.4);
            color: white;
        }

        .invitation-filter-group {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            gap: 0;
        }

        .invitation-filter-group .form-select {
            max-width: 210px;
        }

        .invitation-filter-group .form-control {
            min-width: 240px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.2s;
            color: #64748b;
            background: #f8fafc;
            text-decoration: none;
        }

        .btn-icon:hover {
            background: #e2e8f0;
            color: #334155;
            transform: translateY(-1px);
        }

        .btn-icon-primary:hover {
            background: #eff6ff;
            color: #3b82f6;
            border-color: #dbeafe;
        }

        .custom-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            min-height: 400px;
        }

        .table-custom thead th {
            background: #f8fafc;
            color: var(--text-soft);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            font-weight: 800;
            padding: 1rem .9rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: .95rem .9rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: .9rem;
        }

        .table-custom tbody tr:hover td {
            background: #fafcfb;
        }

        .badge-soft {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }

        .registration-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .3rem .7rem;
            font-size: .72rem;
            font-weight: 700;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .registration-pill.pending {
            background: rgba(245, 158, 11, .12);
            color: #92400e;
            border-color: rgba(245, 158, 11, .22);
        }

        .registration-pill.accepted {
            background: rgba(34, 197, 94, .12);
            color: #166534;
            border-color: rgba(34, 197, 94, .22);
        }

        .registration-pill.rejected {
            background: rgba(239, 68, 68, .12);
            color: #991b1b;
            border-color: rgba(239, 68, 68, .22);
        }

        .registration-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            justify-content: flex-end;
        }

        .registration-meta {
            color: var(--text-soft);
            font-size: .78rem;
        }

        .registrations-summary {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .summary-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: var(--text-main);
            font-size: .82rem;
            font-weight: 700;
        }

        .search-container {
            position: relative;
            max-width: 480px;
            width: 100%;
        }

        .search-input {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 15px 12px 45px;
            width: 100%;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .search-input:focus {
            background: white;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(15,143,131,.1);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .registration-actions { justify-content: flex-start; }
            .registrations-summary { width: 100%; }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="page-header">
        <div>
            <h1 class="h4 fw-bold mb-1">التسجيلات العامة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('events.index') }}" class="text-decoration-none text-muted">الفعاليات</a></li>
                    <li class="breadcrumb-item active text-primary">التسجيلات العامة</li>
                </ol>
            </nav>
            <p class="text-muted mb-0">{{ $event->title ?: $event->name }}</p>
        </div>
        <div class="registrations-summary">
            <span class="summary-pill">
                <i class="fas fa-list-ul text-primary"></i>{{ $totalRegistrations ?? 0 }} تسجيل
            </span>
            <span class="summary-pill">
                <i class="fas fa-hourglass-half text-warning"></i>{{ (int) ($statusStats['pending'] ?? 0) }} قيد المراجعة
            </span>
            <span class="summary-pill">
                <i class="fas fa-circle-check text-success"></i>{{ (int) ($statusStats['accepted'] ?? 0) }} مقبول
            </span>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left ms-1"></i> العودة للفعاليات
            </a>
        </div>
    </div>

    <div class="custom-card">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-white d-flex flex-column flex-xl-row justify-content-between align-items-stretch align-items-xl-center gap-3">
                <form action="{{ route('events.registrations.index', $event) }}" method="get" class="w-100" style="max-width: 720px;">
                    <div class="input-group invitation-filter-group">
                        <select name="status" class="form-select" style="max-width: 210px;">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>كل الحالات</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="accepted" {{ $statusFilter === 'accepted' ? 'selected' : '' }}>مقبول</option>
                            <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                        <span class="input-group-text bg-white border-end-0 border-start-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="search"
                               name="searchInput"
                               class="form-control border-start-0 ps-0"
                               placeholder="بحث بالاسم، البريد، الهاتف، المسمى الوظيفي..."
                               value="{{ $search ?? '' }}">
                        <button class="btn btn-primary px-4" type="submit">
                            بحث
                        </button>
                        @if(($search ?? '') !== '' || ($statusFilter ?? 'all') !== 'all')
                            <a href="{{ route('events.registrations.index', $event) }}" class="btn btn-outline-secondary px-3">
                                مسح
                            </a>
                        @endif
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <span class="badge btn-light border text-muted btn-sm rounded-3 px-3 fw-bold">
                        <i class="fas fa-clock me-1"></i>{{ (int) ($statusStats['rejected'] ?? 0) }} مرفوض
                    </span>
                </div>
            </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0 align-middle">
                <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>وقت التسجيل</th>
                    @foreach($dynamicFields as $field)
                        <th>{{ $field['label'] }}</th>
                    @endforeach
                    <th>الحالة</th>
                    <th class="text-end">الإجراء</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $row->guest_name }}</div>
                            <div class="registration-meta">#{{ $row->id }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $row->guest_email }}</div>
                        </td>
                        <td class="registration-meta">
                            {{ optional($row->created_at)->format('Y-m-d H:i') }}
                        </td>
                        @foreach($dynamicFields as $field)
                            @php($value = data_get($row->form_payload, $field['key']))
                            <td>
                                @if($field['type'] === 'checkbox')
                                    {{ $value ? 'نعم' : 'لا' }}
                                @elseif(is_null($value) || $value === '')
                                    -
                                @else
                                    {{ is_array($value) ? implode(', ', $value) : $value }}
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <span class="registration-pill {{ $row->status }} text-uppercase">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="registration-actions">
                                @if($row->status === 'pending')
                                    <form action="{{ route('events.registrations.review', [$event, $row]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="decision" value="accepted">
                                        <button class="btn-icon btn-icon-primary" type="submit" title="قبول">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('events.registrations.review', [$event, $row]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="decision" value="rejected">
                                        <button class="btn-icon" type="submit" title="رفض">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </form>
                                @endif

                                @can('delete', $row)
                                                                <form action="{{ route('events.registrations.destroy', [$event, $row]) }}"
                                                                            method="POST"
                                                                            class="d-inline"
                                                                            data-confirm="هل تريد حذف هذا التسجيل نهائياً؟">
                                        @csrf
                                        @method('DELETE')
                                                                                <button class="btn-icon js-confirm-action" type="submit" title="حذف" data-confirm="هل تريد حذف هذا التسجيل نهائياً؟">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + count($dynamicFields) }}" class="text-center py-5 text-muted">
                            لا توجد تسجيلات مطابقة للبحث أو الفلترة الحالية.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
