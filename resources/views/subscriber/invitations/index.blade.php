@extends('layouts.app')

@section('title', __('invitations.title'))

@push('styles')
    <style>
        /* --- General Layout --- */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }

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

        /* --- Buttons & Inputs --- */
        .btn-action { background: var(--grad-purple); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4); color: white; }

        .search-container { position: relative; max-width: 400px; }
        .search-input { border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 12px; padding: 12px 15px 12px 45px; width: 100%; font-size: 0.95rem; transition: 0.3s; }
        .search-input:focus { background: white; border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* --- Card & Table --- */
        .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden; min-height: 400px;}
        .table-custom thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

        /* --- Avatar --- */
        .avatar-initials { width: 40px; height: 40px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; color: var(--primary-color); background: #eff6ff; margin-right: 12px; }

        /* --- Badges --- */
        .badge-soft { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }

        /* --- New Action Buttons Styling --- */
        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all 0.2s;
            color: #64748b;
            background: #f8fafc;
            text-decoration: none;
        }
        .btn-icon:hover {
            background: #e2e8f0;
            color: #334155;
            transform: translateY(-1px);
        }
        .btn-icon-primary:hover { background: #eff6ff; color: #3b82f6; border-color: #dbeafe; }

        /* --- Dropdown Styling --- */
        .dropdown-menu-custom {
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 6px;
            min-width: 200px;
        }
        .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
        }
        .dropdown-item:hover {
            background-color: #f8fafc;
            color: var(--primary-color);
        }
        .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .dropdown-divider {
            margin: 4px 0;
            border-top-color: #f1f5f9;
        }
    </style>
@endpush

@section('content')
    <span id="invitation-i18n" class="d-none" data-copy-failed="{{ __('invitations.js.copy_failed') }}"></span>
@php
    $whatsappMessage = '';
    $whatsappMessageTickets = '';
