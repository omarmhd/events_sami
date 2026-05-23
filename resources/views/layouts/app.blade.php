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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

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

    /* ── Subscriber Footer (shared with public event footer) ───── */
    .event-footer {
        padding: 0 1rem 2.25rem;
    }

    .event-footer-inner {
        max-width: 980px;
        margin: 0 auto;
        padding: 1.15rem 1.25rem;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem 1.25rem;
        align-items: center;
        color: #4b5563;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid rgba(220, 232, 228, 0.92);
        border-radius: 22px;
        box-shadow: 0 18px 34px -28px rgba(10, 35, 37, 0.28);
        backdrop-filter: blur(10px);
    }

    .event-footer-branding {
        display: flex;
        align-items: center;
        gap: 0.95rem;
        flex-wrap: wrap;
    }

    .event-footer-brand-copy {
        display: grid;
        gap: 0.2rem;
    }

    .event-footer-brand-title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 600;
        color: #1f2937;
    }

    .event-footer-brand-subtitle {
        margin: 0;
        font-size: 0.88rem;
        line-height: 1.6;
        color: #6b7280;
    }

    .event-footer-stack {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.75rem 1rem;
        justify-self: end;
    }

    .event-footer-brand {
        display: inline-flex;
        align-items: center;
    }

    .event-footer-email,
    .event-footer-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        border-radius: 999px;
        padding: 0.55rem 0.95rem;
        font-size: 0.9rem;
        font-weight: 600;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .event-footer-email {
        color: var(--primary-color);
        background: rgba(15, 143, 131, 0.08);
        border: 1px solid rgba(15, 143, 131, 0.16);
    }

    .event-footer-email:hover,
    .event-footer-email:focus-visible {
        transform: translateY(-1px);
        color: var(--primary-color);
        background: rgba(15, 143, 131, 0.12);
        box-shadow: 0 8px 18px rgba(15, 143, 131, 0.08);
        outline: none;
    }

    .event-footer-link {
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(34, 34, 34, 0.08);
        color: var(--primary-color);
    }

    .event-footer-link:hover,
    .event-footer-link:focus-visible {
        transform: translateY(-1px);
        color: var(--primary-color);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
        outline: none;
    }

    .event-footer-email i {
        color: var(--primary-accent);
    }

    @media (max-width: 767px) {
        .event-footer {
            padding-bottom: 1.75rem;
        }

        .event-footer-inner {
            padding: 1rem 0.75rem;
            grid-template-columns: 1fr;
            text-align: center;
            border-radius: 18px;
        }

        .event-footer-email,
        .event-footer-link {
            width: 100%;
            justify-content: center;
        }

        .event-footer-branding,
        .event-footer-stack {
            justify-content: center;
            justify-self: center;
        }
    }

    </style>

    @stack('styles')
</head>

