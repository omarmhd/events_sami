@extends('layouts.app')

@section('title', __('ui.account.title'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   Account Settings — متوافق مع نظام التصميم (saas-ui.css)
══════════════════════════════════════════════════════════════ */

/* ── Tabs Nav ─────────────────────────────────────────────── */
.account-tabs {
    display:flex; flex-wrap:wrap; gap:.5rem;
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--radius-lg);
    padding:.5rem;
    margin-bottom:1.5rem;
    box-shadow:var(--shadow-soft);
}
.account-tab-link {
    flex:1 1 auto; min-width:140px;
    display:inline-flex; align-items:center; justify-content:center; gap:.45rem;
    padding:.7rem 1rem;
    border-radius:var(--radius-md);
    border:1px solid transparent;
    background:transparent;
    color:var(--text-soft);
    font-size:.85rem; font-weight:600;
    cursor:pointer; user-select:none;
    transition:all .2s ease;
}
.account-tab-link i { font-size:.85rem; }
.account-tab-link:hover {
    background:var(--surface-soft);
    color:var(--text-main);
}
.account-tab-link.active {
    background:linear-gradient(135deg, rgba(15,143,131,.12), rgba(15,118,110,.08));
    color:var(--primary-color);
    border-color:rgba(15,143,131,.25);
    box-shadow:0 2px 6px rgba(15,143,131,.08);
}

/* ── Card per section ─────────────────────────────────────── */
.account-card {
    background:#fff;
    border:1px solid var(--line);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow-soft);
    overflow:hidden;
    margin-bottom:1.5rem;
}
.account-card-header {
    padding:1.1rem 1.4rem;
    border-bottom:1px solid var(--line);
    background:var(--surface-soft);
}
.account-card-title {
    margin:0 0 .2rem;
    font-size:.95rem; font-weight:700;
    color:var(--text-main);
    display:flex; align-items:center; gap:.55rem;
}
.account-card-title i {
    width:30px; height:30px; border-radius:9px;
    background:var(--primary-soft); color:var(--primary-color);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.85rem;
}
.account-card-subtitle {
    margin:0;
    font-size:.78rem; color:var(--text-soft);
    line-height:1.55;
}
.account-card-body { padding:1.35rem 1.4rem; }
.account-card-footer {
    padding:.9rem 1.4rem;
    border-top:1px solid var(--line);
    background:var(--surface-soft);
    display:flex; justify-content:flex-end; gap:.5rem;
}

/* ── Forms ────────────────────────────────────────────────── */
.account-form .form-label {
    font-size:.78rem; font-weight:700;
    color:var(--text-main);
    margin-bottom:.35rem;
}
.account-form .form-control,
.account-form .form-select {
    border:1px solid var(--line);
    border-radius:var(--radius-sm);
    background:var(--surface-soft);
    font-size:.86rem;
    padding:.55rem .75rem;
}
.account-form .form-control:focus,
.account-form .form-select:focus {
    border-color:var(--primary-color);
    box-shadow:0 0 0 .15rem rgba(15,143,131,.15);
    background:#fff;
}
.account-hint {
    font-size:.72rem;
    color:var(--text-soft);
    margin-top:.35rem;
    line-height:1.5;
}
.account-hint i { margin-inline-end:.25rem; }

.subdomain-group .input-group-text {
    background:var(--surface-muted);
    border:1px solid var(--line);
    color:var(--text-soft);
    font-size:.78rem;
    direction:ltr;
}

