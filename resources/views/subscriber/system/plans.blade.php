@extends('layouts.system')

@section('title', 'إدارة الخطط')

@push('styles')
<style>
    /* ── Feature Pills ── */
    .feature-pill {
        display: inline-flex; align-items: center; gap: 5px;
        background: #f0fdf9; color: #065f46;
        border-radius: 6px; padding: 3px 8px;
        font-size: 0.71rem; font-weight: 600; margin: 2px 2px;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .feature-pill.off {
        background: #f1f5f9; color: #94a3b8;
        text-decoration: line-through;
    }
    /* Platform features get colored borders */
    .feature-pill.platform-on  { border-color: rgba(15,143,131,.3); background:#f0fdf9; }
    .feature-pill.platform-off { border-color: #e2e8f0; background:#f8fafc; color:#cbd5e1; text-decoration:line-through; }
    /* Limit badge inside pill */
    .pill-limit {
        background: #fff;
        border: 1px solid currentColor;
        border-radius: 4px;
        padding: 0px 5px;
        font-size: 0.63rem;
        font-weight: 800;
        opacity: .85;
    }
</style>
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">إدارة الخطط</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">إدارة الخطط</li>
                </ol>
            </nav>
        </div>
        <button class="btn-save" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="fas fa-plus me-2"></i> خطة جديدة
        </button>
    </div>

    {{-- ── Plans Table ── --}}
    <div class="custom-card">
        <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-layer-group" style="color:var(--primary-color);font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--text-main);">خطط الاشتراك</h5>
                    <p class="mb-0 small" style="color:var(--text-soft);">{{ $plans->count() }} خطة مسجّلة في النظام</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>الخطة</th>
                        <th>الرمز</th>
                        <th class="text-center">السعر السنوي</th>
                        <th class="text-center">الفعاليات</th>
                        <th class="text-center">المدعوون</th>
                        <th class="text-center">المميزات</th>
                        <th class="text-center">الاشتراكات</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">{{ $plan->name }}</div>
                                @if($plan->highlight_label)
                                    <span style="font-size:0.7rem;background:rgba(15,143,131,.1);color:var(--primary-color);padding:2px 7px;border-radius:5px;font-weight:700;">
                                        {{ $plan->highlight_label }}
                                    </span>
                                @endif
                                @if($plan->description)
                                    <div class="small mt-1" style="color:var(--text-soft);">{{ Str::limit($plan->description, 60) }}</div>
                                @endif
                            </td>
                            <td>
                                <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:0.78rem;">{{ $plan->code }}</code>
                            </td>
                            <td class="text-center fw-bold" style="color:var(--primary-color);">
                                {{ number_format($plan->annual_price, 0) }} ر.س
                            </td>
                            <td class="text-center">
                                {{ $plan->annual_event_limit ?? '∞' }}
                            </td>
                            <td class="text-center">
                                {{ $plan->per_event_invitee_limit ?? '∞' }}
                            </td>
                            <td class="text-center">
                                @php
                                    // Platform keys that deserve special colour treatment
                                    $platformKeys = [
                                        'registration_forms', 'teams', 'visual_identity',
                                        'event_header_image', 'event_footer_image',
                                    ];
                                    $platformColors = [
                                        'registration_forms' => '#1d4ed8',
                                        'teams'              => '#7c3aed',
                                        'visual_identity'    => '#be185d',
                                        'event_header_image' => '#0f6b63',
                                        'event_footer_image' => '#0369a1',
                                    ];
                                @endphp

                                @if(!empty($plan->features))
                                    <div class="d-flex flex-wrap gap-1 justify-content-center" style="max-width:260px;margin:0 auto;">
                                        @foreach($plan->features as $f)
                                            @php
                                                $fKey     = $f['key'] ?? null;
                                                $fEnabled = (bool)($f['enabled'] ?? true);
                                                $fLimit   = $f['limit'] ?? null;
                                                $isPlatform = in_array($fKey, $platformKeys);
                                                $pColor   = $isPlatform ? ($platformColors[$fKey] ?? '#0f8f83') : null;

                                                if ($isPlatform) {
                                                    $pillClass = $fEnabled ? 'platform-on' : 'platform-off';
                                                    $pillStyle = $fEnabled
                                                        ? "color:{$pColor};border-color:{$pColor}44;background:{$pColor}0e;"
                                                        : '';
                                                } else {
                                                    $pillClass = $fEnabled ? '' : 'off';
                                                    $pillStyle = '';
                                                }
                                            @endphp
                                            <span class="feature-pill {{ $pillClass }}"
                                                  style="{{ $pillStyle }}"
                                                  title="{{ $f['label'] ?? $fKey }}{{ $fLimit ? ' — حد: '.$fLimit : '' }}{{ !$fEnabled ? ' (معطّل)' : '' }}">

                                                <i class="{{ $f['icon'] ?? 'fas fa-circle-check' }}"></i>
                                                {{ Str::limit($f['label'] ?? $fKey ?? '', 14) }}

                                                @if($fLimit && $fEnabled)
                                                    <span class="pill-limit">{{ $fLimit }}</span>
                                                @endif

                                                @if(!$fEnabled)
                                                    <i class="fas fa-ban" style="font-size:0.6rem;opacity:.5;"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Legacy columns fallback --}}
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="feature-pill {{ $plan->includes_csv_import ? '' : 'off' }}" title="استيراد CSV">
                                            <i class="fas fa-file-csv"></i> CSV
                                        </span>
                                        <span class="feature-pill {{ $plan->includes_bulk_resend ? '' : 'off' }}" title="إرسال جماعي">
                                            <i class="fas fa-paper-plane"></i> جماعي
                                        </span>
                                        <span class="feature-pill {{ $plan->includes_customization ? '' : 'off' }}" title="تخصيص">
                                            <i class="fas fa-wand-magic-sparkles"></i> تخصيص
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center fw-bold">
                                {{ $plan->subscriptions_count ?? 0 }}
                            </td>
                            <td class="text-center">
                                @if($plan->is_active)
                                    <span class="badge-soft badge-success">نشطة</span>
                                @else
                                    <span class="badge-soft badge-neutral">معطّلة</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn-action btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editPlanModal{{ $plan->id }}">
                                        <i class="fas fa-pen-to-square"></i> تعديل
                                    </button>
                                    <form action="{{ route('system.plans.destroy', $plan) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                    <i class="fas fa-layer-group fa-2x opacity-25"></i>
                                    <span class="small">لا توجد خطط بعد — أنشئ أول خطة</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('modals')
    {{-- ── Create Plan Modal ── --}}
    <div class="modal fade" id="createPlanModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-plus-circle me-2" style="color:var(--primary-color);"></i> إنشاء خطة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.plans.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 pt-3">
                        @include('subscriber.system.partials.plan-form', ['plan' => null])
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-floppy-disk me-2"></i> حفظ الخطة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Edit Plan Modals ── --}}
    @foreach($plans as $plan)
    <div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-pen-to-square me-2" style="color:var(--primary-color);"></i> تعديل: {{ $plan->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.plans.update', $plan) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="modal-body px-4 pt-3">
                        @include('subscriber.system.partials.plan-form', ['plan' => $plan])
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
    @endforeach
@endpush
