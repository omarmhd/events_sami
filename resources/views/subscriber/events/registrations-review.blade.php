@extends('layouts.app')

@section('title', 'التسجيلات العامة')

@push('styles')
    <style>
        .registrations-shell {
            background: linear-gradient(180deg, rgba(15,143,131,.06), rgba(15,143,131,0));
            border-radius: 28px;
            padding: 1.25rem;
        }

        .registrations-card {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .registrations-toolbar {
            border-bottom: 1px solid #eef2f7;
            background: #fff;
        }

        .registrations-toolbar .form-select,
        .registrations-toolbar .form-control {
            background: #f8fafc;
            border-color: #e2e8f0;
            border-radius: 12px;
        }

        .registrations-toolbar .form-select:focus,
        .registrations-toolbar .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(15,143,131,.12);
            background: #fff;
        }

        .registrations-stat {
            background: #fff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 1rem 1.1rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .04);
        }

        .registrations-stat .label {
            font-size: .78rem;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 700;
        }

        .registrations-stat .value {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }

        .registrations-table thead th {
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

        .registrations-table tbody td {
            padding: .95rem .9rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: .9rem;
        }

        .registrations-table tbody tr:hover td {
            background: #fafcfb;
        }

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

        .registration-actions .btn {
            border-radius: 10px;
        }

        .registration-meta {
            color: var(--text-soft);
            font-size: .78rem;
        }

        @media (max-width: 768px) {
            .registrations-shell { padding: .75rem; }
            .registrations-toolbar .input-group { flex-direction: column; }
            .registrations-toolbar .input-group > * { width: 100%; border-radius: 12px !important; }
            .registration-actions { justify-content: flex-start; }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-0 registrations-shell">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">التسجيلات العامة</h1>
            <p class="text-muted mb-0">{{ $event->title ?: $event->name }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="badge rounded-pill text-bg-light border text-dark px-3 py-2">
                <i class="fas fa-list-ul me-1"></i>{{ $totalRegistrations ?? 0 }} تسجيل
            </span>
            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left ms-1"></i> العودة للفعاليات
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="registrations-stat">
                <div class="label">الإجمالي</div>
                <div class="value">{{ $totalRegistrations ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="registrations-stat">
                <div class="label">قيد المراجعة</div>
                <div class="value">{{ (int) ($statusStats['pending'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="registrations-stat">
                <div class="label">مقبول / مرفوض</div>
                <div class="value">{{ (int) ($statusStats['accepted'] ?? 0) + (int) ($statusStats['rejected'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="registrations-card">
        <div class="registrations-toolbar p-3 p-lg-4">
            <form action="{{ route('events.registrations.index', $event) }}" method="get">
                <div class="input-group flex-wrap gap-2 align-items-stretch">
                    <select name="status" class="form-select" style="max-width: 200px;">
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
                        <i class="fas fa-search me-1"></i> بحث
                    </button>

                    @if(($search ?? '') !== '' || ($statusFilter ?? 'all') !== 'all')
                        <a href="{{ route('events.registrations.index', $event) }}" class="btn btn-outline-secondary px-3">
                            مسح
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table registrations-table mb-0 align-middle">
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
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> قبول
                                        </button>
                                    </form>
                                    <form action="{{ route('events.registrations.review', [$event, $row]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="decision" value="rejected">
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-xmark me-1"></i> رفض
                                        </button>
                                    </form>
                                @endif

                                @can('delete', $row)
                                    <form action="{{ route('events.registrations.destroy', [$event, $row]) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('هل تريد حذف هذا التسجيل نهائياً؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> حذف
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 5 + count($dynamicFields) }}" class="text-center py-5 text-muted">
                            لا توجد تسجيلات مطابقة للبحث أو الفلترة الحالية.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $rows->links() }}
    </div>
</div>
@endsection
