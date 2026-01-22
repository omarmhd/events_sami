<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMI Event - @yield('title')</title>

    {{-- Fonts & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    {{-- Global Styles --}}
    <style>
        :root {
            --primary-bg: #F8FAFC;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --primary-color: #6366f1;
            --grad-purple: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --grad-blue: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --input-focus-border: #6366f1;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--primary-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: white; padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px; border-radius: 0 0 16px 16px;
        }

        @media (max-width: 991px) {
            .mobile-header { display: flex; justify-content: space-between; align-items: center; }
            .sidebar-col { display: none; } /* Hide sidebar on mobile by default */
            .sidebar-col.show { display: block; position: fixed; top: 0; left: 0; height: 100vh; z-index: 1000; background: rgba(255,255,255,0.95); width: 80%; max-width: 300px; padding-top: 20px; box-shadow: 5px 0 15px rgba(0,0,0,0.1); overflow-y: auto; }
        }

        .nav-link-custom.active { background: #eff6ff; color: var(--primary-color); font-weight: 700; }
        .nav-link-custom i { width: 24px; }

        /* --- جديد: تصميم بطاقة الإحصائيات --- */
        .stats-card {
            background: linear-gradient(135deg, #4f46e5 0%, #818cf8 100%); /* لون نيلي متدرج */
            color: white;
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        /* دائرة شفافة خلفية للزينة */
        .stats-card::before {
            content: ''; position: absolute; top: -20px; right: -20px;
            width: 100px; height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%; pointer-events: none;
        }

        .stat-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 12px;
        }
        .stat-label { font-size: 0.85rem; opacity: 0.9; }
        .stat-value { font-weight: 800; font-size: 1.1rem; }



    </style>

    @stack('styles')
</head>
<body>

<div class="mobile-header d-lg-none">
    <div class="fw-bold fs-5 text-dark">Sami<span style="color: #6366f1;">Event</span></div>
    <button class="btn btn-light border" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div class="container-fluid p-4">
    <div class="row g-4">

        <div class="col-lg-3 col-xl-2 sidebar-col" id="sidebarArea">
            @include('layouts.partials.sidebar')
        </div>

        {{-- Main Content Column --}}
        <div class="col-lg-9 col-xl-10 animate__animated animate__fadeIn">
            @yield('content')
        </div>

    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // إعدادات عامة للتوست
    const toastConfig = {
        duration: 3000,
        gravity: "top", // top or bottom
        position: "center", // left, center, right
        close: true,
        stopOnFocus: true, // يمنع اختفاء الرسالة عند وضع الماوس عليها
    };

    // 1. فحص رسائل النجاح (Success)
    @if(session('success'))
    Toastify({
        text: "{{ session('success') }}",
        ...toastConfig,
        style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)", // لون أخضر متدرج
        }
    }).showToast();
    @endif

    // 2. فحص رسائل الخطأ (Error)
    @if(session('error'))
    Toastify({
        text: "{{ session('error') }}",
        ...toastConfig,
        style: {
            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }
    }).showToast();
    @endif


    @if($errors->any())
    @foreach($errors->all() as $error)
    Toastify({
        text: "{{ $error }}",
        ...toastConfig,
        style: {
            background: "#dc3545", // لون أحمر ثابت
        }
    }).showToast();
    @endforeach
    @endif
</script>
</body>
</html>
<script>
    function toggleSidebar() {
        document.getElementById('sidebarArea').classList.toggle('show');
    }
</script>
@stack('scripts')
</body>
</html>
