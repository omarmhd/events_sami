@extends('layouts.app')

@section('title', $mode === 'create' ? 'إضافة فعالية' : 'تعديل الفعالية')

@section('content')
    @php
        $baseRegistrationUrl = url('/public/events');
        $initialEventType = old('event_type', $event->event_type ?: 'private');
        $publicRegistrationFieldsHidden = $initialEventType !== 'public';
        $scheduleItems = old('schedule_items_json')
            ? json_decode(old('schedule_items_json'), true)
            : ($event->schedule_items ?? [
                [
                    'title' => 'الاستقبال والتسجيل',
                    'stage' => 'نقطة الدخول',
                    'start_time' => old('from_time', $event->from_time),
                    'end_time' => '',
                    'description' => 'استقبال الضيوف وتأكيد البيانات وتسليم تفاصيل الحضور.',
                ],
            ]);
    @endphp

    <style>
        .event-builder-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, .95fr);
            gap: 1.5rem;
        }

        .event-builder-card {
            background: #fff;
            border-radius: 28px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            padding: 1.5rem;
        }

        .experience-tile {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 22px;
            padding: 1rem;
            background: linear-gradient(180deg, #fff, #f8fafc);
        }

        .schedule-item {
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 22px;
            padding: 1rem;
            background: #f8fafc;
        }

        .event-preview {
            position: sticky;
            top: 24px;
        }

        .event-preview-card {
            background: #fff;
            border-radius: 28px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            padding: 1.5rem;
        }

        .preview-field {
            border: 1px dashed rgba(20, 50, 74, 0.18);
            border-radius: 18px;
            padding: .95rem;
            background: #f8fafc;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 88px 1fr;
            gap: .85rem;
            padding: .85rem 0;
            border-top: 1px solid var(--line);
        }

        .flow-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .42rem .82rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            color: var(--text-main);
        }

        .field-hint {
            font-size: .78rem;
            color: var(--text-soft);
            margin-top: .35rem;
        }

        .registration-link-preview {
            margin-top: .55rem;
            font-size: .78rem;
            background: var(--surface-soft);
            border: 1px dashed var(--line);
            border-radius: 10px;
            padding: .5rem .65rem;
            direction: ltr;
            text-align: left;
            color: var(--text-muted);
            word-break: break-all;
        }

        .flow-panel {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: var(--surface-soft);
            padding: .85rem;
        }

        @media (max-width: 992px) {
            .event-builder-shell {
                grid-template-columns: 1fr;
            }

            .event-preview {
                position: static;
            }
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">{{ $mode === 'create' ? 'إنشاء فعالية جديدة' : 'تحديث تفاصيل الفعالية' }}</h1>
            <p class="text-muted mb-0">أدخل تفاصيل الفعالية كاملة: النطاق الفرعي، النوع (عام/خاص)، بيانات المكان والوقت، رسائل البريد، وصور الهيدر.</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary rounded-pill px-4">العودة للفعاليات</a>
    </div>

    {{-- Persistent validation error summary — shown when any field fails validation --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius:16px; border-right:4px solid #dc3545;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-triangle-exclamation text-danger fs-5"></i>
                <strong>يوجد {{ $errors->count() }} خطأ في النموذج — يرجى المراجعة قبل الحفظ:</strong>
            </div>
            <ul class="mb-0 ps-3" style="font-size:.92rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $mode === 'create' ? route('events.store') : route('events.update', $event) }}" method="POST" id="eventBuilderForm" enctype="multipart/form-data">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <input type="hidden" name="schedule_items_json" id="scheduleItemsJson" value="{{ old('schedule_items_json', json_encode($scheduleItems)) }}">

        <div class="event-builder-shell" id="eventBuilderData" data-initial-schedule='@json($scheduleItems)'>
            <div class="d-flex flex-column gap-4">
                <div class="event-builder-card">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">عنوان الفعالية</label>
                            <input type="text" name="title" id="eventTitleInput" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $event->title ?: $event->name) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">
                                معرّف الفعالية في الرابط
                                <span class="text-danger">*</span>
                                @if($mode === 'edit')
                                    <span class="badge bg-warning text-dark fw-normal ms-1" style="font-size:.68rem;" title="تغيير المعرف سيُبطل الروابط القديمة">
                                        <i class="fas fa-exclamation-triangle me-1"></i>تغييره يُبطل الروابط
                                    </span>
                                @endif
                            </label>
                            <div class="input-group">
                                <input type="text"
                                       name="event_slug"
                                       id="eventSlugInput"
                                       class="form-control @error('event_slug') is-invalid @enderror"
                                       value="{{ old('event_slug', $event->event_slug) }}"
                                       pattern="[a-zA-Z0-9\-]+"
                                       title="أحرف إنجليزية وأرقام وشرطة فقط"
                                       required
                                       {{ $mode === 'edit' ? '' : '' }}>
                                @if($mode === 'create')
                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            id="generateSlugBtn"
                                            title="توليد تلقائي من العنوان">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                @endif
                                @error('event_slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field-hint mt-1">
                                <i class="fas fa-link me-1"></i>يُستخدم في رابط صفحة الفعالية والدعوات.
                                أحرف إنجليزية وأرقام وشرطة (<code>-</code>) فقط. مثال: <code>summit-2026</code>
                            </div>
                            <div class="registration-link-preview mt-1" id="registrationLinkPreview">{{ $baseRegistrationUrl }}/{{ old('event_slug', $event->event_slug ?: 'event-slug') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">نوع الفعالية</label>
                            <select name="event_type" id="eventTypeInput" class="form-select @error('event_type') is-invalid @enderror" required>
                                <option value="private" {{ old('event_type', $event->event_type ?: 'private') === 'private' ? 'selected' : '' }}>دعوات خاصة</option>
                                <option value="public" {{ old('event_type', $event->event_type) === 'public' ? 'selected' : '' }}>رابط عام</option>
                            </select>
                            @error('event_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">تصنيف الفعالية</label>
                            <select name="experience_type" id="experienceTypeInput" class="form-select @error('experience_type') is-invalid @enderror" required>
                                @foreach(['conference' => 'مؤتمر', 'summit' => 'قمة تنفيذية', 'workshop' => 'ورشة عمل', 'training' => 'تدريب', 'networking' => 'لقاء شبكي', 'exhibition' => 'معرض'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('experience_type', $event->experience_type ?: 'conference') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('experience_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">سعة الحضور</label>
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $event->capacity) }}" min="1" placeholder="اختياري">
                            @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="event-builder-card">
                    <h2 class="h5 fw-bold mb-3">الوقت والموقع وجدول الفعالية</h2>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">تاريخ الفعالية</label>
                            <input type="date" name="date" id="eventDateInput" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional($event->date)->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">وقت البداية</label>
                            <input type="time" name="from_time" id="eventStartInput" class="form-control @error('from_time') is-invalid @enderror" value="{{ old('from_time', $event->from_time) }}" required>
                            @error('from_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">وقت النهاية</label>
                            <input type="time" name="to_time" id="eventEndInput" class="form-control @error('to_time') is-invalid @enderror" value="{{ old('to_time', $event->to_time) }}" required>
                            @error('to_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">الموقع (نص)</label>
                            <input type="text" name="location_name" id="eventLocationInput" class="form-control @error('location_name') is-invalid @enderror" value="{{ old('location_name', $event->location_name) }}" required>
                            @error('location_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">رابط خرائط Google</label>
                            <input type="url" name="google_map_url" class="form-control @error('google_map_url') is-invalid @enderror" value="{{ old('google_map_url', $event->google_map_url) }}">
                            @error('google_map_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="h6 fw-bold mb-1">جدول سير الفعالية</h3>
                            <p class="text-muted small mb-0">أضف المراحل الرئيسية ليظهر التسلسل واضحًا للمنظمين والضيوف.</p>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-4" id="addScheduleItemButton">إضافة بند</button>
                    </div>

                    <div id="scheduleBuilder" class="d-flex flex-column gap-3"></div>
                </div>

                <div class="event-builder-card">
                    <h2 class="h5 fw-bold mb-3">تدفق التسجيل</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="flow-panel">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="flow-chip" id="flowChip"><i class="fas fa-diagram-project"></i>تدفق التسجيل</span>
                                    <small class="text-muted" id="flowDescription">يتم تحديث التدفق تلقائيًا حسب نوع الفعالية.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">حالة النشر</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $event->status ?: 'draft') === 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="published" {{ old('status', $event->status) === 'published' ? 'selected' : '' }}>منشورة</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div id="publicRegistrationFields" class="row g-3 mt-1 {{ $publicRegistrationFieldsHidden ? 'd-none' : '' }}" {{ $publicRegistrationFieldsHidden ? 'hidden' : '' }} aria-hidden="{{ $publicRegistrationFieldsHidden ? 'true' : 'false' }}">
                        <div class="col-md-7" id="registrationFormWrap">
                            <label class="form-label fw-semibold">
                                النموذج المستخدم للتسجيل العام
                                @if(!($registrationFormsEnabled ?? true))
                                    <a href="{{ route('billing.upgrade') }}"
                                       class="badge text-decoration-none ms-1"
                                       style="background:#fef3c7;color:#92400e;font-size:.72rem;font-weight:600;padding:3px 8px;border-radius:20px;">
                                        <i class="fas fa-arrow-up me-1"></i>يحتاج ترقية
                                    </a>
                                @endif
                            </label>

                            @if($registrationFormsEnabled ?? true)
                                {{-- Feature enabled: normal select --}}
                                <select name="registration_form_id" id="registrationFormInput"
                                        class="form-select @error('registration_form_id') is-invalid @enderror">
                                    <option value="">اختر نموذجًا</option>
                                    @foreach($registrationForms as $formModel)
                                        <option value="{{ $formModel->id }}"
                                            {{ (string) old('registration_form_id', $event->registration_form_id) === (string) $formModel->id ? 'selected' : '' }}>
                                            {{ $formModel->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('registration_form_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">هذا الحقل مطلوب عندما يكون نوع الفعالية "رابط عام".</div>
                            @else
                                {{-- Feature disabled: locked UI --}}
                                <div style="border:1.5px dashed #d1d5db;border-radius:10px;padding:12px 14px;background:#f9fafb;display:flex;align-items:center;gap:10px;">
                                    <i class="fas fa-lock" style="color:#9ca3af;font-size:1rem;"></i>
                                    <div>
                                        <div style="font-size:.85rem;font-weight:600;color:#6b7280;">نموذج افتراضي (اسم + بريد إلكتروني)</div>
                                        <div style="font-size:.78rem;color:#9ca3af;margin-top:2px;">سيتم استخدامه تلقائياً — <a href="{{ route('billing.upgrade') }}" style="color:var(--primary-color);text-decoration:none;">قم بالترقية</a> لاختيار نموذج مخصص</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-5" id="manualApprovalWrap">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="requiresManualApprovalInput" name="requires_manual_approval" value="1" {{ old('requires_manual_approval', $event->requires_manual_approval) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">مراجعة التسجيل يدويًا</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="event-builder-card">
                    <h2 class="h5 fw-bold mb-3">المحتوى ورسائل البريد</h2>
                    <div class="alert alert-info mb-3">
                        <strong>ملاحظة:</strong> وصف الفعالية هو المصدر الأساسي لرسائل البريد — يُرسل تلقائياً في بريدَي الدعوة والتأكيد. يمكنك تخصيص نص البريد أدناه إذا أردت رسالة مختلفة.
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">وصف الفعالية (عربي) <span class="text-danger">*</span></label>
                            <textarea name="description" id="eventDescriptionInput" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">وصف الفعالية (إنجليزي) <span class="badge bg-secondary fw-normal ms-1" style="font-size:.7rem;">اختياري</span></label>
                            <textarea name="description_en" rows="4" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $event->description_en) }}</textarea>
                            @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Email body fields (optional – subject handled automatically) ── --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                نص بريد الدعوة
                                <span class="badge bg-secondary fw-normal ms-1" style="font-size:.7rem;">اختياري</span>
                            </label>
                            <textarea name="invitation_email_body" rows="3" class="form-control @error('invitation_email_body') is-invalid @enderror" placeholder="اتركه فارغاً لاستخدام وصف الفعالية تلقائياً.">{{ old('invitation_email_body', $event->invitation_email_body) }}</textarea>
                            @error('invitation_email_body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                نص بريد التأكيد
                                <span class="badge bg-secondary fw-normal ms-1" style="font-size:.7rem;">اختياري</span>
                            </label>
                            <textarea name="confirmation_email_body" rows="3" class="form-control @error('confirmation_email_body') is-invalid @enderror" placeholder="اتركه فارغاً لاستخدام وصف الفعالية تلقائياً.">{{ old('confirmation_email_body', $event->confirmation_email_body) }}</textarea>
                            @error('confirmation_email_body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- ── Header Image Upload (full width since footer is removed) ─── --}}
                        @php
                            $headerEnabled = $headerImageEnabled ?? true;
                            $hCfg          = $headerImageCfg    ?? config('features.event_header_image', []);
                        @endphp

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                صورة رأس الفعالية
                                <span class="badge bg-secondary fw-normal ms-1" style="font-size:.7rem;">اختياري</span>
                                @if(!$headerEnabled)
                                    <span class="badge bg-warning text-dark ms-1" style="font-size:.7rem;">
                                        <i class="fas fa-lock me-1"></i>غير متاح في خطتك
                                    </span>
                                @endif
                            </label>

                            @if($headerEnabled)
                                <input type="file"
                                       id="headerImageInput"
                                       name="header_image"
                                       class="form-control @error('header_image') is-invalid @enderror"
                                       accept=".{{ str_replace(',', ',.', $hCfg['mimes'] ?? 'jpg,jpeg,png,webp') }},image/jpeg,image/png,image/webp">
                                @error('header_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    تُعرض في أعلى بريد الدعوة والتأكيد ·
                                    الصيغ: {{ strtoupper(str_replace(',', ' / ', $hCfg['mimes'] ?? 'JPG,PNG,WEBP')) }} ·
                                    الحد الأقصى {{ round(($hCfg['max_kb'] ?? 2048) / 1024, 1) }} MB ·
                                    المقاس المقترح {{ $hCfg['ideal_w'] ?? 1200 }}×{{ $hCfg['ideal_h'] ?? 400 }} بكسل.
                                </div>
                                {{-- Live preview shown immediately after file selection --}}
                                {{-- Hidden flag — always present so controller can read it on save --}}
                                <input type="hidden" name="clear_header_image" id="clearHeaderImageFlag" value="0">

                                <div id="headerImagePreview" class="mt-2 {{ empty($event->header_image_path) ? 'd-none' : '' }}" style="position:relative;max-width:100%;"> 
                                    <img id="headerImagePreviewImg"
                                         src="{{ $event->header_image_path ?? '' }}"
                                         alt="معاينة صورة الترويسة"
                                         style="max-width:100%;height:auto;border-radius:10px;border:1px solid rgba(15,23,42,.12);display:block;">

                                    {{-- X button overlaid on top-right corner of the image --}}
                                    <button type="button"
                                            id="clearHeaderImageBtn"
                                            onclick="removeHeaderImage()"
                                            title="حذف الصورة"
                                            style="position:absolute;top:6px;right:6px;
                                                   width:26px;height:26px;border-radius:50%;
                                                   background:rgba(220,38,38,.85);color:#fff;
                                                   border:none;cursor:pointer;font-size:13px;
                                                   display:flex;align-items:center;justify-content:center;
                                                   line-height:1;padding:0;">
                                        &times;
                                    </button>
                                </div>
                            @else
                                {{-- Feature locked: show upgrade prompt --}}
                                <div style="background:rgba(245,158,11,.07);border:1.5px dashed rgba(245,158,11,.4);border-radius:12px;padding:18px 16px;text-align:center;">
                                    <i class="fas fa-image" style="font-size:1.6rem;color:#d97706;margin-bottom:8px;display:block;"></i>
                                    <p style="font-size:.83rem;color:#78350f;margin:0 0 10px;">
                                        تخصيص صورة رأس الفعالية متاح في خطط أعلى.
                                        عند التعطيل تُستخدم ألوان المنصة بدلاً من الصورة.
                                    </p>
                                    <a href="{{ route('feature.unavailable', ['feature' => 'event_header_image']) }}"
                                       class="btn btn-warning btn-sm rounded-pill" style="font-size:.8rem;">
                                        <i class="fas fa-arrow-up me-1"></i>ترقية الخطة
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <a href="{{ route('events.index') }}" class="btn btn-light border rounded-pill px-4">إلغاء</a>
                    <button class="btn btn-primary rounded-pill px-4" type="submit">{{ $mode === 'create' ? 'إنشاء الفعالية' : 'حفظ التعديلات' }}</button>
                </div>
            </div>

            <div class="event-preview">
                <div class="event-preview-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">معاينة مباشرة</h2>
                            <p class="text-muted small mb-0">نفس مبدأ بناء النماذج: تحديث فوري أثناء إدخال البيانات.</p>
                        </div>
                    </div>

                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold mb-1">عنوان الفعالية</label>
                        <div class="fw-bold" id="previewTitle">{{ old('title', $event->title ?: $event->name ?: 'عنوان الفعالية') }}</div>
                        <div class="text-muted small mt-1" id="previewMeta">{{ old('experience_type', $event->experience_type ?: 'conference') }}</div>
                    </div>

                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold mb-1">التاريخ والوقت</label>
                        <div class="mb-1"><i class="fas fa-calendar-day me-2"></i><span id="previewDate">{{ old('date', optional($event->date)->format('Y-m-d') ?: 'اختر التاريخ') }}</span></div>
                        <div><i class="fas fa-clock me-2"></i><span id="previewTime">{{ old('from_time', $event->from_time ?: '--:--') }} - {{ old('to_time', $event->to_time ?: '--:--') }}</span></div>
                    </div>

                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold mb-1">الموقع</label>
                        <div><i class="fas fa-location-dot me-2"></i><span id="previewLocation">{{ old('location_name', $event->location_name ?: 'اختر الموقع') }}</span></div>
                    </div>

                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold mb-1">تدفق التسجيل</label>
                        <div id="previewRegistrationText">{{ old('event_type', $event->event_type ?: 'private') === 'public' ? 'رابط عام (قبول تلقائي غالبًا)' : 'دعوات خاصة (مراجعة يدوية غالبًا)' }}</div>
                    </div>

                    <div class="preview-field mb-3">
                        <label class="form-label fw-semibold mb-1">رابط صفحة التسجيل</label>
                        <div id="previewRegistrationLink" style="direction:ltr;text-align:left;font-size:.86rem;word-break:break-all;">{{ $baseRegistrationUrl }}/{{ old('event_slug', $event->event_slug ?: 'event-slug') }}</div>
                    </div>

                    <div class="preview-field">
                        <label class="form-label fw-semibold mb-1">ملخص جدول الفعالية</label>
                        <div id="timelinePreview"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // ── Image preview helpers ─────────────────────────────────────────────────
            /**
             * Wire up a file input to show a live image preview in the given container.
             * @param {string} inputId     - ID of the <input type="file">
             * @param {string} previewId   - ID of the wrapper div to show/hide
             * @param {string} imgId       - ID of the <img> element inside the wrapper
             */
            function initImagePreview(inputId, previewId, imgId) {
                const input   = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const img     = document.getElementById(imgId);

                if (!input || !preview || !img) return;

                input.addEventListener('change', function () {
                    const file = this.files && this.files[0];

                    if (!file) {
                        // User cleared the selection — keep showing the existing image if any
                        return;
                    }

                    if (!file.type.startsWith('image/')) {
                        preview.style.display = 'none';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        img.src = e.target.result;
                        // Use inline-block so the X button stays anchored to the image corner
                        preview.style.display = 'inline-block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Initialize preview for header image only (footer image removed)
            initImagePreview('headerImageInput', 'headerImagePreview', 'headerImagePreviewImg');

            // ── Slug auto-generate from event title ───────────────────────────────
            /**
             * Convert an Arabic or mixed title into a Latin-only slug.
             * Arabic characters are transliterated to basic Latin equivalents,
             * spaces become hyphens, and non-alphanumeric chars are dropped.
             *
             * @param {string} text
             * @returns {string}
             */
            function titleToSlug(text) {
                const arMap = {
                    'أ':'a','ا':'a','إ':'i','آ':'a','ب':'b','ت':'t','ث':'th','ج':'j',
                    'ح':'h','خ':'kh','د':'d','ذ':'th','ر':'r','ز':'z','س':'s','ش':'sh',
                    'ص':'s','ض':'d','ط':'t','ظ':'z','ع':'a','غ':'gh','ف':'f','ق':'q',
                    'ك':'k','ل':'l','م':'m','ن':'n','ه':'h','و':'w','ي':'y','ى':'a',
                    'ة':'h','ء':'a','ئ':'y','ؤ':'w','لا':'la',
                };

                let slug = text.trim();

                // Replace Arabic chars
                slug = slug.replace(/[\u0600-\u06FF]/g, ch => arMap[ch] || '');

                // Lowercase, replace spaces/underscores with hyphens
                slug = slug.toLowerCase().replace(/[\s_]+/g, '-');

                // Keep only alphanumeric and hyphens, collapse multiple hyphens
                slug = slug.replace(/[^a-z0-9-]/g, '').replace(/-{2,}/g, '-');

                // Trim leading/trailing hyphens
                slug = slug.replace(/^-+|-+$/g, '');

                return slug || 'event';
            }

            const generateSlugBtn = document.getElementById('generateSlugBtn');
            if (generateSlugBtn) {
                generateSlugBtn.addEventListener('click', function () {
                    const title = document.getElementById('eventTitleInput').value;
                    if (!title.trim()) {
                        document.getElementById('eventTitleInput').focus();
                        return;
                    }
                    const slug = titleToSlug(title);
                    document.getElementById('eventSlugInput').value = slug;
                    updateRegistrationLinkPreview();
                });
            }

            // Also auto-fill slug on first title input if slug is still empty (create mode only)
            const slugInput = document.getElementById('eventSlugInput');
            if (slugInput && !slugInput.value) {
                document.getElementById('eventTitleInput').addEventListener('blur', function () {
                    if (!slugInput.value && this.value.trim()) {
                        slugInput.value = titleToSlug(this.value);
                        updateRegistrationLinkPreview();
                    }
                });
            }

            // ── Event builder logic ───────────────────────────────────────────────────
            const eventBuilderData = document.getElementById('eventBuilderData');
            const scheduleBuilder = document.getElementById('scheduleBuilder');
            const scheduleItemsJson = document.getElementById('scheduleItemsJson');
            const initialSchedule = JSON.parse(eventBuilderData.dataset.initialSchedule || '[]');

            function scheduleTemplate(item, index) {
                return `
                    <div class="schedule-item" data-schedule-index="${index}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h6 fw-bold mb-0">الخطوة ${index + 1}</h3>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" data-remove-schedule>حذف</button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">العنوان</label>
                                <input type="text" class="form-control" data-role="title" value="${item.title ?? ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنصة / المرحلة</label>
                                <input type="text" class="form-control" data-role="stage" value="${item.stage ?? ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">من</label>
                                <input type="time" class="form-control" data-role="start_time" value="${item.start_time ?? ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">إلى</label>
                                <input type="time" class="form-control" data-role="end_time" value="${item.end_time ?? ''}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">وصف مختصر</label>
                                <input type="text" class="form-control" data-role="description" value="${item.description ?? ''}">
                            </div>
                        </div>
                    </div>
                `;
            }

            function readScheduleItems() {
                return Array.from(scheduleBuilder.querySelectorAll('[data-schedule-index]')).map((item) => ({
                    title: item.querySelector('[data-role="title"]').value,
                    stage: item.querySelector('[data-role="stage"]').value,
                    start_time: item.querySelector('[data-role="start_time"]').value,
                    end_time: item.querySelector('[data-role="end_time"]').value,
                    description: item.querySelector('[data-role="description"]').value,
                }));
            }

            function syncSchedule() {
                const items = readScheduleItems();
                scheduleItemsJson.value = JSON.stringify(items);
                renderTimeline(items);
            }

            function bindScheduleEvents() {
                scheduleBuilder.querySelectorAll('input').forEach((input) => {
                    input.addEventListener('input', syncSchedule);
                });

                scheduleBuilder.querySelectorAll('[data-remove-schedule]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const items = readScheduleItems();
                        items.splice(Number(button.closest('[data-schedule-index]').dataset.scheduleIndex), 1);
                        renderSchedule(items);
                        syncSchedule();
                    });
                });
            }

            function renderSchedule(items) {
                scheduleBuilder.innerHTML = '';
                items.forEach((item, index) => {
                    scheduleBuilder.insertAdjacentHTML('beforeend', scheduleTemplate(item, index));
                });
                bindScheduleEvents();
            }

            function renderTimeline(items) {
                const container = document.getElementById('timelinePreview');
                if (!items.length) {
                    container.innerHTML = '<div style="opacity:.72;">ستظهر عناصر الجدول هنا.</div>';
                    return;
                }

                container.innerHTML = items.map((item) => `
                    <div class="timeline-item">
                        <div class="fw-semibold">${item.start_time || '--:--'}</div>
                        <div>
                            <div class="fw-semibold">${item.title || 'عنصر بدون عنوان'}</div>
                            <div style="opacity:.75; font-size:.92rem;">${item.stage || item.description || ''}</div>
                        </div>
                    </div>
                `).join('');
            }

            function updateFlowBehavior() {
                const eventType = document.getElementById('eventTypeInput').value;
                const flowChip = document.getElementById('flowChip');
                const flowDescription = document.getElementById('flowDescription');
                const publicRegistrationFields = document.getElementById('publicRegistrationFields');
                const registrationWrap = document.getElementById('registrationFormWrap');
                const registrationInput = document.getElementById('registrationFormInput');
                const manualApproval = document.getElementById('requiresManualApprovalInput');
                const manualApprovalContainer = document.getElementById('manualApprovalWrap');

                function showElement(element, displayValue = '') {
                    if (!element) {
                        return;
                    }

                    element.classList.remove('d-none');
                    element.hidden = false;
                    element.style.display = displayValue;
                    element.setAttribute('aria-hidden', 'false');
                }

                function hideElement(element) {
                    if (!element) {
                        return;
                    }

                    element.classList.add('d-none');
                    element.hidden = true;
                    element.style.display = 'none';
                    element.setAttribute('aria-hidden', 'true');
                }

                if (eventType === 'public') {
                    flowChip.innerHTML = '<i class="fas fa-link"></i>تدفق عام';
                    flowDescription.textContent = 'يتم استقبال التسجيل من رابط عام، ويمكن اعتماد القبول التلقائي أو تفعيل المراجعة يدويًا.';
                    showElement(publicRegistrationFields, 'flex');
                    showElement(registrationWrap);
                    if (registrationInput) {
                        registrationInput.disabled = false;
                    }
                    if (manualApproval) {
                        manualApproval.disabled = false;
                    }
                    showElement(manualApprovalContainer);

                    if (manualApproval && !manualApproval.dataset.userTouched) {
                        manualApproval.checked = false;
                    }
                } else {
                    flowChip.innerHTML = '<i class="fas fa-user-lock"></i>تدفق خاص';
                    flowDescription.textContent = 'الفعالية تعمل بنمط الدعوات الخاصة وغالبًا تحتاج مراجعة يدوية قبل القبول.';
                    hideElement(publicRegistrationFields);
                    hideElement(registrationWrap);
                    if (registrationInput) {
                        registrationInput.disabled = true;
                        registrationInput.value = '';
                    }
                    if (manualApproval) {
                        manualApproval.disabled = true;
                        manualApproval.checked = false;
                    }
                    hideElement(manualApprovalContainer);
                }
            }

            function updateRegistrationLinkPreview() {
                const slug = (document.getElementById('eventSlugInput').value || 'event-slug').trim();
                const url = `{{ $baseRegistrationUrl }}/${slug}`;

                document.getElementById('registrationLinkPreview').textContent = url;
                document.getElementById('previewRegistrationLink').textContent = url;
            }

            function updatePreview() {
                document.getElementById('previewTitle').textContent = document.getElementById('eventTitleInput').value || 'عنوان الفعالية';
                document.getElementById('previewMeta').textContent = document.getElementById('experienceTypeInput').selectedOptions[0]?.text || '';
                document.getElementById('previewDate').textContent = document.getElementById('eventDateInput').value || 'اختر التاريخ';
                document.getElementById('previewTime').textContent = `${document.getElementById('eventStartInput').value || '--:--'} - ${document.getElementById('eventEndInput').value || '--:--'}`;
                document.getElementById('previewLocation').textContent = document.getElementById('eventLocationInput').value || 'اختر الموقع';
                document.getElementById('previewRegistrationText').textContent = document.getElementById('eventTypeInput').value === 'public'
                    ? 'رابط عام: يتم فتح التسجيل عبر الصفحة العامة للفعالية.'
                    : 'دعوات خاصة: الحضور عبر الدعوات الخاصة فقط.';

                updateRegistrationLinkPreview();
            }

            ['eventTitleInput', 'experienceTypeInput', 'eventDateInput', 'eventStartInput', 'eventEndInput', 'eventLocationInput', 'eventTypeInput', 'eventSlugInput'].forEach((id) => {
                document.getElementById(id).addEventListener('input', updatePreview);
                document.getElementById(id).addEventListener('change', updatePreview);
            });

            document.getElementById('requiresManualApprovalInput').addEventListener('change', function() {
                this.dataset.userTouched = '1';
            });

            document.getElementById('eventTypeInput').addEventListener('input', updateFlowBehavior);
            document.getElementById('eventTypeInput').addEventListener('change', updateFlowBehavior);

            document.getElementById('addScheduleItemButton').addEventListener('click', () => {
                const items = readScheduleItems();
                items.push({ title: '', stage: '', start_time: '', end_time: '', description: '' });
                renderSchedule(items);
                syncSchedule();
            });

            renderSchedule(initialSchedule.length ? initialSchedule : [{ title: '', stage: '', start_time: '', end_time: '', description: '' }]);
            syncSchedule();
            updateFlowBehavior();
            updatePreview();

            // ── Remove header image (X button) ───────────────────────────────────
            // Sets the hidden flag to 1 and hides the preview instantly.
            // The actual deletion happens server-side when the form is saved.
            function removeHeaderImage() {
                document.getElementById('clearHeaderImageFlag').value = '1';
                document.getElementById('headerImagePreview').style.display = 'none';
                // Clear file input so no upload overrides the delete on save
                const fi = document.getElementById('headerImageInput');
                if (fi) fi.value = '';
            }

            // If user picks a new file, cancel any pending delete and show new preview
            document.getElementById('headerImageInput')?.addEventListener('change', function () {
                document.getElementById('clearHeaderImageFlag').value = '0';
            });
        </script>
    @endpush
@endsection
