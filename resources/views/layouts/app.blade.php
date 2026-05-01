<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.platform_name') }} - @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">

    <link href="{{ asset('assets/saas-ui.css') }}" rel="stylesheet">

    {{-- ─── App Modal System — shared styles used by every modal in the app ─── --}}
    <style>
    /* ════════════════════════════════════════════════════════════
       App Modal System
       استخدم x-modal في كل صفحة لضمان مظهر موحد.
       ضع المودال دائماً في stack(modals) لا داخل section(content)
       لتجنب مشكلة backdrop-filter stacking context في .page-wrap
    ════════════════════════════════════════════════════════════ */

    /* ── Modal shell ──────────────────────────────────────────── */
    .app-modal-content {
        border-radius: var(--radius-lg);
        border: 1px solid var(--line);
        box-shadow:
            0 24px 56px -12px rgba(16,42,42,.18),
            0 4px 16px -4px rgba(0,0,0,.08);
        overflow: hidden;
    }

    /* ── Header ───────────────────────────────────────────────── */
    .app-modal-header {
        padding: 1.1rem 1.4rem;
        border-bottom: 1px solid var(--line);
        background: var(--surface-soft);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
    }
    .app-modal-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0 0 2px;
        line-height: 1.3;
    }
    .app-modal-subtitle {
        font-size: .78rem;
        color: var(--text-soft);
        margin: 0;
        line-height: 1.4;
    }

    /* ── Body + Footer defaults ───────────────────────────────── */
    .app-modal-content .modal-body {
        padding: 1.35rem 1.4rem;
    }
    .app-modal-content .modal-footer {
        padding: .9rem 1.4rem;
        border-top: 1px solid var(--line);
        background: var(--surface-soft);
        gap: .5rem;
    }

    /* ── Form fields inside modals ────────────────────────────── */
    .app-modal-field label {
        font-size: .8rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: .35rem;
        display: block;
    }
    .app-modal-field .form-control,
    .app-modal-field .form-select {
        background: var(--surface-soft);
        border: 1.5px solid var(--line);
        border-radius: var(--radius-sm);
        color: var(--text-main);
        font-size: .875rem;
        padding: .6rem .9rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .app-modal-field .form-control:focus,
    .app-modal-field .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15,143,131,.12);
        background: #fff;
    }

    /* ── Info banner inside modal ─────────────────────────────── */
    .app-modal-info-banner {
        background: rgba(15,143,131,.08);
        border: 1px solid rgba(15,143,131,.2);
        border-radius: var(--radius-md);
        padding: .75rem 1rem;
        color: var(--primary-color);
        font-size: .83rem;
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        margin-bottom: 1.1rem;
    }

    /* ── Global Confirm Modal icon ────────────────────────────── */
    .app-confirm-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto .9rem;
        font-size: 1.3rem;
        transition: background .25s, color .25s;
    }
    </style>

    @stack('styles')
</head>

