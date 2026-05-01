<!-- resources/views/checkin/scanner.blade.php -->
@extends('layouts.app')

@section('title', 'QR Check-in Scanner')

@push('styles')
<style>
/* ── Scanner Shell ─────────────────────────────── */
.scanner-shell {
    display:grid;
    grid-template-columns: 1fr 340px;
    gap: 1.25rem;
    align-items: start;
}
@media(max-width:860px){ .scanner-shell{ grid-template-columns:1fr; } }

/* ── Camera Card ──────────────────────────────── */
.scanner-card {
    background:#fff;border-radius:var(--radius-xl);border:1px solid var(--line);
    overflow:hidden;box-shadow:var(--shadow-card);
}
.scanner-header {
    background:var(--grad-primary);padding:1.25rem 1.5rem;color:#fff;
    display:flex;align-items:center;justify-content:space-between;
}
.scanner-title { font-weight:700;font-size:1.05rem;display:flex;align-items:center;gap:.6rem; }
.scanner-body { padding:1.5rem; }

/* Viewfinder */
#qr-reader {
    width:100%!important;border-radius:var(--radius-lg)!important;overflow:hidden!important;
    border:2px dashed rgba(99,102,241,.3)!important;min-height:280px;
    background:#f8f9ff;
}
#qr-reader video { border-radius:calc(var(--radius-lg) - 2px)!important; }
/* hide default qr-code library UI clutter */
#qr-reader__dashboard_section_csr span,
#qr-reader__dashboard_section_swaplink,
#qr-reader__status_span { display:none!important; }
#qr-reader__camera_permission_button {
    background:var(--grad-primary)!important;border:none!important;
    color:#fff!important;border-radius:var(--radius-sm)!important;padding:.5rem 1.25rem!important;
    font-weight:600!important;font-size:.85rem!important;cursor:pointer!important;
}
#qr-reader__filescan_input { display:none!important; }

/* Pulse ring on scan frame */
.scan-frame {
    position:relative;border-radius:var(--radius-lg);overflow:hidden;
}
.scan-frame::before {
    content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    width:180px;height:180px;border-radius:20px;
    border:2px solid rgba(99,102,241,.5);
    box-shadow:0 0 0 0 rgba(99,102,241,.3);
    animation:scanPulse 2s infinite;pointer-events:none;z-index:10;
}
@keyframes scanPulse {
    0%  { box-shadow:0 0 0 0 rgba(99,102,241,.4); }
    70% { box-shadow:0 0 0 18px rgba(99,102,241,0); }
    100%{ box-shadow:0 0 0 0 rgba(99,102,241,0); }
}

/* ── Result Overlay ────────────────────────────── */
.result-overlay {
    position:fixed;inset:0;z-index:9999;
    display:flex;align-items:center;justify-content:center;
    background:rgba(0,0,0,.45);backdrop-filter:blur(6px);
    opacity:0;pointer-events:none;transition:opacity .3s;
}
.result-overlay.show { opacity:1;pointer-events:all; }
.result-box {
    background:#fff;border-radius:var(--radius-xl);padding:2.25rem 2rem;
    max-width:420px;width:90%;text-align:center;position:relative;
    box-shadow:0 30px 80px -20px rgba(0,0,0,.3);
    transform:scale(.9);transition:transform .3s;
}
.result-overlay.show .result-box { transform:scale(1); }
.result-icon {
    width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    font-size:2rem;margin:0 auto 1.25rem;
}
.result-icon.success { background:rgba(16,185,129,.12);color:var(--success-color); }
.result-icon.error   { background:rgba(244,63,94,.12);color:var(--danger-color); }
.result-icon.warn    { background:rgba(245,158,11,.12);color:var(--warning-color); }
.result-name { font-size:1.3rem;font-weight:800;color:var(--text-main);margin-bottom:.35rem; }
.result-detail { font-size:.87rem;color:var(--text-soft);margin-bottom:1.25rem; }

/* ── Sidebar Panel ─────────────────────────────── */
.sidebar-panel { display:flex;flex-direction:column;gap:1rem; }