@endphp
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark mb-1">{{ __('invitations.title') }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">{{ __('ui.sidebar.dashboard') }}</a></li>
                    <li class="breadcrumb-item active text-primary">{{ __('invitations.title') }}</li>
                </ol>
            </nav>
        </div>

        <a href="{{route("invitations.create")}}" class="btn-action text-decoration-none">
            <i class="fas fa-paper-plane me-2"></i> {{ __('invitations.actions.new') }}
        </a>
    </div>

    <div class="custom-card">
        <div class="card-body p-0">

            <div class="p-4 border-bottom bg-white d-flex flex-column flex-xl-row justify-content-between align-items-stretch align-items-xl-center gap-3">

                <form action="{{ route('invitations.index') }}" method="get" class="w-100" style="max-width: 720px;">
                    <div class="input-group invitation-filter-group">
                        <select name="event_id" class="form-select" style="max-width: 210px;">
                            <option value="">{{ __('invitations.event_fallback') }}</option>
                            @foreach(($events ?? collect()) as $eventItem)
                                <option value="{{ $eventItem->id }}" {{ (string) request('event_id') === (string) $eventItem->id ? 'selected' : '' }}>
                                    {{ $eventItem->title ?: $eventItem->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="input-group-text bg-white border-end-0 border-start-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="search"
                               name="searchInput"
                               class="form-control border-start-0 ps-0"
                               placeholder="{{ __('invitations.index.search_placeholder') }}"
                               value="{{ old('searchInput', $search ?? request('searchInput')) }}">
                        <button class="btn btn-primary px-4" type="submit">
                            {{ __('invitations.index.search') }}
                        </button>
                        @if(request('searchInput') || request('event_id'))
                            <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary px-3">
                                {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('invitations.export') }}" class="btn btn-light border text-muted btn-sm rounded-3 px-3 fw-bold">
                        <i class="fas fa-download me-1"></i> {{ __('invitations.actions.export_csv') }}
                    </a>

                    <form action="{{ route('invitations.import_csv') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                        <input type="file" name="csv_file" class="form-control form-control-sm" style="max-width: 180px;" {{ !$canCsvImport ? 'disabled' : '' }}>
                        <button type="submit"
                                class="btn btn-sm btn-outline-primary {{ !$canCsvImport ? 'disabled' : '' }}"
                                {{ !$canCsvImport ? 'disabled' : '' }}>
                            {{ __('invitations.actions.import_csv') }}
                        </button>
                    </form>

                    <form action="{{ route('invitations.resend_all') }}" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ request('event_id') }}">
                        <button type="submit"
                                class="btn btn-sm btn-outline-warning {{ !$canBulkResend ? 'disabled' : '' }} js-confirm-action"
                                data-confirm="{{ __('invitations.js.confirm_resend_all_filtered') }}"
                                {{ !$canBulkResend ? 'disabled' : '' }}>
                            {{ __('invitations.actions.bulk_resend_all') }}
                        </button>
                    </form>
                </div>
            </div>

            <form id="bulk-invitations-form" action="{{ route('invitations.bulk_resend_selected') }}" method="POST">
                @csrf
                <input type="hidden" name="searchInput" value="{{ old('searchInput', $search ?? request('searchInput')) }}">
                <input type="hidden" name="event_id" value="{{ request('event_id') }}">

                <div class="px-4 py-3 border-bottom bg-light d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 bulk-actions-bar">
                    <div>
                        <div class="fw-bold text-dark">{{ __('invitations.actions.bulk_actions') }}</div>
                        <div class="small text-muted">{{ __('invitations.index.plan_gate_note') }}</div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge rounded-pill text-bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                            <span id="selected-invitations-count">0</span> {{ __('selected') }}
                        </span>
                        <button type="submit"
                                class="btn btn-primary js-bulk-action"
                                formaction="{{ route('invitations.bulk_resend_selected') }}"
                                {{ !$canBulkResend ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane me-1"></i> {{ __('invitations.actions.bulk_resend_selected') }}
                        </button>
                        <button type="submit"
                                class="btn btn-outline-danger js-bulk-action js-confirm-action"
                                formaction="{{ route('invitations.bulk_destroy_selected') }}"
                                data-confirm="{{ __('invitations.index.confirm_delete') }}"
                                {{ !$canBulkResend ? 'disabled' : '' }}>
                            <i class="fas fa-trash-alt me-1"></i> {{ __('Delete selected') }}
                        </button>
                    </div>
                </div>

            @if(!$canCsvImport || !$canBulkResend)
                <div class="px-4 py-2 border-bottom bg-light small text-muted">
                    {{ __('invitations.index.plan_gate_note') }}
                    <a href="{{ route('billing.upgrade') }}" class="text-decoration-none">{{ __('invitations.index.upgrade_plan') }}</a>.
                </div>
            @endif

                <div class="table-responsive">
                <table class="table table-custom mb-0 align-middle">
                    <thead>
                    <tr>
                        <th style="width: 44px;" class="ps-4">
                            <input type="checkbox" id="select-all-invitations" class="form-check-input">
                        </th>
                        <th class="ps-2">{{ __('invitations.table.name') }}</th>
                        <th>{{ __('invitations.table.event') }}</th>
                        <th>{{ __('invitations.table.position') }}</th>
                        <th class="text-center">{{ __('invitations.table.guests') }}</th>
                        <th class="text-center">{{ __('invitations.table.status') }}</th>
                        <th>{{ __('invitations.table.sent_date') }}</th>
                        <th>{{ __('invitations.table.responded') }}</th>
                        <th class="text-end pe-4">{{ __('invitations.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse(($rows ?? collect()) as $row)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox"
                                       class="form-check-input invitation-row-checkbox"
                                       name="selected_ids[]"
                                       value="{{ $row->id }}">
                            </td>
                            <td class="ps-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initials">
                                        {{ strtoupper(substr($row->invitee_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $row->invitee_name }}</div>
                                        <div class="small text-muted" style="font-size: 0.8rem;">{{ $row->invitee_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $row->event?->title ?: $row->event?->name ?: __('invitations.table.no_event') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $row->invitee_position ?: __('invitations.table.no_position') }}</div>
                            </td>
                            <td class="text-center">
                                @if($row->selected_guests > 0)
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $row->selected_guests }}</span>
                                @else
                                    <span class="text-muted opacity-25">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($row->status) {
                                        'accepted' => 'badge-success',
                                        'declined' => 'badge-danger',
                                        'pending' => 'badge-warning',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                @php
                                    $statusKey = 'invitations.status.' . strtolower((string) $row->status);
                                    $statusLabel = __($statusKey);
                                @endphp
                                <span class="badge-soft {{ $statusClass }}">{{ $statusLabel === $statusKey ? ucfirst((string) $row->status) : $statusLabel }}</span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">
                                    {{ $row->responded_at ? \Carbon\Carbon::parse($row->responded_at)->format('M d, Y') : "-" }}
                                </div>
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    @php
                                        $whatsappMessage = '';
                                        $whatsappMessageTickets = '';

                                        // Use PublicUrlService so the link uses the tenant subdomain
                                        // when the company's plan has `custom_subdomain` enabled.
                                        $_invCompany = $row->company ?? $row->event?->company ?? null;
                                        $_urlSvc     = app(\App\Services\PublicUrlService::class);
                                        $invitationLink = $_invCompany
                                            ? $_urlSvc->rsvpUrl($_invCompany, $row->invitation_token)
                                            : route('rsvp.show', $row->invitation_token);
                                        $ticketsLink = $_invCompany
                                            ? $_urlSvc->downloadPdfUrl($_invCompany, $row->invitation_token)
                                            : route('downloadPdf', $row->invitation_token);

                                        $whatsappMessageTickets =  "{$row->invitee_name},\n\n" .
                                                                  "Thank you for accepting the invitation.\n\n" .
                                                                  "You can download your tickets here:\n" .
                                                                  "{$ticketsLink}";

                                        $descEn = strip_tags(optional($row->event)->description_en);
                                        $descAr = strip_tags(optional($row->event)->description);

                                        $whatsappMessage =  "{$row->invitee_name},\n\n" .
                                                            "{$descAr}\n\n" .
                                                            "{$descEn}\n\n" .
                                                            "للاطلاع على تفاصيل الدعوة / View invitation details:\n" .
                                                            "{$invitationLink}";
                                    @endphp

                                    {{-- Edit --}}
                                    <a href="{{ route('invitations.edit', $row->id) }}"
                                       class="btn-icon btn-icon-primary"
                                       title="{{ __('invitations.index.edit_invitation') }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- Copy Invitation --}}
                                    <button type="button"
                                            class="btn-icon js-copy-btn"
                                            data-copy-text="{{ $whatsappMessage }}"
                                            title="{{ __('invitations.index.copy_invite_message') }}">
                                        <i class="fas fa-copy"></i>
                                    </button>

                                    {{-- More Actions Dropdown --}}
                                    <div class="dropdown">
                                        <button class="btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('invitations.index.more_options') }}">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">

                                            @if($row->status != "pending")
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 js-copy-btn"
                                                            data-copy-text="{{ $whatsappMessageTickets }}">
                                                        <i class="fas fa-ticket-alt text-warning"></i> {{ __('invitations.index.copy_tickets_link') }}
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            @endif

                                            <li>
                                                <form action="{{ route('invitations.resend') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                                    <button class="dropdown-item d-flex align-items-center gap-2 js-confirm-action"
                                                            type="submit"
                                                            data-confirm="{{ __('invitations.index.confirm_resend_email') }}">
                                                        <i class="fas fa-sync-alt text-info"></i> {{ __('invitations.actions.resend') }}
                                                    </button>
                                                </form>
                                            </li>

                                            <li><hr class="dropdown-divider"></li>

                                            <li>
                                                <form action="{{ route('invitations.destroy', $row->id) }}" method="POST">
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
                            <tr><td colspan="9" class="text-center py-5 text-muted">{{ __('invitations.table.empty_filtered') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    {{ $rows->links() }}
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@section("infoCard")
    <div class="stats-card d-none d-md-block">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-chart-pie"></i>
            <h6 class="fw-bold mb-0">{{ __('invitations.index.overview') }}</h6>
        </div>

        <div class="stat-row">
            <span class="stat-label">{{ __('invitations.kpi.total') }}</span>
            <span class="stat-value">{{ $stats["all"] ?? '0' }}</span>
        </div>
        <hr class="my-2 opacity-25">
        <div class="stat-row">
            <span class="stat-label"><i class="fas fa-circle text-warning small me-1"></i> {{ __('invitations.kpi.pending') }}</span>
            <span class="stat-value">{{ $stats["pending"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label"><i class="fas fa-circle text-success small me-1"></i> {{ __('invitations.kpi.accepted') }}</span>
            <span class="stat-value">{{ $stats["accepted"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label"><i class="fas fa-circle text-danger small me-1"></i> {{ __('invitations.kpi.declined') }}</span>
            <span class="stat-value">{{ $stats["declined"] ?? '0' }}</span>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Copy function with visual feedback
        function copyToClipboard(button) {
            const text = button.getAttribute('data-copy-text') || '';
            navigator.clipboard.writeText(text).then(function() {
                // Check if element is a button or an anchor inside a dropdown
                let icon = button.querySelector('i');
                let originalClass = icon.className;

                // Change icon to checkmark
                icon.className = 'fas fa-check text-success';

                // Revert back after 2 seconds
                setTimeout(() => {
                    icon.className = originalClass;
                }, 2000);

            }, function(err) {
                console.error('Could not copy text: ', err);
                const i18n = document.getElementById('invitation-i18n');
                alert(i18n ? i18n.getAttribute('data-copy-failed') : 'Failed to copy to clipboard.');
            });
        }

        document.addEventListener('click', function (event) {
            const copyButton = event.target.closest('.js-copy-btn');
            if (copyButton) {
                event.preventDefault();
                copyToClipboard(copyButton);
                return;
            }

            const confirmButton = event.target.closest('.js-confirm-action');
            if (confirmButton) {
                return;
            }
        });

        function updateBulkSelectionState() {
            const rowCheckboxes = Array.from(document.querySelectorAll('.invitation-row-checkbox'));
            const checked = rowCheckboxes.filter(function (item) { return item.checked; });
            const selectedCountNode = document.getElementById('selected-invitations-count');
            const selectAllNode = document.getElementById('select-all-invitations');
            const bulkButtons = document.querySelectorAll('.js-bulk-action');

            if (selectedCountNode) {
                selectedCountNode.textContent = String(checked.length);
            }

            if (selectAllNode && rowCheckboxes.length > 0) {
                selectAllNode.checked = checked.length === rowCheckboxes.length;
                selectAllNode.indeterminate = checked.length > 0 && checked.length < rowCheckboxes.length;
            }

            bulkButtons.forEach(function (button) {
                if (!button.hasAttribute('disabled')) {
                    button.disabled = checked.length === 0;
                }
            });
        }

        document.addEventListener('change', function (event) {
            const selectAllNode = event.target.closest('#select-all-invitations');
            if (selectAllNode) {
                const rowCheckboxes = document.querySelectorAll('.invitation-row-checkbox');
                rowCheckboxes.forEach(function (item) {
                    item.checked = selectAllNode.checked;
                });
                updateBulkSelectionState();
                return;
            }

            if (event.target.closest('.invitation-row-checkbox')) {
                updateBulkSelectionState();
            }
        });

        const bulkForm = document.getElementById('bulk-invitations-form');
        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                const checked = document.querySelectorAll('.invitation-row-checkbox:checked');
                if (checked.length === 0) {
                    event.preventDefault();
                }
            });
        }

        updateBulkSelectionState();

        // Initialize Bootstrap Tooltips (If using Bootstrap 5)
        document.addEventListener("DOMContentLoaded", function(){
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
