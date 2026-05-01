@extends('layouts.app')

@section('title', __('ui.team.title'))

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════════
   Team Management — متوافق مع نظام التصميم (saas-ui.css)
══════════════════════════════════════════════════════════════ */

/* ── Role Legend Cards ────────────────────────────────────── */
.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: .75rem;
    margin-bottom: 1.75rem;
}
.role-legend-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: .9rem 1rem;
    transition: transform .2s ease, box-shadow .2s ease;
}
.role-legend-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-hover); }
.role-legend-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display:flex; align-items:center; justify-content:center;
    font-size: .9rem; margin-bottom: .55rem;
}
.role-legend-name { font-size:.8rem; font-weight:700; color:var(--text-main); margin-bottom:.2rem; }
.role-legend-desc { font-size:.72rem; color:var(--text-soft); line-height:1.45; }

/* ── Add Member Card ──────────────────────────────────────── */
.add-member-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    margin-bottom: 1.75rem;
    overflow: hidden;
}
.add-member-header {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--line);
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; user-select: none;
}
.add-member-title {
    font-size:.95rem; font-weight:700; color:var(--text-main);
    display:flex; align-items:center; gap:.5rem; margin:0;
}
.add-member-title i { color:var(--primary-color); }
.add-member-body { padding: 1.25rem 1.5rem; }

.add-member-body .form-label {
    font-size:.78rem; font-weight:700; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:.06em; margin-bottom:.35rem;
}
.add-member-body .form-control,
.add-member-body .form-select {
    border-color: var(--line);
    border-radius: var(--radius-sm);
    background: var(--surface-soft);
    color: var(--text-main);
    font-size: .88rem;
    min-height: 42px;
}
.add-member-body .form-control:focus,
.add-member-body .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 .18rem rgba(15,143,131,.16);
    background: #fff;
}

/* ── Team Table ───────────────────────────────────────────── */
.team-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}
.team-card-header {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--line);
    display: flex; align-items:center; justify-content:space-between;
}
.team-card-title {
    font-size:.95rem; font-weight:700; color:var(--text-main);
    display:flex; align-items:center; gap:.5rem; margin:0;
}
.team-card-title i { color:var(--primary-color); }

.team-table { width:100%; border-collapse:collapse; }
.team-table th {
    padding: .7rem 1rem;
    font-size:.72rem; font-weight:700; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:.07em;
    background:var(--surface-soft); border-bottom:1px solid var(--line);
    white-space:nowrap;
}
.team-table td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--line);
    vertical-align: middle;
    font-size: .875rem;
    color: var(--text-main);
}
.team-table tbody tr:last-child td { border-bottom: none; }
.team-table tbody tr:hover td { background: var(--surface-soft); }

/* ── Avatar ───────────────────────────────────────────────── */
.member-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: var(--grad-primary);
    color: #fff; font-weight:700; font-size:.88rem;
    display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.member-name { font-weight:600; color:var(--text-main); font-size:.875rem; }
.member-email { font-size:.75rem; color:var(--text-soft); }