/* KPI mini-cards */
.mini-kpi {
    background:#fff;border-radius:var(--radius-md);border:1px solid var(--line);
    padding:.9rem 1.1rem;display:flex;align-items:center;gap:.9rem;
    transition:box-shadow .2s;
}
.mini-kpi:hover { box-shadow:var(--shadow-soft); }
.mini-kpi-icon {
    width:38px;height:38px;border-radius:11px;
    display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;
}
.mini-kpi-label { font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em; }
.mini-kpi-val   { font-size:1.3rem;font-weight:800;color:var(--text-main);line-height:1; }

/* Recent check-ins list */
.checkin-log {
    background:#fff;border-radius:var(--radius-lg);border:1px solid var(--line);
    overflow:hidden;
}
.checkin-log-header {
    padding:.85rem 1.1rem;border-bottom:1px solid var(--line);
    font-weight:700;font-size:.88rem;color:var(--text-main);
    display:flex;align-items:center;gap:.5rem;
}
.checkin-log-header i { color:var(--success-color); }
.checkin-item {
    display:flex;align-items:center;gap:.8rem;padding:.75rem 1.1rem;
    border-bottom:1px solid var(--line);animation:slideInRight .3s ease;
}
.checkin-item:last-child { border-bottom:none; }
@keyframes slideInRight { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
.checkin-avatar {
    width:32px;height:32px;border-radius:50%;background:var(--primary-soft);
    display:flex;align-items:center;justify-content:center;font-size:.8rem;
    font-weight:700;color:var(--primary-color);flex-shrink:0;
}
.checkin-name { font-size:.83rem;font-weight:600;color:var(--text-main); }
.checkin-time { font-size:.72rem;color:var(--text-soft); }
.checkin-badge {
    margin-left:auto;font-size:.68rem;font-weight:600;padding:.2rem .55rem;
    border-radius:99px;flex-shrink:0;
}
.checkin-badge.new { background:rgba(16,185,129,.12);color:var(--success-color); }
.checkin-badge.dup { background:rgba(245,158,11,.12);color:var(--warning-color); }

/* Manual search input */
.search-scan-bar {
    display:flex;gap:.5rem;
}
.search-scan-bar input {
    flex:1;border-radius:var(--radius-sm)!important;border:1px solid var(--line)!important;
    padding:.55rem .9rem!important;font-size:.88rem!important;background:#f9fafc!important;
    transition:border-color .2s,box-shadow .2s;
}
.search-scan-bar input:focus { border-color:rgba(99,102,241,.5)!important;box-shadow:0 0 0 3px rgba(99,102,241,.1)!important;outline:none!important; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">QR Check-in Scanner</h4>
        <p class="text-muted small mb-0">Scan attendee QR codes or search manually to check in guests</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        {{-- Event selector --}}
        <select id="event-selector" class="form-select form-select-sm rounded-pill" style="min-width:220px;border-color:var(--line)">
            <option value="">— All Events —</option>
            @foreach($events ?? [] as $evt)
                <option value="{{ $evt->id }}">{{ $evt->name }}</option>
            @endforeach
        </select>
        <button id="stop-btn" class="btn btn-sm btn-outline-danger rounded-pill d-none px-3">
            <i class="fas fa-stop me-1"></i>Stop
        </button>
    </div>
</div>

<div class="scanner-shell">

    {{-- ── Left: Camera + Manual Search ─────────────── --}}
    <div class="d-flex flex-column gap-3">

        {{-- Camera card --}}
        <div class="scanner-card animate__animated animate__fadeIn">
            <div class="scanner-header">
                <div class="scanner-title">
                    <i class="fas fa-qrcode"></i> Camera Scanner
                </div>
                <span id="scan-status-badge" class="badge rounded-pill" style="background:rgba(255,255,255,.2);font-size:.75rem;">
                    Initialising…
                </span>
            </div>
            <div class="scanner-body">
                <div class="scan-frame">
                    <div id="qr-reader"></div>
                </div>
                <p class="text-center mt-3 mb-0" style="font-size:.8rem;color:var(--text-soft)">
                    <i class="fas fa-info-circle me-1"></i>Point the camera at the attendee's QR ticket
                </p>
            </div>
        </div>

        {{-- Manual Search --}}
        <div class="card-surface animate__animated animate__fadeIn">
            <div class="section-title mb-3" style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem">
                <i class="fas fa-magnifying-glass" style="color:var(--primary-color)"></i> Manual Lookup
            </div>
            <form id="manual-check-form" class="search-scan-bar" onsubmit="manualScan(event)">
                @csrf
                <input type="text" id="manual-input" placeholder="Name, email or ticket number…" autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-search me-1"></i>Check In
                </button>
            </form>
            <div id="manual-result" class="mt-3"></div>
        </div>

    </div>

    {{-- ── Right: Stats + Recent Log ──────────────────── --}}
    <div class="sidebar-panel animate__animated animate__fadeInRight">

        {{-- Live Stats --}}
        <div class="row g-2">
            <div class="col-6">
                <div class="mini-kpi">
                    <div class="mini-kpi-icon" style="background:rgba(16,185,129,.1);color:var(--success-color)">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <div>
                        <div class="mini-kpi-label">Checked In</div>
                        <div class="mini-kpi-val" id="stat-checked">0</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="mini-kpi">
                    <div class="mini-kpi-icon" style="background:var(--primary-soft);color:var(--primary-color)">
                        <i class="fas fa-ticket"></i>
                    </div>
                    <div>
                        <div class="mini-kpi-label">Total</div>
                        <div class="mini-kpi-val" id="stat-total">—</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="mini-kpi">
                    <div class="mini-kpi-icon" style="background:rgba(244,63,94,.1);color:var(--danger-color)">
                        <i class="fas fa-user-xmark"></i>
                    </div>
                    <div>
                        <div class="mini-kpi-label">Remaining</div>
                        <div class="mini-kpi-val" id="stat-remaining">—</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="mini-kpi">
                    <div class="mini-kpi-icon" style="background:rgba(245,158,11,.1);color:var(--warning-color)">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div>
                        <div class="mini-kpi-label">Rate</div>
                        <div class="mini-kpi-val" id="stat-rate">0%</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent check-ins log --}}
        <div class="checkin-log">
            <div class="checkin-log-header">
                <i class="fas fa-clock-rotate-left"></i> Recent Check-ins
            </div>
            <div id="recent-list">
                <div class="px-3 py-4 text-center" style="font-size:.82rem;color:var(--text-soft)">
                    <i class="fas fa-radar fa-lg mb-2 d-block" style="opacity:.3"></i>
                    Scanned attendees will appear here
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ── Result Overlay ───────────────────────────── --}}
<div class="result-overlay" id="result-overlay" onclick="closeOverlay()">
    <div class="result-box" onclick="event.stopPropagation()">
        <div class="result-icon" id="result-icon">
            <i class="fas fa-circle-check"></i>
        </div>
        <div class="result-name" id="result-name">—</div>
        <div class="result-detail" id="result-detail">—</div>
        <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-primary rounded-pill px-4" onclick="closeOverlay()">
                <i class="fas fa-qrcode me-2"></i>Next Scan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@latest/html5-qrcode.min.js"></script>
