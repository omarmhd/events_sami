<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SAMI Events'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-dark: #222222;
            --primary-light: #F4F6F8;
            --primary-accent: #DABC9A;
            --card: rgba(255, 255, 255, 0.95);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Manrope', sans-serif;
            color: var(--primary-dark);
            background: linear-gradient(135deg, var(--primary-light) 0%, #ffffff 100%);
            margin: 0;
            padding: 0;
        }

        html[dir="rtl"] body {
            font-family: 'Cairo', serif;
        }

        .hero {
            padding: 3rem 1rem 2rem;
        }

        .hero-shell {
            border-radius: 0;
            overflow: visible;
            background: transparent;
            color: var(--primary-dark);
            box-shadow: none;
            border: none;
        }

        .hero-banner {
            position: relative;
            min-height: 240px;
            background: linear-gradient(135deg, #0d3b37 0%, #0f6b62 50%, var(--primary-accent) 100%);
            border-radius: 28px;
            overflow: hidden;
        }

        .hero-banner img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
        }

        .hero-body {
            padding: 1.8rem 0 0;
            text-align: center;
        }

        @media (max-width: 767px) {
            .hero-banner img {
                height: 220px;
            }
            .hero-body {
                padding: 2rem 1.5rem;
            }
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 999px;
            padding: 0.6rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 700;
            width: fit-content;
            margin-bottom: 1.2rem;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-body h1 {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            line-height: 1.2;
            text-align: center;
        }

        @media (max-width: 767px) {
            .hero-body h1 {
                font-size: 1.7rem;
            }
        }

        .hero-body p {
            font-size: 1rem;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 1.3rem;
            text-align: center;
        }

        .meta-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-bottom: 1.4rem;
            justify-content: center;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 1rem;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid rgba(34, 34, 34, 0.08);
            font-size: 0.9rem;
            color: #1f2937;
        }

        .meta-pill i {
            color: var(--primary-accent);
        }

        .glass-card {
            background: var(--card);
            border: 1px solid rgba(34, 34, 34, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            padding: 2rem;
        }

        @media (max-width: 767px) {
            .glass-card {
                padding: 1.5rem;
            }
        }

        .card-header-section {
            border-bottom: 1px solid rgba(34, 34, 34, 0.08);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-label {
            text-transform: uppercase;
            font-weight: 600;
            color: var(--primary-accent);
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.8rem;
        }

        .section-desc {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 0.5rem;
        }

        .schedule-line {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(34, 34, 34, 0.06);
        }

        .schedule-line:last-child {
            border-bottom: none;
        }

        .schedule-time {
            font-weight: 700;
            color: var(--primary-accent);
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-dark);
            font-size: 0.95rem;
            margin-bottom: 0.6rem;
        }

        .form-control,
        .form-select {
            border: 1px solid rgba(34, 34, 34, 0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 3px rgba(218, 188, 154, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #3d3d3d 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            padding: 0.95rem 2rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1a1a1a 0%, #222 100%);
            box-shadow: 0 12px 28px rgba(34, 34, 34, 0.25);
            transform: translateY(-2px);
            color: #fff;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(218, 188, 154, 0.1) 0%, rgba(212, 188, 154, 0.05) 100%);
            border: 1px solid rgba(218, 188, 154, 0.3);
            color: var(--primary-dark);
            border-radius: 16px;
            padding: 1rem;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .badge-event-type {
            display: inline-block;
            background: var(--primary-accent);
            color: var(--primary-dark);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        @media (max-width: 576px) {
            .hero {
                padding: 2rem 0;
            }

            .hero-media {
                min-height: 200px;
            }

            .hero-copy {
                padding: 1.5rem;
            }

            .hero-copy h1 {
                font-size: 1.5rem;
            }

            .schedule-line {
                grid-template-columns: 80px 1fr;
                gap: 0.8rem;
            }

            .meta-pills {
                flex-direction: column;
                gap: 0.6rem;
                align-items: center;
            }

            .meta-pill {
                width: auto;
            }
        }

        .loading-spinner {
            display: none;
        }

        .loading-spinner.show {
            display: inline-block;
        }

        .event-footer {
            padding: 0 1rem 2.25rem;
        }

        .event-footer-inner {
            max-width: 980px;
            margin: 0 auto;
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.85rem 1.1rem;
            text-align: center;
            color: #4b5563;
            border-top: 1px solid rgba(34, 34, 34, 0.08);
        }

        .event-footer-brand {
            display: inline-flex;
            align-items: center;
        }

        .event-footer-copy {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1f2937;
        }

        .event-footer-email {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-dark);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(34, 34, 34, 0.08);
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .event-footer-email:hover {
            transform: translateY(-1px);
            color: var(--primary-dark);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.06);
        }

        .event-footer-email i {
            color: var(--primary-accent);
        }

        @media (max-width: 767px) {
            .event-footer {
                padding-bottom: 1.75rem;
            }

            .event-footer-inner {
                padding: 1rem 0.75rem;
            }

            .event-footer-email {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
@php
    $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'SAMI Events'));
    $contactEmail = \App\Models\SystemSetting::get('support_email', '');
@endphp
@yield('content')

<footer class="event-footer">
    <div class="event-footer-inner">
        <div class="event-footer-brand">
            <x-platform-logo size="sm" theme="light" />
        </div>

        <p class="event-footer-copy">مع تحيات منصة {{ $platformName }}</p>

        @if(!empty($contactEmail))
            <a class="event-footer-email" href="mailto:{{ $contactEmail }}">
                <i class="fas fa-envelope"></i>
                <span>{{ $contactEmail }}</span>
            </a>
        @endif
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
