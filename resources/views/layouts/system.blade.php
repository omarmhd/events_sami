<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')) }} System - @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="{{ asset('assets/saas-ui.css') }}" rel="stylesheet">

    {{-- ══════════════════════════════════════════════════════════════
         System Admin — Unified Design Tokens
         جميع الصفحات ترث هذه الأصناف تلقائياً.
         ضع CSS الخاص بالصفحة فقط في @push('styles').
    ══════════════════════════════════════════════════════════════ --}}
    <style>
    /* ── هيكل الصفحة ─────────────────────────────────────────────── */
    .page-header {
        display: flex; justify-content: space-between;
        align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;
    }

    /* ── البطاقات ────────────────────────────────────────────────── */
    .custom-card {
        background: #fff; border-radius: 24px;
        border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden;
    }
    .search-card {
        background: #fff; border-radius: 16px;
        border: 1px solid #f1f5f9; box-shadow: var(--shadow-card);
        padding: 1.1rem 1.4rem; margin-bottom: 1.25rem;
    }

    /* ── الجدول الموحّد ──────────────────────────────────────────── */
    .table-custom thead th {
        background: #f8fafc; color: var(--text-muted); font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;
        padding: 14px 16px; border-bottom: 1px solid #e2e8f0;
    }
    .table-custom tbody td {
        padding: 14px 16px; vertical-align: middle;
        border-bottom: 1px solid #f1f5f9; font-size: 0.875rem;
    }
    .table-custom tbody tr:last-child td { border-bottom: none; }
    .table-custom tbody tr:hover td { background: #fafcfb; }

    /* ── شارات الحالة ────────────────────────────────────────────── */
    .badge-soft    { padding: 5px 10px; border-radius: 7px; font-size: 0.73rem; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #166534; }
    .badge-warning { background: #fef9c3; color: #854d0e; }
    .badge-danger  { background: #fee2e2; color: #991b1b; }
    .badge-neutral { background: #f1f5f9; color: #64748b; }
    .badge-info    { background: #e0f2fe; color: #075985; }
    .badge-purple  { background: #f5f3ff; color: #6d28d9; }

    /* ── زر الحفظ / الإجراء الرئيسي ─────────────────────────────── */
    .btn-save {
        background: var(--grad-primary); color: #fff; border: none;
        padding: 9px 22px; border-radius: 10px; font-weight: 600;
        font-size: 0.85rem; transition: .3s; cursor: pointer;
        box-shadow: 0 4px 12px rgba(15,143,131,.3);
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(15,143,131,.4); color: #fff; }

    /* ── أزرار الإجراءات في صفوف الجدول ─────────────────────────── */
    .btn-action {
        border: none; padding: 5px 13px; border-radius: 8px;
        font-size: 0.78rem; font-weight: 600; transition: .2s;
        cursor: pointer; white-space: nowrap;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-action-edit        { background: #eff6ff; color: #1d4ed8; }
    .btn-action-edit:hover  { background: #bfdbfe; color: #1d4ed8; }
    .btn-action-plan        { background: #f5f3ff; color: #6d28d9; }
    .btn-action-plan:hover  { background: #ddd6fe; color: #6d28d9; }
    .btn-action-paid        { background: #dcfce7; color: #166534; }
    .btn-action-paid:hover  { background: #bbf7d0; color: #166534; }
    .btn-action-activate    { background: #dcfce7; color: #166534; }
    .btn-action-activate:hover { background: #86efac; color: #166534; }
    .btn-action-danger      { background: #fee2e2; color: #991b1b; }
    .btn-action-danger:hover { background: #fca5a5; color: #991b1b; }
    .btn-action-warning     { background: #fef9c3; color: #854d0e; }
    .btn-action-warning:hover { background: #fde68a; color: #854d0e; }
    .btn-action-impersonate { background: #1e293b; color: #f1f5f9; }
    .btn-action-impersonate:hover { background: #334155; color: #f1f5f9; }

    /* ── حقول النماذج ────────────────────────────────────────────── */
    .field-label { font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 5px; display: block; }
    .field-hint  { font-size: 0.73rem; color: var(--text-soft); margin-top: 3px; display: block; }
    .form-control,
    .form-select {
        background: #f8fafc; border: 1.5px solid #e2e8f0;
        border-radius: 10px; font-size: 0.875rem; padding: 0.55rem 0.9rem;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15,143,131,.1);
        background: #fff; outline: none;
    }
    .section-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--text-soft);
        margin-bottom: 0.75rem; display: flex; align-items: center; gap: 8px;
    }
    .section-label::after { content: ''; flex: 1; height: 1px; background: #f1f5f9; }

    /* ── بطاقات KPI المصغّرة (مشتركة بين عدة صفحات) ─────────────── */
    .mini-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
    .mini-kpi { background: #fff; border-radius: 16px; border: 1px solid #f1f5f9; padding: 1rem 1.25rem; box-shadow: var(--shadow-card); }
    .mini-kpi-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 0.5rem; }
    .mini-kpi-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); }
    .mini-kpi-val   { font-size: 1.8rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }

    /* ── استجابة الشاشات الصغيرة ─────────────────────────────────── */
    @media (max-width: 768px) {
        .mini-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    </style>

    @stack('styles')
</head>
<body>
<div class="mobile-header d-lg-none">
    <div class="brand-text text-white">Maan<span>Invite</span></div>
    <button type="button" onclick="toggleSidebar()" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
    </button>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

<div class="container-fluid app-shell">
    <div class="row g-4">
        <aside class="col-lg-3 col-xl-2 sidebar-col" id="sidebarArea">
            @include('layouts.partials.system-sidebar')
        </aside>
        <main class="col-lg-9 col-xl-10 app-main">
            <div class="page-wrap">
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- ─── Global Modals Slot ─────────────────────────────────────────────
     All page-level modals pushed via @push('modals') render here —
     outside .page-wrap so backdrop-filter stacking context doesn't
     trap position:fixed Bootstrap modals.
──────────────────────────────────────────────────────────────────── --}}
@stack('modals')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
@stack('scripts')
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarArea');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !backdrop) return;
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
    }
    function closeSidebar() {
        const sidebar = document.getElementById('sidebarArea');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar || !backdrop) return;
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
    }
    const _toastBase = { duration: 3500, gravity: 'top', position: 'center', close: true, stopOnFocus: true };
    const _toastStyles = {
        success : { background: 'linear-gradient(135deg,#10b981,#34d399)', boxShadow: '0 10px 25px rgba(16,185,129,.4)' },
        error   : { background: 'linear-gradient(135deg,#f43f5e,#fb7185)', boxShadow: '0 10px 25px rgba(244,63,94,.4)'  },
        warning : { background: 'linear-gradient(135deg,#f59e0b,#fbbf24)', boxShadow: '0 10px 25px rgba(245,158,11,.4)' },
    };
    function sysToast(msg, type = 'success') {
        Toastify({ text: msg, ..._toastBase, style: { ..._toastStyles[type], borderRadius: '12px', fontWeight: '600' } }).showToast();
    }
    @if(session('success')) sysToast(@json(session('success')), 'success'); @endif
    @if(session('error'))   sysToast(@json(session('error')),   'error');   @endif
    @if(session('warning')) sysToast(@json(session('warning')), 'warning'); @endif
    @if($errors->any())
    @foreach($errors->all() as $err) sysToast(@json($err), 'error'); @endforeach
    @endif
</script>
</body>
</html>

