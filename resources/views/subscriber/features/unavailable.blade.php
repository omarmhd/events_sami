@extends('layouts.app')

@php
    use App\Support\FeatureRegistry;

    $featureKey         = $featureKey ?? request('feature', 'unknown');
    $meta               = FeatureRegistry::get($featureKey);
    $featureLabel       = $meta['label']       ?? $featureKey;
    $featureIcon        = $meta['icon']        ?? 'fas fa-lock';
    $featureDescription = $meta['description'] ?? 'هذه الميزة غير متاحة في خطتك الحالية.';
    $featureCategory    = $meta['category']    ?? '';

    // Category label map
    $categoryLabels = [
        'core'          => 'ميزة أساسية',
        'communication' => 'تواصل',
        'analytics'     => 'تقارير وتحليل',
        'platform'      => 'ميزة منصة',
        'enterprise'    => 'مؤسسات',
    ];
    $categoryLabel = $categoryLabels[$featureCategory] ?? null;

    // Color per category / feature
    $colorMap = [
        'registration_forms' => ['color'=>'#2563eb','bg'=>'#eff6ff','border'=>'#bfdbfe','badge'=>'#dbeafe'],
        'teams'              => ['color'=>'#7c3aed','bg'=>'#f5f3ff','border'=>'#ddd6fe','badge'=>'#ede9fe'],
        'visual_identity'    => ['color'=>'#be185d','bg'=>'#fdf2f8','border'=>'#fbcfe8','badge'=>'#fce7f3'],
        'event_header_image' => ['color'=>'#0f6b63','bg'=>'#f0fdf9','border'=>'#99f6e4','badge'=>'#ccfbf1'],
        'event_footer_image' => ['color'=>'#0369a1','bg'=>'#f0f9ff','border'=>'#bae6fd','badge'=>'#e0f2fe'],
        'csv_import'         => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
        'bulk_resend'        => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
        'sms'                => ['color'=>'#0f8f83','bg'=>'#f0fdf9','border'=>'#99f6e4','badge'=>'#ccfbf1'],
        'whatsapp'           => ['color'=>'#16a34a','bg'=>'#f0fdf4','border'=>'#bbf7d0','badge'=>'#dcfce7'],
        'advanced_analytics' => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
        'export_reports'     => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
        'api_access'         => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
        'sso'                => ['color'=>'#374151','bg'=>'#f9fafb','border'=>'#e5e7eb','badge'=>'#f3f4f6'],
    ];
    $c = $colorMap[$featureKey] ?? ['color'=>'#0f8f83','bg'=>'#f0fdf9','border'=>'#99f6e4','badge'=>'#ccfbf1'];

    // What you gain bullets — per feature
    $benefits = [
        'registration_forms' => [
            'إنشاء نماذج تسجيل احترافية بحقول مخصصة',
            'ربط النماذج مباشرة بفعاليات متعددة',
            'تحديد عدد المسموح بهم وإدارة الطلبات',
            'متابعة التسجيلات بشكل فوري',
        ],
        'teams' => [
            'إضافة أعضاء فريق بأدوار وصلاحيات مختلفة',
            'تفويض إدارة الفعاليات لأعضاء محددين',
            'تتبع نشاط كل عضو في المنظومة',
        ],
        'visual_identity' => [
            'تخصيص شعار المنظمة في كل الإيميلات',
            'اختيار الألوان المطابقة لهويتك',
            'تخصيص اسم المُرسِل وعنوان البريد',
            'تجربة احترافية ومتسقة مع علامتك التجارية',
        ],
        'event_header_image' => [
            'رفع صورة رأس مخصصة لكل فعالية',
            'تعزيز الهوية البصرية لكل حدث',
            'تمييز فعالياتك بصورة جذابة',
        ],
        'event_footer_image' => [
            'رفع صورة تذييل مخصصة لكل فعالية',
            'إضافة ملاحظات بصرية أو شعارات في نهاية الإيميل',
        ],
        'csv_import' => [
            'رفع قوائم المدعوين بشكل جماعي دفعة واحدة',
            'توفير الوقت لدى إدارة فعاليات كبيرة',
            'دعم ملفات CSV بأعمدة مرنة',
        ],
        'bulk_resend' => [
            'إعادة إرسال الدعوات لمئات الأشخاص بضغطة واحدة',
            'تحديد مجموعات وإعادة الإرسال انتقائياً',
            'تعزيز معدل استجابة المدعوين',
        ],
    ];
    $featureBenefits = $benefits[$featureKey] ?? [
        'الوصول لإمكانيات متقدمة',
        'تحسين كفاءة إدارة الفعاليات',
        'تجربة أكثر احترافية',
    ];
@endphp

@section('title', 'الميزة غير متاحة — ' . $featureLabel)

@push('styles')
<style>
.fu-page {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 16px 60px;
    direction: rtl;
}

/* ── Two-column layout ── */
.fu-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    max-width: 860px;
    width: 100%;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 32px 80px -20px rgba(0,0,0,.18);
}
@media (max-width: 640px) {
    .fu-layout { grid-template-columns: 1fr; }
    .fu-left { display: none; }
}

