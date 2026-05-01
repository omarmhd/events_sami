@extends('layouts.app')

@section('title', 'التسجيلات العامة')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">التسجيلات العامة</h1>
            <p class="text-muted mb-0">{{ $event->title ?: $event->name }}</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left ms-1"></i> العودة للفعاليات
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
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
                        <td>{{ $row->guest_name }}</td>
                        <td>{{ $row->guest_email }}</td>
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
                        <td><span class="badge bg-secondary text-uppercase">{{ $row->status }}</span></td>
                        <td class="text-end">
                            @if($row->status === 'pending')
                                <form action="{{ route('events.registrations.review', [$event, $row]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="decision" value="accepted">
                                    <button class="btn btn-sm btn-success">قبول</button>
                                </form>
                                <form action="{{ route('events.registrations.review', [$event, $row]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="decision" value="rejected">
                                    <button class="btn btn-sm btn-outline-danger">رفض</button>
                                </form>
                            @else
                                <span class="text-muted small">تمت المراجعة</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 4 + count($dynamicFields) }}" class="text-center py-5 text-muted">لا توجد تسجيلات حتى الآن.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