/* ── Role Badges ──────────────────────────────────────────── */
.role-badge {
    display:inline-flex; align-items:center; gap:.35rem;
    border-radius:99px; padding:.25rem .75rem;
    font-size:.72rem; font-weight:700; white-space:nowrap;
}
.role-owner   { background:linear-gradient(135deg,rgba(15,143,131,.15),rgba(15,118,110,.1)); color:var(--primary-color); border:1px solid rgba(15,143,131,.25); }
.role-admin   { background:rgba(99,102,241,.1);  color:#6366f1;  border:1px solid rgba(99,102,241,.2); }
.role-operator{ background:rgba(14,165,233,.1);  color:#0ea5e9;  border:1px solid rgba(14,165,233,.2); }
.role-validator{background:rgba(245,158,11,.12); color:var(--warning-color); border:1px solid rgba(245,158,11,.25); }
.role-viewer  { background:var(--surface-muted); color:var(--text-soft); border:1px solid var(--line); }

/* ── You badge ────────────────────────────────────────────── */
.you-badge {
    display:inline-flex; align-items:center;
    background:var(--primary-soft); color:var(--primary-color);
    border-radius:99px; padding:.1rem .55rem; font-size:.68rem; font-weight:700;
    margin-inline-start:.4rem; vertical-align:middle;
}

/* ── Inline role form ─────────────────────────────────────── */
.role-form { display:flex; align-items:center; gap:.5rem; }
.role-form .form-select {
    border-color:var(--line); border-radius:var(--radius-sm);
    background:var(--surface-soft); font-size:.8rem;
    min-height:34px; padding:.25rem .6rem;
    color:var(--text-main); max-width:170px;
}
.role-form .form-select:focus {
    border-color:var(--primary-color);
    box-shadow:0 0 0 .15rem rgba(15,143,131,.15);
}
.btn-save-role {
    border:1px solid var(--primary-color); border-radius:var(--radius-sm);
    background:transparent; color:var(--primary-color);
    font-size:.75rem; font-weight:700; padding:.25rem .65rem;
    min-height:34px; transition:all .2s;
    white-space:nowrap;
}
.btn-save-role:hover { background:var(--primary-color); color:#fff; }

/* ── Remove button ────────────────────────────────────────── */
.btn-remove {
    border:1px solid rgba(179,38,30,.3); border-radius:var(--radius-sm);
    background:transparent; color:var(--danger-color);
    font-size:.75rem; font-weight:700; padding:.25rem .65rem;
    min-height:34px; transition:all .2s;
}
.btn-remove:hover { background:var(--danger-color); color:#fff; border-color:var(--danger-color); }

/* ── Empty state ──────────────────────────────────────────── */
.team-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:3rem 1rem; color:var(--text-soft); text-align:center; gap:.6rem;
}
.team-empty-icon {
    width:64px; height:64px; border-radius:50%;
    background:var(--primary-soft); margin:0 auto;
    display:flex; align-items:center; justify-content:center;
}

/* ── Password toggle ──────────────────────────────────────── */
.pw-toggle {
    background:var(--surface-soft); border-color:var(--line); border-inline-start:none;
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;
    cursor:pointer; color:var(--text-soft);
}
[dir=rtl] .pw-toggle {
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    border-inline-start:1px solid var(--line); border-inline-end:none;
}

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width:767.98px) {
    .app-page-hero { padding:1.35rem 1.25rem; border-radius:var(--radius-lg); }
    .team-table th:nth-child(3),
    .team-table td:nth-child(3),
    .team-table th:nth-child(4),
    .team-table td:nth-child(4) { display:none; }
    .roles-grid { grid-template-columns:repeat(2,1fr); }
    .add-member-body { padding:1rem; }
    .team-card-header { flex-wrap:wrap; gap:.5rem; }
}
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════════════════ --}}
<div class="app-page-hero animate__animated animate__fadeInDown">
    <div class="app-page-hero-content d-flex flex-wrap align-items-start justify-content-between gap-3">
        <div>
            <div class="app-page-hero-kicker">
                <i class="fas fa-users me-2"></i>{{ __('ui.sidebar.team') }}
            </div>
            <h1 class="app-page-hero-title">
                {{ __('ui.team.title') }}
            </h1>
            <p class="app-page-hero-subtitle">{{ __('ui.team.subtitle') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:99px;padding:.35rem 1rem;font-size:.8rem;font-weight:600;">
                <i class="fas fa-users-line me-1"></i>
                {{ __('ui.team.members_count', ['count' => $users->total()]) }}
            </span>
            <button type="button"
                onclick="document.getElementById('addMemberForm').classList.toggle('d-none')"
                class="btn btn-sm"
                style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:99px;padding:.35rem 1.1rem;font-size:.8rem;font-weight:600;">
                <i class="fas fa-user-plus me-1"></i>{{ __('ui.team.add_member') }}
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROLES LEGEND
══════════════════════════════════════════════════════════ --}}
<div class="roles-grid animate__animated animate__fadeInUp" style="animation-delay:.05s">

    @foreach([
        ['role'=>'organizer_owner','icon'=>'fas fa-crown',       'color'=>'var(--primary-color)','bg'=>'var(--primary-soft)'],
        ['role'=>'organizer_admin','icon'=>'fas fa-shield-halved','color'=>'#6366f1',             'bg'=>'rgba(99,102,241,.1)'],
        ['role'=>'operator',       'icon'=>'fas fa-screwdriver-wrench','color'=>'#0ea5e9',        'bg'=>'rgba(14,165,233,.1)'],
        ['role'=>'validator',      'icon'=>'fas fa-qrcode',       'color'=>'var(--warning-color)','bg'=>'rgba(245,158,11,.1)'],
        ['role'=>'viewer',         'icon'=>'fas fa-eye',          'color'=>'var(--text-muted)',   'bg'=>'var(--surface-muted)'],
    ] as $r)
    <div class="role-legend-card">
        <div class="role-legend-icon" style="background:{{ $r['bg'] }};color:{{ $r['color'] }}">
            <i class="{{ $r['icon'] }}"></i>
        </div>
        <div class="role-legend-name" style="color:{{ $r['color'] }}">{{ __('ui.team.role_'.$r['role']) }}</div>
        <div class="role-legend-desc">{{ __('ui.team.role_desc_'.$r['role']) }}</div>
    </div>
    @endforeach