/* ── Left decorative panel ── */
.fu-left {
    background: {{ $c['color'] }};
    padding: 52px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.fu-left::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.fu-left::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -40px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.fu-big-icon {
    width: 100px;
    height: 100px;
    border-radius: 24px;
    background: rgba(255,255,255,.15);
    border: 2px solid rgba(255,255,255,.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.6rem;
    color: #fff;
    margin: 0 auto 24px;
    position: relative;
    z-index: 1;
}
.fu-lock-chip {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    font-size: .75rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 100px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
}
.fu-left-title {
    color: #fff;
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}
.fu-left-sub {
    color: rgba(255,255,255,.80);
    font-size: .85rem;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

/* ── Right content panel ── */
.fu-right {
    background: #fff;
    padding: 48px 40px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.fu-eyebrow {
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: {{ $c['color'] }};
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.fu-right-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 10px;
    line-height: 1.35;
}
.fu-right-desc {
    font-size: .88rem;
    color: #475569;
    line-height: 1.7;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f5f9;
}

/* ── Benefits list ── */
.fu-benefits { margin-bottom: 28px; }
.fu-benefits-title {
    font-size: .72rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 12px;
}
.fu-benefit-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 8px;
    font-size: .84rem;
    color: #334155;
    line-height: 1.5;
}
.fu-benefit-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: {{ $c['bg'] }};
    border: 1.5px solid {{ $c['border'] }};
    color: {{ $c['color'] }};
    font-size: .6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}

/* ── Buttons ── */
.fu-btn-upgrade {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: {{ $c['color'] }};
    color: #fff;
    font-size: .92rem;
    font-weight: 700;
    padding: 13px 24px;
    border-radius: 12px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    margin-bottom: 10px;
}
.fu-btn-upgrade:hover {
    opacity: .88;
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
}
.fu-btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: transparent;
    color: #64748b;
    font-size: .85rem;
    font-weight: 600;
    padding: 11px 24px;
    border-radius: 12px;
    text-decoration: none;
    border: 1.5px solid #e2e8f0;
    transition: border-color .2s, color .2s;
}
.fu-btn-back:hover {
    border-color: #94a3b8;
    color: #334155;
    text-decoration: none;
}

.fu-contact-note {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
    font-size: .76rem;
    color: #94a3b8;
    text-align: center;
}
.fu-contact-note a {
    color: {{ $c['color'] }};
    font-weight: 600;
    text-decoration: none;
}
.fu-contact-note a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="fu-page">
    <div class="fu-layout">

        {{-- ── Left decorative panel ── --}}
        <div class="fu-left">
            {{-- Lock chip --}}
            <div class="fu-lock-chip">
                <i class="fas fa-lock"></i>
                غير متاح في خطتك
            </div>

            {{-- Big feature icon --}}
            <div class="fu-big-icon">
                <i class="{{ $featureIcon }}"></i>
            </div>

            {{-- Feature name --}}
            <div class="fu-left-title">{{ $featureLabel }}</div>
            <div class="fu-left-sub">{{ $featureDescription }}</div>

            {{-- Category badge --}}
            @if($categoryLabel)
            <div style="margin-top:20px;position:relative;z-index:1;">
                <span style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;
                             font-size:.68rem;font-weight:700;padding:4px 12px;border-radius:100px;">
                    {{ $categoryLabel }}
                </span>
            </div>
            @endif
        </div>

        {{-- ── Right content panel ── --}}
        <div class="fu-right">
            <div class="fu-eyebrow">
                <i class="fas fa-arrow-up-right-from-square"></i>
                ترقية الخطة مطلوبة
            </div>

            <h1 class="fu-right-title">افتح الوصول إلى<br>{{ $featureLabel }}</h1>

            <p class="fu-right-desc">
                {{ $featureDescription }}<br>
                <span style="color:#94a3b8;font-size:.82rem;">قم بترقية خطتك للاستفادة من هذه الميزة والكثير غيرها.</span>
            </p>

            {{-- Benefits list --}}
            <div class="fu-benefits">
                <div class="fu-benefits-title">ما الذي ستحصل عليه</div>
                @foreach($featureBenefits as $benefit)
                <div class="fu-benefit-item">
                    <div class="fu-benefit-dot"><i class="fas fa-check"></i></div>
                    <span>{{ $benefit }}</span>
                </div>
                @endforeach
            </div>

            {{-- CTA --}}
            <a href="{{ route('billing.upgrade') }}" class="fu-btn-upgrade">
                <i class="fas fa-arrow-up"></i>
                ترقية الخطة الآن
            </a>

            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard.index') }}"
               class="fu-btn-back">
                <i class="fas fa-arrow-right"></i>
                العودة للخلف
            </a>

            <div class="fu-contact-note">
                لديك سؤال؟
                <a href="{{ route('billing.contact-request') }}">تواصل مع فريق الدعم</a>
                — نحن هنا للمساعدة
            </div>
        </div>

    </div>
</div>
@endsection
