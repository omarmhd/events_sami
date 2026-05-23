@extends('layouts.app')

@section('title', 'الهوية البصرية')

@push('styles')
<style>
    /* --- Page Header --- */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }

    /* --- Card --- */
    .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden; }

    /* --- Section Divider --- */
    .section-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-soft);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    /* --- Form Fields --- */
    .field-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
        display: block;
    }
    .field-hint {
        font-size: 0.75rem;
        color: var(--text-soft);
        margin-top: 4px;
    }
    .form-control, .form-select {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        color: var(--text-main);
        font-size: 0.875rem;
        padding: 0.6rem 0.9rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15,143,131,.1);
        background: #fff;
        outline: none;
    }

    /* --- Color Picker --- */
    .color-picker-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        transition: border-color .2s;
        cursor: pointer;
    }
    .color-picker-wrap:focus-within {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15,143,131,.1);
        background: #fff;
    }
    .color-picker-wrap input[type="color"] {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        padding: 0;
        cursor: pointer;
        background: none;
        flex-shrink: 0;
    }
    .color-picker-wrap input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    .color-picker-wrap input[type="color"]::-webkit-color-swatch { border: none; border-radius: 6px; }
    .color-value-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-soft);
        font-family: monospace;
    }

    /* --- Image Preview --- */
    .img-preview-wrap {
        position: relative;
        margin-top: 8px;
    }
    .img-preview {
        width: 100%;
        height: 80px;
        object-fit: contain;
        border-radius: 10px;
        border: 1px dashed #e2e8f0;
        background: #f8fafc;
        padding: 6px;
        display: none;
    }
    .img-preview.has-img { display: block; }

    /* --- File Upload Drop Zone --- */
    .file-drop-zone {
        border: 2px dashed #d1e8e2;
        border-radius: 12px;
        background: #f8fcfa;
        padding: 20px 16px;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        position: relative;
    }
    .file-drop-zone:hover,
    .file-drop-zone.drag-over {
        border-color: var(--primary-color);
        background: rgba(15,143,131,.04);
    }
    .file-drop-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .file-drop-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(15,143,131,.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        color: var(--primary-color);
        font-size: 1rem;
        pointer-events: none;
    }
    .file-drop-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-main);
        pointer-events: none;
    }
    .file-drop-hint {
        font-size: 0.73rem;
        color: var(--text-soft);
        margin-top: 2px;
        pointer-events: none;
    }
    .file-drop-selected {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-top: 6px;
        pointer-events: none;
        display: none;
    }

    /* Header image preview (banner style) */
    .header-img-preview {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-top: 10px;
        display: none;
    }
    .header-img-preview.has-img { display: block; }

    /* --- Save Button --- */
    .btn-save {
        background: var(--grad-primary);
        color: white;
        border: none;
        padding: 11px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(15,143,131,.3);
        transition: .3s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15,143,131,.4);
        color: white;
    }

