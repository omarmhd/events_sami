<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$event->title_en}} - RSVP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary-color: #DABC9A;
            --primary-dark: #b89b7c;
            --text-dark: #1F2937;
            --bg-body: #Fcfbf9;
            --white: #ffffff;
            --shadow-card: 0 20px 40px -5px rgba(0, 0, 0, 0.08);
            --radius-lg: 24px;
            --beige-medium: #eaddca;
            --beige-deep: #d2b48c;

            /* --- اللون الذي ستختاره (خلفية الشعار والعنوان العربي) --- */
            /* يمكنك تغيير هذا الكود (#224D59) إلى أي لون تريده */
            --custom-bg-color: #224D59;
            --custom-text-color: #ffffff; /* لون النص/الأيقونة فوق الخلفية */
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-body); color: var(--text-dark); margin: 0; overflow-x: hidden; }

        .top-banner {
            background: linear-gradient(135deg, var(--beige-medium) 0%, var(--beige-deep) 100%);
            height: 380px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            position: relative;
        }

        /* --- حاوية الشعار الجديدة (مع الخلفية) --- */
        .logo-container {
            position: absolute;
            top: 30px;
            left: 30px;

            padding: 15px; /* مساحة داخلية حول الشعار */
            border-radius: 16px; /* زوايا دائرية للخلفية */
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner-logo {
            width: 80px; /* التحكم بحجم الشعار هنا */
            height: auto;
            /* لم نعد بحاجة للـ absolute هنا لأن الحاوية تقوم بالمهمة */
        }

        .event-title { font-size: 2.8rem; font-weight: 800; color: #3e2b26; line-height: 1.2; }

        /* --- تنسيق العنوان العربي (مع الخلفية) --- */
        .event-title-ar {
            font-family: 'Cairo', sans-serif;
            font-size: 2rem;
            font-weight: 700;


            color: #3e2b26;

            display: inline-block; /* ليأخذ حجم النص فقط */
            padding: 5px 25px;     /* هوامش داخلية */
            border-radius: 50px;   /* شكل كبسولة */
            margin-top: 15px;
        }

        @media (max-width: 576px) {
            .logo-container {
                top: 20px;
                left: 20px;
                padding: 10px;
            }
            .banner-logo {
                width: 50px;
            }
            .event-title { font-size: 2rem; }
            .event-title-ar { font-size: 1.2rem; padding: 5px 15px; }
        }

        .main-card-container { margin-top: -100px; padding-bottom: 50px; }
        .floating-card { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-card); padding: 40px; border: 1px solid rgba(218, 188, 154, 0.2); }
        .event-meta { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee; }
        .meta-item { display: flex; align-items: center; gap: 10px; background: #fffaf5; padding: 12px 25px; border-radius: 50px; font-size: 0.9rem; font-weight: 600; border: 1px solid var(--beige-medium); }

        .status-options { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 25px; }
        @media (max-width: 576px) { .status-options { grid-template-columns: 1fr; } }

        .status-tile { background: white; border: 2px solid #e5e7eb; border-radius: 16px; padding: 25px 10px; cursor: pointer; transition: all 0.3s ease; text-align: center; }
        .status-tile:hover { border-color: var(--primary-color); transform: translateY(-4px); }
        .status-tile.active-accept { border-color: #198754; background-color: #f2fcf6; }
        .status-tile.active-accept i { color: #198754 !important; }
        .status-tile.active-maybe { border-color: #ffc107; background-color: #fffbf0; }
        .status-tile.active-maybe i { color: #ffc107 !important; }
        .status-tile.active-decline { border-color: #dc3545; background-color: #fef5f5; }
        .status-tile.active-decline i { color: #dc3545 !important; }

        .action-section { display: none; background: #fcfbf9; padding: 30px; border-radius: 16px; margin-top: 30px; border: 1px solid #eee; animation: slideDown 0.4s ease; }
        .btn-main { background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; font-weight: 700; padding: 16px; width: 100%; border-radius: 12px; border: none; margin-top: 25px; font-size: 1.1rem; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
        .spinner-border-sm { --bs-spinner-width: 1.3rem; --bs-spinner-height: 1.3rem; }

        /* --- تصميم بطاقة الوصف (Description Card) --- */
        .description-card {
            background: #fdfcfb;
            border: 1px solid #efe6dc;
            border-radius: 12px;
            padding: 30px 20px;
            margin: 0 auto 40px auto;
            max-width: 90%;
            position: relative;
        }

        .description-card::before, .description-card::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border: 1px solid #DABC9A;
            transition: all 0.3s ease;
        }

        .description-card::before { top: -5px; left: -5px; border-right: none; border-bottom: none; border-radius: 12px 0 0 0; }
        .description-card::after { bottom: -5px; right: -5px; border-left: none; border-top: none; border-radius: 0 0 12px 0; }

        .desc-en {
            font-family: 'Poppins', sans-serif; font-size: 1.1rem; color: #555; font-weight: 500; line-height: 1.6; letter-spacing: 0.5px; margin-bottom: 20px;
        }
        .desc-divider { display: flex; align-items: center; justify-content: center; margin: 15px 0; }
        .desc-divider span { height: 1px; width: 50px; background-color: #eaddca; }
        .desc-divider i { color: #DABC9A; margin: 0 15px; font-size: 10px; }
        .desc-ar {
            font-family: 'Cairo', sans-serif; font-size: 1.35rem; color: #2c2c2c; font-weight: 700; line-height: 1.7; margin-bottom: 0;
        }
    </style>
</head>
<body>

@php
    $hasResponded = $guest->status !== 'pending';
    $isAccepted = $guest->status === 'accepted';
    $isMaybe = $guest->status === 'maybe';
@endphp

<div class="top-banner">

    <div class="logo-container">
        <svg class="banner-logo" xmlns="http://www.w3.org/2000/svg" id="Layer_2" viewBox="0 0 327.46 134.25">
            <defs>
                <style>
                    /* جعلنا الشعار أبيض ليظهر بوضوح فوق الخلفية الملونة */
                    .cls-1 { fill: #ffffff; }
                    .cls-2 { fill: #ffffff; }
                </style>
            </defs>
            <g id="Layer_1-2">
                <g>
                    <polygon class="cls-1" points="148.89 80.33 120.48 80.33 107.5 96.5 157.64 96.5 162.73 105.42 185.43 105.42 147.07 34.58 133.51 51.47 148.89 80.33"/>
                    <polygon class="cls-1" points="297.5 34.58 297.5 34.58 272.73 34.58 244.71 72.75 216.46 34.58 191.63 34.58 191.6 34.58 191.6 105.42 211.56 105.42 211.56 61.51 244.77 106.4 277.68 61.58 277.68 105.42 297.64 105.42 297.64 34.58 297.5 34.58"/>
                    <rect class="cls-1" x="307.5" y="34.58" width="19.96" height="70.85"/>
                    <polygon class="cls-2" points="118.2 34.58 65.09 134.25 67.05 134.25 147.07 34.58 118.2 34.58"/>
                    <polygon class="cls-2" points="149.8 0 124.42 25.37 149.8 25.37 149.8 0"/>
                    <path class="cls-1" d="M83.39,53.85v-19.27H15.8c-5.59,.17-8.31,1.77-11.17,4.62-2.86,2.86-4.63,6.81-4.63,11.17,0,8.86,6.26,12.66,13,15.99l40.38,19.95H.54v19.11H70.69l13.29-24.95L27.33,53.85h56.06Z"/>
                </g>
            </g>
        </svg>
    </div>

    <div class="container animate__animated animate__fadeIn" style="margin-top: -50px;">
        <h1 class="event-title mb-0">{{$event->title_en}}</h1>

        <h1 class="event-title-ar">{{$event->title}}</h1>
    </div>
</div>

<div class="container main-card-container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="floating-card animate__animated animate__zoomIn mb-5"
                 id="successSection"
                 style="{{ $hasResponded ? 'display: block;' : 'display: none;' }}">

                <div class="text-center py-5">
                    <div class="mb-4">
                        <i id="successIcon"
                           class="@if($isAccepted) fas fa-envelope-circle-check text-success
                                  @elseif($isMaybe) fas fa-question-circle text-warning
                                  @else far fa-check-circle text-muted @endif"
                           style="font-size: 5rem;"></i>
                    </div>

                    <h2 class="fw-bold mb-3" id="successTitle"
                        style="@if($isAccepted) color: #198754; @elseif($isMaybe) color: #ffc107; @else color: #6c757d; @endif">
                        {{ $isAccepted ? 'Attendance Confirmed!' : ($isMaybe ? 'Response Recorded' : 'Response Recorded') }}
                    </h2>

                    <p class="lead mb-4" id="successMessage">
                        @if($hasResponded)
                            @if($isAccepted)
                                You have already confirmed your attendance.<br>Please check your email for the QR Code.
                            @elseif($isMaybe)
                                You marked as 'Maybe'. Please update us when you are sure.
                            @else
                                You have previously declined this invitation.
                            @endif
                        @else
                            Processing...
                        @endif
                    </p>
                </div>
            </div>

            <div class="floating-card animate__animated animate__fadeInUp"
                 id="mainFormCard"
                 style="{{ $hasResponded ? 'display: none;' : 'display: block;' }}">

                <div class="event-meta">
                    <div class="meta-item"><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($event->date)->format('D M d, Y') }}</div>
                    <div class="meta-item"><i class="far fa-clock"></i> {{$event->from_time}}-{{$event->to_time}}</div>
                    <div class="meta-item"><a href="https://maps.app.goo.gl/CcbukaXy4DyZckLy7"> <i class="fas fa-location-dot"></i> {{$event->address}}</a></div>
                </div>

                <div class="text-center mb-5">
                    <div class="badge bg-warning-subtle text-warning-emphasis mb-3 px-4 py-2 rounded-pill">Exclusive Invitation</div>
                    <div class="description-card animate__animated animate__fadeIn">
                        <p class="desc-en" style="text-align: left">
                            {{$event->description_en}}
                        </p>

                        <div class="desc-divider">
                            <span></span>
                            <i class="fas fa-circle"></i>
                            <span></span>
                        </div>

                        <p class="desc-ar" style="text-align: right">
                            {{$event->description}}
                        </p>
                    </div>                </div>

                <form id="rsvpForm">
                    <input type="hidden" name="response_status" id="response_status">

                    <h5 class="fw-bold text-center mb-4">Will you be attending?</h5>

                    <div class="status-options">
                        <div class="status-tile" id="btnAccept" onclick="selectStatus('accepted')">
                            <i class="fas fa-check-circle" style="font-size: 2.5rem; margin-bottom: 15px; display: block; color: #d1d5db;"></i>
                            <h5>Yes, I'll Attend</h5>
                        </div>

                        <div class="status-tile" id="btnMaybe" onclick="selectStatus('maybe')">
                            <i class="fas fa-question-circle" style="font-size: 2.5rem; margin-bottom: 15px; display: block; color: #d1d5db;"></i>
                            <h5>Maybe</h5>
                        </div>

                        <div class="status-tile" id="btnDecline" onclick="selectStatus('declined')">
                            <i class="fas fa-times-circle" style="font-size: 2.5rem; margin-bottom: 15px; display: block; color: #d1d5db;"></i>
                            <h5>Sorry, I Can't</h5>
                        </div>
                    </div>

                    <div id="guestSection" class="action-section">
                        <label class="fw-bold mb-2 d-block">Additional Guests</label>
                        <select name="guests_count" class="form-select mb-4 p-3 rounded-3">
                            <option value="0">Just me</option>
                            @for($i=1;$i<=$guest->allowed_guests;$i++)
                                <option value="{{$i}}">{{$i}} Guest</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn-main" id="submitBtn">
                            <span class="btn-text">Confirm Attendance</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>

                    <div id="maybeSection" class="action-section" style="background: #fffbf0; border-color: #ffeeba;">
                        <p class="text-warning-emphasis fw-bold text-center mb-3">Not sure yet</p>
                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 text-white" id="maybeSubmitBtn">
                            <span class="btn-text">Send Response</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>

                    <div id="declineSection" class="action-section" style="background: #fff8f8; border-color: #fadddd;">
                        <p class="text-danger fw-bold text-center mb-3">Confirm declining invitation?</p>
                        <button type="submit" class="btn btn-outline-danger w-100 py-3 fw-bold rounded-3" id="declineBtn">
                            <span class="btn-text">Yes, Decline</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-4 rounded-4 shadow-sm mt-4"
                 id="locationBox"
                 style="{{ $hasResponded ? 'display: none;' : 'display: block;' }}">
                <h6 class="fw-bold mb-3">Location</h6>
                <p class="text-muted small mb-3">{{$event->address}}</p>
                <div style="height: 250px; background: #eee; border-radius: 12px; overflow: hidden;">
                    <iframe src="https://maps.google.com/maps?q={{ urlencode($event->address) }}&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const btnAccept = document.getElementById('btnAccept');
    const btnMaybe = document.getElementById('btnMaybe');
    const btnDecline = document.getElementById('btnDecline');

    const guestSection = document.getElementById('guestSection');
    const maybeSection = document.getElementById('maybeSection');
    const declineSection = document.getElementById('declineSection');

    const statusInput = document.getElementById('response_status');

    function selectStatus(status) {
        statusInput.value = status;

        btnAccept.classList.remove('active-accept');
        btnMaybe.classList.remove('active-maybe');
        btnDecline.classList.remove('active-decline');

        btnAccept.querySelector('i').style.color = '#d1d5db';
        btnMaybe.querySelector('i').style.color = '#d1d5db';
        btnDecline.querySelector('i').style.color = '#d1d5db';

        guestSection.style.display = 'none';
        maybeSection.style.display = 'none';
        declineSection.style.display = 'none';

        if (status === 'accepted') {
            btnAccept.classList.add('active-accept');
            btnAccept.querySelector('i').style.color = '#198754';
            guestSection.style.display = 'block';
            guestSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        } else if (status === 'maybe') {
            btnMaybe.classList.add('active-maybe');
            btnMaybe.querySelector('i').style.color = '#ffc107';
            maybeSection.style.display = 'block';
            maybeSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        } else {
            btnDecline.classList.add('active-decline');
            btnDecline.querySelector('i').style.color = '#dc3545';
            declineSection.style.display = 'block';
            declineSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    const form = document.getElementById('rsvpForm');
    if(form){
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const status = formData.get('response_status');

            let activeBtn;
            if(status === 'accepted') activeBtn = document.getElementById('submitBtn');
            else if(status === 'maybe') activeBtn = document.getElementById('maybeSubmitBtn');
            else activeBtn = document.getElementById('declineBtn');

            const btnText = activeBtn.querySelector('.btn-text');
            const spinner = activeBtn.querySelector('.spinner-border');

            activeBtn.disabled = true;
            btnText.style.opacity = '0.5';
            spinner.classList.remove('d-none');

            fetch("{{ route('rsvp.submit', $guest->invitation_token) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById('mainFormCard').style.display = 'none';
                        document.getElementById('locationBox').style.display = 'none';

                        const successSection = document.getElementById('successSection');
                        const successTitle = document.getElementById('successTitle');
                        const successMsg = document.getElementById('successMessage');
                        const successIcon = document.getElementById('successIcon');

                        successSection.style.display = 'block';

                        if(status === 'accepted') {
                            successTitle.style.color = '#198754';
                            successTitle.innerText = "Attendance Confirmed!";
                            successMsg.innerText = "Check your email. Your entry tickets have been sent. Please scan them upon arrival.";
                            successIcon.className = "fas fa-envelope-circle-check text-success";
                        } else if(status === 'maybe') {
                            successTitle.style.color = '#ffc107';
                            successTitle.innerText = "Response Recorded";
                            successMsg.innerText = "We've noted you as 'Maybe'. Please check your email for your entry ticket and QR code.";
                            successIcon.className = "fas fa-question-circle text-warning";
                        } else {
                            successTitle.style.color = '#6c757d';
                            successTitle.innerText = "Response Recorded";
                            successMsg.innerText = "Your response has been received. Thank you.";
                            successIcon.className = "far fa-check-circle text-muted";
                        }

                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(function (error) {
                    let errorMessage = 'Something went wrong. Please try again.';

                    if (error && error.response && error.response.data && error.response.data.message) {
                        errorMessage = error.response.data.message;
                    } else if (error && error.message) {
                        errorMessage = error.message;
                    }

                    alert(errorMessage);

                    activeBtn.disabled = false;
                    btnText.style.opacity = '1';
                    spinner.classList.add('d-none');
                });

        });
    }
</script>
</body>
</html>
