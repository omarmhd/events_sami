<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>{{$event->title}} - Registration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary-color: #46AA32;
            --secondary-color: #2E8B57;
            --accent-color: #8FD970;
            --text-dark: #1F2937;
            --text-gray: #6B7280;
            --bg-light: #F3F4F6;
            --white: #ffffff;
            --gradient-primary: linear-gradient(135deg, #46AA32 0%, #2E8B57 100%);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-glow: 0 0 20px rgba(70, 170, 50, 0.4);
        }

        body {
            font-family: 'Poppins', 'Tajawal', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            background: var(--white);
            position: relative;
            padding-bottom: 3rem;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .hero-bg-accent {
            position: absolute;
            top: -50%;
            left: -10%;
            width: 120%;
            height: 80%;
            background: radial-gradient(circle, rgba(70,170,50,0.1) 0%, rgba(255,255,255,0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        .event-image-container {
            position: relative;
            z-index: 1;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-top: 2rem;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .event-image-container:hover {
            transform: translateY(-5px);
        }

        .event-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
        }

        .hero-content {
            text-align: center;
            padding-top: 2rem;
            position: relative;
            z-index: 2;
        }

        .event-title {
            font-weight: 700;
            font-size: 2.5rem;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Info Cards (Grid) */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            border: 1px solid #eef2f6;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent-color);
        }

        .info-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(70, 170, 50, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .info-text h6 {
            margin: 0;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-gray);
        }

        .info-text p {
            margin: 0;
            font-weight: 500;
            color: var(--text-dark);
        }

        /* Register Button */
        .btn-register-main {
            background: var(--gradient-primary);
            color: white;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            box-shadow: var(--shadow-glow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-register-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2E8B57 0%, #46AA32 100%);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .btn-register-main:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(70, 170, 50, 0.5);
            color: white;
        }

        .btn-register-main:hover::before {
            opacity: 1;
        }

        /* Content Sections */
        .content-section {
            padding: 3rem 0;
        }

        .custom-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: var(--shadow-sm);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .modal-header {
            background: var(--bg-light);
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Stepper */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }

        .stepper-item {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .step-counter {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-gray);
            margin-bottom: 6px;
            transition: all 0.3s ease;
        }

        .stepper-item.active .step-counter {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 4px rgba(70, 170, 50, 0.2);
        }

        .stepper-item.completed .step-counter {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }

        .step-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-gray);
        }

        .stepper-item.active .step-name {
            color: var(--primary-color);
        }

        /* Form Controls */
        .form-floating > .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
        }

        .form-floating > .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(70, 170, 50, 0.1);
        }

        .form-floating > label {
            padding-left: 1rem;
        }

        /* Buttons in Modal */
        .btn-action {
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary-action {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary-action:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .step {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .step.active {
            display: block;
        }

        /* Guests Form Styles */
        .guest-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .guest-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 2rem;
            background: white;
            margin-top: 3rem;
            border-top: 1px solid #e5e7eb;
            color: var(--text-gray);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .event-title { font-size: 1.8rem; }
            .hero-section { padding-bottom: 2rem; border-radius: 0 0 30px 30px; }
            .modal-body { padding: 1.5rem; }
            .step-name { display: none; } /* Hide step names on small screens */
        }
    </style>
</head>
<body>

<header class="hero-section">
    <div class="hero-bg-accent"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="event-image-container animate__animated animate__fadeInDown">
                    <img src="{{asset('assets')}}/header.jpg" alt="{{$event->title}}" class="event-image">
                </div>

                <div class="hero-content animate__animated animate__fadeInUp animate__delay-1s">
                    <h1 class="event-title">{{$event->title}}</h1>

                    <div class="info-grid">
                        <div class="info-card">
                            <div class="info-icon-box"><i class="fas fa-calendar-alt"></i></div>
                            <div class="info-text">
                                <h6>Date</h6>
                                <p>{{ \Carbon\Carbon::parse($event->date)->format('D M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon-box"><i class="fas fa-clock"></i></div>
                            <div class="info-text">
                                <h6>Time</h6>
                                <p>{{$event->from_time}} - {{$event->to_time}}</p>
                            </div>
                        </div>
                        <div class="info-card">
                            <div class="info-icon-box"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="info-text">
                                <h6>Location</h6>
                                <p>{{ Str::limit($event->address, 25) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn-register-main" data-bs-toggle="modal" data-bs-target="#multiStepModal">
                            <i class="fas fa-ticket-alt me-2"></i> Register Now | التسجيل الآن
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container content-section">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="custom-card animate__animated animate__fadeInLeft animate__delay-1s">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> About Event</h3>
                <div class="text-secondary lh-lg">
                    <p class="mb-3"><strong>{{$event->description}}</strong></p>
                    <p class="mb-1 text-dark"><strong>{{$event->title_en}}</strong></p>
                    <p><strong>{{$event->description_en}}</strong></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="custom-card animate__animated animate__fadeInRight animate__delay-1s h-100">
                <h3 class="section-title"><i class="fas fa-location-dot"></i> Location</h3>
                <p class="text-muted mb-3"><small>{{$event->address}}</small></p>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.3205642742287!2d46.803344284995894!3d24.921147184025262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2ef840d318501d%3A0x34ce1212fec3df21!2z2YXZhtiq2KzYuSDYsdi02YjYtCDZhNmE2KXYrdiq2YHYp9mE2KfYqg!5e0!3m2!1sar!2seg!4v1736964707181!5m2!1sar!2seg" height="300" style="border:0;width:100%" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <p class="mb-0"><i class="fa fa-headset me-2"></i>Need help? Contact our support team for assistance.</p>
</div>

<div class="modal fade" id="multiStepModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Event Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="javascript:void(0)" id="regForm">
                <div class="modal-body">
                    <div class="stepper-wrapper">
                        <div class="stepper-item active" id="stepper1">
                            <div class="step-counter">1</div>
                            <div class="step-name">Personal</div>
                        </div>
                        <div class="stepper-item" id="stepper2">
                            <div class="step-counter">2</div>
                            <div class="step-name">Guests</div>
                        </div>
                        <div class="stepper-item" id="stepper3">
                            <div class="step-counter">3</div>
                            <div class="step-name">Confirm</div>
                        </div>
                    </div>

                    <div class="alert alert-danger message shadow-sm" role="alert" style="display: none; border-radius: 10px;"></div>

                    <div class="step active" id="step1">
                        <h4 class="mb-4 fw-bold text-dark">Personal Information</h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="inputName" name="name" placeholder="Full Name">
                                    <label for="inputName">Full Name (First + Last)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="inputPosition" name="position" placeholder="Position">
                                    <label for="inputPosition">Position</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="inputNationality" name="nationality" placeholder="Nationality">
                                    <label for="inputNationality">Nationality</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email">
                                    <label for="inputEmail">Email Address</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="button" class="btn btn-primary-action btn-action btn-next w-100 w-md-auto">
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="step" id="step2">
                        <h4 class="mb-4 fw-bold text-dark">Guest Registration</h4>

                        <div class="mb-4 text-center p-4 bg-light rounded-4 border">
                            <label class="form-label fw-bold d-block mb-3 fs-5">Are you attending with guests?</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="has_plus_one" id="plusOneNo" value="no" checked>
                                <label class="btn btn-outline-danger px-4" for="plusOneNo">No</label>

                                <input type="radio" class="btn-check" name="has_plus_one" id="plusOneYes" value="yes">
                                <label class="btn btn-outline-success px-4" for="plusOneYes">Yes</label>
                            </div>
                        </div>

                        <div id="plusOneSection" style="display: none;">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-muted">Number of Guests</label>
                                <select class="form-select" id="guestCountSelector">
                                    <option value="1" selected>1 Guest</option>
                                    <option value="2">2 Guests</option>

                                </select>
                            </div>

                            <div id="dynamicGuestsContainer"></div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-action btn-prev">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary-action btn-action btn-next">
                                Next Step <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="step" id="step3">
                        <div class="text-center py-4">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="mb-3 fw-bold">Almost Done!</h4>
                            <p class="text-muted">Please confirm your registration.</p>

                            <div class="row justify-content-center text-start">
                                <div class="col-md-8">
                                    <div class="p-3 bg-light rounded-3 border mb-3">
                                        <h6 class="text-primary border-bottom pb-2">Main Applicant</h6>
                                        <p class="mb-1"><strong>Name:</strong> <span class="review-name"></span></p>
                                        <p class="mb-1"><strong>Email:</strong> <span class="review-email"></span></p>
                                    </div>

                                    <div id="reviewGuestSection" style="display: none;">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="text-success border-bottom pb-2">Guests Included</h6>
                                            <p class="mb-0">You are registering <strong><span class="review-guest-count">0</span></strong> additional guest(s).</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-action btn-prev">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" id="save" class="btn btn-success btn-action btn-submit px-5">
                                Complete Registration <i class="fas fa-check ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let currentStep = 1;

        // Toggle Guest Section logic
        $('input[name="has_plus_one"]').change(function() {
            if ($(this).val() === 'yes') {
                $('#plusOneSection').slideDown();
            } else {
                $('#plusOneSection').slideUp();
            }
        });

        // Toggle Guest 2 based on count
        $('#guestCountSelector').change(function() {
            if ($(this).val() == '2') {
                $('#guest2Form').slideDown();
            } else {
                $('#guest2Form').slideUp();
            }
        });

        function updateStepper(step) {
            $('.stepper-item').removeClass('active completed');
            for(let i=1; i < step; i++) {
                $(`#stepper${i}`).addClass('completed');
            }
            $(`#stepper${step}`).addClass('active');
        }

        function validateStep1() {
            var isValid = true;
            let errorMessages = [];
            $('.message').hide(); // Reset error box

            const name = $('#inputName').val().trim();
            const position = $('#inputPosition').val().trim();
            const nationality = $('#inputNationality').val().trim();
            const email = $('#inputEmail').val().trim();

            if (!name) { errorMessages.push('Full Name is required'); isValid = false; }
            if (!position) { errorMessages.push('Position is required'); isValid = false; }
            if (!nationality) { errorMessages.push('Nationality is required'); isValid = false; }

            // Email Validation (Generic regex)
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Note: If you want to restrict to specific domains only, uncomment this:
            // const allowedDomains = ['aecl.com'];
            // const emailDomain = email.split('@')[1];

            if (!email || !emailPattern.test(email)) {
                errorMessages.push('Valid Email Address is required');
                isValid = false;
            }

            // Sync Check for Duplicate Email (if needed)
            if (isValid) {
                $.ajax({
                    url: '{{route("check_email")}}',
                    type: 'get',
                    async: false, // Sync for validation flow
                    data: { email: email },
                    success: function (response) {
                        if (response.message && response.status == false) {
                            isValid = false;
                            errorMessages.push(response.message);
                        }
                    }
                });
            }

            if (!isValid) {
                const messageList = $('<ul></ul>').addClass('mb-0 ps-3');
                errorMessages.forEach(message => {
                    messageList.append(`<li>${message}</li>`);
                });
                $('.message').html(messageList).slideDown();
            }

            return isValid;
        }

// 1. Toggle Guest Section Logic
        $('input[name="has_plus_one"]').change(function() {
            if ($(this).val() === 'yes') {
                $('#plusOneSection').slideDown();
                // Generate forms for the current selected value immediately
                generateGuestForms($('#guestCountSelector').val());
            } else {
                $('#plusOneSection').slideUp();
                $('#dynamicGuestsContainer').empty(); // Clear data if user selects No
            }
        });

        // 2. Listen to Dropdown Change
        $('#guestCountSelector').change(function() {
            const count = $(this).val();
            generateGuestForms(count);
        });

        // 3. Dynamic Form Generator Function
        function generateGuestForms(count) {
            let html = '';

            for (let i = 0; i < count; i++) {
                let guestNum = i + 1;
                html += `
            <div class="guest-box animate__animated animate__fadeIn">
                <div class="guest-title border-bottom pb-2 mb-3 d-flex justify-content-between">
                    <span><i class="fas fa-user me-2"></i> Guest ${guestNum} Details</span>
                    <span class="text-muted small">#${guestNum}</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control guest-input" name="guest[${i}][name]" placeholder="Full Name">
                            <label>Full Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control guest-input" name="guest[${i}][position]" placeholder="Position">
                            <label>Position</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control guest-input" name="guest[${i}][nationality]" placeholder="Nationality">
                            <label>Nationality</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control guest-input" name="guest[${i}][email]" placeholder="Email">
                            <label>Email</label>
                        </div>
                    </div>
                </div>
            </div>
            `;
            }

            $('#dynamicGuestsContainer').html(html);
        }

        function validateStep2() {
            let isValid = true;
            let errorMessages = [];

            // 1. إعادة تعيين الأخطاء السابقة (تنظيف)
            $('.message').slideUp();
            $('#dynamicGuestsContainer .form-control').removeClass('is-invalid');

            const hasPlusOne = $('input[name="has_plus_one"]:checked').val();

            if (hasPlusOne === 'yes') {
                // 2. الدوران على كل "صندوق ضيف" تم توليده
                $('#dynamicGuestsContainer .guest-box').each(function(index) {
                    let guestNum = index + 1;
                    let $box = $(this); // الصندوق الحالي

                    // جلب الحقول داخل هذا الصندوق
                    let nameInput = $box.find(`input[name="guest[${index}][name]"]`);
                    let posInput = $box.find(`input[name="guest[${index}][position]"]`);
                    let natInput = $box.find(`input[name="guest[${index}][nationality]"]`);
                    let emailInput = $box.find(`input[name="guest[${index}][email]"]`);

                    // أ- التحقق من الاسم
                    if (nameInput.val().trim() === '') {
                        nameInput.addClass('is-invalid');
                        isValid = false;
                    }

                    // ب- التحقق من المنصب
                    if (posInput.val().trim() === '') {
                        posInput.addClass('is-invalid');
                        isValid = false;
                    }

                    // ج- التحقق من الجنسية
                    if (natInput.val().trim() === '') {
                        natInput.addClass('is-invalid');
                        isValid = false;
                    }

                    // د- التحقق من الإيميل (فارغ أو صيغة خاطئة)
                    let emailVal = emailInput.val().trim();
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (emailVal === '') {
                        emailInput.addClass('is-invalid');
                        isValid = false;
                    } else if (!emailPattern.test(emailVal)) {
                        emailInput.addClass('is-invalid');
                        isValid = false;
                        // إضافة رسالة خطأ محددة للإيميل إذا أردت
                        // errorMessages.push(`Guest ${guestNum}: Invalid Email format`);
                    }
                });

                if (!isValid) {
                    errorMessages.push('Please fill out all required fields for guests marked in red.');
                }
            }

            // عرض رسائل الخطأ إن وجدت
            if (!isValid) {
                const messageList = $('<ul></ul>').addClass('mb-0 ps-3');
                errorMessages.forEach(message => {
                    messageList.append(`<li>${message}</li>`);
                });
                $('.message').html(messageList).slideDown();
            }

            return isValid;
        }
        $('.btn-next').on('click', function () {
            if (currentStep === 1) {
                if (!validateStep1()) return;

                // Populate Review Data
                $('.review-name').text($('#inputName').val());
                $('.review-email').text($('#inputEmail').val());
            }

            if (currentStep === 2) {
                if (!validateStep2()) return;

                // Populate Review Guest Data
                const hasPlusOne = $('input[name="has_plus_one"]:checked').val();
                if(hasPlusOne === 'yes') {
                    $('#reviewGuestSection').show();
                    $('.review-guest-count').text($('#guestCountSelector').val());
                } else {
                    $('#reviewGuestSection').hide();
                }
            }

            if (currentStep < 3) {
                $(`#step${currentStep}`).fadeOut(200, function() {
                    currentStep++;
                    updateStepper(currentStep);
                    $(`#step${currentStep}`).fadeIn(300).addClass('active');
                    $('.message').hide(); // Hide errors when switching steps
                });
            }
        });

        $('.btn-prev').on('click', function () {
            if (currentStep > 1) {
                $(`#step${currentStep}`).fadeOut(200, function() {
                    currentStep--;
                    updateStepper(currentStep);
                    $(`#step${currentStep}`).fadeIn(300).addClass('active');
                    $('.message').hide();
                });
            }
        });

        $('#save').on('click', function () {
            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...');
            $(".message").text("Please wait...").removeClass('alert-danger alert-success').addClass('alert-info').slideDown();

            // Basic Data
            let formData = {
                name: $('#inputName').val(),
                position: $('#inputPosition').val(),
                nationality: $('#inputNationality').val(),
                email: $('#inputEmail').val(),
                has_plus_one: $('input[name="has_plus_one"]:checked').val(),
                guests: [],
                _token: $("meta[name='csrf-token']").attr("content")
            };

            // Collect Dynamic Guests Data
            if (formData.has_plus_one === 'yes') {
                $('#dynamicGuestsContainer .guest-box').each(function(index) {
                    let guest = {
                        name: $(this).find(`input[name="guest[${index}][name]"]`).val(),
                        position: $(this).find(`input[name="guest[${index}][position]"]`).val(),
                        nationality: $(this).find(`input[name="guest[${index}][nationality]"]`).val(),
                        email: $(this).find(`input[name="guest[${index}][email]"]`).val()
                    };
                    formData.guests.push(guest);
                });
            }

            // AJAX Request
            $.ajax({
                url: '{{route("save")}}',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.status) {
                        $(".message").html(`<i class="fas fa-check-circle me-2"></i> ${response.message || 'Registration successful!'}`).removeClass('alert-danger alert-info').addClass('alert-success');
                        setTimeout(function () {
                            $('#multiStepModal').modal('hide');
                            location.reload();
                        }, 2000);
                    } else {
                        $(".message").html(`<i class="fas fa-exclamation-circle me-2"></i> ${response.message}`).removeClass('alert-success alert-info').addClass('alert-danger');
                        $btn.prop('disabled', false).html('Complete Registration <i class="fas fa-check ms-2"></i>');
                    }
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An unexpected error occurred.';
                    $(".message").text(errorMessage).removeClass('alert-success alert-info').addClass('alert-danger');
                    $btn.prop('disabled', false).html('Complete Registration <i class="fas fa-check ms-2"></i>');
                }
            });
        });
        // Reset Modal on Close
        $('#multiStepModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
            $('#plusOneSection').hide();
            $('#guest2Form').hide();
            $(".message").text('').hide();

            // Reset Stepper
            currentStep = 1;
            $('.step').hide();
            $('#step1').show().addClass('active');
            updateStepper(1);
        });
    });
</script>
</body>
</html>