<body>
    @php
        $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
        $contactEmail = \App\Models\SystemSetting::get('support_email', '');
    @endphp
    <div class="mobile-header d-lg-none">
        {{-- Mobile header logo — always uses platform admin settings (SystemSetting).
             Tenant branding (CompanyBranding) is only for email templates. --}}
        <div class="d-flex align-items-center gap-2">
            <x-platform-logo size="sm" theme="light" class="mobile-platform-logo" />
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

    <footer class="event-footer">
        <div class="event-footer-inner">
            <div class="event-footer-branding">
                <div class="event-footer-brand">
                    <x-platform-logo size="sm" theme="light" />
                </div>

                <div class="event-footer-brand-copy">
                    <p class="event-footer-brand-title">مع تحيات منصة {{ $platformName }}</p>
                    <p class="event-footer-brand-subtitle">منصة موحدة لإدارة الدعوات، التذاكر، والحضور بطريقة أوضح وأبسط.</p>
                </div>
            </div>

            <div class="event-footer-stack">
                <a class="event-footer-link event-footer-link--soft" href="{{ route('platform.about') }}">
                    <i class="fas fa-circle-info"></i>
                    <span>تعرف على منصة معا</span>
                </a>

                @if(!empty($contactEmail))
                    <a class="event-footer-email" href="mailto:{{ $contactEmail }}">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $contactEmail }}</span>
                    </a>
                @endif
            </div>
        </div>
    </footer>

    {{-- ─── Global Modals Slot ─────────────────────────────────────────────
         All page-level modals pushed via @push('modals') render here —
         outside .page-wrap so backdrop-filter stacking context doesn't
         trap position:fixed Bootstrap modals.
    ──────────────────────────────────────────────────────────────────── --}}
    @stack('modals')

        {{-- ─── Global Confirm / Delete Modal ─────────────────────────────────
            Kept as a fallback when SweetAlert2 is unavailable.
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function appConfirmSubmit(form, method) {
            const submitForm = document.createElement('form');
            submitForm.method = 'POST';
            submitForm.action = form.action;
            submitForm.style.display = 'none';

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrf) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrf;
                submitForm.appendChild(csrfInput);
            }

            if (method && method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                submitForm.appendChild(methodInput);
            }

            document.body.appendChild(submitForm);
            submitForm.submit();
        }

        function appOpenConfirm({
            title = 'تأكيد',
            body = '',
            icon = 'circle-question',
            danger = false,
            confirmLabel = 'تأكيد',
            cancelLabel = 'إلغاء',
            onConfirm = null,
            formAction = null,
            formMethod = 'DELETE',
        } = {}) {
            const confirmColor = danger ? '#dc2626' : '#0f8f83';

            if (window.Swal && typeof Swal.fire === 'function') {
                return Swal.fire({
                    title,
                    html: body,
                    icon: danger ? 'warning' : icon,
                    showCancelButton: true,
                    confirmButtonText: confirmLabel,
                    cancelButtonText: cancelLabel,
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn rounded-pill px-4',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-4',
                    },
                    didOpen: () => {
                        const confirmButton = Swal.getConfirmButton();
                        if (confirmButton) {
                            confirmButton.style.backgroundColor = confirmColor;
                            confirmButton.style.borderColor = confirmColor;
                            confirmButton.style.boxShadow = '0 8px 22px rgba(15,143,131,.18)';
                        }
                    },
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    if (typeof onConfirm === 'function') {
                        onConfirm();
                        return;
                    }

                    if (formAction) {
                        const tempForm = document.createElement('form');
                        tempForm.method = 'POST';
                        tempForm.action = formAction;
                        tempForm.style.display = 'none';

                        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (csrf) {
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrf;
                            tempForm.appendChild(csrfInput);
                        }

                        if (formMethod && formMethod !== 'POST') {
                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = formMethod;
                            tempForm.appendChild(methodInput);
                        }

                        document.body.appendChild(tempForm);
                        tempForm.submit();
                    }
                });
            }

            const modal = document.getElementById('appConfirmModal');
            const iconWrap = document.getElementById('appConfirmIconWrap');
            const iconEl = document.getElementById('appConfirmIcon');
            const titleEl = document.getElementById('appConfirmTitle');
            const bodyEl = document.getElementById('appConfirmBody');
            const cancelBtn = document.getElementById('appConfirmCancelBtn');
            const okBtn = document.getElementById('appConfirmOkBtn');
            const form = document.getElementById('appConfirmForm');
            const methodInput = document.getElementById('appConfirmMethod');
            const submitBtn = document.getElementById('appConfirmSubmitBtn');

            if (!modal || !iconWrap || !iconEl || !titleEl || !bodyEl || !cancelBtn || !okBtn || !form || !methodInput || !submitBtn) {
                return;
            }

            titleEl.textContent = title;
            bodyEl.innerHTML = body;
            cancelBtn.textContent = cancelLabel;

            const color = danger ? 'var(--danger-color)' : 'var(--primary-color)';
            const bgWrap = danger ? 'rgba(179,38,30,.1)' : 'rgba(15,143,131,.1)';
            iconWrap.style.background = bgWrap;
            iconEl.className = `fas fa-${icon}`;
            iconEl.style.color = color;

            const btnCls = danger ? 'btn-danger' : 'btn-primary';

            if (formAction) {
                okBtn.classList.add('d-none');
                form.classList.remove('d-none');
                form.classList.add('d-inline');
                form.action = formAction;
                methodInput.value = formMethod;
                submitBtn.textContent = confirmLabel;
                submitBtn.className = `btn ${btnCls} rounded-pill px-4`;
                submitBtn.style.fontSize = '.875rem';
            } else {
                form.classList.add('d-none');
                form.classList.remove('d-inline');
                okBtn.classList.remove('d-none');
                okBtn.textContent = confirmLabel;
                okBtn.className = `btn ${btnCls} rounded-pill px-4`;
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
                return appOpenConfirm({ title, body, icon, danger, confirmLabel, cancelLabel, onConfirm, formAction, formMethod });
            }

            return { toast, confirm };
        })();

        document.addEventListener('submit', function (event) {
            const form = event.target.closest('form[data-confirm]');
            if (!form) {
                return;
            }

            event.preventDefault();

            AppUI.confirm({
                title: form.getAttribute('data-confirm-title') || 'تأكيد',
                body: form.getAttribute('data-confirm') || '',
                icon: form.getAttribute('data-confirm-icon') || 'circle-question',
                danger: form.getAttribute('data-confirm-danger') !== 'false',
                confirmLabel: form.getAttribute('data-confirm-ok') || 'تأكيد',
                cancelLabel: form.getAttribute('data-confirm-cancel') || 'إلغاء',
                formAction: form.action,
                formMethod: (form.querySelector('input[name="_method"]')?.value || 'POST').toUpperCase(),
            });
        }, true);

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-confirm], .js-confirm-action');
            if (!trigger || trigger.closest('form[data-confirm]')) {
                return;
            }

            const message = trigger.getAttribute('data-confirm') || '';
            if (!message) {
                return;
            }

            const title = trigger.getAttribute('data-confirm-title') || 'تأكيد';
            const icon = trigger.getAttribute('data-confirm-icon') || 'circle-question';
            const danger = trigger.getAttribute('data-confirm-danger') !== 'false';
            const confirmLabel = trigger.getAttribute('data-confirm-ok') || 'تأكيد';
            const cancelLabel = trigger.getAttribute('data-confirm-cancel') || 'إلغاء';

            const form = trigger.closest('form');
            const isSubmitControl = trigger.matches('button[type="submit"], input[type="submit"], button:not([type])');

            if (trigger.tagName === 'A' || form || isSubmitControl) {
                event.preventDefault();
                event.stopPropagation();
            }

            AppUI.confirm({
                title,
                body: message,
                icon,
                danger,
                confirmLabel,
                cancelLabel,
                formAction: form ? form.action : null,
                formMethod: form ? (form.querySelector('input[name="_method"]')?.value || 'POST').toUpperCase() : 'POST',
                onConfirm: !form && trigger.tagName === 'A' ? function () {
                    window.location.href = trigger.href;
                } : null,
            });
        }, true);

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