<body>
    <div class="mobile-header d-lg-none">
        {{-- Mobile header logo — always uses platform admin settings (SystemSetting).
             Tenant branding (CompanyBranding) is only for email templates. --}}
        @php
            $mobileLogoUrl   = \App\Models\SystemSetting::get('platform_logo_url', '');
            $mobileBrandName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
        @endphp
        <div class="d-flex align-items-center gap-2">
            @if(!empty($mobileLogoUrl))
                <img src="{{ $mobileLogoUrl }}" alt="{{ $mobileBrandName }}"
                     style="height:24px;max-width:110px;object-fit:contain;">
            @else
                <div class="brand-text">{{ $mobileBrandName }}</div>
            @endif
        </div>
        <button type="button" onclick="toggleSidebar()" aria-label="{{ __('ui.mobile.toggle_navigation') }}">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    <div class="container-fluid app-shell">
        <div class="row g-4">
            <aside class="col-lg-3 col-xl-2 sidebar-col" id="sidebarArea">
                @include('layouts.partials.sidebar')
            </aside>

            <main class="col-lg-9 col-xl-10 app-main">
                <div class="page-wrap animate__animated animate__fadeIn">
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

    {{-- ─── Global Confirm / Delete Modal ─────────────────────────────────
         Controlled entirely via AppUI.confirm({ ... }) — no per-page HTML needed.
    ──────────────────────────────────────────────────────────────────── --}}
    <div class="modal fade app-modal" id="appConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content app-modal-content">

                <div class="modal-body p-4 text-center">
                    <div class="app-confirm-icon" id="appConfirmIconWrap">
                        <i id="appConfirmIcon" class="fas fa-circle-question"></i>
                    </div>
                    <h6 class="fw-bold mb-2" id="appConfirmTitle" style="color:var(--text-main);font-size:.95rem;"></h6>
                    <p class="mb-0" id="appConfirmBody" style="font-size:.84rem;color:var(--text-soft);line-height:1.5;"></p>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 justify-content-center gap-2">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-pill px-4"
                            style="font-size:.875rem;"
                            data-bs-dismiss="modal"
                            id="appConfirmCancelBtn">إلغاء</button>

                    {{-- Callback mode (no form needed) --}}
                    <button type="button"
                            class="btn rounded-pill px-4"
                            style="font-size:.875rem;"
                            id="appConfirmOkBtn">تأكيد</button>

                    {{-- Form mode (DELETE / POST / PATCH) --}}
                    <form id="appConfirmForm" method="POST" class="d-none d-inline">
                        @csrf
                        <input type="hidden" name="_method" id="appConfirmMethod" value="DELETE">
                        <button type="submit"
                                class="btn rounded-pill px-4"
                                style="font-size:.875rem;"
                                id="appConfirmSubmitBtn">تأكيد</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarArea');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar || !backdrop) {
                return;
            }

            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebarArea');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (!sidebar || !backdrop) {
                return;
            }

            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });

        // ════════════════════════════════════════════════════════
        //  AppUI — Global UI helpers (toast + confirm modal)
        //  استخدم AppUI.toast() و AppUI.confirm() من أي صفحة
        // ════════════════════════════════════════════════════════
        window.AppUI = (function () {

            // ── Toast styles map ─────────────────────────────────
            const _styles = {
                success : { bg: 'linear-gradient(135deg,#10b981 0%,#34d399 100%)', shadow: 'rgba(16,185,129,.4)' },
                error   : { bg: 'linear-gradient(135deg,#f43f5e 0%,#fb7185 100%)', shadow: 'rgba(244,63,94,.4)'  },
                warning : { bg: 'linear-gradient(135deg,#f59e0b 0%,#fbbf24 100%)', shadow: 'rgba(245,158,11,.4)' },
                info    : { bg: 'linear-gradient(135deg,#0ea5e9 0%,#38bdf8 100%)', shadow: 'rgba(14,165,233,.4)'  },
            };

            const _base = { duration: 3500, gravity: 'top', position: 'center', close: true, stopOnFocus: true };

            // ── AppUI.toast(msg, type) ────────────────────────────
            function toast(message, type = 'success') {
                const s = _styles[type] || _styles.success;
                Toastify({
                    text: message,
                    ..._base,
                    style: {
                        background: s.bg,
                        borderRadius: '12px',
                        boxShadow: `0 10px 25px ${s.shadow}`,
                        fontWeight: '600',
                    },
                }).showToast();
            }

            // ── AppUI.confirm({ ... }) ────────────────────────────
            //  title        : string   — عنوان التأكيد
            //  body         : string   — نص HTML التوضيحي
            //  icon         : string   — FontAwesome icon name (بدون fa-)
            //  danger       : bool     — زر أحمر بدلاً من الأخضر
            //  confirmLabel : string   — نص زر التأكيد
            //  cancelLabel  : string   — نص زر الإلغاء
            //  onConfirm    : function — callback (بدلاً من إرسال form)
            //  formAction   : string   — URL لإرسال form تلقائياً
            //  formMethod   : string   — DELETE | POST | PATCH (default: DELETE)
            function confirm({
                title        = 'تأكيد',
                body         = '',
                icon         = 'circle-question',
                danger       = false,
                confirmLabel = 'تأكيد',
                cancelLabel  = 'إلغاء',
                onConfirm    = null,
                formAction   = null,
                formMethod   = 'DELETE',
            } = {}) {
                const modal       = document.getElementById('appConfirmModal');
                const iconWrap    = document.getElementById('appConfirmIconWrap');
                const iconEl      = document.getElementById('appConfirmIcon');
                const titleEl     = document.getElementById('appConfirmTitle');
                const bodyEl      = document.getElementById('appConfirmBody');
                const cancelBtn   = document.getElementById('appConfirmCancelBtn');
                const okBtn       = document.getElementById('appConfirmOkBtn');
                const form        = document.getElementById('appConfirmForm');
                const methodInput = document.getElementById('appConfirmMethod');
                const submitBtn   = document.getElementById('appConfirmSubmitBtn');

                // Content
                titleEl.textContent   = title;
                bodyEl.innerHTML      = body;
                cancelBtn.textContent = cancelLabel;

                // Icon + color
                const color  = danger ? 'var(--danger-color)' : 'var(--primary-color)';
                const bgWrap = danger ? 'rgba(179,38,30,.1)'  : 'rgba(15,143,131,.1)';
                iconWrap.style.background = bgWrap;
                iconEl.className  = `fas fa-${icon}`;
                iconEl.style.color = color;

                const btnCls = danger ? 'btn-danger' : 'btn-primary';

                if (formAction) {
                    // Form mode
                    okBtn.classList.add('d-none');
                    form.classList.remove('d-none');
                    form.classList.add('d-inline');
                    form.action       = formAction;
                    methodInput.value = formMethod;
                    submitBtn.textContent = confirmLabel;
                    submitBtn.className   = `btn ${btnCls} rounded-pill px-4`;
                    submitBtn.style.fontSize = '.875rem';
                } else {
                    // Callback mode — clone to clear old listeners
                    form.classList.add('d-none');
                    form.classList.remove('d-inline');
                    okBtn.classList.remove('d-none');
                    okBtn.textContent = confirmLabel;
                    okBtn.className   = `btn ${btnCls} rounded-pill px-4`;
                    okBtn.style.fontSize = '.875rem';
                    const fresh = okBtn.cloneNode(true);
                    okBtn.parentNode.replaceChild(fresh, okBtn);
                    if (onConfirm) {
                        fresh.addEventListener('click', function () {
                            bootstrap.Modal.getInstance(modal)?.hide();
                            onConfirm();
                        });
                    }
                }

                new bootstrap.Modal(modal).show();
            }

            return { toast, confirm };
        })();

        // ─── Flash messages via AppUI.toast ─────────────────────
        @if(session('success'))
        AppUI.toast(@json(session('success')), 'success');
        @endif

        @if(session('error'))
        AppUI.toast(@json(session('error')), 'error');
        @endif

        @if(session('warning'))
        AppUI.toast(@json(session('warning')), 'warning');
        @endif

        @if($errors->any())
        @foreach($errors->all() as $error)
        AppUI.toast(@json($error), 'error');
        @endforeach
        @endif
    </script>

    @stack('scripts')
</body>

</html>