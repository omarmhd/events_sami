@extends('layouts.app')

@section('title', 'Tickets')

@push('styles')
    <style>
        /* --- Page Header --- */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn-action { background: var(--grad-purple, linear-gradient(135deg, #6366f1, #8b5cf6)); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4); color: white; }
        .avatar-initials { width: 40px; height: 40px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; color: var(--primary-color, #4f46e5); background: #eff6ff; margin-right: 12px; }

        /* --- Responsive Table/List Styles --- */
        .list-header { background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; padding: 16px; border-radius: 12px 12px 0 0; border: 1px solid #e2e8f0; border-bottom: none; }

        /* The Row/Card Container */
        .invitation-item {
            background: white;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px;
            transition: background 0.2s;
        }
        .invitation-item:last-child { border-bottom: none; }
        .invitation-item:hover { background: #fdfdfd; }

        /* Mobile specific adjustments for the single loop */
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .btn-action { width: 100%; }

            /* Transform row into card on mobile */
            .invitation-item {
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                margin-bottom: 16px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                padding: 20px;
                position: relative; /* For absolute positioning of delete button */
            }

            /* Mobile Labels */
            .mobile-label { display: block; font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }

            /* Delete button absolute on mobile top-right */
            .delete-btn-wrapper { position: absolute; top: 15px; right: 15px; }
        }

        /* Utilities */
        .badge-soft { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }

        /* Stats Scroll (Mobile) */
        .mobile-stats-container { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 20px; scrollbar-width: none; }
        .mobile-stats-container::-webkit-scrollbar { display: none; }
        .mobile-stat-box { min-width: 110px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    </style>
@endpush

@section('content')

    {{-- 1. Header & Stats (Same as before) --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold text-dark mb-1">Tickets</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-primary">Tickets</li>
                </ol>
            </nav>
        </div>
{{--        <a href="{{route("invitations.create")}}" class="btn-action text-decoration-none"><i class="fas fa-paper-plane me-2"></i> Send New Invitation</a>--}}
    </div>

    {{-- Mobile Stats --}}
{{--    <div class="d-md-none">--}}
{{--        <div class="mobile-stats-container">--}}
{{--            <div class="mobile-stat-box"><span class="fw-bold fs-5">{{ $stats["all"] ?? '0' }}</span><span class="small text-muted">Total</span></div>--}}
{{--            <div class="mobile-stat-box" style="border-bottom: 3px solid #ffc107;"><span class="fw-bold fs-5 text-warning">{{ $stats["pending"] ?? '0' }}</span><span class="small text-muted">Pending</span></div>--}}
{{--            <div class="mobile-stat-box" style="border-bottom: 3px solid #198754;"><span class="fw-bold fs-5 text-success">{{ $stats["accepted"] ?? '0' }}</span><span class="small text-muted">Accepted</span></div>--}}
{{--        </div>--}}
{{--    </div>--}}

    {{-- 2. Search Bar --}}
    <div class="bg-white border rounded-top-4 p-3 mb-0 border-bottom">
        <form action="{{ route('invitations.index') }}" method="get" class="w-100" style="max-width: 500px;">
            <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="fas fa-search text-muted"></i>
            </span>

                <input type="text"
                       name="searchInput"
                       class="form-control"
                       placeholder="Search by name, email,job"
                       value="{{ request('searchInput') }}">

                <button class="btn btn-primary" type="submit">
                    Search
                </button>
            </div>
        </form>
    </div>

    {{-- 3. UNIFIED LIST (Responsive Table) --}}
    {{-- قمنا بإزالة overflow-hidden --}}
    <div class="bg-white border border-top-0 rounded-bottom-4">
        {{-- Table Head (Visible Desktop ONLY) --}}
        <div class="d-none d-md-flex row list-header m-0">
            <div class="col-md-4">Invitee</div>
            <div class="col-md-2">Job Title</div>
            <div class="col-md-4 text-center">Main Check-in</div>
            <div class="col-md-2">Guests</div>
{{--            <div class="col-md-1 text-end">Action</div>--}}
        </div>

        {{-- Single Loop for Both Mobile & Desktop --}}
        <div class="p-3 p-md-0"> @forelse($rows as $row)
                @php
                    $mainTicket = $row->InvitationQrs->where('type','main')->first();
                    $guests = $row->InvitationQrs->where('type','guest');
                @endphp

                <div class="invitation-item row align-items-center m-0">

                    {{-- 1. Invitee Info --}}
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-initials">{{ strtoupper(substr($row->invitee_name, 0, 1)) }}</div>
                            <div>
                                <div class="fw-bold text-dark">{{ $row->invitee_name }}</div>
                                <div class="small text-muted">{{ $row->invitee_email }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Job Title --}}
                    <div class="col-6 col-md-2 mb-3 mb-md-0">
                        <span class="mobile-label d-md-none">Job Title</span> {{-- Mobile Label --}}
                        <div class="text-secondary small fw-bold">
                            <i class="fas fa-briefcase d-md-none me-1 opacity-50"></i> {{ $row->invitee_position ?? '-' }}
                        </div>
                    </div>



                    {{-- 4. Main Check-in Button --}}
                    <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                        @if($mainTicket && $mainTicket->is_used)
                            <div class="w-100 d-md-none btn btn-soft-success mb-2 disabled"><i class="fas fa-check"></i> Checked In</div>
                            <span class="d-none d-md-inline badge-soft badge-success">Checked In</span>
                        @else
                            <form action="{{route("attendance.checked_in")}}" method="POST" class="d-block w-100">
                                @csrf
                                <input type="hidden" name="id" value="{{ $mainTicket ? $mainTicket->id : '' }}">
                                <button class="btn btn-outline-primary w-100 d-md-none" {{ !$mainTicket ? 'disabled' : '' }}>
                                    Check-in Main
                                </button>
                                 Desktop Button (Icon)
                                <button class="d-none d-md-inline-block btn btn-light btn-sm text-success border shadow-sm" {{ !$mainTicket ? 'disabled' : '' }} title="Check-in">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- 5. Guests (Updated Dropdown) --}}
                    <div class="col-12 col-md-2 mb-3 mb-md-0 position-relative">
                        @if($guests->count())
                            <div class="dropdown">

                                <button class="btn btn-light btn-sm border dropdown-toggle w-100 w-md-auto d-flex justify-content-between justify-content-md-center align-items-center"
                                        type="button"
                                        id="dropdownMenuButton-{{$row->id}}"
                                        data-bs-toggle="dropdown"
                                        data-bs-display="static"
                                        aria-expanded="false">
                                    <span>Guests ({{ $guests->where('is_used', true)->count() }}/{{ $guests->count() }})</span>
                                </button>

                                {{--
                                    2. z-index: 1050: لضمان ظهور القائمة فوق الصفوف التالية
                                    3. max-height + overflow: لعمل سكرول داخل القائمة نفسها
                                --}}
                                <ul class="dropdown-menu p-2 shadow-lg border-0 w-100"
                                    aria-labelledby="dropdownMenuButton-{{$row->id}}"
                                    style="min-width: 240px; max-height: 200px; overflow-y: auto; z-index: 1050;">

                                    @foreach($guests as $index => $guest)
                                        <li class="d-flex justify-content-between align-items-center mb-2 px-2 border-bottom pb-2">
                                            <span class="small fw-bold text-dark">Guest {{ $index + 1 }}</span>
                                            @if($guest->is_used)
                                                <span class="badge-soft badge-success"><i class="fas fa-check"></i></span>
                                            @else
                                                <form action="{{route("attendance.checked_in")}}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $guest->id }}">
                                                    <button class="btn btn-sm btn-outline-primary py-0" style="font-size: 0.75rem">Check-in</button>
                                                </form>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <span class="text-muted small d-none d-md-block">No Guests</span>
                        @endif
                    </div>

                    {{-- 6. Delete Action --}}
{{--                    <div class="col-12 col-md-1 text-end delete-btn-wrapper">--}}
{{--                        <form action="{{ route('invitations.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Delete?');">--}}
{{--                            @csrf @method('DELETE')--}}
{{--                            <button class="btn btn-light btn-sm text-danger border shadow-sm"><i class="fas fa-trash-alt"></i></button>--}}
{{--                        </form>--}}
{{--                    </div>--}}

                </div>
            @empty
                <div class="text-center py-5">
                    <div class="text-muted">No invitations found</div>
                </div>
            @endforelse

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $rows->links() }}
            </div>

        </div>
    </div>
@endsection

@section("infoCard")
    {{-- Sidebar Stats (Desktop Only) --}}
    <div class="stats-card d-none d-md-block">
        <h6 class="fw-bold mb-3">Overview</h6>
        <div class="stat-row"><span class="stat-label">Total Tickets</span><span class="stat-value">{{ $stats->total ?? '0' }}</span></div>
        <div class="stat-row"><span class="stat-label">Checked In</span><span class="stat-value">{{ $stats->is_used ?? '0' }}</span></div>
        <div class="stat-row"><span class="stat-label">Not Checked</span><span class="stat-value">{{ $stats->not_checked ?? '0' }}</span></div>

    </div>
@endsection
