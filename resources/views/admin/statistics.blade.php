<!-- resources/views/stats.blade.php -->
@extends('layout')

@section('title', 'Statistics')

@section('content')
    <div class="text-left mb-4 d-flex justify-content-between align-items-center">
        <h6 class="page-title">
            <i class="fas fa-chart-bar"></i> Statistics Attendance
        </h6>
        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i>
        </a>
    </div>
    <div class="container my-4">
        <!-- Summary Row -->
        <div class="row g-4">
            <!-- Card 1: Checked Public -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">All Attendance</h5>
                        <p class="display-4 fw-bold text-primary">{{ $checked_public }}</p>
                        <p class="text-muted">Total number of attendees.</p>
                    </div>
                </div>
            </div>
            <!-- Card 2: Checked Employees -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Employee Attendance</h5>
                        <p class="display-4 fw-bold text-success">{{ $checked_emp }}</p>
                        <p class="text-muted">Number of employees who attended.</p>
                    </div>
                </div>
            </div>
            <!-- Card 3: Checked Children -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Children Attendance</h5>
                        <p class="display-4 fw-bold text-danger">{{ $checked_ch }}</p>
                        <p class="text-muted">Number of children who attended.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Section -->
        <div class="row g-4 mt-4">
            <!-- Card 4: All Tickets -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">All Tickets</h5>
                        <p class="display-4 fw-bold text-primary">{{ $tickets_all }}</p>
                        <p class="text-muted">Total number of tickets processed.</p>
                    </div>
                </div>
            </div>
            <!-- Card 5: Employee Tickets -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Employee Tickets</h5>
                        <p class="display-4 fw-bold text-success">{{ $tickets_emp }}</p>
                        <p class="text-muted">Tickets resolved for employees.</p>
                    </div>
                </div>
            </div>
            <!-- Card 6: Children Tickets -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Children Tickets</h5>
                        <p class="display-4 fw-bold text-danger">{{ $tickets_ch }}</p>
                        <p class="text-muted">Tickets resolved for children.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
