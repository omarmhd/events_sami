<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            height: 120px;
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }
        .btn-custom i {
            font-size: 2.5rem;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        /* Card Hover Effects */
        .transition-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 1.25rem; /* Rounded corners */
        }

        .transition-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.12) !important;
        }

        /* Icon Containers */
        .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Light Background Colors for Icons */
        .bg-primary-light { background-color: #f0f7ff; }
        .bg-info-light    { background-color: #e6fbff; }
        .bg-success-light { background-color: #f0fff4; }
        .bg-warning-light { background-color: #fffaf0; }

        /* Visual Flourish */
        .header-line {
            width: 50px;
            height: 3px;
            background: #0d6efd;
            border-radius: 50px;
        }

        .card-title {
            color: #2d3748;
            margin-top: 0.5rem;
            font-size: 1.1rem;
        }

        /* Mobile Tweaks */
        @media (max-width: 576px) {
            .card-body { padding: 1.5rem 1rem !important; }
            .icon-wrapper { width: 48px; height: 48px; }
            .icon-wrapper i { font-size: 1.25rem !important; }
            .card-title { font-size: 0.95rem; }


        }
        .transition-hover {
            transition: all 0.3s ease;
            border-radius: 16px !important;
        }

        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.08) !important;
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        /* Colors */
        .bg-primary-light { background-color: #eef2ff; }
        .bg-info-light    { background-color: #e0f7fa; }
        .bg-success-light { background-color: #ebfaf0; }
        .bg-warning-light { background-color: #fff9eb; }
        .bg-danger-light  { background-color: #fff2f2; }

        .header-line {
            width: 40px;
            height: 4px;
            background: #0d6efd;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    @yield('content')
</div>
</body>
</html>