/* ── Buttons ──────────────────────────────────────────────── */
.btn-account-save {
    background:linear-gradient(135deg, var(--primary-color), #0f766e);
    color:#fff; border:none;
    border-radius:99px;
    padding:.55rem 1.4rem;
    font-size:.82rem; font-weight:700;
    transition:all .2s ease;
    box-shadow:0 4px 12px rgba(15,143,131,.2);
}
.btn-account-save:hover {
    transform:translateY(-1px);
    box-shadow:0 8px 20px rgba(15,143,131,.3);
    color:#fff;
}

/* ── Owner-only banner ────────────────────────────────────── */
.account-owner-lock {
    display:flex; align-items:flex-start; gap:.75rem;
    background:rgba(245,158,11,.08);
    border:1px solid rgba(245,158,11,.2);
    color:#92400e;
    border-radius:var(--radius-md);
    padding:.85rem 1rem;
    font-size:.8rem;
    line-height:1.55;
}
.account-owner-lock i { color:var(--warning-color); font-size:1rem; margin-top:.1rem; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width:575.98px) {
    .account-tab-link { min-width:0; flex:1 1 33%; padding:.6rem .35rem; font-size:.78rem; }
    .account-tab-link span { display:none; }
    .account-tab-link i { font-size:1rem; }
    .account-card-body { padding:1rem; }
    .account-card-header, .account-card-footer { padding:.85rem 1rem; }
}
</style>
@endpush

@section('content')

@php
    // التبويب النشط — يأتي إما من الكويري ?tab= أو من رسالة تحويل سابقة
    $activeTab = request()->query('tab', session('active_tab', 'profile'));
    $allowedTabs = ['profile', 'security', 'company'];
    if (!in_array($activeTab, $allowedTabs, true)) {
        $activeTab = 'profile';
    }
    if (!$isOwner && $activeTab === 'company') {
        $activeTab = 'profile';
    }
@endphp

{{-- ══════════════════════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════════════════ --}}
<div class="app-page-hero animate__animated animate__fadeInDown">
    <div class="app-page-hero-content d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div>
            <div class="app-page-hero-kicker">
                <i class="fas fa-user-gear me-2"></i>{{ __('ui.account.kicker') }}
            </div>
            <h1 class="app-page-hero-title">{{ __('ui.account.title') }}</h1>
            <p class="app-page-hero-subtitle">{{ __('ui.account.subtitle') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:99px;padding:.35rem 1rem;font-size:.8rem;font-weight:600;color:#fff;">
                <i class="fas fa-envelope me-1"></i>{{ $user->email }}
            </span>
        </div>
    </div>
</div>

{{-- ── Flash messages ─────────────────────────────────────── --}}
@if(session('success'))
<div class="alert alert-success border-0 rounded-3 mb-3 animate__animated animate__fadeIn"
     style="background:rgba(16,185,129,.1);color:#065f46;border:1px solid rgba(16,185,129,.25) !important;">
    <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger border-0 rounded-3 mb-3 animate__animated animate__fadeIn"
     style="background:rgba(179,38,30,.08);color:var(--danger-color);border:1px solid rgba(179,38,30,.2) !important;">
    <i class="fas fa-circle-exclamation me-2"></i>{{ $errors->first() }}
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     TABS NAVIGATION
══════════════════════════════════════════════════════════ --}}
<div class="account-tabs animate__animated animate__fadeInUp" role="tablist">
    <button type="button"
            class="account-tab-link {{ $activeTab === 'profile' ? 'active' : '' }}"
            data-tab="profile"
            role="tab" aria-selected="{{ $activeTab === 'profile' ? 'true' : 'false' }}">
        <i class="fas fa-id-badge"></i>
        <span>{{ __('ui.account.tabs.profile') }}</span>
    </button>
    <button type="button"
            class="account-tab-link {{ $activeTab === 'security' ? 'active' : '' }}"
            data-tab="security"
            role="tab" aria-selected="{{ $activeTab === 'security' ? 'true' : 'false' }}">
        <i class="fas fa-shield-halved"></i>
        <span>{{ __('ui.account.tabs.security') }}</span>
    </button>
    @if($isOwner)
    <button type="button"
            class="account-tab-link {{ $activeTab === 'company' ? 'active' : '' }}"
            data-tab="company"
            role="tab" aria-selected="{{ $activeTab === 'company' ? 'true' : 'false' }}">
        <i class="fas fa-building"></i>
        <span>{{ __('ui.account.tabs.company') }}</span>
    </button>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     PANE: PROFILE
══════════════════════════════════════════════════════════ --}}
<div class="account-pane animate__animated animate__fadeIn"
     data-pane="profile"
     style="display: {{ $activeTab === 'profile' ? 'block' : 'none' }};">

    <div class="account-card">
        <div class="account-card-header">
            <h2 class="account-card-title">
                <i class="fas fa-user"></i>
                {{ __('ui.account.sections.profile_title') }}
            </h2>
            <p class="account-card-subtitle">{{ __('ui.account.sections.profile_subtitle') }}</p>
        </div>

        <form action="{{ route('account.profile.update') }}" method="POST" class="account-form">
            @csrf
            @method('PATCH')

            <div class="account-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="acc_name">{{ __('ui.account.field.name') }}</label>
                        <input type="text" id="acc_name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required maxlength="120">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_phone">{{ __('ui.account.field.phone') }}</label>
                        <input type="text" id="acc_phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" maxlength="30"
                               placeholder="+966 5X XXX XXXX">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="account-card-footer">
                <button type="submit" class="btn-account-save">
                    <i class="fas fa-floppy-disk me-1"></i>
                    {{ __('ui.account.buttons.save_profile') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     PANE: SECURITY (Email + Password)
══════════════════════════════════════════════════════════ --}}
<div class="account-pane animate__animated animate__fadeIn"
     data-pane="security"
     style="display: {{ $activeTab === 'security' ? 'block' : 'none' }};">

    {{-- ─── Email card ──────────────────────────────────── --}}
    <div class="account-card">
        <div class="account-card-header">
            <h2 class="account-card-title">
                <i class="fas fa-envelope"></i>
                {{ __('ui.account.sections.email_title') }}
            </h2>
            <p class="account-card-subtitle">{{ __('ui.account.sections.email_subtitle') }}</p>
        </div>

        <form action="{{ route('account.email.update') }}" method="POST" class="account-form">
            @csrf
            @method('PATCH')

            <div class="account-card-body">
                <div class="mb-3">
                    <label class="form-label">{{ app()->getLocale() === 'ar' ? 'البريد الحالي' : 'Current email' }}</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled readonly>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="acc_email_new">{{ __('ui.account.field.email') }}</label>
                        <input type="email" id="acc_email_new" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required maxlength="160" autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_email_confirm">{{ __('ui.account.field.email_confirmation') }}</label>
                        <input type="email" id="acc_email_confirm" name="email_confirmation"
                               class="form-control @error('email_confirmation') is-invalid @enderror"
                               value="{{ old('email_confirmation') }}" required maxlength="160">
                        @error('email_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_email_pw">{{ __('ui.account.field.current_password') }}</label>
                        <input type="password" id="acc_email_pw" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="account-hint mt-3">
                    <i class="fas fa-circle-info"></i>{{ __('ui.account.hints.email_change_warning') }}
                </div>
            </div>

            <div class="account-card-footer">
                <button type="submit" class="btn-account-save">
                    <i class="fas fa-paper-plane me-1"></i>
                    {{ __('ui.account.buttons.save_email') }}
                </button>
            </div>
        </form>
    </div>

    {{-- ─── Password card ──────────────────────────────── --}}
    <div class="account-card">
        <div class="account-card-header">
            <h2 class="account-card-title">
                <i class="fas fa-key"></i>
                {{ __('ui.account.sections.password_title') }}
            </h2>
            <p class="account-card-subtitle">{{ __('ui.account.sections.password_subtitle') }}</p>
        </div>

        <form action="{{ route('account.password.update') }}" method="POST" class="account-form">
            @csrf
            @method('PATCH')

            <div class="account-card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label" for="acc_pw_current">{{ __('ui.account.field.current_password') }}</label>
                        <input type="password" id="acc_pw_current" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_pw_new">{{ __('ui.account.field.new_password') }}</label>
                        <input type="password" id="acc_pw_new" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required minlength="8" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_pw_confirm">{{ __('ui.account.field.new_password_confirm') }}</label>
                        <input type="password" id="acc_pw_confirm" name="password_confirmation"
                               class="form-control"
                               required minlength="8" autocomplete="new-password">
                    </div>
                </div>

                <div class="account-hint mt-3">
                    <i class="fas fa-shield-halved"></i>{{ __('ui.account.hints.password_min') }}
                </div>
            </div>

            <div class="account-card-footer">
                <button type="submit" class="btn-account-save">
                    <i class="fas fa-lock me-1"></i>
                    {{ __('ui.account.buttons.save_password') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     PANE: COMPANY (Owner only)
══════════════════════════════════════════════════════════ --}}
@if($isOwner && $company)
<div class="account-pane animate__animated animate__fadeIn"
     data-pane="company"
     style="display: {{ $activeTab === 'company' ? 'block' : 'none' }};">

    <div class="account-card">
        <div class="account-card-header">
            <h2 class="account-card-title">
                <i class="fas fa-building"></i>
                {{ __('ui.account.sections.company_title') }}
            </h2>
            <p class="account-card-subtitle">{{ __('ui.account.sections.company_subtitle') }}</p>
        </div>

        <form action="{{ route('account.company.update') }}" method="POST" class="account-form">
            @csrf
            @method('PATCH')

            <div class="account-card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="acc_company_name">{{ __('ui.account.field.company_name') }}</label>
                        <input type="text" id="acc_company_name" name="company_name"
                               class="form-control @error('company_name') is-invalid @enderror"
                               value="{{ old('company_name', $company->name) }}" required maxlength="160">
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="acc_company_phone">{{ __('ui.account.field.company_phone') }}</label>
                        <input type="text" id="acc_company_phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $company->phone) }}" maxlength="30"
                               placeholder="+966 11 XXX XXXX">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_company_email">{{ __('ui.account.field.company_email') }}</label>
                        <input type="email" id="acc_company_email" name="contact_email"
                               class="form-control @error('contact_email') is-invalid @enderror"
                               value="{{ old('contact_email', $company->contact_email) }}" maxlength="160">
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="acc_timezone">{{ __('ui.account.field.timezone') }}</label>
                        <input type="text" id="acc_timezone" name="timezone"
                               class="form-control @error('timezone') is-invalid @enderror"
                               value="{{ old('timezone', $company->timezone ?: 'Asia/Riyadh') }}" maxlength="64">
                        @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label" for="acc_subdomain">{{ __('ui.account.field.subdomain') }}</label>
                        <div class="input-group subdomain-group">
                            <input type="text" id="acc_subdomain" name="subdomain"
                                   class="form-control @error('subdomain') is-invalid @enderror"
                                   value="{{ old('subdomain', $company->subdomain) }}"
                                   required minlength="3" maxlength="40"
                                   pattern="[a-zA-Z0-9_\-]+"
                                   style="direction:ltr;">
                            <span class="input-group-text">{{ __('ui.account.hints.subdomain_suffix') }}</span>
                            @error('subdomain')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="account-card-footer">
                <button type="submit" class="btn-account-save">
                    <i class="fas fa-floppy-disk me-1"></i>
                    {{ __('ui.account.buttons.save_company') }}
                </button>
            </div>
        </form>
    </div>
</div>
@elseif(!$isOwner)
<div class="account-pane" data-pane="company" style="display:none;">
    <div class="account-owner-lock">
        <i class="fas fa-lock"></i>
        <div>{{ __('ui.account.errors.owner_only_hint') }}</div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     TABS BEHAVIOR
══════════════════════════════════════════════════════════ --}}
<script>
(function () {
    const tabs  = document.querySelectorAll('.account-tab-link');
    const panes = document.querySelectorAll('.account-pane');

    function activate(tab) {
        tabs.forEach(t => {
            const isActive = t.dataset.tab === tab;
            t.classList.toggle('active', isActive);
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        panes.forEach(p => {
            p.style.display = (p.dataset.pane === tab) ? 'block' : 'none';
        });

        // Sync URL ?tab= so refresh / share-link keeps the same view.
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
    }

    tabs.forEach(t => {
        t.addEventListener('click', () => activate(t.dataset.tab));
    });
})();
</script>

@endsection
