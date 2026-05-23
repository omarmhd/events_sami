@extends('layouts.system')

@section('title', 'إدارة الفواتير')

{{-- جميع الأصناف المشتركة موجودة في layouts/system.blade.php --}}

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">إدارة الفواتير</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">الفواتير</li>
                </ol>
            </nav>
        </div>
        <button class="btn-save" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
            <i class="fas fa-plus me-2"></i> إنشاء فاتورة
        </button>
    </div>

    {{-- ── KPI Row ── --}}
    <div class="mini-kpi-grid">
        <div class="mini-kpi">
            <div class="mini-kpi-icon" style="background:rgba(99,102,241,.1);color:#6366f1;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="mini-kpi-label">إجمالي الفواتير</div>
            <div class="mini-kpi-val">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="mini-kpi">
            <div class="mini-kpi-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="mini-kpi-label">مدفوعة</div>
            <div class="mini-kpi-val" style="color:#10b981;">{{ number_format($stats['paid']) }}</div>
        </div>
        <div class="mini-kpi">
            <div class="mini-kpi-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="mini-kpi-label">معلّقة</div>
            <div class="mini-kpi-val" style="color:#f59e0b;">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="mini-kpi">
            <div class="mini-kpi-icon" style="background:rgba(244,63,94,.1);color:#f43f5e;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="mini-kpi-label">متأخرة</div>
            <div class="mini-kpi-val" style="color:#f43f5e;">{{ number_format($stats['overdue']) }}</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="custom-card mb-3">
        <div class="p-3">
            <form action="{{ route('system.invoices') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="field-label mb-1">بحث</label>
                    <input type="search" name="search" class="form-control" style="min-width:220px;"
                           placeholder="رقم الفاتورة أو اسم المنظمة..."
                           value="{{ $search }}">
                </div>
                <div>
                    <label class="field-label mb-1">الحالة</label>
                    <select name="status" class="form-select" style="min-width:150px;">
                        <option value="">كل الحالات</option>
                        <option value="paid"    {{ $status === 'paid'    ? 'selected' : '' }}>مدفوعة</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>معلّقة</option>
                        <option value="unpaid"  {{ $status === 'unpaid'  ? 'selected' : '' }}>غير مدفوعة</option>
                    </select>
                </div>
                <button type="submit" class="btn-save" style="align-self:flex-end;">
                    <i class="fas fa-search me-1"></i> بحث
                </button>
                @if($search || $status)
                    <a href="{{ route('system.invoices') }}" class="btn btn-light rounded-3" style="align-self:flex-end; font-size: 0.85rem;">
                        <i class="fas fa-xmark me-1"></i> مسح
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- ── Invoices Table ── --}}
    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المنظمة</th>
                        <th>الخطة</th>
                        <th class="text-center">المبلغ</th>
                        <th class="text-center">الضريبة</th>
                        <th class="text-center">الإجمالي</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">تاريخ الاستحقاق</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $isOverdue = in_array($invoice->status, ['pending','unpaid']) && $invoice->due_at && $invoice->due_at->isPast();
                        @endphp
                        <tr>
                            <td>
                                <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:0.78rem;">
                                    {{ $invoice->invoice_number }}
                                </code>
                            </td>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">
                                    {{ optional($invoice->company)->name ?? '—' }}
                                </div>
                                <div class="small" style="color:var(--text-soft);">
                                    {{ optional($invoice->company)->contact_email }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size:0.8rem;">
                                    {{ optional(optional($invoice->subscription)->plan)->name ?? '—' }}
                                </span>
                            </td>
                            <td class="text-center">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
                            <td class="text-center">{{ number_format($invoice->tax_amount, 2) }}</td>
                            <td class="text-center fw-bold" style="color:var(--primary-color);">
                                {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                            </td>
                            <td class="text-center">
                                @if($invoice->status === 'paid')
                                    <span class="badge-soft badge-success"><i class="fas fa-check me-1"></i>مدفوعة</span>
                                @elseif($isOverdue)
                                    <span class="badge-soft badge-danger"><i class="fas fa-exclamation me-1"></i>متأخرة</span>
                                @else
                                    <span class="badge-soft badge-warning"><i class="fas fa-clock me-1"></i>معلّقة</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="small {{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                    {{ $invoice->due_at ? $invoice->due_at->format('Y-m-d') : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($invoice->status !== 'paid')
                                        <button class="btn-action btn-action-paid"
                                                data-bs-toggle="modal"
                                                data-bs-target="#markPaidModal{{ $invoice->id }}">
                                            <i class="fas fa-circle-check"></i> تسجيل دفع
                                        </button>
                                    @else
                                        <span class="small text-muted">
                                            {{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '—' }}
                                        </span>
                                    @endif
                                                                        <form action="{{ route('system.invoices.destroy', $invoice) }}" method="POST"
                                                                                    data-confirm="حذف هذه الفاتورة؟">
                                        @csrf @method('DELETE')
                                                                                <button type="submit" class="btn-action btn-action-danger js-confirm-action" data-confirm="حذف هذه الفاتورة؟">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                    <i class="fas fa-file-invoice-dollar fa-2x opacity-25"></i>
                                    <span class="small">لا توجد فواتير مطابقة</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div class="d-flex justify-content-center py-4 border-top">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

@endsection

@push('modals')
    {{-- ── Mark Paid Modals ── --}}
    @foreach($invoices as $invoice)
        @if($invoice->status !== 'paid')
        <div class="modal fade" id="markPaidModal{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:20px;border:none;">
                    <div class="modal-header border-0 px-4 pt-4 pb-0">
                        <h5 class="fw-bold" style="color:var(--text-main);">
                            <i class="fas fa-circle-check me-2" style="color:var(--primary-color);"></i>
                            تسجيل دفع الفاتورة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('system.invoices.mark_paid', $invoice) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-body px-4">
                            <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <div class="small fw-bold" style="color:var(--text-soft);">الفاتورة</div>
                                <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                <div class="fw-bold mt-1" style="color:var(--primary-color);font-size:1.1rem;">
                                    {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="field-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select">
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="cash">نقد</option>
                                    <option value="credit_card">بطاقة ائتمانية</option>
                                    <option value="cheque">شيك</option>
                                    <option value="manual">يدوي</option>
                                </select>
                            </div>
                            <div class="form-check p-0" style="background:#f0fdf9;border:1.5px solid #bbf7d0;border-radius:10px;padding:10px 16px 10px 36px !important;">
                                <input class="form-check-input" type="checkbox" name="auto_activate" value="1"
                                       id="autoActivate{{ $invoice->id }}" checked>
                                <label class="form-check-label" for="autoActivate{{ $invoice->id }}" style="font-size:0.85rem;font-weight:600;color:#166534;padding-right:8px;">
                                    <i class="fas fa-bolt me-1"></i> تفعيل الاشتراك تلقائياً بعد الدفع
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-circle-check me-2"></i> تأكيد الدفع
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    {{-- ── Create Invoice Modal ── --}}
    <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px;border:none;">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="fw-bold" style="color:var(--text-main);">
                        <i class="fas fa-file-invoice-dollar me-2" style="color:var(--primary-color);"></i>
                        إنشاء فاتورة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('system.invoices.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 pt-3">
                        <div class="row g-3">

                            <div class="col-12">
                                <div class="section-label">
                                    <i class="fas fa-building" style="color:var(--primary-color);"></i>
                                    المنظمة والخطة
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label">المنظمة <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">اختر المنظمة...</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label">الخطة <span class="text-danger">*</span></label>
                                <select name="plan_id" class="form-select" id="planSelectInvoice" required>
                                    <option value="">اختر الخطة...</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->annual_price }}">
                                            {{ $plan->name }} — {{ number_format($plan->annual_price, 0) }} ر.س
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="section-label">
                                    <i class="fas fa-tag" style="color:var(--primary-color);"></i>
                                    تفاصيل الفاتورة
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">المبلغ (قبل الضريبة) <span class="text-danger">*</span></label>
                                <input name="amount" type="number" step="0.01" min="0" class="form-control"
                                       id="invoiceAmount" placeholder="0.00" required
                                       oninput="calcTax(this.value)">
                                <span style="font-size:0.72rem;color:var(--text-soft);margin-top:3px;display:block;">ضريبة 15% تُحتسب تلقائياً</span>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">الإجمالي مع الضريبة</label>
                                <input id="invoiceTotal" class="form-control" readonly placeholder="—" style="background:#f0fdf9;font-weight:700;color:var(--primary-color);">
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                                <input name="due_at" type="date" class="form-control" required
                                       value="{{ now()->addDays(7)->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">حالة الفاتورة</label>
                                <select name="status" class="form-select" id="invoiceStatus"
                                        onchange="toggleAutoActivate(this.value)">
                                    <option value="pending">معلّقة</option>
                                    <option value="paid">مدفوعة</option>
                                    <option value="unpaid">غير مدفوعة</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">—</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="cash">نقد</option>
                                    <option value="credit_card">بطاقة ائتمانية</option>
                                    <option value="cheque">شيك</option>
                                    <option value="manual">يدوي</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="field-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2"
                                          placeholder="أي ملاحظات على هذه الفاتورة..."></textarea>
                            </div>

                            <div class="col-12" id="autoActivateSection" style="display:none;">
                                <div class="form-check p-0" style="background:#f0fdf9;border:1.5px solid #bbf7d0;border-radius:10px;padding:10px 16px 10px 36px !important;">
                                    <input class="form-check-input" type="checkbox" name="auto_activate" value="1"
                                           id="autoActivateNew" checked>
                                    <label class="form-check-label" for="autoActivateNew" style="font-size:0.85rem;font-weight:600;color:#166534;padding-right:8px;">
                                        <i class="fas fa-bolt me-1"></i> تفعيل اشتراك المنظمة تلقائياً عند إنشاء الفاتورة
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-file-invoice-dollar me-2"></i> إنشاء الفاتورة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endpush

@push('scripts')
<script>
    // ── Auto-fill amount from plan price ─────────────────────────────────
    document.getElementById('planSelectInvoice')?.addEventListener('change', function () {
        const price = this.options[this.selectedIndex].dataset.price;
        if (price) {
            document.getElementById('invoiceAmount').value = parseFloat(price).toFixed(2);
            calcTax(price);
        }
    });

    // ── Tax calculation ───────────────────────────────────────────────────
    function calcTax(amount) {
        const amt = parseFloat(amount) || 0;
        const tax = amt * 0.15;
        const total = amt + tax;
        document.getElementById('invoiceTotal').value = total.toFixed(2) + ' ر.س';
    }

    // ── Show auto-activate checkbox when status = paid ────────────────────
    function toggleAutoActivate(status) {
        const section = document.getElementById('autoActivateSection');
        if (section) section.style.display = status === 'paid' ? 'block' : 'none';
    }
    // Initialize
    toggleAutoActivate(document.getElementById('invoiceStatus')?.value);
</script>
@endpush
