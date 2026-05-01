@extends('layouts.system')

@section('title', 'مستخدمو النظام')

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">مستخدمو النظام</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">مستخدمو النظام</li>
                </ol>
            </nav>
        </div>
        <button class="btn-save" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i> مشرف جديد
        </button>
    </div>

    {{-- ── Search Bar ── --}}
    <div class="search-card">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="field-label">البحث في المستخدمين</label>
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control" placeholder="الاسم أو البريد الإلكتروني">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn-save" style="padding: 8px 20px;">
                    <i class="fas fa-magnifying-glass me-1"></i> بحث
                </button>
                @if($search)
                    <a href="{{ route('system.users') }}" class="btn btn-light rounded-3 ms-1" style="padding: 8px 16px; font-size: 0.85rem;">
                        <i class="fas fa-xmark me-1"></i> مسح
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Users Table ── --}}
    <div class="custom-card">
        <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-user-shield" style="color:var(--primary-color);font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--text-main);">دليل المستخدمين</h5>
                    <p class="mb-0 small" style="color:var(--text-soft);">جميع المستخدمين عبر المنظمات وصلاحياتهم</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>المستخدم</th>
                        <th>الدور</th>
                        <th>المنظمة</th>
                        <th class="text-center">مشرف النظام</th>
                        <th class="text-center">آخر دخول</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">{{ $user->name }}</div>
                                <div class="small" style="color:var(--text-soft);">{{ $user->email }}</div>
                            </td>
                            <td>
                                @php
                                    $roleLabels = [
                                        'super_admin'     => ['label' => 'مشرف أعلى',    'class' => 'badge-danger'],
                                        'saas_admin'      => ['label' => 'مشرف نظام',    'class' => 'badge-purple'],
                                        'organizer_owner' => ['label' => 'مالك منظمة',   'class' => 'badge-info'],
                                        'organizer_admin' => ['label' => 'مشرف منظمة',   'class' => 'badge-warning'],
                                        'organizer_staff' => ['label' => 'موظف منظمة',   'class' => 'badge-neutral'],
                                    ];
                                    $roleInfo = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'badge-neutral'];
                                @endphp
                                <span class="badge-soft {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                            </td>
                            <td>
                                @if($user->company)
                                    <div style="font-size:0.85rem;color:var(--text-main);">{{ $user->company->name }}</div>
                                    <code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:0.73rem;">
                                        {{ $user->company->subdomain }}
                                    </code>
                                @else
                                    <span style="color:var(--text-soft);font-size:0.85rem;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->is_system_admin)
                                    <span class="badge-soft badge-success"><i class="fas fa-shield-halved me-1"></i>نعم</span>
                                @else
                                    <span class="badge-soft badge-neutral">لا</span>
                                @endif
                            </td>
                            <td class="text-center small" style="color:var(--text-soft);">
                                {{ optional($user->last_login_at)->format('Y-m-d H:i') ?: '—' }}
                            </td>
                            <td class="text-center">
                                @if($user->is_system_admin)
                                    <button class="btn-action btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal{{ $user->id }}">
                                        <i class="fas fa-pen-to-square"></i> تعديل
                                    </button>
                                @else
                                    <span style="color:var(--text-soft);font-size:0.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                    <i class="fas fa-users fa-2x opacity-25"></i>
                                    <span class="small">لا توجد مستخدمون مطابقون</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-top" style="background:#f8fafc;">
                {{ $users->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>

@endsection

@push('modals')

    {{-- ── Create System Admin Modal ── --}}
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-user-plus me-2" style="color:var(--primary-color);"></i> إضافة مشرف نظام
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 pt-3">
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="section-label">
                                    <i class="fas fa-user" style="color:var(--primary-color);"></i>
                                    بيانات المشرف
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="الاسم الكامل" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="email@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control"
                                       placeholder="8 أحرف على الأقل" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">الدور <span class="text-danger">*</span></label>
                                <select name="role" class="form-select">
                                    <option value="saas_admin">مشرف نظام (saas_admin)</option>
                                    <option value="super_admin">مشرف أعلى (super_admin)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-floppy-disk me-2"></i> إنشاء المشرف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Edit System Admin Modals ── --}}
    @foreach($users as $user)
        @if($user->is_system_admin)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:20px;border:none;">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="fw-bold" style="color:var(--text-main);">
                            <i class="fas fa-pen-to-square me-2" style="color:var(--primary-color);"></i>
                            تعديل: {{ $user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('system.users.update', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-body px-4 pt-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="field-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="field-label">الدور <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select">
                                        <option value="saas_admin"  {{ $user->role === 'saas_admin'  ? 'selected' : '' }}>مشرف نظام (saas_admin)</option>
                                        <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>مشرف أعلى (super_admin)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="field-label">كلمة مرور جديدة</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="اتركه فارغاً للإبقاء على الحالية" minlength="8">
                                    <span class="field-hint">اتركه فارغاً إذا لم تكن تريد تغييرها</span>
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
        @endif
    @endforeach

@endpush
