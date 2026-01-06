@extends('layout')

@section('title', 'Staff Management')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <div class="container-fluid px-4 py-4 fade-in-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Staff Directory</h2>
                <p class="text-muted mb-0">All employees registered for the event.</p>
            </div>
            <a href="{{ route('dashboard.index') }}" class="btn btn-light shadow-sm text-muted rounded-3 px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-4">

                <form action="{{ route('emps') }}" method="get" class="mb-4">
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
                                    placeholder="Search by name, email, or job number..."
                                    value="{{ request('searchInput') }}"
                                    style="box-shadow: none;"
                                >
                                <button type="submit" class="btn btn-primary-theme text-white px-4">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle table-hover custom-table">
                        <thead class="bg-light text-muted">
                        <tr>
                            <th scope="col" class="ps-3" width="5%">#</th>
                            <th scope="col" width="30%">Employee</th>
                            <th scope="col">Job Info</th>
                            <th scope="col" class="text-center">Invited Children</th>
                            <th scope="col">Registered At</th>
                            <th scope="col" class="text-end pe-3">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($emps as $emp)
                            <tr>
                                <td class="ps-3"><span class="text-muted fw-bold">{{ $emp->id }}</span></td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3 shadow-sm">
                                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $emp->name }}</div>
                                            <div class="small text-muted">{{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-light text-dark border mb-1 align-self-start">
                                            <i class="fas fa-id-badge me-1 text-primary-theme"></i> {{ $emp->employee_number }}
                                        </span>
                                        <small class="text-primary-theme fw-bold">{{ $emp->event_name }}</small>
                                    </div>
                                </td>

                                <td class="text-center">
                                    @if($emp->count_children > 0)
                                        <span class="badge bg-primary-subtle text-primary-theme rounded-pill px-3 py-2">
                                            <i class="fas fa-child me-1"></i> {{ $emp->count_children }} Children
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="small text-dark fw-bold">
                                        {{ \Carbon\Carbon::parse($emp->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ \Carbon\Carbon::parse($emp->created_at)->format('h:i A') }}
                                    </div>
                                </td>

                                <td class="text-end pe-3">
                                    <form action="{{ route('resendTickets') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to regenerate tickets for {{ $emp->name }}?');">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-primary-theme action-btn" title="Regenerate Tickets">
                                            <i class="fas fa-sync-alt me-1"></i> Resend Tickets
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-search fa-3x mb-3"></i>
                                        <p>No employees found matching your search.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($emps, 'links'))
                    <div class="d-flex justify-content-end mt-4">
                        {{ $emps->withQueryString()->links() }}
                        {{-- ملاحظة: إذا كنت تستخدم Bootstrap 5 تأكد أن إعدادات الـ Pagination في Laravel مضبوطة على Bootstrap 5 --}}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Animation */
        .fade-in-up { animation: fadeInUp 0.6s ease-out; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* General Font */
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }

        /* Theme Colors */
        .text-primary-theme { color: #1c92d2 !important; }
        .bg-primary-theme { background-color: #1c92d2 !important; }

        /* Buttons */
        .btn-primary-theme {
            background-color: #1c92d2;
            border-color: #1c92d2;
            transition: all 0.3s ease;
        }
        .btn-primary-theme:hover {
            background-color: #157ab0;
            border-color: #157ab0;
        }

        .btn-outline-primary-theme {
            color: #1c92d2;
            border-color: #1c92d2;
            background-color: transparent;
        }
        .btn-outline-primary-theme:hover {
            background-color: #1c92d2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(28, 146, 210, 0.2);
        }

        /* Avatar */
        .avatar-circle {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #1c92d2 0%, #157ab0 100%); /* Theme Gradient */
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Badges */
        .bg-primary-subtle { background-color: rgba(28, 146, 210, 0.1); }

        /* Table Styling */
        .custom-table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1.2rem 0.5rem;
            border-bottom: 2px solid #f0f0f0;
            background-color: #fcfcfc;
        }

        .custom-table tbody td {
            padding: 1.2rem 0.5rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .custom-table tbody tr:hover { background-color: #fcfcfc; }

    </style>
@endsection
