@extends('layouts.app')

@section('title', $mode === 'create' ? 'إضافة نموذج تسجيل' : 'تعديل نموذج التسجيل')

@section('content')
    @php
        $existingFields = old('fields_json')
            ? json_decode(old('fields_json'), true)
            : ($formModel->fields ?? [
                [
                    'label'       => 'Company',
                    'type'        => 'text',
                    'required'    => false,
                    'placeholder' => 'Participant company or entity',
                    'help_text'   => '',
                    'width'       => 'half',
                    'options'     => [],
                ],
            ]);
    @endphp

    <style>
        .builder-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, .95fr);
            gap: 1.5rem;
        }

        .builder-card {
            background: #fff;
            border-radius: 28px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            padding: 1.5rem;
        }

        .field-block {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 22px;
            padding: 1rem;
            background: linear-gradient(180deg, #fff, #f8fafc);
            transition: border-color .2s;
        }

        .field-block.has-error {
            border-color: #dc3545;
        }

        .preview-field {
            border: 1px dashed rgba(20, 50, 74, 0.18);
            border-radius: 18px;
            padding: .95rem;
            background: #f8fafc;
        }

        @media (max-width: 992px) {
            .builder-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $mode === 'create' ? 'إنشاء نموذج تسجيل جديد' : 'تحديث نموذج التسجيل' }}</h1>
            <p class="text-muted mb-0">صمّم نموذجًا موحدًا يمكن ربطه بعدة فعاليات عامة بنفس جودة واجهة الفعاليات.</p>
        </div>
        <a href="{{ route('registration-forms.index') }}" class="btn btn-outline-secondary rounded-pill px-4">العودة للنماذج</a>
    </div>

    <form
        action="{{ $mode === 'create' ? route('registration-forms.store') : route('registration-forms.update', $formModel) }}"
        method="POST"
        id="registrationFormBuilder"
        novalidate
    >
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <input type="hidden" name="fields_json" id="fieldsJsonInput" value="{{ old('fields_json', json_encode($existingFields)) }}">

        <div class="builder-shell"
             id="registrationFormBuilderData"
             data-field-types='@json(array_values($fieldTypes))'
             data-field-widths='@json(array_values($fieldWidths))'
             data-initial-fields='@json($existingFields)'>

            {{-- ─── Left column: form settings + field builder ─── --}}
            <div class="d-flex flex-column gap-4">

                {{-- Form meta --}}
                <div class="builder-card">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">اسم النموذج <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $formModel->name) }}"
                                placeholder="مثال: نموذج تسجيل المؤتمر السنوي"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">سيُنشئ النظام معرّفًا فريدًا للنموذج تلقائيًا من الاسم.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">العنوان التمهيدي</label>
                            <input
                                type="text"
                                name="headline"
                                class="form-control @error('headline') is-invalid @enderror"
                                value="{{ old('headline', $formModel->headline) }}"
                                placeholder="مثال: بيانات تسجيل الحضور والمتحدثين"
                            >
                            @error('headline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">النص التعريفي</label>
                            <textarea
                                name="intro_text"
                                rows="3"
                                class="form-control @error('intro_text') is-invalid @enderror"
                                placeholder="اشرح للمستخدم سبب جمع البيانات وما المتوقع أثناء التسجيل."
                            >{{ old('intro_text', $formModel->intro_text) }}</textarea>
                            @error('intro_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="isActive"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $formModel->is_active ?? true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label fw-semibold" for="isActive">
                                    تفعيل النموذج وإتاحته للربط مع الفعاليات
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Field builder --}}
                <div class="builder-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">الحقول المخصصة</h2>
                            <p class="text-muted mb-0 small">حقول الاسم والبريد الأساسية ثابتة، وهذه الحقول مخصصة لمتطلبات كل فعالية.</p>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-4" id="addFieldButton">
                            إضافة حقل
                        </button>
                    </div>

                    {{-- Validation error summary for fields --}}
                    <div id="fieldErrorBanner" class="alert alert-danger d-none mb-3" role="alert"></div>

                    <div id="fieldBuilderList" class="d-flex flex-column gap-3"></div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('registration-forms.index') }}" class="btn btn-light border rounded-pill px-4">إلغاء</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="submitBtn">
                        {{ $mode === 'create' ? 'حفظ النموذج' : 'حفظ التعديلات' }}
                    </button>
                </div>
            </div>

            {{-- ─── Right column: live preview ─── --}}
            <div class="d-flex flex-column gap-4">
                <div class="builder-card position-sticky" style="top: 24px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">معاينة مباشرة</h2>
                            <p class="text-muted small mb-0">يتم تحديث المعاينة تلقائيًا أثناء تعديل الحقول.</p>
                        </div>
                    </div>

                    {{-- Fixed system fields --}}
                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" disabled placeholder="حقل أساسي">
                    </div>
                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" disabled placeholder="حقل أساسي">
                    </div>

                    <div id="formPreviewList" class="d-flex flex-column gap-3"></div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            (() => {
                'use strict';

                // ─── DOM refs ───────────────────────────────────────────────
                const builderData    = document.getElementById('registrationFormBuilderData');
                const builderList    = document.getElementById('fieldBuilderList');
                const previewList    = document.getElementById('formPreviewList');
                const fieldsJson     = document.getElementById('fieldsJsonInput');
                const errorBanner    = document.getElementById('fieldErrorBanner');
                const submitBtn      = document.getElementById('submitBtn');
                const form           = document.getElementById('registrationFormBuilder');

                const fieldTypes     = JSON.parse(builderData.dataset.fieldTypes  || '[]');
                const fieldWidths    = JSON.parse(builderData.dataset.fieldWidths  || '[]');
                const initialFields  = JSON.parse(builderData.dataset.initialFields || '[]');

                // Human-readable labels for field types and widths
                const typeLabels = {
                    text: 'نص قصير', email: 'بريد إلكتروني', tel: 'هاتف',
                    textarea: 'نص طويل', select: 'قائمة منسدلة', radio: 'اختيار واحد',
                    checkbox: 'خانة تحقق', number: 'رقم', date: 'تاريخ',
                };
                const widthLabels = { full: 'عرض كامل', half: 'نصف', third: 'ثلث' };

                // ─── Helpers ────────────────────────────────────────────────
                function esc(v) {
                    return String(v ?? '')
                        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function widthClass(w) {
                    return w === 'half' ? 'col-md-6' : w === 'third' ? 'col-md-4' : 'col-12';
                }

                function emptyField() {
                    return { label: '', type: 'text', required: false, placeholder: '', help_text: '', width: 'full', options: [] };
                }

                // ─── Read current state from DOM ────────────────────────────
                function readFields() {
                    return Array.from(builderList.querySelectorAll('[data-field-index]')).map((block) => {
                        const options = (block.querySelector('[data-role="options"]').value || '')
                            .split('\n').map(v => v.trim()).filter(Boolean);
                        return {
                            label:       block.querySelector('[data-role="label"]').value,
                            type:        block.querySelector('[data-role="type"]').value,
                            required:    block.querySelector('[data-role="required"]').checked,
                            placeholder: block.querySelector('[data-role="placeholder"]').value,
                            help_text:   block.querySelector('[data-role="help_text"]').value,
                            width:       block.querySelector('[data-role="width"]').value,
                            options,
                        };
                    });
                }

                // ─── Validation ─────────────────────────────────────────────
                function validateFields(fields) {
                    const errors = [];

                    fields.forEach((field, i) => {
                        const num = i + 1;
                        if (!field.label.trim()) {
                            errors.push(`الحقل ${num}: يجب إدخال اسم ظاهر للحقل.`);
                        }
                        if (['select', 'radio'].includes(field.type) && field.options.length < 2) {
                            errors.push(`الحقل ${num} (${typeLabels[field.type] || field.type}): يجب إضافة خيارَين على الأقل.`);
                        }
                    });

                    // Detect duplicate labels within the same form
                    const labels = fields.map(f => f.label.trim().toLowerCase()).filter(Boolean);
                    labels.forEach((lbl, i) => {
                        if (lbl && labels.indexOf(lbl) !== i) {
                            errors.push(`الحقل ${i + 1}: الاسم الظاهر "${fields[i].label.trim()}" مكرر — يجب أن يكون فريدًا.`);
                        }
                    });

                    return errors;
                }

                function showErrors(errors) {
                    if (!errors.length) {
                        errorBanner.classList.add('d-none');
                        errorBanner.innerHTML = '';
                        return;
                    }
                    errorBanner.innerHTML = '<strong>يرجى تصحيح الأخطاء التالية:</strong><ul class="mb-0 mt-1">'
                        + errors.map(e => `<li>${esc(e)}</li>`).join('')
                        + '</ul>';
                    errorBanner.classList.remove('d-none');
                    errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                function markBlockErrors(fields) {
                    builderList.querySelectorAll('[data-field-index]').forEach((block, i) => {
                        const field = fields[i];
                        const hasLabel = field && field.label.trim();
                        const hasOptions = !['select', 'radio'].includes(field?.type) || (field.options.length >= 2);
                        block.classList.toggle('has-error', !hasLabel || !hasOptions);

                        // Inline feedback on label input
                        const labelInput = block.querySelector('[data-role="label"]');
                        labelInput.classList.toggle('is-invalid', !hasLabel);
                    });
                }

                // ─── Sync JSON hidden input ──────────────────────────────────
                function syncFields() {
                    const fields = readFields();
                    fieldsJson.value = JSON.stringify(fields);
                    renderPreview(fields);
                }

                // ─── Preview renderer ────────────────────────────────────────
                function renderPreview(fields) {
                    previewList.innerHTML = '';

                    if (!fields.length) {
                        previewList.innerHTML = '<div class="text-muted small">ستظهر الحقول المخصصة هنا.</div>';
                        return;
                    }

                    fields.forEach((field) => {
                        const optHtml = (field.options || []).map(o => `<option>${esc(o)}</option>`).join('');
                        let control;

                        switch (field.type) {
                            case 'textarea':
                                control = `<textarea class="form-control" rows="3" disabled placeholder="${esc(field.placeholder || field.label)}"></textarea>`;
                                break;
                            case 'select':
                                control = `<select class="form-select" disabled><option>${esc(field.placeholder || 'اختر قيمة')}</option>${optHtml}</select>`;
                                break;
                            case 'radio':
                                control = (field.options || []).map(o => `
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">${esc(o)}</label>
                                    </div>`).join('') || '<span class="text-muted small">أضف خيارات لترى المعاينة</span>';
                                break;
                            case 'checkbox':
                                control = `<div class="form-check">
                                    <input class="form-check-input" type="checkbox" disabled>
                                    <label class="form-check-label">${esc(field.label)}</label>
                                </div>`;
                                break;
                            default:
                                control = `<input type="${esc(field.type)}" class="form-control" disabled placeholder="${esc(field.placeholder || field.label)}">`;
                        }

                        previewList.insertAdjacentHTML('beforeend', `
                            <div class="preview-field ${widthClass(field.width)}">
                                <label class="form-label fw-semibold mb-1">
                                    ${esc(field.label) || '<span class="text-muted fst-italic">بدون اسم</span>'}
                                    ${field.required ? '<span class="text-danger">*</span>' : ''}
                                </label>
                                ${control}
                                ${field.help_text ? `<div class="text-muted small mt-1">${esc(field.help_text)}</div>` : ''}
                            </div>
                        `);
                    });
                }

                // ─── Builder renderer ────────────────────────────────────────
                function renderBuilder(fields) {
                    builderList.innerHTML = '';

                    fields.forEach((field, index) => {
                        const typeOpts  = fieldTypes.map(t =>
                            `<option value="${t}" ${field.type === t ? 'selected' : ''}>${typeLabels[t] || t}</option>`).join('');
                        const widthOpts = fieldWidths.map(w =>
                            `<option value="${w}" ${field.width === w ? 'selected' : ''}>${widthLabels[w] || w}</option>`).join('');
                        const showOpts  = ['select', 'radio'].includes(field.type) ? '' : 'd-none';

                        builderList.insertAdjacentHTML('beforeend', `
                            <div class="field-block" data-field-index="${index}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="h6 fw-bold mb-0">الحقل ${index + 1}</h3>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-action="remove">حذف</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">الاسم الظاهر <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control ${!field.label.trim() ? 'is-invalid' : ''}"
                                            data-role="label"
                                            value="${esc(field.label)}"
                                            placeholder="مثال: اسم الشركة"
                                        >
                                        <div class="invalid-feedback">هذا الحقل مطلوب.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">النوع</label>
                                        <select class="form-select" data-role="type">${typeOpts}</select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">العرض</label>
                                        <select class="form-select" data-role="width">${widthOpts}</select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" data-role="required" ${field.required ? 'checked' : ''}>
                                            <label class="form-check-label">إجباري</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">النص الإرشادي</label>
                                        <input type="text" class="form-control" data-role="placeholder" value="${esc(field.placeholder)}" placeholder="تلميح للمستخدم">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">نص المساعدة</label>
                                        <input type="text" class="form-control" data-role="help_text" value="${esc(field.help_text)}" placeholder="ملاحظة إضافية أسفل الحقل">
                                    </div>
                                    <div class="col-12 ${showOpts}" data-options-wrap>
                                        <label class="form-label">الخيارات <span class="text-danger">*</span> <small class="text-muted">(كل خيار في سطر، 2 على الأقل)</small></label>
                                        <textarea class="form-control" rows="3" data-role="options" placeholder="الخيار الأول&#10;الخيار الثاني&#10;الخيار الثالث">${esc((field.options || []).join('\n'))}</textarea>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    // Attach events to all inputs inside newly rendered blocks
                    builderList.querySelectorAll('input, select, textarea').forEach((el) => {
                        el.addEventListener('input', syncFields);
                        el.addEventListener('change', (e) => {
                            if (e.target.dataset.role === 'type') {
                                const wrap = e.target.closest('[data-field-index]').querySelector('[data-options-wrap]');
                                wrap.classList.toggle('d-none', !['select', 'radio'].includes(e.target.value));
                            }
                            syncFields();
                        });
                    });

                    // Remove button
                    builderList.querySelectorAll('[data-action="remove"]').forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const current = readFields();
                            current.splice(Number(btn.closest('[data-field-index]').dataset.fieldIndex), 1);
                            renderBuilder(current);
                            syncFields();
                        });
                    });
                }

                // ─── Form submission guard ───────────────────────────────────
                form.addEventListener('submit', (e) => {
                    const fields = readFields();
                    const errors = validateFields(fields);
                    markBlockErrors(fields);

                    if (errors.length) {
                        e.preventDefault();
                        showErrors(errors);
                        return;
                    }

                    showErrors([]);
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'جارٍ الحفظ…';
                });

                // ─── Init ────────────────────────────────────────────────────
                renderBuilder(initialFields.length ? initialFields : [emptyField()]);
                syncFields();
            })();
        </script>
    @endpush
@endsection
