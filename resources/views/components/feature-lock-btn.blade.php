{{--
    feature-lock-btn  — Blade component
    ─────────────────────────────────────
    Shows a styled "locked" button. When clicked, navigates to the
    feature.unavailable page so the user can learn about upgrading.

    Props:
        $feature   (string)   — FeatureRegistry key, e.g. 'csv_import'
        $label     (string)   — Button text
        $icon      (string)   — FontAwesome class, e.g. 'fas fa-upload'
        $size      (string)   — 'sm' | 'md' (default 'md')
        $class     (string)   — Extra CSS classes on the button
        $style     (string)   — Inline style override
        $tooltip   (string)   — Custom tooltip text (optional)
        $block     (bool)     — Full-width button (default false)

    Usage:
        <x-feature-lock-btn feature="csv_import"
                            label="استيراد CSV"
                            icon="fas fa-upload" />

        <x-feature-lock-btn feature="bulk_resend"
                            label="إعادة الإرسال الجماعي"
                            icon="fas fa-repeat"
                            size="sm" />
--}}

@props([
    'feature'  => '',
    'label'    => 'ترقية الخطة',
    'icon'     => 'fas fa-lock',
    'size'     => 'md',
    'class'    => '',
    'style'    => '',
    'tooltip'  => null,
    'block'    => false,
])

@php
    $url     = route('feature.unavailable', ['feature' => $feature]);
    $tip     = $tooltip ?? 'هذه الميزة غير متاحة في خطتك — اضغط لمعرفة المزيد';
    $padX    = $size === 'sm' ? '10px' : '14px';
    $padY    = $size === 'sm' ? '5px'  : '9px';
    $fsize   = $size === 'sm' ? '.75rem' : '.84rem';
    $isize   = $size === 'sm' ? '.72rem' : '.82rem';
    $width   = $block ? 'width:100%;justify-content:center;' : '';
@endphp

<a href="{{ $url }}"
   class="flb-btn {{ $class }}"
   title="{{ $tip }}"
   style="display:inline-flex;align-items:center;gap:7px;
          padding:{{ $padY }} {{ $padX }};
          border-radius:8px;
          border:1.5px solid #e2e8f0;
          background:linear-gradient(135deg,#f8fafc,#f1f5f9);
          color:#64748b;
          font-size:{{ $fsize }};
          font-weight:700;
          text-decoration:none;
          cursor:pointer;
          transition:border-color .2s, background .2s, color .2s;
          {{ $width }}
          {{ $style }}">

    {{-- Lock badge --}}
    <span style="display:inline-flex;align-items:center;justify-content:center;
                 width:{{ $size === 'sm' ? '16px' : '20px' }};
                 height:{{ $size === 'sm' ? '16px' : '20px' }};
                 border-radius:50%;
                 background:rgba(245,158,11,.15);
                 color:#d97706;
                 font-size:{{ $isize }};
                 flex-shrink:0;">
        <i class="fas fa-lock"></i>
    </span>

    {{-- Feature icon --}}
    <i class="{{ $icon }}" style="font-size:{{ $isize }};opacity:.6;"></i>

    {{-- Label --}}
    <span>{{ $label }}</span>

    {{-- "Upgrade" chip --}}
    <span style="font-size:.62rem;font-weight:800;letter-spacing:.02em;
                 background:linear-gradient(135deg,#f59e0b,#d97706);
                 color:#fff;
                 padding:2px 7px;
                 border-radius:20px;
                 white-space:nowrap;
                 margin-right:2px;">
        ترقية
    </span>
</a>

<style>
.flb-btn:hover {
    border-color: #f59e0b !important;
    background: linear-gradient(135deg, #fffbeb, #fef3c7) !important;
    color: #92400e !important;
    text-decoration: none !important;
}
</style>
