@extends('layout')

@section('title', 'Smart Dashboard')

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary-bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --text-main: #0F172A;
            --text-muted: #64748B;

            /* Modern Gradients */
            --grad-purple: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --grad-blue: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            --grad-green: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            --grad-orange: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            --grad-dark: linear-gradient(135deg, #1e293b 0%, #334155 100%);

            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Ambient Background Background */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
            background: var(--primary-bg);
        }

        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.4;
            animation: floatOrb 10s infinite ease-in-out alternate;
        }

        .orb-1 {
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: #6366f1;
        }

        .orb-2 {
            bottom: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: #3b82f6;
            animation-delay: -5s;
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 50px); }
        }

        /* Hero Section */
        .dashboard-header {
            padding: 3rem 0;
            position: relative;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 50px;
            box-shadow: var(--shadow-card);
            margin-bottom: 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .welcome-badge i {
            color: #f59e0b;
            margin-right: 0.5rem;
        }

        .dashboard-title {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(to right, #1e293b, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 2rem;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--shadow-card);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .icon-container {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.4s ease;
            position: relative;
            z-index: 2;
        }

        .dashboard-card:hover .icon-container {
            transform: scale(1.1) rotate(5deg);
        }

        .card-label {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .card-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.5;
            position: relative;
            z-index: 2;
        }

        /* Feature Card (Wide) */
        .feature-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--grad-dark);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .feature-card:hover {
            transform: scale(1.01);
            box-shadow: var(--shadow-hover);
        }

        .feature-card:hover::before {
            width: 10px;
        }

        .feature-content h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .feature-action {
            width: 50px;
            height: 50px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-action {
            background: var(--text-main);
            color: white;
            transform: translateX(5px);
        }

        /* Logout Button */
        .btn-logout {
            background: white;
            border: 2px solid #e2e8f0;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: #ef4444;
            transform: translateY(-2px);
        }

        /* Gradients */
        .bg-grad-purple { background: var(--grad-purple); }
        .bg-grad-blue { background: var(--grad-blue); }
        .bg-grad-green { background: var(--grad-green); }
        .bg-grad-orange { background: var(--grad-orange); }
        .bg-grad-dark { background: var(--grad-dark); }

        /* Animation Delays */
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        @media (max-width: 768px) {
            .dashboard-title { font-size: 2rem; }
            .feature-card { flex-direction: column; text-align: center; gap: 1.5rem; }
            .feature-card::before { width: 100%; height: 6px; top: 0; left: 0; }
        }
    </style>

    <div class="ambient-bg">
        <div class="ambient-orb orb-1"></div>
        <div class="ambient-orb orb-2"></div>
    </div>

    <div class="container pb-5">
        <header class="dashboard-header text-center animate__animated animate__fadeInDown">
            <div class="welcome-badge">
                <i class="fas fa-sun"></i> Hi Admin
            </div>
            <h1 class="dashboard-title">Overview Dashboard</h1>
            <p class="text-muted">Manage your event ecosystem efficiently.</p>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp delay-1">
                <a href="{{ route('register_attendance') }}" class="dashboard-card">
                    <div class="icon-container bg-grad-purple">
                        <i class="fas fa-user-pen"></i>
                    </div>
                    <h3 class="card-label">Check-in</h3>
                    <p class="card-desc">Manual registration for attendees</p>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp delay-2">
                <a href="{{ route('qr') }}" class="dashboard-card">
                    <div class="icon-container bg-grad-blue">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="card-label">QR Scanner</h3>
                    <p class="card-desc">Instant digital ticket validation</p>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp delay-3">
                <a href="{{ route('attendance_list') }}" class="dashboard-card">
                    <div class="icon-container bg-grad-green">
                        <i class="fas fa-table-list"></i>
                    </div>
                    <h3 class="card-label">Live Records</h3>
                    <p class="card-desc">Real-time attendance list</p>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-3 animate__animated animate__fadeInUp delay-4">
                <a href="{{ route('statistics') }}" class="dashboard-card">
                    <div class="icon-container bg-grad-orange">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="card-label">Analytics</h3>
                    <p class="card-desc">Data insights & reports</p>
                </a>
            </div>
        </div>

        <div class="row mb-5 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="col-12">
                <a href="/emps" class="text-decoration-none">
                    <div class="feature-card">
                        <div class="d-flex align-items-center gap-4 flex-column flex-md-row">
                            <div class="icon-container bg-grad-dark mb-0" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fas fa-users-gear"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Staff Management Hub</h4>
                                <p class="mb-0 text-muted">All employees registered for the event and resend digital tickets.</p>
                            </div>
                        </div>
                        <div class="feature-action">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="text-center animate__animated animate__fadeIn animate__delay-2s">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-power-off"></i>  Logout
                </button>
            </form>
            <p class="mt-3 text-muted" style="font-size: 0.8rem;"></p>
        </div>
    </div>
@endsection
