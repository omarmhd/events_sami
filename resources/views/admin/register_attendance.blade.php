@extends('layout')

@section('title', 'Register Attendance')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <div class="container-fluid px-4 py-4 fade-in-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-user-check me-2 text-primary-theme"></i>Register Attendance
                </h2>
                <p class="text-muted mb-0">Search for tickets and check-in attendees.</p>
            </div>
            <a href="{{ route('dashboard.index') }}" class="btn btn-light shadow-sm text-muted rounded-3 px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-4">

                <form action="{{ route('search_on_ticket') }}" method="get" class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8 col-lg-7">
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-end-0 ps-3">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    id="searchInput"
                                    name="searchInput"
                                    class="form-control border-start-0 ps-0 py-2"
                                    placeholder="Search by name, email, job number, or ticket..."
                                    value="{{ request('searchInput') }}"
                                    style="box-shadow: none;"
                                />
                                <button type="submit" id="searchBtn" class="btn btn-primary-theme text-white px-4">
                                    Search
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-5 text-md-end">
                            <a href="/export" title="Export Excel" class="btn btn-success text-white shadow-sm rounded-3 px-3 py-2">
                                <i class="fas fa-file-excel me-2"></i> Export List
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle table-hover custom-table">
                        <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-3"># Ticket</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Checked In At</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="tableBody">
                        @empty($tickets)
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                        <p>No tickets found. Try searching for a name or ID.</p>
                                    </div>
                                </td>
                            </tr>
                        @endempty

                        @isset($tickets)
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td data-label="# Ticket" class="ps-3">
                                        <span class="fw-bold text-primary-theme">{{ $ticket->id }}</span>
                                    </td>

                                    <td data-label="Name">
                                        <span class="fw-bold text-dark">{{ $ticket->employee_name }}</span>
                                    </td>

                                    <td data-label="Email" class="text-muted small">
                                        {{ $ticket->employee_email }}
                                    </td>

                                    <td data-label="Type">
                                        @if($ticket->is_children == 'no')
                                            <span class="badge bg-primary-subtle text-primary-theme px-3 py-2 rounded-pill">
                                                Employee
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-dark px-3 py-2 rounded-pill">
                                                Child
                                            </span>
                                        @endif
                                    </td>

                                    <td data-label="Checked In At">
                                        @if($ticket->checked_in_at)
                                            <span class="text-success fw-bold small">
                                                <i class="fas fa-check-circle me-1"></i> {{ $ticket->checked_in_at }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td data-label="Actions" class="text-end pe-3">
                                        @if(!$ticket->checked_in_at)
                                            <a href="{{ route('checked_in', $ticket->id) }}" class="btn btn-sm btn-success text-white px-3 shadow-sm rounded-pill action-btn">
                                                <i class="fas fa-check me-1"></i> Register
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-light text-muted border px-3 rounded-pill" disabled>
                                                Done
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <style>
        /* General Page Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        /* Animations */
        .fade-in-up { animation: fadeInUp 0.5s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Theme Colors */
        .text-primary-theme { color: #1c92d2 !important; }

        .btn-primary-theme {
            background-color: #1c92d2;
            border-color: #1c92d2;
            transition: all 0.3s ease;
        }
        .btn-primary-theme:hover {
            background-color: #157ab0;
            border-color: #157ab0;
        }

        /* Badge Colors */
        .bg-primary-subtle { background-color: rgba(28, 146, 210, 0.1); }
        .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.15); }
        .text-warning-dark { color: #997404; }

        /* Table Styling */
        .custom-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 1.2rem 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            background-color: #fcfcfc;
        }

        .custom-table tbody td {
            padding: 1rem 0.5rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .custom-table tbody tr:hover { background-color: #fcfcfc; }

        /* Action Button Effect */
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2) !important;
        }

        /* Mobile Responsive View */
        @media (max-width: 768px) {
            .custom-table thead { display: none; }

            .custom-table tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #eee;
                border-radius: 12px;
                background-color: #fff;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
                padding: 15px;
            }

            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #f5f5f5;
            }

            .custom-table tbody td:last-child { border-bottom: none; }

            .custom-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #8898aa;
                font-size: 0.85rem;
                text-transform: uppercase;
            }
        }
    </style>
@endsection