<script>
const CHECKIN_URL  = "{{ route('scan.checkin') }}";
const CSRF_TOKEN   = "{{ csrf_token() }}";

let scanner = null;
let scanning = false;
let checkedCount = 0;
let lastScan = '';

// ── Start Scanner ─────────────────────────────────
function startScanner() {
    if (scanner) return;
    scanner = new Html5Qrcode("qr-reader");
    const config = { fps: 12, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 };

    scanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .then(() => {
            scanning = true;
            document.getElementById('scan-status-badge').textContent = '● Live';
            document.getElementById('scan-status-badge').style.background = 'rgba(16,185,129,.3)';
            document.getElementById('stop-btn').classList.remove('d-none');
        })
        .catch(err => {
            document.getElementById('scan-status-badge').textContent = 'No Camera';
            console.warn('Camera error:', err);
        });
}

// ── Stop Scanner ──────────────────────────────────
document.getElementById('stop-btn').addEventListener('click', () => {
    if (scanner && scanning) {
        scanner.stop().then(() => {
            scanner = null; scanning = false;
            document.getElementById('scan-status-badge').textContent = 'Stopped';
            document.getElementById('stop-btn').classList.add('d-none');
        });
    }
});

// ── QR Decode Success ─────────────────────────────
function onScanSuccess(decodedText) {
    if (decodedText === lastScan) return; // debounce duplicate
    lastScan = decodedText;
    setTimeout(() => { lastScan = ''; }, 2500);
    submitCheckin(decodedText);
}
function onScanFailure(err) { /* silent */ }

