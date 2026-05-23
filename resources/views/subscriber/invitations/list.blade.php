{{-- resources/views/invitations/list.blade.php --}}
@extends('layouts.app')

@section('title', __('invitations.title'))

@push('styles')
<style>
.invitations-page {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* --- Custom Checkbox --- */
.form-check-input[type="checkbox"] {
    width: 1.2rem;
    height: 1.2rem;
    border: 2px solid #cbd5e1;
    border-radius: 5px;
    cursor: pointer;
    transition: border-color .2s, background-color .2s, box-shadow .2s;
    vertical-align: middle;
    flex-shrink: 0;
    appearance: none;
    -webkit-appearance: none;
    background-color: #fff;
    position: relative;
}
.form-check-input[type="checkbox"]:hover {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(15,143,131,.12);
}
.form-check-input[type="checkbox"]:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(15,143,131,.15);
}
.form-check-input[type="checkbox"]:checked::after {
    content: '';
    position: absolute;
    top: 1px;
    left: 4px;
    width: 5px;
    height: 9px;
    border: 2px solid #fff;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
}
.form-check-input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(15,143,131,.2);
}

.invitations-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.invitations-head h1 {
    margin: 0;
    font-size: clamp(1.4rem, 2.5vw, 1.95rem);
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: .6rem;
}

.invitations-head p {
    margin: .35rem 0 0;
    color: var(--text-muted);
    font-size: .9rem;
}

.inv-actions {
    display: flex;
    gap: .55rem;
    flex-wrap: wrap;
}

.inv-head-btn {
    border-radius: 999px;
    padding: .6rem 1rem;
    font-size: .82rem;
    font-weight: 700;
}

.inv-kpi-row {
    display: grid;
    grid-template-columns: repeat(5, minmax(120px, 1fr));
    gap: .75rem;
}

.inv-kpi-chip {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: .7rem .95rem;
    display: flex;
    align-items: center;
    gap: .6rem;
    min-width: 0;
}

.inv-kpi-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.inv-kpi-lbl {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-muted);
}

.inv-kpi-num {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}

.inv-toolbar {
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    background: var(--surface);
    padding: .85rem;
    display: flex;
    flex-direction: column;
    gap: .8rem;
}

.inv-tabs {
    display: flex;
    gap: .45rem;
    flex-wrap: wrap;
}

.inv-tab {
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface-soft);
    color: var(--text-main);
    padding: .45rem .95rem;
    font-size: .78rem;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s ease;
}

.inv-tab:hover {
    border-color: rgba(99, 102, 241, .38);
    color: var(--primary-color);
}

.inv-tab.active {
    border-color: transparent;
    color: #fff;
    background: var(--grad-primary);
    box-shadow: 0 10px 24px -14px rgba(34, 34, 34, .55);
}

.import-panel {
    background: var(--surface-soft);
    border-radius: var(--radius-md);
    border: 1.5px dashed rgba(99, 102, 241, .28);
    padding: 1rem;
    display: none;
}

.import-panel.show {
    display: block;
}

.inv-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.inv-table thead th {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--text-muted);
    padding: .72rem 1rem;
    background: var(--surface-soft);
    border-bottom: 1px solid var(--line);
    white-space: nowrap;
}

.inv-table tbody tr {
    transition: background .15s;
}

.inv-table tbody tr:hover {
    background: var(--surface-soft);
}

.inv-table tbody td {
    padding: .82rem 1rem;
    border-bottom: 1px solid var(--line);
    font-size: .84rem;
    color: var(--text-main);
    vertical-align: middle;
}

.inv-table tbody tr:last-child td {
    border-bottom: none;
}

.st-badge {
    padding: .25rem .7rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
}

.st-accepted { background: rgba(16, 185, 129, .1); color: var(--success-color); }
.st-pending  { background: rgba(245, 158, 11, .1); color: var(--warning-color); }
.st-declined, .st-rejected { background: rgba(244, 63, 94, .1); color: var(--danger-color); }
.st-maybe    { background: rgba(139, 92, 246, .1); color: #8b5cf6; }
.st-sent     { background: rgba(14, 165, 233, .1); color: var(--accent-color); }

.inv-actions-row {
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
}

.action-btn {
    border: 1px solid var(--line);
    border-radius: 8px;
    background: #fff;
    color: var(--text-main);
    font-size: .75rem;
    line-height: 1;
    padding: .42rem .56rem;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    cursor: pointer;
    transition: all .2s ease;
}

.action-btn:hover {
    border-color: rgba(99, 102, 241, .35);
    color: var(--primary-color);
    background: var(--primary-soft);
}

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--text-soft);
}

