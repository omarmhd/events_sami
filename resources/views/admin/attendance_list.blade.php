@extends('layout')

@section('title', 'Attendance List')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <div class="container-fluid px-4 py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-users me-2 text-primary-theme"></i>Attendance List
                </h2>
                <p class="text-muted mb-0">Manage and view all checked-in attendees.</p>
            </div>
            <a href="{{ route('dashboard.index') }}" class="btn btn-light shadow-sm text-muted">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">

                <form action="{{ route('attendance_list') }}" method="get" class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-end-0 ps-3">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    name="searchInput"
                                    class="form-control border-start-0 ps-0 py-2"
                                    placeholder="Search by name, email, job number..."
                                    value="{{ request('searchInput') }}"
                                    style="box-shadow: none;"
                                />
                                <button type="submit" class="btn btn-primary-theme px-4 text-white">
                                    Search
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-6 text-md-end">
                            <a href="/export" class="btn btn-success text-white shadow-sm rounded-3 px-3 py-2">
                                <i class="fas fa-file-excel me-2"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table">
                        <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-3"># Ticket</th>
                            <th>Employee Name</th>
                            <th>Job No</th>
                            <th>Email Address</th>
                            <th>Type</th>
                            <th>Checked In</th>
                        </tr>
                        </thead>
                        <tbody>

                        @empty($tickets)
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-search fa-3x mb-3 text-light-gray"></i>
                                        <p class="mb-0">No results found matching your search.</p>
                                    </div>
                                </td>
                            </tr>
                        @endempty

                        @isset($tickets)
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td data-label="# Ticket" class="fw-bold text-primary-theme ps-3">
                                        {{ $ticket->id }}
                                    </td>

                                    <td data-label="Name">
                                        <span class="fw-bold text-dark">{{ $ticket->employee_name }}</span>
                                    </td>

                                    <td data-label="Job No">
                                        <span class="badge bg-light text-dark border">
                                            {{ $ticket->employee_number }}
                                        </span>
                                    </td>

                                    <td data-label="Email" class="text-muted">
                                        {{ $ticket->employee_email }}
                                    </td>

                                    <td data-label="Type">
                                        @if($ticket->is_children == 'no')
                                            <span class="badge bg-primary-subtle text-primary-theme px-3 py-2 rounded-pill">
                                                <i class="fas fa-user-tie me-1"></i> Employee
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-dark px-3 py-2 rounded-pill">
                                                <i class="fas fa-child me-1"></i> Child
                                            </span>
                                        @endif
                                    </td>

                                    <td data-label="Checked In">
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i> {{ $ticket->checked_in_at }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                    </table>
                </div>

                @if(isset($tickets) && method_exists($tickets, 'links'))
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $tickets->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <style>
        /* General Page Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9; /* نفس خلفية صفحة اللوجن */
        }

        /* Custom Theme Colors */
        .text-primary-theme {
            color: #1c92d2 !important;
        }

        .bg-primary-theme {
            background-color: #1c92d2 !important;
        }

        .btn-primary-theme {
            background-color: #1c92d2;
            border-color: #1c92d2;
            transition: all 0.3s ease;
        }

        .btn-primary-theme:hover {
            background-color: #157ab0;
            border-color: #157ab0;
        }

        .bg-primary-subtle {
            background-color: rgba(28, 146, 210, 0.1);
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.15);
        }

        .text-warning-dark {
            color: #997404;
        }

        /* Table Styling */
        .custom-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f0f0f0;
            padding: 15px 10px;
        }

        .custom-table tbody td {
            padding: 15px 10px;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.95rem;
        }

        .custom-table tbody tr:hover {
            background-color: #fcfcfc;
        }

        /* Mobile Responsive View */
        @media (max-width: 768px) {
            .custom-table thead {
                display: none;
            }

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

            .custom-table tbody td:last-child {
                border-bottom: none;
            }

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