</style>
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">الهوية البصرية</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">الهوية البصرية</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ── Brand Identity Card ── --}}
    <div class="custom-card">

        {{-- ══ FEATURE LOCK BANNER (shown when visual_identity is disabled) ══ --}}
        @if(!($visualIdentityEnabled ?? true))
        <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);
                    border-bottom:2px solid #fde68a;
                    padding:20px 24px;
                    display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            {{-- Icon --}}
            <div style="width:52px;height:52px;border-radius:14px;
                        background:#fef3c7;border:2px solid #fde68a;
                        display:flex;align-items:center;justify-content:center;
                        font-size:1.4rem;color:#d97706;flex-shrink:0;">
                <i class="fas fa-lock"></i>
            </div>
            {{-- Text --}}
            <div style="flex:1;min-width:200px;">
                <div style="font-weight:800;color:#92400e;font-size:.95rem;margin-bottom:3px;">
                    الهوية البصرية غير متاحة في خطتك الحالية
                </div>
                <div style="font-size:.82rem;color:#b45309;line-height:1.5;">
                    قم بترقية خطتك لتخصيص الشعار والألوان وبيانات المُرسل في جميع رسائلك.
                </div>
            </div>
            {{-- CTA --}}
            <a href="{{ route('feature.unavailable', ['feature' => 'visual_identity']) }}"
               style="display:inline-flex;align-items:center;gap:8px;
                      background:#d97706;color:#fff;font-weight:700;font-size:.85rem;
                      padding:10px 18px;border-radius:10px;text-decoration:none;
                      white-space:nowrap;transition:opacity .2s;">
                <i class="fas fa-arrow-up"></i> معرفة المزيد والترقية
            </a>
        </div>
        @endif

        <form action="{{ route('email-settings.branding') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Card Header --}}
            <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05) 0%,rgba(255,255,255,0) 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fas fa-palette" style="color:var(--primary-color);font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--text-main);">الهوية البصرية</h5>
                        <p class="mb-0 small" style="color: var(--text-soft);">
                            @if($visualIdentityEnabled ?? true)
                                الهوية والألوان التي تظهر في رسائل البريد الإلكتروني
                            @else
                                معاينة للإعدادات الافتراضية — التعديل يتطلب ترقية الخطة
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Overlay when disabled: dim the form and disable all inputs --}}
            @if(!($visualIdentityEnabled ?? true))
            <div style="position:relative;">
                <div style="position:absolute;inset:0;z-index:10;cursor:not-allowed;border-radius:0 0 14px 14px;
                            background:rgba(255,255,255,.55);backdrop-filter:blur(2px);">
                </div>
            @endif

            <div class="p-4">
                {{-- Disable all inputs when feature is off (fieldset trick) --}}
                @if(!($visualIdentityEnabled ?? true))
                <fieldset disabled style="border:none;padding:0;margin:0;">
                @endif

                {{-- ── Sender Info ── --}}
                <div class="section-label">
                    <i class="fas fa-envelope" style="color:var(--primary-color);"></i>
                    معلومات البريد
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="field-label">اسم العلامة التجارية</label>
                        <input name="brand_name"
                               class="form-control"
                               placeholder="مثال: شركة المستقبل"
                               value="{{ old('brand_name', $branding->brand_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="field-label">بريد المُرسل</label>
                        <input name="sender_email"
                               type="email"
                               class="form-control"
                               placeholder="noreply@example.com"
                               value="{{ old('sender_email', $branding->sender_email) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="field-label">بريد الرد</label>
                        <input name="reply_to_email"
                               type="email"
                               class="form-control"
                               placeholder="support@example.com"
                               value="{{ old('reply_to_email', $branding->reply_to_email) }}">
                        <span class="field-hint">يظهر في الفوتر كوسيلة تواصل عند وجود مشكلة، ويُستخدم أيضًا كعنوان ردّ للبريد الصادر.</span>
                    </div>
                </div>

                {{-- ── Visual Assets ── --}}
                <div class="section-label">
                    <i class="fas fa-image" style="color:var(--primary-color);"></i>
                    الصور والألوان
                </div>

                {{-- Hidden delete flags — set to 1 via JS when user clicks X --}}
                <input type="hidden" name="clear_logo"   id="clearLogoFlagEmail"  value="0">
                <input type="hidden" name="clear_header" id="clearHeaderFlagEmail" value="0">

                <div class="row g-3 mb-4">
                    {{-- Logo --}}
                    <div class="col-md-4">
                        <label class="field-label">شعار العلامة التجارية</label>

                        {{-- Preview + X (shown only if logo exists) --}}
                        <div id="logoPreviewWrapEmail" class="{{ $branding->logo_url ? '' : 'd-none' }}" style="margin-bottom:8px;">
                            <div style="position:relative;display:inline-block;">
                                <img id="preview-logo"
                                     src="{{ $branding->logo_url }}"
                                     style="height:60px;max-width:160px;object-fit:contain;border:1px dashed #d1e8e2;border-radius:10px;padding:6px;background:#f8fcfa;">
                                <button type="button" onclick="removeEmailLogo()"
                                        style="position:absolute;top:-8px;inset-inline-end:-8px;width:22px;height:22px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:12px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
                            </div>
                        </div>

                        <div class="file-drop-zone" id="logoDropZone">
                            <input type="file" name="logo_file" id="logoFileInput"
                                   accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml">
                            <div class="file-drop-icon"><i class="fas fa-image"></i></div>
                            <div class="file-drop-title">اسحب الشعار هنا أو انقر للاختيار</div>
                            <div class="file-drop-hint">PNG, JPG, SVG, WEBP — حجم أقصى 2 MB</div>
                            <div class="file-drop-selected" id="logoFileSelected"></div>
                        </div>
                    </div>

                    {{-- Header image --}}
                    <div class="col-md-4">
                        <label class="field-label">صورة الرأس</label>

                        {{-- Preview + X --}}
                        <div id="headerPreviewWrapEmail" class="{{ $branding->header_image_url ? '' : 'd-none' }}" style="margin-bottom:8px;">
                            <div style="position:relative;display:inline-block;">
                                <img id="headerImgPreview"
                                     src="{{ $branding->header_image_url }}"
                                     style="height:60px;max-width:100%;object-fit:contain;border:1px dashed #d1e8e2;border-radius:10px;padding:6px;background:#f8fcfa;">
                                <button type="button" onclick="removeEmailHeader()"
                                        style="position:absolute;top:-8px;inset-inline-end:-8px;width:22px;height:22px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:12px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
                            </div>
                        </div>

                        <div class="file-drop-zone" id="headerDropZone">
                            <input type="file" name="header_image_file" id="headerImageFile"
                                   accept="image/png,image/jpeg,image/gif,image/webp">
                            <div class="file-drop-icon"><i class="fas fa-cloud-arrow-up"></i></div>
                            <div class="file-drop-title">اسحب الصورة هنا أو انقر للاختيار</div>
                            <div class="file-drop-hint">PNG, JPG, WEBP — حجم أقصى 4 MB</div>
                            <div class="file-drop-selected" id="headerFileSelected"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="field-label">اللون الرئيسي</label>
                                <div class="color-picker-wrap">
                                    <input name="primary_color"
                                           type="color"
                                           id="primaryColorPicker"
                                           value="{{ old('primary_color', $branding->primary_color ?? '#0f8f83') }}">
                                    <span class="color-value-text" id="primaryColorText">
                                        {{ old('primary_color', $branding->primary_color ?? '#0f8f83') }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="field-label">اللون الثانوي</label>
                                <div class="color-picker-wrap">
                                    <input name="secondary_color"
                                           type="color"
                                           id="secondaryColorPicker"
                                           value="{{ old('secondary_color', $branding->secondary_color ?? '#f59e0b') }}">
                                    <span class="color-value-text" id="secondaryColorText">
                                        {{ old('secondary_color', $branding->secondary_color ?? '#f59e0b') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Save ── --}}
                <div class="d-flex justify-content-end align-items-center gap-3 pt-3 border-top flex-wrap">

                    @if(!($visualIdentityEnabled ?? true))
                        {{-- Feature disabled: show lock button instead of save --}}
                        <x-feature-lock-btn feature="visual_identity"
                                            label="ترقية لتفعيل الهوية البصرية"
                                            icon="fas fa-palette" />
                    @else
                        <button type="submit" class="btn-save">
                            <i class="fas fa-floppy-disk me-2"></i> حفظ الهوية البصرية
                        </button>
                    @endif
                </div>

                @if(!($visualIdentityEnabled ?? true))
                </fieldset>
                @endif

            </div>

            @if(!($visualIdentityEnabled ?? true))
            </div>{{-- /.overlay wrapper --}}
            @endif

        </form>
    </div>

@endsection

@push('scripts')
<script>
    // ── Live color value display ─────────────────────────────────
    ['primary', 'secondary'].forEach(function(key) {
        const picker = document.getElementById(key + 'ColorPicker');
        const text   = document.getElementById(key + 'ColorText');
        if (!picker || !text) return;
        picker.addEventListener('input', function() {
            text.textContent = this.value;
        });
    });

    /**
     * Generic image drop-zone initialiser.
     * Handles file input change, drag-over highlight, drop, and live preview.
     *
     * @param {string} fileInputId  - ID of the <input type="file">
     * @param {string} dropZoneId   - ID of the drop-zone wrapper element
     * @param {string} previewId    - ID of the <img> preview element
     * @param {string} selectedId   - ID of the selected-filename label element
     */
    function initImageDropZone(fileInputId, dropZoneId, previewId, selectedId) {
        const fileInput  = document.getElementById(fileInputId);
        const dropZone   = document.getElementById(dropZoneId);
        const preview    = document.getElementById(previewId);
        const selectedEl = document.getElementById(selectedId);

        if (!fileInput || !dropZone) return;

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;

            if (selectedEl) {
                selectedEl.textContent = file.name;
                selectedEl.style.display = 'block';
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.add('has-img');
                }
            };
            reader.readAsDataURL(file);
        }

        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) handleFile(this.files[0]);
        });

        dropZone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', function () {
            this.classList.remove('drag-over');
        });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const file = e.dataTransfer?.files?.[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                handleFile(file);
            }
        });
    }

    // ── X buttons: hide preview + set delete flag ────────────────
    function removeEmailLogo() {
        document.getElementById('clearLogoFlagEmail').value = '1';
        document.getElementById('logoPreviewWrapEmail').style.display = 'none';
        document.getElementById('logoFileInput').value = '';
    }
    function removeEmailHeader() {
        document.getElementById('clearHeaderFlagEmail').value = '1';
        document.getElementById('headerPreviewWrapEmail').style.display = 'none';
        document.getElementById('headerImageFile').value = '';
    }

    // When user picks a new file → cancel any pending delete + show new preview
    function initImageDropZoneWithFlag(fileInputId, dropZoneId, previewId, selectedId, previewWrapId, flagId) {
        initImageDropZone(fileInputId, dropZoneId, previewId, selectedId);
        document.getElementById(fileInputId)?.addEventListener('change', function() {
            if (!this.files[0]) return;
            // Cancel delete flag
            if (flagId) document.getElementById(flagId).value = '0';
            // Show the preview wrap
            const wrap = document.getElementById(previewWrapId);
            if (wrap) wrap.style.display = '';
        });
    }

    // Initialise both drop zones
    initImageDropZoneWithFlag('logoFileInput',   'logoDropZone',   'preview-logo',     'logoFileSelected',   'logoPreviewWrapEmail',   'clearLogoFlagEmail');
    initImageDropZoneWithFlag('headerImageFile', 'headerDropZone', 'headerImgPreview', 'headerFileSelected', 'headerPreviewWrapEmail', 'clearHeaderFlagEmail');
</script>
@endpush