// ── Submit Check-in ───────────────────────────────
function submitCheckin(code, isManual = false) {
    const eventId = document.getElementById('event-selector').value;
    fetch(CHECKIN_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ code, event_id: eventId || null })
    })
    .then(r => r.json())
    .then(data => {
        showResult(data);
        if (data.success || data.status === 'checked_in') {
            checkedCount++;
            addRecentEntry(data.name ?? code, data.duplicate ?? false);
            updateStats();
        }
        if (isManual) document.getElementById('manual-input').value = '';
    })
    .catch(() => {
        showResult({ success: false, message: 'Network error — please try again.' });
    });
}

// ── Show Result Overlay ───────────────────────────
function showResult(data) {
    const ok  = data.success || data.status === 'checked_in';
    const dup = data.duplicate ?? data.status === 'already_checked_in';
    const icon = document.getElementById('result-icon');
    const overlay = document.getElementById('result-overlay');

    icon.className = 'result-icon ' + (ok ? (dup ? 'warn' : 'success') : 'error');
    icon.innerHTML = `<i class="fas ${ok ? (dup ? 'fa-triangle-exclamation' : 'fa-circle-check') : 'fa-circle-xmark'}"></i>`;
    document.getElementById('result-name').textContent = data.name ?? (ok ? 'Check-in OK' : 'Failed');
    document.getElementById('result-detail').textContent = data.message ?? '';

    overlay.classList.add('show');
    setTimeout(() => overlay.classList.remove('show'), 3500);
}

function closeOverlay() {
    document.getElementById('result-overlay').classList.remove('show');
}

// ── Manual Form Submit ────────────────────────────
function manualScan(e) {
    e.preventDefault();
    const val = document.getElementById('manual-input').value.trim();
    if (!val) return;
    submitCheckin(val, true);
}

// ── Add Recent Entry ──────────────────────────────
function addRecentEntry(name, isDuplicate) {
    const list = document.getElementById('recent-list');
    const initials = name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2);
    const now = new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
    const html = `
        <div class="checkin-item">
            <div class="checkin-avatar">${initials}</div>
            <div class="flex-grow-1 min-w-0">
                <div class="checkin-name text-truncate">${name}</div>
                <div class="checkin-time">${now}</div>
            </div>
            <span class="checkin-badge ${isDuplicate ? 'dup' : 'new'}">${isDuplicate ? 'Already in' : 'New'}</span>
        </div>`;
    if (list.querySelector('.px-3')) list.innerHTML = '';
    list.insertAdjacentHTML('afterbegin', html);
    // Keep max 20 entries
    const items = list.querySelectorAll('.checkin-item');
    if (items.length > 20) items[items.length - 1].remove();
}

// ── Update Stats Display ──────────────────────────
function updateStats() {
    document.getElementById('stat-checked').textContent = checkedCount;
    const total = parseInt(document.getElementById('stat-total').textContent);
    if (!isNaN(total)) {
        document.getElementById('stat-remaining').textContent = Math.max(0, total - checkedCount);
        document.getElementById('stat-rate').textContent = Math.round(checkedCount/total*100) + '%';
    }
}

// ── Boot ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', startScanner);
</script>
@endpush