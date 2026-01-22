@extends('layouts.app')

@section('title', 'Staff Directory')

@push('styles')
    <style>
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn-action { background: var(--grad-purple); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4); color: white; }
        .search-container { position: relative; max-width: 400px; }
        .search-input { border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 12px; padding: 12px 15px 12px 45px; width: 100%; font-size: 0.95rem; transition: 0.3s; }
        .search-input:focus { background: white; border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden; }
        .table-custom thead th { background: #f8fafc; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .table-custom tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .avatar-initials { width: 40px; height: 40px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; color: var(--primary-color); background: #eff6ff; margin-right: 12px; }
        .badge-soft { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-neutral { background: #f1f5f9; color: #64748b; }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark mb-1">Invitations</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-primary">Invitations</li>
                </ol>
            </nav>
        </div>

        <a href="{{route("invitations.create")}}" class="btn-action text-decoration-none">
            <i class="fas fa-paper-plane me-2"></i> Send New Invitation
        </a>
    </div>

    <div class="custom-card">
        <div class="card-body p-0">

            <div class="p-4 border-bottom bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

                <!-- Search -->
                <form action="{{ route('invitations.index') }}" method="get" class="w-100" style="max-width: 500px;">
                    <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fas fa-search text-muted"></i>
            </span>

                        <input type="text"
                               name="searchInput"
                               class="form-control"
                               placeholder="Search by name, email, phone, job,status"
                               value="{{ request('searchInput') }}">

                        <button class="btn btn-primary" type="submit">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Actions -->
                <div class="d-flex gap-2">


                    <a href="{{route("invitations.export")}}" class="btn btn-light border text-muted btn-sm rounded-3 px-3 fw-bold">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                    <tr>
                        <th class="ps-4">Employee Details</th>
                        <th>Job Title</th>
                        <th class="text-center">Guests</th>
                        <th class="text-center">Status</th>
                        <th>Sent Date</th>
                        <th>Responded Date</th>

                        <th class="text-end pe-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td class="ps-4">
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
                                <div class="fw-bold text-dark fs-6">{{ $row->invitee_position }}</div>
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
                                <span class="badge-soft {{ $statusClass }}">{{ ucfirst($row->status) }}</span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">        {{ $row->responded_at ? \Carbon\Carbon::parse($row->responded_at)->format('M d, Y') : "-" }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('invitations.resend') }}" method="POST" class="d-inline" onsubmit="return confirm('Regenerate ticket?');">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <button class="btn btn-light btn-sm text-primary border shadow-sm"><i class="fas fa-sync-alt"></i></button>
                                </form>
                                <form action="{{ route('invitations.destroy', $row->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this invitation?\nAll tickets related to this invitation will also be deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-light btn-sm text-danger border shadow-sm ms-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5">No records found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    {{ $rows->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section("infoCard")
    <div class="stats-card d-none d-md-block">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-chart-pie"></i>
            <h6 class="fw-bold mb-0">Overview</h6>
        </div>

        <div class="stat-row">
            <span class="stat-label">Total</span>
            <span class="stat-value">{{ $stats["all"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Pending</span>
            <span class="stat-value">{{ $stats["pending"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Accepted</span>
            <span class="stat-value">{{ $stats["accepted"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Maybe</span>
            <span class="stat-value">{{ $stats["maybe"] ?? '0' }}</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">Declined</span>
            <span class="stat-value">{{ $stats["declined"] ?? '0' }}</span>
        </div>
{{--        <div class="mb-1">--}}
{{--            <div class="d-flex justify-content-between mb-1">--}}
{{--                <span class="stat-label" style="font-size: 0.75rem;">Tickets Sold</span>--}}
{{--                <span class="stat-label fw-bold" style="font-size: 0.75rem;">75%</span>--}}
{{--            </div>--}}
{{--            <div class="progress-track">--}}
{{--                <div class="progress-fill" style="width: 75%;"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

@endsection
