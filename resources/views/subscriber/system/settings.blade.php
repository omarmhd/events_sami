@extends('layouts.system')

@section('title', 'إعدادات النظام')

@push('styles')
<style>
    /* ── Page layout ─────────────────────────────────────────────────── */
    .custom-card { margin-bottom: 1.5rem; border-radius: 16px; border: 1px solid #e8f0ee; background: #fff; box-shadow: 0 1px 4px rgba(15,143,131,.06); overflow: hidden; }

    /* ── Color picker ─────────────────────────────────────────────────── */
    .color-picker-wrap { display: flex; align-items: center; gap: 12px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 8px 12px; transition: border-color .2s; cursor: pointer; }
    .color-picker-wrap:focus-within { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(15,143,131,.1); }
    .color-picker-wrap input[type="color"] { width: 32px; height: 32px; border: none; border-radius: 8px; padding: 0; cursor: pointer; background: none; flex-shrink: 0; }
    .color-picker-wrap input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    .color-picker-wrap input[type="color"]::-webkit-color-swatch { border: none; border-radius: 6px; }
    .color-value-text { font-size: 0.8rem; font-weight: 600; color: var(--text-soft); font-family: monospace; }

    /* ── Logo upload zone ────────────────────────────────────────────── */
    .logo-upload-zone { border: 2px dashed #d1e8e2; border-radius: 12px; background: #f8fcfa; padding: 18px 16px; text-align: center; cursor: pointer; transition: border-color .2s, background .2s; position: relative; min-height: 90px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .logo-upload-zone:hover { border-color: var(--primary-color); background: rgba(15,143,131,.04); }
    .logo-upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }

    /* ── Logo preview box ────────────────────────────────────────────── */
    .logo-preview-box { border: 1.5px dashed #d1e8e2; border-radius: 12px; background: #f8fcfa; padding: 12px; display: flex; align-items: center; justify-content: center; min-height: 90px; }
    .logo-preview-box img { max-height: 70px; max-width: 100%; object-fit: contain; }
    .logo-preview-box .no-logo-text { font-size: 0.8rem; color: var(--text-soft); }

    /* ── Toggle switch ───────────────────────────────────────────────── */
    .toggle-wrap { display: flex; align-items: center; gap: 12px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; }
    .form-switch .form-check-input { width: 2.5em; height: 1.3em; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: var(--primary-color); border-color: var(--primary-color); }

    /* ── Section groups ──────────────────────────────────────────────── */
    .setting-group { padding: 1.5rem 2rem; }

    /* ── Delete button ───────────────────────────────────────────────── */
    .btn-delete-img { border: 1px solid #fca5a5; color: #dc2626; background: #fff5f5; border-radius: 8px; font-size: 0.78rem; font-weight: 600; padding: 5px 12px; width: 100%; cursor: pointer; }
    .btn-delete-img:hover { background: #fee2e2; }
</style>
@endpush

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">إعدادات النظام</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">إعدادات النظام</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-3" role="alert"
             style="background:#dcfce7;color:#166534;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-3" role="alert"
             style="background:#fee2e2;color:#991b1b;">
            <i class="fas fa-triangle-exclamation me-2"></i>
            @if($errors->any())
                {{ $errors->first() }}
            @else
                {{ session('error') }}
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('system.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ── Branding Card ── --}}
        <div class="custom-card">
            <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-palette" style="color:var(--primary-color);font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--text-main);">هوية المنصة</h5>
                        <p class="mb-0 small" style="color:var(--text-soft);">اسم المنصة والشعار والألوان العامة</p>
                    </div>
                </div>
            </div>

            <div class="setting-group">
                <div class="row g-4">

                    {{-- Platform Name --}}
                    <div class="col-md-6">
                        <label class="field-label">اسم المنصة</label>
                        <input name="platform_name" class="form-control"
                               placeholder="مثال: منصة معا"
                               value="{{ old('platform_name', $settings['platform_name'] ?? 'MaanInvite') }}">
                        <span class="field-hint">يظهر في العنوان ورسائل البريد</span>
                    </div>

                    {{-- Colors --}}
                    <div class="col-md-3">
                        <label class="field-label">اللون الرئيسي</label>
                        <div class="color-picker-wrap">
                            <input name="primary_color" type="color" id="primaryColorPicker"
                                   value="{{ old('primary_color', $settings['primary_color'] ?? '#0f8f83') }}">
                            <span class="color-value-text" id="primaryColorText">
                                {{ old('primary_color', $settings['primary_color'] ?? '#0f8f83') }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="field-label">اللون الثانوي</label>
                        <div class="color-picker-wrap">
                            <input name="secondary_color" type="color" id="secondaryColorPicker"
                                   value="{{ old('secondary_color', $settings['secondary_color'] ?? '#f59e0b') }}">
                            <span class="color-value-text" id="secondaryColorText">
                                {{ old('secondary_color', $settings['secondary_color'] ?? '#f59e0b') }}
                            </span>
                        </div>
                    </div>

                    {{-- Logo Section --}}
                    <div class="col-12">
                        <label class="field-label">شعار المنصة</label>

                        {{-- Hidden flag: 1 = delete logo on save --}}
                        <input type="hidden" name="clear_logo" id="clearLogoFlag" value="0">

                        <div class="row g-3 align-items-start">

                            {{-- Preview + X button --}}
                            <div class="col-auto" id="logoPreviewWrap" style="{{ empty($settings['platform_logo_url']) ? 'display:none;' : '' }}">
                                <div style="position:relative;display:inline-block;">
                                    <img id="logoPreviewImg"
                                         src="{{ $settings['platform_logo_url'] ?? '' }}"
                                         style="height:60px;max-width:160px;object-fit:contain;border:1px dashed #d1e8e2;border-radius:10px;padding:6px;background:#f8fcfa;">
                                    <button type="button" onclick="removeLogo()"
                                            style="position:absolute;top:-8px;inset-inline-end:-8px;width:22px;height:22px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:12px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                                        ×
                                    </button>
                                </div>
                            </div>

                            {{-- File upload --}}
                            <div class="col-md-3">
                                <label class="logo-upload-zone" for="platformLogoFileInput">
                                    <input type="file" name="platform_logo_file" id="platformLogoFileInput"
                                           accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml">
                                    <i class="fas fa-cloud-arrow-up mb-1" style="color:var(--primary-color);font-size:1.2rem;pointer-events:none;"></i>
                                    <div style="font-size:0.78rem;font-weight:600;color:var(--text-main);pointer-events:none;">رفع شعار</div>
                                    <div style="font-size:0.71rem;color:var(--text-soft);pointer-events:none;">SVG، PNG، JPG</div>
                                </label>
                            </div>

                            {{-- External URL --}}
                            <div class="col-md-5">
                                <input name="platform_logo_url" type="url" class="form-control"
                                       placeholder="https://example.com/logo.png"
                                       value="{{ old('platform_logo_url', $settings['platform_logo_url'] ?? '') }}">
                                <span class="field-hint">رابط خارجي — اختياري</span>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Contact & Limits Card ── --}}
        <div class="custom-card">
            <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-gear" style="color:var(--primary-color);font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--text-main);">إعدادات النظام العامة</h5>
                        <p class="mb-0 small" style="color:var(--text-soft);">بريد الدعم، الفترة التجريبية، حالة الصيانة</p>
                    </div>
                </div>
            </div>

            <div class="setting-group">
                <div class="row g-3">

                    <div class="col-md-5">
                        <label class="field-label">بريد الدعم الفني</label>
                        <input name="support_email" type="email" class="form-control"
                               placeholder="support@example.com"
                               value="{{ old('support_email', $settings['support_email'] ?? '') }}">
                        <span class="field-hint">يُستخدم في رسائل المنصة وصفحة الاتصال</span>
                    </div>

                    <div class="col-md-3">
                        <label class="field-label">مدة الفترة التجريبية (يوم)</label>
                        <input name="trial_days" type="number" min="1" max="365" class="form-control"
                               placeholder="14"
                               value="{{ old('trial_days', $settings['trial_days'] ?? '14') }}">
                        <span class="field-hint">للحسابات الجديدة فقط</span>
                    </div>

                    <div class="col-md-4">
                        <label class="field-label">وضع الصيانة</label>
                        <div class="toggle-wrap">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       name="maintenance_mode" id="maintenanceToggle" value="1"
                                       {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                            </div>
                            <label class="fw-bold" for="maintenanceToggle" style="font-size:0.875rem;cursor:pointer;">
                                <span id="maintenanceLabel">
                                    {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'مفعّل — المنصة للصيانة' : 'معطّل — المنصة تعمل' }}
                                </span>
                            </label>
                        </div>
                        <span class="field-hint">عند التفعيل يُعاد توجيه الزوار لصفحة الصيانة</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Save ── --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn-save">
                <i class="fas fa-floppy-disk me-2"></i> حفظ الإعدادات
            </button>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    // ── Color pickers ──────────────────────────────────────────────────────
    ['primary', 'secondary'].forEach(function(key) {
        const picker = document.getElementById(key + 'ColorPicker');
        const text   = document.getElementById(key + 'ColorText');
        if (!picker || !text) return;
        picker.addEventListener('input', function() { text.textContent = this.value; });
    });

    // ── Logo: file picked → show preview, cancel any pending delete ──────
    document.getElementById('platformLogoFileInput')?.addEventListener('change', function() {
        if (!this.files[0]) return;
        document.getElementById('clearLogoFlag').value = '0';
        const reader = new FileReader();
        reader.onload = e => {
            const img  = document.getElementById('logoPreviewImg');
            const wrap = document.getElementById('logoPreviewWrap');
            img.src = e.target.result;
            wrap.style.display = '';
        };
        reader.readAsDataURL(this.files[0]);
    });

    // ── Logo: click X → hide preview, set delete flag ─────────────────
    function removeLogo() {
        document.getElementById('clearLogoFlag').value = '1';
        document.getElementById('logoPreviewWrap').style.display = 'none';
        document.getElementById('platformLogoFileInput').value = '';
    }

    // ── Maintenance mode label ────────────────────────────────────────────
    document.getElementById('maintenanceToggle')?.addEventListener('change', function() {
        const label = document.getElementById('maintenanceLabel');
        if (label) {
            label.textContent = this.checked
                ? 'مفعّل — المنصة للصيانة'
                : 'معطّل — المنصة تعمل';
        }
    });
</script>
@endpush
