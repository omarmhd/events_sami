@extends('layouts.system')

@section('title', 'طلبات التجديد')

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">طلبات التجديد والتواصل</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">طلبات التجديد</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ── Status Filter ── --}}
    <div class="search-card">
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <span class="fw-bold small" style="color:var(--text-soft);">تصفية:</span>
            @foreach(['pending' => ['pending', 'قيد الانتظار', 'badge-warning'], 'contacted' => ['contacted', 'تم التواصل', 'badge-success'], 'dismissed' => ['dismissed', 'مؤرشف', 'badge-neutral'], 'all' => ['all', 'الكل', 'badge-info']] as $val => $info)
            <a href="{{ route('system.renewal-requests', ['status' => $val]) }}"
               class="badge-soft {{ $filter === $val ? $info[2] : 'badge-neutral' }}"
               style="text-decoration:none;padding:6px 14px;cursor:pointer;">
                {{ $info[1] }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Requests Table ── --}}
    <div class="custom-card">
        <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-rotate-right" style="color:var(--primary-color);font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--text-main);">طلبات التجديد والاشتراك</h5>
                    <p class="mb-0 small" style="color:var(--text-soft);">
                        طلبات المشتركين لتجديد اشتراكاتهم أو رفع التعليق أو الترقية
                    </p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>المشترك</th>
                        <th>المنظمة</th>
                        <th>سبب الطلب</th>
                        <th class="text-center">الخطة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">تاريخ الطلب</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <div class="fw-bold" style="color:var(--text-main);">{{ $req->contact_name }}</div>
                            <div class="small" style="color:var(--text-soft);">{{ $req->contact_email }}</div>
                            @if($req->contact_phone)
                            <div class="small" style="color:var(--text-soft);">
                                <i class="fas fa-phone me-1" style="font-size:.65rem;"></i>{{ $req->contact_phone }}
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($req->company)
                            <div style="font-size:.875rem;font-weight:700;color:var(--text-main);">{{ $req->company->name }}</div>
                            <code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:.73rem;">{{ $req->company->subdomain }}</code>
                            @else
                            <span style="color:var(--text-soft);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($req->message)
                            <div style="font-size:.8rem;color:var(--text-muted);max-width:200px;">
                                {{ Str::limit($req->message, 80) }}
                            </div>
                            @else
                            <span style="color:var(--text-soft);font-size:.82rem;">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:.78rem;">
                                {{ strtoupper($req->plan_code ?? '—') }}
                            </code>
                        </td>
                        <td class="text-center">
                            @if($req->status === 'pending')
                                <span class="badge-soft badge-warning">
                                    <i class="fas fa-clock me-1"></i>قيد الانتظار
                                </span>
                            @elseif($req->status === 'contacted')
                                <span class="badge-soft badge-success">
                                    <i class="fas fa-phone-volume me-1"></i>تم التواصل
                                </span>
                                @if($req->contacted_at)
                                <div class="small mt-1" style="color:var(--text-soft);">
                                    {{ $req->contacted_at->format('Y-m-d') }}
                                </div>
                                @endif
                            @elseif($req->status === 'dismissed')
                                <span class="badge-soft badge-neutral">مؤرشف</span>
                            @endif
                        </td>
                        <td class="text-center small" style="color:var(--text-soft);">
                            {{ $req->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @if($req->status === 'pending')
                                <form action="{{ route('system.renewal-requests.contacted', $req) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action btn-action-paid"
                                            title="تحديد كـ تم التواصل">
                                        <i class="fas fa-phone-volume"></i> تواصلنا
                                    </button>
                                </form>
                                <form action="{{ route('system.renewal-requests.dismiss', $req) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action btn-action-danger"
                                            title="أرشفة الطلب"
                                            onclick="return confirm('أرشفة هذا الطلب؟')">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                                @elseif($req->status === 'contacted')
                                <form action="{{ route('system.renewal-requests.dismiss', $req) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action btn-action-danger"
                                            title="أرشفة الطلب"
                                            onclick="return confirm('أرشفة هذا الطلب؟')">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                                @else
                                <span style="color:var(--text-soft);font-size:.8rem;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                <i class="fas fa-rotate-right fa-2x opacity-25"></i>
                                <span class="small">لا توجد طلبات {{ $filter === 'pending' ? 'قيد الانتظار' : '' }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="p-4 border-top" style="background:#f8fafc;">
            {{ $requests->appends(['status' => $filter])->links() }}
        </div>
        @endif
    </div>

@endsection