@media (max-width: 991.98px) {
    .inv-kpi-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 575.98px) {
    .inv-kpi-row {
        grid-template-columns: 1fr;
    }

    .invitations-head {
        align-items: stretch;
    }

    .inv-actions {
        width: 100%;
    }

    .inv-head-btn {
        flex: 1;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')

<div class="invitations-page">
    @php
        $total = (int) ($totalInvitations ?? $invitations->total());
        $accepted = (int) ($statusStats['accepted'] ?? 0);
        $pending = (int) ($statusStats['pending'] ?? 0);
        $declined = (int) ($declinedCount ?? 0);
        $maybe = (int) ($statusStats['maybe'] ?? 0);
        $activeFilter = $activeStatus ?? request('status', 'all');
        $isGlobalInvitationsPage = request()->routeIs('invitations.index');
        $listRoute = $isGlobalInvitationsPage ? route('invitations.index') : route('events.invitations.index', $event);
        $eventId = $event?->id;
        $selectedEventId = (int) ($selectedEventId ?? request('event_id', $eventId));
    @endphp

    <div class="invitations-head">
        <div>
            <h1><i class="fas fa-envelope-open-text"></i>{{ __('invitations.title') }}</h1>
            <p><i class="fas fa-calendar-days"></i> {{ $event->name ?? __('invitations.event_fallback') }}</p>
        </div>
        <div class="inv-actions">
            <a href="{{ $eventId ? route('invitations.create', ['event_id' => $eventId]) : route('events.create') }}" class="btn btn-primary inv-head-btn {{ !$eventId ? 'disabled' : '' }}">
                <i class="fas fa-paper-plane"></i>
                <span>{{ __('invitations.actions.new') }}</span>
            </a>
            <a href="{{ $eventId ? ($isGlobalInvitationsPage ? route('invitations.export') : route('events.invitations.export_csv', $event)) : '#' }}" class="btn btn-outline-secondary inv-head-btn {{ !$eventId ? 'disabled' : '' }}">
                <i class="fas fa-download"></i>
                <span>{{ __('invitations.actions.export_csv') }}</span>
            </a>

            {{-- CSV Import: show lock button if feature disabled --}}
            @if($canCsvImport ?? false)
                <button type="button" class="btn btn-outline-primary inv-head-btn" onclick="toggleImport()">
                    <i class="fas fa-upload"></i>
                    <span>{{ __('invitations.actions.import_csv') }}</span>
                </button>
            @else
                <x-feature-lock-btn feature="csv_import"
                                    label="{{ __('invitations.actions.import_csv') }}"
                                    icon="fas fa-upload"
                                    size="md" />
            @endif

            {{-- Bulk resend: show lock button if feature disabled --}}
            @if($canBulkResend ?? false)
                <button type="button" class="btn btn-primary inv-head-btn" onclick="bulkResendSelected()">
                    <i class="fas fa-repeat"></i>
                    <span>{{ __('invitations.actions.bulk_resend_selected') }}</span>
                </button>
            @else
                <x-feature-lock-btn feature="bulk_resend"
                                    label="{{ __('invitations.actions.bulk_resend_selected') }}"
                                    icon="fas fa-repeat"
                                    size="md" />
            @endif
        </div>
    </div>

    <div class="inv-kpi-row animate__animated animate__fadeIn">
        <div class="inv-kpi-chip">
            <div class="inv-kpi-dot" style="background:var(--primary-color)"></div>
            <div><div class="inv-kpi-lbl">{{ __('invitations.kpi.total') }}</div><div class="inv-kpi-num">{{ $total }}</div></div>
        </div>
        <div class="inv-kpi-chip">
            <div class="inv-kpi-dot" style="background:var(--success-color)"></div>
            <div><div class="inv-kpi-lbl">{{ __('invitations.kpi.accepted') }}</div><div class="inv-kpi-num">{{ $accepted }}</div></div>
        </div>
        <div class="inv-kpi-chip">
            <div class="inv-kpi-dot" style="background:var(--warning-color)"></div>
            <div><div class="inv-kpi-lbl">{{ __('invitations.kpi.pending') }}</div><div class="inv-kpi-num">{{ $pending }}</div></div>
        </div>
        <div class="inv-kpi-chip">
            <div class="inv-kpi-dot" style="background:var(--danger-color)"></div>
            <div><div class="inv-kpi-lbl">{{ __('invitations.kpi.declined') }}</div><div class="inv-kpi-num">{{ $declined }}</div></div>
        </div>
        @if($maybe > 0)
        <div class="inv-kpi-chip">
            <div class="inv-kpi-dot" style="background:#8b5cf6"></div>
            <div><div class="inv-kpi-lbl">{{ __('invitations.kpi.maybe') }}</div><div class="inv-kpi-num">{{ $maybe }}</div></div>
        </div>
        @endif
    </div>

    <div class="inv-toolbar">
        <div class="inv-tabs">
            <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="inv-tab {{ $activeFilter === 'all' ? 'active' : '' }}">
                {{ __('invitations.filters.all') }} <span class="ms-1 opacity-75">{{ $total }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="inv-tab {{ $activeFilter === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock fa-xs"></i> {{ __('invitations.filters.pending') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'accepted']) }}" class="inv-tab {{ $activeFilter === 'accepted' ? 'active' : '' }}">
                <i class="fas fa-circle-check fa-xs"></i> {{ __('invitations.filters.accepted') }}
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'declined']) }}" class="inv-tab {{ in_array($activeFilter, ['declined', 'rejected']) ? 'active' : '' }}">
                <i class="fas fa-circle-xmark fa-xs"></i> {{ __('invitations.filters.declined') }}
            </a>
            @if($maybe > 0)
            <a href="{{ request()->fullUrlWithQuery(['status' => 'maybe']) }}" class="inv-tab {{ $activeFilter === 'maybe' ? 'active' : '' }}">
                <i class="fas fa-circle-question fa-xs"></i> {{ __('invitations.filters.maybe') }}
            </a>
            @endif
        </div>

        <form method="GET" action="{{ $listRoute }}" class="row g-2 align-items-center">
            <input type="hidden" name="status" value="{{ $activeFilter }}">
            <div class="col-md-4 col-lg-3">
                <select name="event_id" class="form-select form-select-sm" {{ !$isGlobalInvitationsPage ? 'disabled' : '' }}>
                    @foreach(($events ?? collect()) as $eventOption)
                        <option value="{{ $eventOption->id }}" {{ (int) $selectedEventId === (int) $eventOption->id ? 'selected' : '' }}>
                            {{ $eventOption->title ?: $eventOption->name }}
                        </option>
                    @endforeach
                </select>
                @if(!$isGlobalInvitationsPage)
                    <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
                @endif
            </div>
            <div class="col-md-8 col-lg-5">
                <input
                    type="search"
                    name="searchInput"
                    class="form-control form-control-sm"
                    value="{{ $search ?? request('searchInput') }}"
                    placeholder="{{ __('invitations.index.search_placeholder') }}"
                >
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="fas fa-search me-1"></i>{{ __('invitations.index.search') }}
                </button>
                @if(request('searchInput'))
                    <a href="{{ $isGlobalInvitationsPage ? route('invitations.index', ['status' => $activeFilter, 'event_id' => $selectedEventId]) : route('events.invitations.index', ['event' => $event->id, 'status' => $activeFilter]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        {{ __('invitations.actions.close') }}
                    </a>
                @endif
            </div>
        </form>

        @if($canCsvImport ?? false)
        {{-- ── Import panel — tabs for CSV and Excel ── --}}
        <div class="import-panel animate__animated animate__fadeIn" id="import-panel" style="display:none;">
            {{-- Tab navigation --}}
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="tabCsvBtn" onclick="switchImportTab('csv')">
                    <i class="fas fa-file-csv me-1"></i> CSV
                </button>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" id="tabExcelBtn" onclick="switchImportTab('excel')">
                    <i class="fas fa-file-excel me-1"></i> Excel (XLSX)
                </button>
                <a href="{{ route('invitations.excel_template') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 ms-auto" title="تحميل نموذج Excel">
                    <i class="fas fa-download me-1"></i> تحميل النموذج
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleImport()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- CSV tab --}}
            <div id="tabCsvPanel">
                <form method="POST"
                      action="{{ $eventId ? ($isGlobalInvitationsPage ? route('invitations.import_csv') : route('events.invitations.bulk_import', $event)) : '#' }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if($isGlobalInvitationsPage)
                        <input type="hidden" name="event_id" value="{{ $eventId }}">
                    @endif
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:.82rem;">
                                {{ __('invitations.import.label') }}
                                <span class="text-muted">{{ __('invitations.import.hint') }}</span>
                            </label>
                            <input type="file"
                                   name="{{ $isGlobalInvitationsPage ? 'csv_file' : 'file' }}"
                                   accept=".csv,.txt"
                                   required
                                   class="form-control form-control-sm"
                                   {{ !$eventId ? 'disabled' : '' }}>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4" {{ !$eventId ? 'disabled' : '' }}>
                                <i class="fas fa-upload me-1"></i> استيراد
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Excel tab (hidden by default) --}}
            <div id="tabExcelPanel" style="display:none;">
                <form method="POST"
                      action="{{ $isGlobalInvitationsPage ? route('invitations.import_excel') : route('invitations.import_excel') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $eventId }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="font-size:.82rem;">
                                ملف Excel أو CSV
                                <span class="text-muted">(الأعمدة: name, email, position, nationality, allowed_guests)</span>
                            </label>
                            <input type="file"
                                   name="excel_file"
                                   accept=".xlsx,.xls,.csv"
                                   required
                                   class="form-control form-control-sm"
                                   {{ !$eventId ? 'disabled' : '' }}>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-4" {{ !$eventId ? 'disabled' : '' }}>
                                <i class="fas fa-upload me-1"></i> استيراد
                            </button>
                        </div>
                    </div>
                    <p class="mt-2 mb-0" style="font-size:.75rem;color:#6b7280;">
                        <i class="fas fa-info-circle me-1"></i>
                        سيتم إرسال دعوات البريد الإلكتروني في الخلفية بعد الاستيراد.
                        <a href="{{ route('invitations.excel_template') }}" class="text-primary">تحميل النموذج الجاهز</a>.
                    </p>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="card-surface p-0 animate__animated animate__fadeInUp" style="animation-delay:.1s">
        <div class="table-responsive" style="border-radius:var(--radius-lg)">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width:32px"><input type="checkbox" id="select-all" class="form-check-input"></th>
                        <th>{{ __('invitations.table.name') }}</th>
                        <th>{{ __('invitations.table.email') }}</th>
                        <th>{{ __('invitations.table.phone') }}</th>
                        <th>{{ __('invitations.table.status') }}</th>
                        <th>{{ __('invitations.table.responded') }}</th>
                        <th>{{ __('invitations.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $inv)
                    @php
                        $displayName = $inv->invitee_name ?? $inv->invited_name;
                        $displayEmail = $inv->invitee_email ?? $inv->invited_email;
                        $displayPhone = $inv->invitee_phone ?? $inv->invited_phone;
                        $statusValue = $inv->status ?: 'pending';
                        $token = $inv->invitation_token ?? $inv->token;

                        // Use PublicUrlService so the link uses the tenant subdomain
                        // when the company's plan has `custom_subdomain` enabled.
                        $_listCompany  = $inv->company ?? $inv->event?->company ?? null;
                        $_listUrlSvc   = app(\App\Services\PublicUrlService::class);
                        $invitationLink = ($token && $_listCompany)
                            ? $_listUrlSvc->rsvpUrl($_listCompany, $token)
                            : ($token ? route('rsvp.show', $token) : '');
                        $ticketsLink = ($token && $_listCompany)
                            ? $_listUrlSvc->downloadPdfUrl($_listCompany, $token)
                            : ($token ? route('downloadPdf', $token) : '');

                        $whatsappMessage = $invitationLink ? ($displayName . "\n\n" . __('invitations.index.copy_invite_message') . "\n" . $invitationLink) : '';
                        $whatsappMessageTickets = $ticketsLink ? ($displayName . "\n\n" . __('invitations.index.copy_tickets_link') . "\n" . $ticketsLink) : '';
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="form-check-input inv-check" value="{{ $inv->id }}"></td>
                        <td class="fw-semibold">{{ $displayName }}</td>
                        <td class="text-muted">{{ $displayEmail }}</td>
                        <td class="text-muted">{{ $displayPhone ?? __('invitations.table.no_phone') }}</td>
                        <td>
                            @php
                                $stClass = match($statusValue) {
                                    'accepted' => 'st-accepted',
                                    'pending'  => 'st-pending',
                                    'maybe'    => 'st-maybe',
                                    'sent'     => 'st-sent',
                                    default    => 'st-declined',
                                };
                                $statusKey = 'invitations.status.' . $statusValue;
                                $statusLabel = __($statusKey);
                            @endphp
                            <span class="st-badge {{ $stClass }}">{{ $statusLabel !== $statusKey ? $statusLabel : ucfirst($statusValue) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.78rem">
                            {{ $inv->responded_at ? \Carbon\Carbon::parse($inv->responded_at)->format('M j, g:ia') : __('invitations.table.no_response') }}
                        </td>
                        <td>
                            <div class="inv-actions-row">
                                <a
                                    href="{{ route('invitations.edit', $inv->id) }}"
                                    class="action-btn"
                                    title="{{ __('invitations.index.edit_invitation') }}"
                                >
                                    <i class="fas fa-pen"></i>
                                    <span>{{ __('invitations.index.edit_invitation') }}</span>
                                </a>
                                <button
                                    type="button"
                                    class="action-btn js-resend-invitation"
                                    data-resend-url="{{ route('events.invitations.resend', $inv) }}"
                                    title="{{ __('invitations.actions.resend') }}"
                                >
                                    <i class="fas fa-paper-plane"></i>
                                    <span>{{ __('invitations.actions.resend') }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="action-btn js-copy-btn"
                                    data-copy-text="{{ $whatsappMessage }}"
                                    title="{{ __('invitations.index.copy_invite_message') }}"
                                >
                                    <i class="fas fa-copy"></i>
                                    <span>{{ __('invitations.index.copy_invite_message') }}</span>
                                </button>

                                <div class="dropdown">
                                    <button class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('invitations.index.more_options') }}">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 js-copy-link"
                                                    data-copy-url="{{ route('events.invitations.copy_link', $inv) }}">
                                                <i class="fas fa-link text-info"></i> {{ __('invitations.actions.copy_link') }}
                                            </button>
                                        </li>

                                        @if($statusValue !== 'pending' && $ticketsLink)
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 js-copy-btn"
                                                        data-copy-text="{{ $whatsappMessageTickets }}">
                                                    <i class="fas fa-ticket-alt text-warning"></i> {{ __('invitations.index.copy_tickets_link') }}
                                                </button>
                                            </li>
                                        @endif

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <form action="{{ route('invitations.destroy', $inv->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item d-flex align-items-center gap-2 text-danger js-confirm-action"
                                                        type="submit"
                                                        data-confirm="{{ __('invitations.index.confirm_delete') }}">
                                                    <i class="fas fa-trash-alt"></i> {{ __('invitations.index.delete_invitation') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-envelope-open fa-2x mb-2 d-block" style="opacity:.25"></i>
                                {{ __('invitations.table.empty_filtered') }}
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-2">
        {{ $invitations->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const I18N = {
    resendSuccess: "{{ __('invitations.js.resend_success') }}",
    resendFailed: "{{ __('invitations.js.resend_failed') }}",
    linkUnavailable: "{{ __('invitations.js.link_unavailable') }}",
    linkCopied: "{{ __('invitations.js.link_copied') }}",
    copyFailed: "{{ __('invitations.js.copy_failed') }}",
    copyFetchError: "{{ __('invitations.js.copy_fetch_error') }}",
    selectOne: "{{ __('invitations.js.select_one') }}",
    confirmBulkResend: "{{ __('invitations.js.confirm_bulk_resend') }}",
};

function toggleImport() {
    const panel = document.getElementById('import-panel');
    if (!panel) return;
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    // Always reset to CSV tab when re-opening.
    if (!isVisible) {
        switchImportTab('csv');
    }
}

/**
 * Switch between the CSV and Excel import tabs inside the import panel.
 * @param {'csv'|'excel'} tab
 */
function switchImportTab(tab) {
    const csvPanel   = document.getElementById('tabCsvPanel');
    const excelPanel = document.getElementById('tabExcelPanel');
    const csvBtn     = document.getElementById('tabCsvBtn');
    const excelBtn   = document.getElementById('tabExcelBtn');

    if (!csvPanel || !excelPanel) return;

    if (tab === 'csv') {
        csvPanel.style.display   = 'block';
        excelPanel.style.display = 'none';
        csvBtn.classList.remove('btn-outline-primary');
        csvBtn.classList.add('btn-primary');
        excelBtn.classList.remove('btn-success');
        excelBtn.classList.add('btn-outline-success');
    } else {
        csvPanel.style.display   = 'none';
        excelPanel.style.display = 'block';
        excelBtn.classList.remove('btn-outline-success');
        excelBtn.classList.add('btn-success');
        csvBtn.classList.remove('btn-primary');
        csvBtn.classList.add('btn-outline-primary');
    }
}

function resendOne(url) {
    return fetch(url, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}
    }).then(r=>r.json()).then(d=>{
        Toastify({ text: d.message ?? I18N.resendSuccess, duration:3000, style:{background:'var(--success-color)'} }).showToast();
        return d;
    }).catch(()=>{
        Toastify({ text:I18N.resendFailed, duration:3000, style:{background:'var(--danger-color)'} }).showToast();
    });
}

function copyLink(url) {
    fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type':'application/json'}
    })
    .then(r => r.json())
    .then(d => {
        if (!d?.url) {
            Toastify({text: d?.message ?? I18N.linkUnavailable, duration: 2500, style:{background:'var(--warning-color)'}}).showToast();
            return;
        }

        navigator.clipboard.writeText(d.url)
            .then(() => Toastify({text: d.message ?? I18N.linkCopied, duration: 2500, style:{background:'var(--success-color)'}}).showToast())
            .catch(() => Toastify({text: I18N.copyFailed, duration: 2500, style:{background:'var(--warning-color)'}}).showToast());
    })
    .catch(() => {
        Toastify({text:I18N.copyFetchError, duration:2500, style:{background:'var(--danger-color)'}}).showToast();
    });
}

function copyToClipboard(button) {
    const text = button.getAttribute('data-copy-text') || '';
    if (!text) {
        Toastify({text:I18N.linkUnavailable, duration:2500, style:{background:'var(--warning-color)'}}).showToast();
        return;
    }

    navigator.clipboard.writeText(text)
        .then(() => Toastify({text:I18N.linkCopied, duration:2500, style:{background:'var(--success-color)'}}).showToast())
        .catch(() => Toastify({text:I18N.copyFailed, duration:2500, style:{background:'var(--warning-color)'}}).showToast());
}

function bulkResendSelected() {
    const checked = [...document.querySelectorAll('.inv-check:checked')].map(c => c.value);
    if (!checked.length) {
        Toastify({text: I18N.selectOne, duration: 2500, style: {background: 'var(--warning-color)'}}).showToast();
        return;
    }

    const confirmMessage = I18N.confirmBulkResend.replace(':count', checked.length);
    AppUI.confirm({
        body: confirmMessage,
        danger: false,
        confirmLabel: 'تأكيد',
        onConfirm: function () {
            // Show loading state on button
            const btn = document.querySelector('[onclick="bulkResendSelected()"]');
            const originalHTML = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الإرسال...';
            }

            fetch('{{ route("invitations.bulk_resend_selected") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({selected_ids: checked}),
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Toastify({text: d.message ?? I18N.resendSuccess, duration: 3500, style: {background: 'var(--success-color)'}}).showToast();
                    // Uncheck all
                    document.getElementById('select-all').checked = false;
                    document.querySelectorAll('.inv-check').forEach(c => c.checked = false);
                } else {
                    Toastify({text: d.message ?? I18N.resendFailed, duration: 3500, style: {background: 'var(--danger-color)'}}).showToast();
                }
            })
            .catch(() => {
                Toastify({text: I18N.resendFailed, duration: 3000, style: {background: 'var(--danger-color)'}}).showToast();
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            });
        },
    });
}

const selectAllCheckbox = document.getElementById('select-all');
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function(){
        document.querySelectorAll('.inv-check').forEach(c => c.checked = this.checked);
    });
}

document.querySelectorAll('.js-resend-invitation').forEach((button) => {
    button.addEventListener('click', () => resendOne(button.dataset.resendUrl));
});

document.querySelectorAll('.js-copy-link').forEach((button) => {
    button.addEventListener('click', () => copyLink(button.dataset.copyUrl));
});

document.querySelectorAll('.js-copy-btn').forEach((button) => {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        copyToClipboard(button);
    });
});

document.querySelectorAll('.js-confirm-action').forEach((button) => {
    button.addEventListener('click', function (event) {
        event.preventDefault();
    });
});
</script>
@endpush