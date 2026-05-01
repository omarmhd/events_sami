@extends('layouts.system')

@section('title', 'إدارة المنظمات')

{{-- جميع الأصناف المشتركة موجودة في layouts/system.blade.php --}}

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">إدارة المنظمات</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">إدارة المنظمات</li>
                </ol>
            </nav>
        </div>
        <button class="btn-save" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
            <i class="fas fa-plus me-2"></i> منظمة جديدة
        </button>
    </div>

    {{-- ── Search Bar ── --}}
    <div class="search-card">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="field-label">البحث في المنظمات</label>
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control" placeholder="الاسم / النطاق الفرعي / البريد الإلكتروني">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn-save" style="padding: 8px 20px;">
                    <i class="fas fa-magnifying-glass me-1"></i> بحث
                </button>
                @if($search)
                    <a href="{{ route('system.companies') }}" class="btn btn-light rounded-3 ms-1" style="padding: 8px 16px; font-size: 0.85rem;">
                        <i class="fas fa-xmark me-1"></i> مسح
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Companies Table ── --}}
    <div class="custom-card">
        <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-building" style="color:var(--primary-color);font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--text-main);">المنظمات المسجّلة</h5>
                    <p class="mb-0 small" style="color:var(--text-soft);">{{ $companies->total() }} منظمة في النظام</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>المنظمة</th>
                        <th>المالك</th>
                        <th>النطاق الفرعي</th>
                        <th class="text-center">الخطة الحالية</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">{{ $company->name }}</div>
                                @if($company->contact_email)
                                    <div class="small mt-1" style="color:var(--text-soft);">{{ $company->contact_email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($company->owner)
                                    <div class="fw-semibold" style="color:var(--text-main); font-size:0.85rem;">{{ $company->owner->name }}</div>
                                    <div class="small" style="color:var(--text-soft);">{{ $company->owner->email }}</div>
                                @else
                                    <span style="color:var(--text-soft); font-size:0.85rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:0.78rem;">{{ $company->subdomain }}</code>
                            </td>
                            <td class="text-center">
                                @php
                                    $planCode = optional(optional($company->latestSubscription)->plan)->code
                                               ?: $company->current_plan_code;
                                @endphp
                                @if($planCode)
                                    <span class="badge-soft badge-neutral" style="font-family:monospace;">{{ strtoupper($planCode) }}</span>
                                @else
                                    <span style="color:var(--text-soft); font-size:0.82rem;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($company->status === 'active')
                                    <span class="badge-soft badge-success">نشطة</span>
                                @elseif($company->status === 'trial')
                                    <span class="badge-soft badge-warning">تجريبية</span>
                                @else
                                    <span class="badge-soft badge-danger">موقوفة</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">

                                    {{-- Edit --}}
                                    <button class="btn-action btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCompanyModal{{ $company->id }}">
                                        <i class="fas fa-pen-to-square me-1"></i> تعديل
                                    </button>

                                    {{-- Subscription plan --}}
                                    <button class="btn-action btn-action-plan"
                                            data-bs-toggle="modal"
                                            data-bs-target="#planModal{{ $company->id }}">
                                        <i class="fas fa-layer-group me-1"></i> الخطة
                                    </button>

                                    {{-- Toggle status --}}
                                    <form action="{{ route('system.companies.status', $company) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        @if($company->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="btn-action btn-action-danger">
                                                <i class="fas fa-ban me-1"></i> تعليق
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="btn-action btn-action-activate">
                                                <i class="fas fa-circle-check me-1"></i> تفعيل
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Impersonate --}}
                                    <form action="{{ route('system.companies.impersonate', $company) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-action btn-action-impersonate">
                                            <i class="fas fa-user-secret me-1"></i> انتحال
                                        </button>
                                    </form>

                                    {{-- Terminate Subscription --}}
                                    @if($company->latestSubscription && in_array($company->latestSubscription->status, ['active','trial']))
                                    <button class="btn-action btn-action-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#terminateSubModal{{ $company->id }}"
                                            title="إنهاء الاشتراك">
                                        <i class="fas fa-circle-xmark me-1"></i> إنهاء الاشتراك
                                    </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                    <i class="fas fa-building fa-2x opacity-25"></i>
                                    <span class="small">لا توجد منظمات — أنشئ أول منظمة</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="p-4 border-top" style="background:#f8fafc;">
                {{ $companies->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>

@endsection

@push('modals')

    {{-- ── Create Company Modal ── --}}
    <div class="modal fade" id="createCompanyModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-plus-circle me-2" style="color:var(--primary-color);"></i> إنشاء منظمة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.companies.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 pt-3">
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="section-label">
                                    <i class="fas fa-building" style="color:var(--primary-color);"></i>
                                    معلومات المنظمة
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label">اسم المنظمة <span class="text-danger">*</span></label>
                                <input type="text" name="organization_name" class="form-control"
                                       placeholder="مثال: شركة الأحداث المتقدمة" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">النطاق الفرعي <span class="text-danger">*</span></label>
                                <input type="text" name="subdomain" class="form-control"
                                       placeholder="مثال: my-company" required
                                       style="font-family:monospace;"
                                       oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9\-]/g,'')">
                                <span style="font-size:0.73rem;color:var(--text-soft);margin-top:3px;display:block;">حروف إنجليزية صغيرة وأرقام وشرطات فقط</span>
                            </div>

                            <div class="col-12 mt-1">
                                <div class="section-label">
                                    <i class="fas fa-user" style="color:var(--primary-color);"></i>
                                    معلومات المالك
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">اسم المالك <span class="text-danger">*</span></label>
                                <input type="text" name="owner_name" class="form-control"
                                       placeholder="الاسم الكامل" required>
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" name="owner_email" class="form-control"
                                       placeholder="email@example.com" required>
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="05xxxxxxxx">
                            </div>

                            <div class="col-12 mt-1">
                                <div class="section-label">
                                    <i class="fas fa-sliders" style="color:var(--primary-color);"></i>
                                    إعدادات الحساب
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">حالة الحساب <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="trial">تجريبية</option>
                                    <option value="active">نشطة</option>
                                    <option value="suspended">موقوفة</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">تقدير الفعاليات السنوية</label>
                                <input type="number" name="annual_events_estimate" class="form-control"
                                       placeholder="اختياري" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-floppy-disk me-2"></i> إنشاء المنظمة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Edit Company Modals ── --}}
    @foreach($companies as $company)
    <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-pen-to-square me-2" style="color:var(--primary-color);"></i> تعديل: {{ $company->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.companies.update', $company) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body px-4 pt-3">
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="section-label">
                                    <i class="fas fa-building" style="color:var(--primary-color);"></i>
                                    معلومات المنظمة
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label">اسم المنظمة <span class="text-danger">*</span></label>
                                <input type="text" name="organization_name" class="form-control"
                                       value="{{ $company->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">النطاق الفرعي <span class="text-danger">*</span></label>
                                <input type="text" name="subdomain" class="form-control"
                                       value="{{ $company->subdomain }}" required
                                       style="font-family:monospace;">
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">البريد الإلكتروني للتواصل</label>
                                <input type="email" name="contact_email" class="form-control"
                                       value="{{ $company->contact_email }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control"
                                       value="{{ $company->phone }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">حالة الحساب <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="trial"     {{ $company->status === 'trial'     ? 'selected' : '' }}>تجريبية</option>
                                    <option value="active"    {{ $company->status === 'active'    ? 'selected' : '' }}>نشطة</option>
                                    <option value="suspended" {{ $company->status === 'suspended' ? 'selected' : '' }}>موقوفة</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">تقدير الفعاليات السنوية</label>
                                <input type="number" name="annual_events_estimate" class="form-control"
                                       value="{{ $company->annual_events_estimate }}" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-floppy-disk me-2"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Subscription Plan Modal ── --}}
    <div class="modal fade" id="planModal{{ $company->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-layer-group me-2" style="color:var(--primary-color);"></i> تغيير خطة الاشتراك
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.companies.subscription', $company) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body px-4 pt-3">
                        <p class="small mb-3" style="color:var(--text-soft);">
                            تغيير خطة الاشتراك لـ <strong style="color:var(--text-main);">{{ $company->name }}</strong>
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="field-label">الخطة الجديدة <span class="text-danger">*</span></label>
                                <select name="plan_code" class="form-select">
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->code }}"
                                            {{ (optional(optional($company->latestSubscription)->plan)->code === $plan->code) ? 'selected' : '' }}>
                                            {{ $plan->name }} ({{ strtoupper($plan->code) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">حالة الاشتراك <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="active">نشط</option>
                                    <option value="trial">تجريبي</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-circle-check me-2"></i> تطبيق الخطة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- ── Terminate Subscription Modals ── --}}
    @foreach($companies as $company)
    @if($company->latestSubscription && in_array($company->latestSubscription->status, ['active','trial']))
    <div class="modal fade" id="terminateSubModal{{ $company->id }}" tabindex="-1" aria-labelledby="terminateSubLabel{{ $company->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
            <div class="modal-content" style="border-radius:20px;border:none;overflow:hidden;">

                {{-- Danger header --}}
                <div style="background:linear-gradient(135deg,#7f1d1d,#ef4444);padding:1.5rem 1.75rem;color:#fff;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0" id="terminateSubLabel{{ $company->id }}">إنهاء الاشتراك</h5>
                            <p class="mb-0 small opacity-80">{{ $company->name }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('system.companies.terminate-subscription', $company) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body p-4">

                        {{-- Warning notice --}}
                        <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;">
                            <p class="mb-1 fw-semibold" style="color:#991b1b;font-size:.88rem;">
                                <i class="fas fa-circle-exclamation me-1"></i> تحذير: هذا الإجراء لا يمكن التراجع عنه
                            </p>
                            <ul class="mb-0 ps-3" style="color:#b91c1c;font-size:.82rem;line-height:1.8;">
                                <li>سيتم إنهاء الاشتراك النشط فوراً.</li>
                                <li>سيتم تعليق حساب المنظمة.</li>
                                <li>سيُعاد توجيه المشترك إلى صفحة انتهاء الاشتراك عند تسجيل الدخول.</li>
                                <li>يمكنك إعادة التفعيل لاحقاً عبر تغيير الحالة أو تطبيق خطة جديدة.</li>
                            </ul>
                        </div>

                        {{-- Subscription summary --}}
                        @php $sub = $company->latestSubscription; @endphp
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.85rem 1rem;margin-bottom:1.25rem;font-size:.83rem;">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color:#64748b;">الخطة الحالية</span>
                                <strong>{{ optional($sub->plan)->name ?? strtoupper($sub->status) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="color:#64748b;">حالة الاشتراك</span>
                                <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                    {{ $sub->status === 'trial' ? 'تجريبي' : 'نشط' }}
                                </span>
                            </div>
                            @if($sub->ends_at)
                            <div class="d-flex justify-content-between">
                                <span style="color:#64748b;">تاريخ الانتهاء المخطط</span>
                                <strong>{{ $sub->ends_at->format('Y/m/d') }}</strong>
                            </div>
                            @endif
                        </div>

                        {{-- Reason field --}}
                        <div class="mb-1">
                            <label class="field-label">سبب الإنهاء (اختياري — للسجل الداخلي)</label>
                            <textarea name="reason" rows="3" class="form-control"
                                      placeholder="مثال: عدم الدفع، انتهاك الشروط، طلب العميل..."
                                      style="resize:none;font-size:.85rem;"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 gap-2">
                        <button type="button" class="btn btn-light rounded-3 flex-grow-1" data-bs-dismiss="modal">
                            <i class="fas fa-arrow-right me-1"></i> إلغاء
                        </button>
                        <button type="submit" class="btn rounded-3 flex-grow-1 fw-bold"
                                style="background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;">
                            <i class="fas fa-circle-xmark me-1"></i> تأكيد الإنهاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

@endpush