</div>

{{-- ══════════════════════════════════════════════════════════
     ADD MEMBER FORM
══════════════════════════════════════════════════════════ --}}
<div id="addMemberForm" class="add-member-card animate__animated animate__fadeInUp {{ $errors->any() ? '' : 'd-none' }}"
     style="animation-delay:.1s">

    <div class="add-member-header" onclick="this.nextElementSibling.classList.toggle('d-none')">
        <h2 class="add-member-title">
            <i class="fas fa-user-plus"></i>
            {{ __('ui.team.add_member') }}
        </h2>
        <i class="fas fa-chevron-down" style="color:var(--text-soft);font-size:.85rem;transition:transform .3s;" id="form-chevron"></i>
    </div>

    <div class="add-member-body" id="add-member-body">
        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-3" style="background:rgba(179,38,30,.08);color:var(--danger-color);">
                <i class="fas fa-circle-exclamation me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('team.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="tm_name">{{ __('ui.team.field_name') }}</label>
                    <input type="text" id="tm_name" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="{{ __('ui.team.placeholder_name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="tm_email">{{ __('ui.team.field_email') }}</label>
                    <input type="email" id="tm_email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="{{ __('ui.team.placeholder_email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="tm_phone">{{ __('ui.team.field_phone') }}</label>
                    <input type="tel" id="tm_phone" name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                        placeholder="{{ __('ui.team.placeholder_phone') }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="tm_role">{{ __('ui.team.field_role') }}</label>
                    <select id="tm_role" name="role"
                        class="form-select @error('role') is-invalid @enderror" required>
                        @foreach(['organizer_admin','operator','validator','viewer'] as $r)
                            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
                                {{ __('ui.team.role_'.$r) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 col-lg-2">
                    <label class="form-label" for="tm_password">{{ __('ui.team.field_password') }}</label>
                    <div class="input-group">
                        <input type="password" id="tm_password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ __('ui.team.placeholder_password') }}" required>
                        <button type="button" class="input-group-text pw-toggle"
                            onclick="togglePw('tm_password', this)">
                            <i class="fas fa-eye-slash" style="font-size:.82rem;"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button"
                    onclick="document.getElementById('addMemberForm').classList.add('d-none')"
                    class="btn btn-outline-secondary rounded-pill px-4" style="font-size:.875rem;">
                    <i class="fas fa-xmark me-2"></i>{{ __('ui.team.btn_cancel') }}
                </button>
                <button type="submit" class="btn btn-primary rounded-pill px-4" style="font-size:.875rem;">
                    <i class="fas fa-user-plus me-2"></i>{{ __('ui.team.btn_create') }}
                </button>
            </div>

        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     TEAM MEMBERS TABLE
══════════════════════════════════════════════════════════ --}}
<div class="team-card animate__animated animate__fadeInUp" style="animation-delay:.15s">

    <div class="team-card-header">
        <h2 class="team-card-title">
            <i class="fas fa-users"></i>
            {{ __('ui.team.title') }}
        </h2>
        <span style="font-size:.8rem;color:var(--text-soft);">
            {{ __('ui.team.members_count', ['count' => $users->total()]) }}
        </span>
    </div>

    <div class="table-responsive">
        <table class="team-table">
            <thead>
                <tr>
                    <th>{{ __('ui.team.col_member') }}</th>
                    <th>{{ __('ui.team.col_role') }}</th>
                    <th>{{ __('ui.team.col_phone') }}</th>
                    <th>{{ __('ui.team.col_last_login') }}</th>
                    <th>{{ __('ui.team.col_joined') }}</th>
                    <th style="text-align:end">{{ __('ui.team.col_actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $member)
                @php
                    $isMe    = (int)$member->id === (int)auth()->id();
                    $isOwner = $member->role === 'organizer_owner';
                    $initials = collect(explode(' ', $member->name))->take(2)->map(fn($w)=>strtoupper(mb_substr($w,0,1)))->implode('');
                    $roleClass = match($member->role) {
                        'organizer_owner' => 'role-owner',
                        'organizer_admin' => 'role-admin',
                        'operator'        => 'role-operator',
                        'validator'       => 'role-validator',
                        default           => 'role-viewer',
                    };
                    $roleIcon = match($member->role) {
                        'organizer_owner' => 'fas fa-crown',
                        'organizer_admin' => 'fas fa-shield-halved',
                        'operator'        => 'fas fa-screwdriver-wrench',
                        'validator'       => 'fas fa-qrcode',
                        default           => 'fas fa-eye',
                    };
                @endphp
                <tr>
                    {{-- العضو --}}
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="member-avatar">{{ $initials ?: '?' }}</div>
                            <div>
                                <div class="member-name">
                                    {{ $member->name }}
                                    @if($isMe)
                                        <span class="you-badge">{{ __('ui.team.you_badge') }}</span>
                                    @endif
                                </div>
                                <div class="member-email">{{ $member->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- الدور --}}
                    <td>
                        @if($isOwner || $isMe)
                            {{-- المالك أو المستخدم الحالي: لا يمكن تغيير دوره --}}
                            <span class="role-badge {{ $roleClass }}">
                                <i class="{{ $roleIcon }}"></i>
                                {{ __('ui.team.role_'.$member->role) }}
                            </span>
                        @else
                            <form action="{{ route('team.role.update', $member) }}" method="POST" class="role-form">
                                @csrf @method('PATCH')
                                <select name="role" class="form-select"
                                    onchange="this.closest('form').submit()"
                                    title="{{ __('ui.team.field_role') }}">
                                    @foreach(['organizer_admin','operator','validator','viewer'] as $r)
                                        <option value="{{ $r }}" {{ $member->role === $r ? 'selected' : '' }}>
                                            {{ __('ui.team.role_'.$r) }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </td>

                    {{-- الجوال --}}
                    <td style="color:var(--text-soft); direction:ltr;">
                        {{ $member->phone ?: '—' }}
                    </td>

                    {{-- آخر دخول --}}
                    <td>
                        @if($member->last_login_at)
                            <span title="{{ $member->last_login_at->format('Y-m-d H:i:s') }}">
                                {{ $member->last_login_at->diffForHumans() }}
                            </span>
                        @else
                            <span style="color:var(--text-soft);font-size:.8rem;">
                                <i class="fas fa-clock fa-xs me-1"></i>{{ __('ui.team.never_logged_in') }}
                            </span>
                        @endif
                    </td>

                    {{-- تاريخ الانضمام --}}
                    <td style="color:var(--text-soft);font-size:.8rem;">
                        {{ $member->created_at->format('Y/m/d') }}
                    </td>

                    {{-- الإجراءات --}}
                    <td style="text-align:end;">
                        @if(!$isMe && !$isOwner)
                            <button type="button"
                                class="btn-remove"
                                onclick="confirmRemove({{ $member->id }}, '{{ addslashes($member->name) }}')"
                                title="{{ __('ui.team.btn_remove') }}">
                                <i class="fas fa-user-minus me-1"></i>{{ __('ui.team.btn_remove') }}
                            </button>
                        @else
                            <span style="font-size:.75rem;color:var(--text-soft);">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="team-empty">
                            <div class="team-empty-icon">
                                <i class="fas fa-users-slash" style="font-size:1.4rem;color:var(--primary-color)"></i>
                            </div>
                            <p class="fw-semibold mb-1" style="color:var(--text-main)">{{ __('ui.team.no_members') }}</p>
                            <p class="small" style="color:var(--text-soft)">{{ __('ui.team.no_members_hint') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="px-4 py-3 border-top" style="border-color:var(--line)!important;">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Password visibility toggle ────────────────────────
    window.togglePw = function (id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash','fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye','fa-eye-slash');
        }
    };

    // ── Remove confirm — uses global AppUI.confirm() ──────
    window.confirmRemove = function (id, name) {
        const bodyTpl = @json(__('ui.team.confirm_remove_body', ['name' => '__NAME__']));
        AppUI.confirm({
            title        : @json(__('ui.team.confirm_remove_title')),
            body         : bodyTpl.replace('__NAME__', `<strong>${name}</strong>`),
            icon         : 'user-minus',
            danger       : true,
            confirmLabel : @json(__('ui.team.btn_confirm_remove')),
            cancelLabel  : @json(__('ui.team.btn_cancel')),
            formAction   : `/team/${id}`,
            formMethod   : 'DELETE',
        });
    };

    // ── Auto-open add-form if validation errors ───────────
    @if($errors->any())
        const addForm = document.getElementById('addMemberForm');
        if (addForm) addForm.classList.remove('d-none');
    @endif

    // ── Flash message auto-dismiss ────────────────────────
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });

})();
</script>
@endpush
