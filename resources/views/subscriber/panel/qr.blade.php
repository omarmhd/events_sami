@extends('layouts.app')

@section('title', 'ماسح QR')

@push('styles')
<style>
/* ─── QR Page ─────────────────────────────────────────────── */

/* Compact page header */
.qr-page-header {
    display: flex;
    align-items: center;
    gap: .85rem;
    margin-bottom: 1.25rem;
}
.qr-page-icon {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    background: var(--grad-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 14px -3px rgba(15,143,131,.45);
}
.qr-page-header h1 {
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 2px;
}
.qr-page-header p {
    font-size: .78rem;
    color: var(--text-soft);
    margin: 0;
}

/* Mini counters row */
.qr-counters {
    display: flex;
    gap: .6rem;
    margin-bottom: 1rem;
}
.qr-counter {
    flex: 1;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: .65rem .75rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.qr-counter-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.qr-counter-dot.green { background: #22c55e; }
.qr-counter-dot.amber { background: #f59e0b; }
.qr-counter-dot.red   { background: var(--danger-color); }
.qr-counter-num {
    font-size: 1.1rem;
    font-weight: 900;
    color: var(--text-main);
    line-height: 1;
}
.qr-counter-lbl {
    font-size: .68rem;
    color: var(--text-soft);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Scanner card */
.scanner-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 1rem;
}
.scanner-topbar {
    padding: .85rem 1.1rem;
    border-bottom: 1px solid var(--line);
    background: var(--surface-soft);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.scanner-topbar-title {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: .45rem;
}
.scanner-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #94a3b8;
    display: inline-block;
    transition: background .3s;
}
.scanner-status-dot.active  { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,.5); }
.scanner-status-dot.success { background: var(--primary-color); box-shadow: 0 0 6px rgba(15,143,131,.5); }
.scanner-status-dot.error   { background: var(--danger-color); }

.scanner-body {
    padding: 1rem;
}

/* Override html5-qrcode styles */
#qr-reader {
    border: none !important;
    width: 100% !important;
}
#qr-reader video {
    border-radius: var(--radius-md) !important;
    width: 100% !important;
    max-height: 360px;
    object-fit: cover;
}
#qr-reader__scan_region {
    border-radius: var(--radius-md);
    overflow: hidden;
    background: #000;
}
#qr-reader__scan_region img { display: none !important; }
#qr-reader__dashboard { padding: .5rem 0 0 !important; }
#qr-reader__dashboard_section_csr button,
#qr-reader__dashboard button {
    background: var(--grad-primary) !important;
    color: #fff !important;
    border: none !important;
    padding: .55rem 1.25rem !important;
    border-radius: var(--radius-sm) !important;
    font-size: .82rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    min-height: 44px !important;
}
#qr-reader__dashboard_section_fsr button,
#qr-reader__filescan_input { display: none !important; }
#qr-reader__header_message { display: none !important; }
#qr-reader__status_span {
    font-size: .75rem !important;
    color: var(--text-soft) !important;
}

