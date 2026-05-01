{{--
    plan-form.blade.php
    ───────────────────
    Reusable partial — works for both Create and Edit modals.
    Pass: $plan = null (create) | SubscriptionPlan instance (edit)

    FEATURES SECTION:
    All feature keys are fixed in FeatureRegistry (no free-text input needed).
    The admin only controls:
      • ON / OFF toggle per feature
      • Number of forms limit (registration_forms only)
    The JSON is built client-side and stored in a hidden input.
--}}

@php
    $uid = $plan?->id ?? 'new';

    // Parse existing features into a key→data map for easy lookup
    $existingRaw  = old('features_json', $plan ? json_encode($plan->features ?? []) : '[]');
    $existingMap  = [];
    foreach (json_decode($existingRaw, true) ?? [] as $f) {
        if (!empty($f['key'])) $existingMap[$f['key']] = $f;
    }

    // ── Canonical feature definitions ────────────────────────────────────────
    // Grouped for visual separation in the UI.
    // 'limit' => true  means this feature gets a numeric count input.
    $featureGroups = [
        [
            'group_label' => 'ميزات المنصة',
            'group_icon'  => 'fas fa-layer-group',
            'group_color' => '#0f6b63',
            'group_hint'  => 'تتحكم مباشرة في الصلاحيات داخل النظام',
            'features'    => [
                [
                    'key'         => 'registration_forms',
                    'label'       => 'نماذج التسجيل',
                    'description' => 'إنشاء نماذج مخصصة وربطها بالفعاليات العامة',
                    'icon'        => 'fas fa-wpforms',
                    'color'       => '#2563eb',
                    'bg'          => '#eff6ff',
                    'border'      => '#bfdbfe',
                    'limit'       => true,
                    'limit_label' => 'الحد الأقصى للنماذج',
                    'limit_hint'  => 'فارغ = غير محدود',
                ],
                [
                    'key'         => 'teams',
                    'label'       => 'إدارة الفريق',
                    'description' => 'إضافة أعضاء فريق وتعيين أدوارهم في المنظمة',
                    'icon'        => 'fas fa-users',
                    'color'       => '#7c3aed',
                    'bg'          => '#f5f3ff',
                    'border'      => '#ddd6fe',
                    'limit'       => false,
                ],
                [
                    'key'         => 'visual_identity',
                    'label'       => 'الهوية البصرية',
                    'description' => 'تخصيص الشعار والألوان وبيانات المُرسِل في الإيميلات',
                    'icon'        => 'fas fa-palette',
                    'color'       => '#be185d',
                    'bg'          => '#fdf2f8',
                    'border'      => '#fbcfe8',
                    'limit'       => false,
                ],
                [
                    'key'         => 'event_header_image',
                    'label'       => 'صورة رأس الفعالية',
                    'description' => 'رفع صورة مخصصة لرأس إيميلات كل فعالية (1200×400)',
                    'icon'        => 'fas fa-image',
                    'color'       => '#0f6b63',
                    'bg'          => '#f0fdf9',
                    'border'      => '#99f6e4',
                    'limit'       => false,
                ],
                [
                    'key'         => 'event_footer_image',
                    'label'       => 'صورة تذييل الفعالية',
                    'description' => 'رفع صورة مخصصة لتذييل إيميلات كل فعالية (1200×260)',
                    'icon'        => 'fas fa-file-image',
                    'color'       => '#0369a1',
                    'bg'          => '#f0f9ff',
                    'border'      => '#bae6fd',
                    'limit'       => false,
                ],
            ],
        ],
        [
            'group_label' => 'ميزات الاستيراد والإرسال',
            'group_icon'  => 'fas fa-paper-plane',
            'group_color' => '#374151',
            'group_hint'  => 'أدوات إدارة المدعوين والإيميلات',
            'features'    => [
                [
                    'key'         => 'csv_import',
                    'label'       => 'استيراد CSV الجماعي',
                    'description' => 'رفع قوائم المدعوين من ملف CSV دفعةً واحدة',
                    'icon'        => 'fas fa-file-csv',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'bulk_resend',
                    'label'       => 'إعادة الإرسال الجماعي',
                    'description' => 'إعادة إرسال الدعوات لعدة أشخاص في آنٍ واحد',
                    'icon'        => 'fas fa-paper-plane',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
            ],
        ],
        [
            'group_label' => 'ميزات الاتصال',
            'group_icon'  => 'fas fa-mobile-screen',
            'group_color' => '#374151',
            'group_hint'  => 'قنوات تواصل إضافية مع المدعوين',
            'features'    => [
                [
                    'key'         => 'sms',
                    'label'       => 'إشعارات SMS',
                    'description' => 'إرسال رسائل نصية للمدعوين',
                    'icon'        => 'fas fa-mobile-screen',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'whatsapp',
                    'label'       => 'رسائل واتساب',
                    'description' => 'إرسال الدعوات عبر واتساب',
                    'icon'        => 'fab fa-whatsapp',
                    'color'       => '#16a34a',
                    'bg'          => '#f0fdf4',
                    'border'      => '#bbf7d0',
                    'limit'       => false,
                ],
            ],
        ],
        [
            'group_label' => 'التقارير والتصدير',
            'group_icon'  => 'fas fa-chart-line',
            'group_color' => '#374151',
            'group_hint'  => '',
            'features'    => [
                [
                    'key'         => 'advanced_analytics',
                    'label'       => 'تقارير متقدمة',
                    'description' => 'تقارير تفصيلية وإحصاءات الحضور والتفاعل',
                    'icon'        => 'fas fa-chart-line',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'export_reports',
                    'label'       => 'تصدير التقارير',
                    'description' => 'تصدير التقارير والإحصاءات بصيغ متعددة',
                    'icon'        => 'fas fa-file-export',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
            ],
        ],
        [
            'group_label' => 'ميزات المؤسسات',
            'group_icon'  => 'fas fa-building',
            'group_color' => '#374151',
            'group_hint'  => 'للعملاء Enterprise فقط',
            'features'    => [
                [
                    'key'         => 'account_manager',
                    'label'       => 'مدير حساب مخصص',
                    'description' => 'مدير حساب شخصي متاح طوال ساعات العمل',
                    'icon'        => 'fas fa-headset',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'sla',
                    'label'       => 'اتفاقية مستوى الخدمة SLA',
                    'description' => 'ضمان اتفاقية مستوى خدمة مكتوبة',
                    'icon'        => 'fas fa-shield-halved',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'white_label',
                    'label'       => 'وايت لابل',
                    'description' => 'إخفاء شعار المنصة بالكامل',
                    'icon'        => 'fas fa-tag',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'api_access',
                    'label'       => 'وصول API',
                    'description' => 'وصول كامل لـ REST API لربط الأنظمة الخارجية',
                    'icon'        => 'fas fa-code',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
                [
                    'key'         => 'sso',
                    'label'       => 'تسجيل دخول موحد SSO',
                    'description' => 'تكامل مع أنظمة SAML/OAuth للهوية المؤسسية',
                    'icon'        => 'fas fa-key',
                    'color'       => '#374151',
                    'bg'          => '#f9fafb',
                    'border'      => '#e5e7eb',
                    'limit'       => false,
                ],
            ],
        ],
    ];
@endphp

{{-- ════════════════════════════════════════════════════
     STYLES (scoped inside modal — no conflicts)
════════════════════════════════════════════════════ --}}
<style>
.pf-section-hdr {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: .78rem;
    font-weight: 700;
    color: #374151;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    margin-bottom: 8px;
    cursor: pointer;
    user-select: none;
}
.pf-section-hdr .pf-section-arrow {
    margin-right: auto;
    transition: transform .2s;
    font-size: .7rem;
    color: #94a3b8;
}
.pf-section-hdr.collapsed .pf-section-arrow { transform: rotate(-90deg); }

.pf-feature-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    transition: border-color .2s, background .2s, opacity .2s;
    margin-bottom: 6px;
}
.pf-feature-card.is-on  { background: #fff; }
.pf-feature-card.is-off { background: #fafafa; opacity: .65; }

.pf-icon-bubble {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.pf-feature-meta { flex: 1; min-width: 0; }
.pf-feature-label { font-size: .88rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
.pf-feature-desc  { font-size: .73rem; color: #64748b; margin-top: 2px; line-height: 1.4; }

.pf-limit-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 3px;
    flex-shrink: 0;
}
.pf-limit-label { font-size: .65rem; font-weight: 700; color: #2563eb; white-space: nowrap; }
.pf-limit-input {
    width: 90px;
    font-size: .8rem;
    font-weight: 700;
    text-align: center;
    border: 2px solid #93c5fd;
    border-radius: 8px;
    padding: 4px 8px;
    background: #eff6ff;
    color: #1d4ed8;
    outline: none;
}
.pf-limit-input:focus { border-color: #2563eb; }
.pf-limit-hint { font-size: .6rem; color: #60a5fa; white-space: nowrap; }

.pf-toggle-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}
.pf-toggle-wrap .form-check-input {
    width: 2.4em;
    height: 1.2em;
    cursor: pointer;
    margin: 0;
}
.pf-toggle-status {
    font-size: .62rem;
    font-weight: 700;
    white-space: nowrap;
}
</style>

<div class="row g-3">

    {{-- ══ المعلومات الأساسية ══════════════════════════════════════════════════ --}}
    <div class="col-12">
        <div class="section-label">
            <i class="fas fa-info-circle" style="color:var(--primary-color);"></i>
            المعلومات الأساسية
        </div>
    </div>

    <div class="{{ $plan ? 'col-md-8' : 'col-md-6' }}">
        <label class="field-label">اسم الخطة <span class="text-danger">*</span></label>
        <input name="name" class="form-control" placeholder="مثال: الخطة الاحترافية"
               value="{{ old('name', optional($plan)->name) }}" required>
    </div>

    @if(!$plan)
    <div class="col-md-6">
        <label class="field-label">رمز الخطة (كود) <span class="text-danger">*</span></label>
        <input name="code" class="form-control" placeholder="مثال: PRO"
               value="{{ old('code') }}" required
               style="font-family:monospace;"
               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9_\-]/g,'')">
        <span class="form-text">حروف إنجليزية وأرقام فقط — لا يمكن تغييره لاحقاً</span>
    </div>
    @endif

    <div class="col-md-4">
        <label class="field-label">تسمية مميزة</label>
        <input name="highlight_label" class="form-control" placeholder="مثال: الأكثر شيوعاً"
               value="{{ old('highlight_label', optional($plan)->highlight_label) }}">
    </div>

    <div class="col-md-{{ $plan ? '4' : '12' }}">
        <label class="field-label">ترتيب العرض</label>
        <input name="sort_order" type="number" min="0" class="form-control" placeholder="0"
               value="{{ old('sort_order', optional($plan)->sort_order ?? 0) }}">
    </div>

    <div class="col-12">
        <label class="field-label">وصف الخطة</label>
        <textarea name="description" class="form-control" rows="2"
                  placeholder="وصف مختصر يظهر للمشترك في صفحة الخطط...">{{ old('description', optional($plan)->description) }}</textarea>
    </div>

    {{-- ══ التسعير ══════════════════════════════════════════════════════════════ --}}
    <div class="col-12 mt-1">
        <div class="section-label">
            <i class="fas fa-tag" style="color:var(--primary-color);"></i>
            التسعير
        </div>
    </div>

    <div class="col-md-4">
        <label class="field-label">السعر السنوي (ر.س) <span class="text-danger">*</span></label>
        <input name="annual_price" type="number" step="0.01" min="0" class="form-control"
               placeholder="0.00"
               value="{{ old('annual_price', optional($plan)->annual_price) }}" required>
    </div>

    <div class="col-md-4">
        <label class="field-label">سعر الفعالية المنفردة (ر.س)</label>
        <input name="per_event_price" type="number" step="0.01" min="0" class="form-control"
               placeholder="اختياري"
               value="{{ old('per_event_price', optional($plan)->per_event_price) }}">
    </div>

    {{-- ══ الحدود ══════════════════════════════════════════════════════════════ --}}
    <div class="col-12 mt-1">
        <div class="section-label">
            <i class="fas fa-sliders" style="color:var(--primary-color);"></i>
            الحدود والقيود
        </div>
    </div>

    <div class="col-md-4">
        <label class="field-label">حد الفعاليات السنوية</label>
        <input name="annual_event_limit" type="number" min="0" class="form-control"
               placeholder="فارغ = غير محدود"
               value="{{ old('annual_event_limit', optional($plan)->annual_event_limit) }}">
        <span class="form-text">فارغ = فعاليات غير محدودة</span>
    </div>

    <div class="col-md-4">
        <label class="field-label">حد المدعوين لكل فعالية</label>
        <input name="per_event_invitee_limit" type="number" min="0" class="form-control"
               placeholder="فارغ = غير محدود"
               value="{{ old('per_event_invitee_limit', optional($plan)->per_event_invitee_limit) }}">
        <span class="form-text">فارغ = مدعوون غير محدودون</span>
    </div>

    {{-- ══ الميزات — UI ثابت ═══════════════════════════════════════════════════
         المفاتيح محددة مسبقاً من FeatureRegistry.
         الأدمن يتحكم فقط في:
           • تفعيل/تعطيل كل ميزة (toggle)
           • عدد النماذج (لـ registration_forms فقط)
    ════════════════════════════════════════════════════════════════════════════ --}}
    <div class="col-12 mt-2">
        <div class="section-label">
            <i class="fas fa-list-check" style="color:var(--primary-color);"></i>
            الميزات والصلاحيات
            <span style="font-size:.68rem;font-weight:400;color:var(--text-soft);margin-right:4px;text-transform:none;letter-spacing:0;">
                — تحكم في ما يُتاح لمشتركي هذه الخطة
            </span>
        </div>
    </div>

    <div class="col-12">
        {{-- Hidden input — serialized JSON sent with form --}}
        <input type="hidden" name="features_json" id="featFeaturesJson_{{ $uid }}">

        {{-- Feature groups --}}
        @foreach($featureGroups as $gIdx => $group)
        <div class="pf-group mb-3" data-group="{{ $gIdx }}">

            {{-- Group header (collapsible) --}}
            <div class="pf-section-hdr" onclick="pfToggleGroup(this)">
                <i class="{{ $group['group_icon'] }}" style="color:{{ $group['group_color'] }};width:16px;text-align:center;"></i>
                <span>{{ $group['group_label'] }}</span>
                @if($group['group_hint'])
                    <span style="font-size:.65rem;color:#94a3b8;font-weight:400;">— {{ $group['group_hint'] }}</span>
                @endif
                {{-- Count badge --}}
                <span class="pf-group-count"
                      style="font-size:.63rem;font-weight:700;background:rgba(15,143,131,.1);color:var(--primary-color);border-radius:20px;padding:1px 8px;">
                    {{ count($group['features']) }}
                </span>
                <i class="fas fa-chevron-down pf-section-arrow"></i>
            </div>

            {{-- Feature cards --}}
            <div class="pf-group-body">
                @foreach($group['features'] as $feat)
                @php
                    $saved    = $existingMap[$feat['key']] ?? null;
                    $isOn     = $saved ? (bool)($saved['enabled'] ?? true) : false;
                    $hasLimit = $feat['limit'] ?? false;
                    $limitVal = $saved['limit'] ?? null;
                @endphp

                <div class="pf-feature-card {{ $isOn ? 'is-on' : 'is-off' }}"
                     style="{{ $isOn ? 'border-color:'.$feat['border'].';background:'.$feat['bg'].';' : '' }}"
                     data-key="{{ $feat['key'] }}"
                     data-icon="{{ $feat['icon'] }}"
                     data-label="{{ $feat['label'] }}">

                    {{-- Icon bubble --}}
                    <div class="pf-icon-bubble"
                         style="background:{{ $feat['bg'] }};border:1.5px solid {{ $feat['border'] }};">
                        <i class="{{ $feat['icon'] }}" style="color:{{ $feat['color'] }};"></i>
                    </div>

                    {{-- Label + description --}}
                    <div class="pf-feature-meta">
                        <div class="pf-feature-label">{{ $feat['label'] }}</div>
                        <div class="pf-feature-desc">{{ $feat['description'] }}</div>
                    </div>

                    {{-- Limit input (registration_forms only) --}}
                    @if($hasLimit)
                    <div class="pf-limit-wrap" style="{{ $isOn ? '' : 'opacity:.4;pointer-events:none;' }}">
                        <span class="pf-limit-label">
                            <i class="fas fa-hashtag me-1"></i>{{ $feat['limit_label'] ?? 'الحد الأقصى' }}
                        </span>
                        <input type="number"
                               class="pf-limit-input feat-limit-input"
                               min="1"
                               placeholder="∞"
                               value="{{ $limitVal ?? '' }}"
                               title="{{ $feat['limit_hint'] ?? 'فارغ = غير محدود' }}">
                        <span class="pf-limit-hint">
                            <i class="fas fa-info-circle me-1"></i>{{ $feat['limit_hint'] ?? 'فارغ = غير محدود' }}
                        </span>
                    </div>
                    @endif

                    {{-- Toggle --}}
                    <div class="pf-toggle-wrap">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input feat-toggle"
                                   type="checkbox"
                                   role="switch"
                                   {{ $isOn ? 'checked' : '' }}>
                        </div>
                        <span class="pf-toggle-status"
                              style="color:{{ $isOn ? '#16a34a' : '#94a3b8' }};">
                            {{ $isOn ? 'مفعّل' : 'معطّل' }}
                        </span>
                    </div>

                </div>{{-- /.pf-feature-card --}}
                @endforeach
            </div>{{-- /.pf-group-body --}}

        </div>{{-- /.pf-group --}}
        @endforeach

        {{-- Legacy system checkboxes (hidden but still submitted for backward compat) --}}
        <input type="hidden" name="includes_csv_import"    id="legCsv_{{ $uid }}"    value="0">
        <input type="hidden" name="includes_bulk_resend"   id="legBulk_{{ $uid }}"   value="0">
        <input type="hidden" name="includes_customization" id="legCustom_{{ $uid }}"  value="0">

    </div>{{-- /.col-12 --}}

    {{-- ══ حالة الخطة ══════════════════════════════════════════════════════════ --}}
    <div class="col-12 mt-2">
        @php $isActiveChecked = old('is_active', $plan ? optional($plan)->is_active : true); @endphp
        <label for="active_{{ $uid }}" class="pf-status-toggle {{ $isActiveChecked ? 'is-active' : 'is-inactive' }}"
               id="statusToggleLabel_{{ $uid }}">

            {{-- Icon side --}}
            <div class="pf-status-icon" id="statusIcon_{{ $uid }}">
                <i class="{{ $isActiveChecked ? 'fas fa-eye' : 'fas fa-eye-slash' }}" id="statusIco_{{ $uid }}"></i>
            </div>

            {{-- Text side --}}
            <div class="pf-status-text">
                <span class="pf-status-title" id="statusTitle_{{ $uid }}">
                    {{ $isActiveChecked ? 'الخطة نشطة' : 'الخطة معطّلة' }}
                </span>
                <span class="pf-status-sub" id="statusSub_{{ $uid }}">
                    {{ $isActiveChecked ? 'تظهر للمشتركين ويمكن الاشتراك فيها' : 'مخفية ولا يمكن الاشتراك فيها' }}
                </span>
            </div>

            {{-- Toggle switch --}}
            <div class="pf-status-switch">
                <input type="checkbox" name="is_active" id="active_{{ $uid }}" value="1"
                       class="pf-status-checkbox"
                       {{ $isActiveChecked ? 'checked' : '' }}
                       onchange="pfStatusChange('{{ $uid }}', this.checked)">
                <span class="pf-status-knob"></span>
            </div>

        </label>
    </div>

    <style>
    .pf-status-toggle {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-radius: 14px;
        border: 2px solid #bbf7d0;
        background: linear-gradient(135deg, #f0fdf9 0%, #ecfdf5 100%);
        cursor: pointer;
        transition: border-color .25s, background .25s;
        user-select: none;
    }
    .pf-status-toggle.is-inactive {
        border-color: #e2e8f0;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .pf-status-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        background: #dcfce7;
        color: #16a34a;
        transition: background .25s, color .25s;
    }
    .pf-status-toggle.is-inactive .pf-status-icon {
        background: #f1f5f9;
        color: #94a3b8;
    }
    .pf-status-text { flex: 1; min-width: 0; }
    .pf-status-title {
        display: block;
        font-size: .9rem;
        font-weight: 800;
        color: #166534;
        transition: color .25s;
    }
    .pf-status-toggle.is-inactive .pf-status-title { color: #64748b; }
    .pf-status-sub {
        display: block;
        font-size: .73rem;
        color: #4ade80;
        margin-top: 2px;
        transition: color .25s;
    }
    .pf-status-toggle.is-inactive .pf-status-sub { color: #94a3b8; }

    /* Custom big toggle switch */
    .pf-status-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }
    .pf-status-checkbox {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .pf-status-knob {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #cbd5e1;
        transition: background .25s;
        cursor: pointer;
    }
    .pf-status-knob::after {
        content: '';
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
        transition: transform .25s;
    }
    .pf-status-checkbox:checked + .pf-status-knob {
        background: #22c55e;
    }
    .pf-status-checkbox:checked + .pf-status-knob::after {
        transform: translateX(-24px);
    }
    </style>

    <script>
    function pfStatusChange(uid, on) {
        var label = document.getElementById('statusToggleLabel_' + uid);
        var icon  = document.getElementById('statusIco_' + uid);
        var title = document.getElementById('statusTitle_' + uid);
        var sub   = document.getElementById('statusSub_' + uid);
        if (!label) return;
        label.classList.toggle('is-active',   on);
        label.classList.toggle('is-inactive', !on);
        icon.className  = on ? 'fas fa-eye' : 'fas fa-eye-slash';
        title.textContent = on ? 'الخطة نشطة' : 'الخطة معطّلة';
        sub.textContent   = on ? 'تظهر للمشتركين ويمكن الاشتراك فيها' : 'مخفية ولا يمكن الاشتراك فيها';
    }
    </script>

</div>{{-- /.row --}}


{{-- ════════════════════════════════════════════════════
     JAVASCRIPT — serialize features + interactions
════════════════════════════════════════════════════ --}}
<script>
(function() {

    var UID = '{{ $uid }}';

    // ── Collapse / expand group ───────────────────────────────────────────────
    window.pfToggleGroup = function(hdr) {
        var body = hdr.nextElementSibling;
        var collapsed = body.style.display === 'none';
        body.style.display = collapsed ? '' : 'none';
        hdr.classList.toggle('collapsed', !collapsed);
    };

    // ── Serialize all feature cards → JSON → hidden input ────────────────────
    function pfSerialize() {
        var jsonInput = document.getElementById('featFeaturesJson_' + UID);
        if (!jsonInput) return;

        var container = pfGetContainer();
        var cards = container.querySelectorAll('.pf-feature-card');

        var features = [];
        var csvOn    = false;
        var bulkOn   = false;

        cards.forEach(function(card) {
            var key     = card.getAttribute('data-key')   || '';
            var icon    = card.getAttribute('data-icon')  || 'fas fa-circle-check';
            var label   = card.getAttribute('data-label') || '';
            var toggle  = card.querySelector('.feat-toggle');
            var limitEl = card.querySelector('.feat-limit-input');
            var enabled = toggle ? toggle.checked : false;
            var limit   = null;
            if (limitEl && limitEl.value !== '' && parseInt(limitEl.value) > 0) {
                limit = parseInt(limitEl.value);
            }
            features.push({ key: key, label: label, icon: icon, enabled: enabled, limit: limit });

            // Sync legacy columns
            if (key === 'csv_import'   && enabled) csvOn  = true;
            if (key === 'bulk_resend'  && enabled) bulkOn = true;
        });

        jsonInput.value = JSON.stringify(features);

        // Sync legacy hidden inputs
        var legCsv   = document.getElementById('legCsv_'   + UID);
        var legBulk  = document.getElementById('legBulk_'  + UID);
        if (legCsv)  legCsv.value  = csvOn  ? '1' : '0';
        if (legBulk) legBulk.value = bulkOn ? '1' : '0';
    }

    // ── Find the modal container for this UID ────────────────────────────────
    function pfGetContainer() {
        // For edit modals the ID is e.g. "editPlanModal123"
        // For create modal the ID is "createPlanModal"
        var el = document.getElementById('editPlanModal' + UID)
             || document.getElementById('createPlanModal');
        return el || document;
    }

    // ── Wire up toggle + limit inputs ─────────────────────────────────────────
    function pfWireCards() {
        var container = pfGetContainer();
        container.querySelectorAll('.pf-feature-card').forEach(function(card) {
            // Skip already-wired cards to avoid duplicate listeners
            if (card.dataset.pfWired) return;
            card.dataset.pfWired = '1';

            var toggle   = card.querySelector('.feat-toggle');
            var statusLb = card.querySelector('.pf-toggle-status');
            var limitWrp = card.querySelector('.pf-limit-wrap');

            if (!toggle) return;

            // Toggle change
            toggle.addEventListener('change', function() {
                var on = toggle.checked;

                // Visual state
                card.classList.toggle('is-on',  on);
                card.classList.toggle('is-off', !on);

                // Border + background: read from data attributes
                var feat = getFeatMeta(card.getAttribute('data-key'));
                if (on && feat) {
                    card.style.borderColor = feat.border;
                    card.style.background  = feat.bg;
                } else {
                    card.style.borderColor = '#e2e8f0';
                    card.style.background  = '#fafafa';
                }

                // Status label
                if (statusLb) {
                    statusLb.textContent = on ? 'مفعّل' : 'معطّل';
                    statusLb.style.color = on ? '#16a34a' : '#94a3b8';
                }

                // Limit wrapper enable/disable
                if (limitWrp) {
                    limitWrp.style.opacity        = on ? '1' : '.4';
                    limitWrp.style.pointerEvents  = on ? '' : 'none';
                }

                pfSerialize();
            });

            // Limit input change
            if (limitWrp) {
                limitWrp.querySelector('.feat-limit-input')?.addEventListener('input', pfSerialize);
            }
        });

        // Initial serialize
        pfSerialize();
    }

    // Feature meta (border + bg) for re-styling on toggle
    var FEAT_META = {
        registration_forms : { border:'#bfdbfe', bg:'#eff6ff' },
        teams              : { border:'#ddd6fe', bg:'#f5f3ff' },
        visual_identity    : { border:'#fbcfe8', bg:'#fdf2f8' },
        event_header_image : { border:'#99f6e4', bg:'#f0fdf9' },
        event_footer_image : { border:'#bae6fd', bg:'#f0f9ff' },
    };
    function getFeatMeta(key) { return FEAT_META[key] || null; }

    // ── Init ──────────────────────────────────────────────────────────────────
    function pfInit() { pfWireCards(); }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(pfInit, 50);
    } else {
        document.addEventListener('DOMContentLoaded', pfInit);
    }

    // Re-init when modal opens (Bootstrap event)
    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target && (e.target.id === 'editPlanModal' + UID || e.target.id === 'createPlanModal')) {
            pfInit();
        }
    });

})();
</script>
