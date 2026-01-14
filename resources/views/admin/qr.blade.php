<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance System</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .scanner-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header-custom {
            background: white;
            padding: 30px 20px 10px;
            border: none;
            text-align: center;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background-color: #eef2ff;
            color: #0d6efd;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        #my-qr-reader {
            border: 2px solid #eaedf0 !important;
            border-radius: 15px;
            overflow: hidden;
            background: #fafafa;
        }

        /* Styling the inner scanner elements */
        #my-qr-reader__dashboard_section_csr button {
            background-color: #0d6efd !important;
            color: white !important;
            border: none !important;
            padding: 8px 20px !important;
            border-radius: 8px !important;
            font-size: 14px !important;
            margin: 10px 0 !important;
        }

        #result-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            border: 1px dashed #dee2e6;
        }

        .btn-confirm {
            padding: 14px;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            color: #6c757d;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 50px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            font-size: 14px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #0d6efd;
            color: white;
        }

        video {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<a href="{{ route('dashboard.index') }}" class="back-btn">
    <i class="fas fa-arrow-left me-2"></i> Dashboard
</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">

            <div class="card scanner-card">
                <div class="card-header-custom">
                    <div class="icon-circle">
                        <i class="fas fa-qrcode fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Attendance Scanner</h4>
                    <p class="text-muted small">Point your camera at the QR code</p>
                </div>

                <div class="card-body p-4">
                    <div id="my-qr-reader"></div>

                    <div id="result-container" class="text-center">
                            <span id="result-text" class="text-muted small">
                                <i class="fas fa-camera me-1"></i> Waiting for QR code...
                            </span>
                    </div>

                    <button id="attendance-btn" class="btn btn-secondary w-100 btn-confirm mt-4" disabled>
                        <i class="fas fa-check-circle me-2"></i> Register Attendance
                    </button>
                </div>
            </div>

            <p class="text-center mt-4 text-muted small">
                <i class="fas fa-shield-alt me-1"></i>
            </p>

        </div>
    </div>
</div>

<script>
    let html5QrcodeScanner;
    let qrData = "";
    const attendanceBtn = document.getElementById("attendance-btn");
    const resultText = document.getElementById("result-text");

    function startScanner() {
        html5QrcodeScanner = new Html5Qrcode("my-qr-reader");

        const config = {
            fps: 15,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrcodeScanner.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch((err) => {
            console.error("Scanner start error:", err);
        });
    }

    function onScanSuccess(decodedText) {
        qrData = decodedText;

        // UI Feedback
        attendanceBtn.disabled = false;
        attendanceBtn.classList.replace("btn-secondary", "btn-primary");
        resultText.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check me-1"></i> Scanned: ${qrData}</span>`;

        // Vibration for mobile feedback
        if (navigator.vibrate) navigator.vibrate(100);

        // Stop scanner to focus on submission
        html5QrcodeScanner.stop();
    }

    function onScanFailure(error) {
        // Silently handle scan failures to avoid console spam
    }

    attendanceBtn.addEventListener("click", function () {
        if (!qrData) return;

        // Loading state
        attendanceBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Sending...`;
        attendanceBtn.disabled = true;

        $.ajax({
            url: "{{ route('checked_in') }}",
            method: "GET",
            data: { qrData: qrData },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Attendance recorded successfully.',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd',
                        timer: 2500
                    }).then(() => {
                        resetUI();
                    });
                } else {
                    Swal.fire({
                        title: 'Warning',
                        text: response.message || 'This QR code is invalid or has expired.',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                    }).then(() => {
                        resetUI();
                    });
                }
            },
            error: function () {
                Swal.fire('Connection Error', 'Please check your internet.', 'error');
                resetUI();
            }
        });
    });

    function resetUI() {
        qrData = "";
        attendanceBtn.disabled = true;
        attendanceBtn.classList.replace("btn-primary", "btn-secondary");
        attendanceBtn.innerHTML = `<i class="fas fa-check-circle me-2"></i> Register Attendance`;
        resultText.innerHTML = `<i class="fas fa-camera me-1"></i> Waiting for QR code...`;
        startScanner();
    }

    document.addEventListener("DOMContentLoaded", startScanner);
</script>
</body>

</html>