/* Result box */
.result-box {
    margin-top: .85rem;
    padding: .85rem 1rem;
    border-radius: var(--radius-md);
    border: 1.5px dashed var(--line);
    background: var(--surface-soft);
    display: flex;
    align-items: center;
    gap: .75rem;
    transition: all .3s;
    min-height: 54px;
}
.result-box.scanned {
    border-color: var(--primary-color);
    border-style: solid;
    background: var(--primary-soft);
}
.result-box.error-state {
    border-color: var(--danger-color);
    border-style: solid;
    background: rgba(179,38,30,.06);
}
.result-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--surface);
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .82rem;
    color: var(--text-soft);
    flex-shrink: 0;
    transition: all .3s;
}
.result-box.scanned .result-icon {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
}
.result-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-soft);
    margin-bottom: 2px;
}
.result-value {
    font-size: .82rem;
    color: var(--text-muted);
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.result-box.scanned .result-value {
    color: var(--primary-color);
    font-weight: 700;
}

/* Check-in button */
.btn-checkin {
    width: 100%;
    padding: .9rem 1rem;
    border-radius: var(--radius-md);
    border: none;
    font-size: .95rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .25s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    margin-top: .85rem;
    min-height: 52px;
}
.btn-checkin:disabled {
    background: var(--surface-muted, #f1f5f9);
    color: var(--text-soft);
    cursor: not-allowed;
    box-shadow: none;
}
.btn-checkin:not(:disabled) {
    background: var(--grad-primary);
    color: #fff;
    box-shadow: 0 6px 18px -4px rgba(15,143,131,.45);
}
.btn-checkin:not(:disabled):active {
    transform: scale(.98);
    box-shadow: 0 3px 10px -4px rgba(15,143,131,.4);
}

/* Mobile sticky button area */
@media (max-width: 767px) {
    .sticky-btn-wrap {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        padding: .75rem 0 calc(.75rem + env(safe-area-inset-bottom));
        background: var(--surface-soft);
        border-top: 1px solid var(--line);
        margin: 0 -1rem -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
        z-index: 10;
    }
    .btn-checkin {
        margin-top: 0;
    }
    .scanner-body {
        padding-bottom: 0;
    }
}

/* Page max-width on large screens */
@media (min-width: 768px) {
    .qr-page-inner {
        max-width: 480px;
    }
}
</style>
@endpush

@section('content')

<div class="qr-page-inner">

    {{-- ─── Header ───────────────────────────────────────────── --}}
    <div class="qr-page-header">
        <div class="qr-page-icon">
            <i class="fas fa-qrcode"></i>
        </div>
        <div>
            <h1>تسجيل الحضور</h1>
            <p class="d-none d-sm-block">وجّه الكاميرا نحو رمز QR للمدعو</p>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="scanner-status-dot" id="statusDot"></span>
            <span id="statusLabel" style="font-size:.75rem;color:var(--text-soft);font-weight:600;">جاهز</span>
        </div>
    </div>

    {{-- ─── Mini Counters ─────────────────────────────────────── --}}
    <div class="qr-counters">
        <div class="qr-counter">
            <span class="qr-counter-dot green"></span>
            <div>
                <div class="qr-counter-num" id="statSuccess">0</div>
                <div class="qr-counter-lbl">حضور</div>
            </div>
        </div>
        <div class="qr-counter">
            <span class="qr-counter-dot amber"></span>
            <div>
                <div class="qr-counter-num" id="statDuplicate">0</div>
                <div class="qr-counter-lbl">مكرر</div>
            </div>
        </div>
        <div class="qr-counter">
            <span class="qr-counter-dot red"></span>
            <div>
                <div class="qr-counter-num" id="statInvalid">0</div>
                <div class="qr-counter-lbl">غير صالح</div>
            </div>
        </div>
    </div>

    {{-- ─── Scanner Card ───────────────────────────────────────── --}}
    <div class="scanner-card">
        <div class="scanner-topbar">
            <span class="scanner-topbar-title">
                <i class="fas fa-camera" style="color:var(--primary-color);"></i>
                الكاميرا
            </span>
        </div>

        <div class="scanner-body">
            <div id="qr-reader"></div>

            {{-- Result --}}
            <div class="result-box" id="resultBox">
                <div class="result-icon" id="resultIcon">
                    <i class="fas fa-camera" id="resultIconInner"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="result-label">النتيجة</div>
                    <div class="result-value" id="resultValue">في انتظار مسح رمز QR...</div>
                </div>
            </div>

            {{-- Button (sticky on mobile) --}}
            <div class="sticky-btn-wrap">
                <button id="checkinBtn" class="btn-checkin" disabled>
                    <i class="fas fa-check-circle"></i>
                    <span id="btnLabel">تسجيل الحضور</span>
                </button>
            </div>
        </div>
    </div>

</div>{{-- end inner --}}

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {
    let scanner   = null;
    let scannedQr = '';
    let counts    = { success: 0, duplicate: 0, invalid: 0 };

    const csrfToken  = @json(csrf_token());
    const checkinUrl = @json(route('scan.checkin'));

    const checkinBtn   = document.getElementById('checkinBtn');
    const btnLabel     = document.getElementById('btnLabel');
    const resultBox    = document.getElementById('resultBox');
    const resultValue  = document.getElementById('resultValue');
    const resultIcon   = document.getElementById('resultIcon');
    const resultIconIn = document.getElementById('resultIconInner');
    const statusDot    = document.getElementById('statusDot');
    const statusLabel  = document.getElementById('statusLabel');

    function setStatus(state) {
        const map = {
            scanning : { dot: 'active',  label: 'جاري المسح…' },
            scanned  : { dot: 'success', label: 'تم المسح' },
            error    : { dot: 'error',   label: 'خطأ' },
            idle     : { dot: '',        label: 'جاهز' },
        };
        const s = map[state] || map.idle;
        statusDot.className   = 'scanner-status-dot ' + s.dot;
        statusLabel.textContent = s.label;
    }

    function updateCounts() {
        document.getElementById('statSuccess').textContent   = counts.success;
        document.getElementById('statDuplicate').textContent = counts.duplicate;
        document.getElementById('statInvalid').textContent   = counts.invalid;
    }

    function startScanner() {
        setStatus('idle');
        scanner = new Html5Qrcode('qr-reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 15, qrbox: { width: 240, height: 240 }, aspectRatio: 1.0 },
            onScanSuccess,
            () => {}
        ).then(() => setStatus('scanning'))
         .catch(err => console.warn('Camera start failed:', err));
    }

    function onScanSuccess(decoded) {
        scannedQr = decoded;
        if (navigator.vibrate) navigator.vibrate(80);

        resultBox.classList.add('scanned');
        resultIconIn.className = 'fas fa-check';
        resultIcon.style.background = 'var(--primary-color)';
        resultIcon.style.color = '#fff';
        resultIcon.style.border = 'none';
        resultValue.textContent = decoded;
        setStatus('scanned');
        checkinBtn.disabled = false;
        scanner.stop().catch(() => {});
    }

    function resetUI() {
        scannedQr = '';
        checkinBtn.disabled = true;
        btnLabel.textContent = 'تسجيل الحضور';
        resultBox.classList.remove('scanned', 'error-state');
        resultIconIn.className = 'fas fa-camera';
        resultIcon.style = '';
        resultValue.textContent = 'في انتظار مسح رمز QR...';
        startScanner();
    }

    checkinBtn.addEventListener('click', function () {
        if (!scannedQr) return;

        checkinBtn.disabled = true;
        btnLabel.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الإرسال...';

        $.ajax({
            url    : checkinUrl,
            method : 'POST',
            data   : { token: scannedQr, _token: csrfToken },

            success: function (res) {
                if (res.status === 'accepted') {
                    counts.success++;
                    updateCounts();
                    Swal.fire({
                        title: 'تم تسجيل الحضور ✅',
                        text: res.name ? res.name : (res.message || ''),
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fff',
                        color: '#102a2a',
                        iconColor: '#0f8f83',
                    }).then(resetUI);

                } else if (res.status === 'already_used') {
                    counts.duplicate++;
                    updateCounts();
                    Swal.fire({
                        title: 'تم الحضور مسبقاً ⚠️',
                        text: res.name ? res.name : (res.message || ''),
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fff',
                        color: '#102a2a',
                    }).then(resetUI);

                } else {
                    counts.invalid++;
                    updateCounts();
                    Swal.fire({
                        title: 'رمز غير صالح ❌',
                        text: res.message || '',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#fff',
                        color: '#102a2a',
                    }).then(resetUI);
                }
            },

            error: function () {
                Swal.fire('خطأ في الاتصال', 'تحقق من اتصالك بالإنترنت.', 'error').then(resetUI);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', startScanner);
})();
</script>
@endpush
