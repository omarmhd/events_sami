@extends('layouts.public.event')

@section('title', ($event->title ?: $event->name) . ' - ' . __('rsvp.title_suffix'))

@push('styles')
<style>
    .rsvp-status-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .rsvp-status-tile {
        border: 1px solid rgba(34, 34, 34, 0.1);
        border-radius: 16px;
        background: #fff;
        padding: 1rem 0.6rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .rsvp-status-tile:hover {
        transform: translateY(-2px);
        border-color: var(--primary-accent);
    }

    .rsvp-status-tile i {
        font-size: 1.4rem;
        margin-bottom: 0.4rem;
        display: block;
        color: #9b9b9b;
    }

    .rsvp-status-tile.active-accept {
        border-color: #198754;
        background: #f2fcf6;
    }

    .rsvp-status-tile.active-accept i {
        color: #198754;
    }

    .rsvp-status-tile.active-maybe {
        border-color: #ffc107;
        background: #fffaf0;
    }

    .rsvp-status-tile.active-maybe i {
        color: #ffc107;
    }

    .rsvp-status-tile.active-decline {
        border-color: #dc3545;
        background: #fff7f7;
    }

    .rsvp-status-tile.active-decline i {
        color: #dc3545;
    }

    .rsvp-action-box {
        margin-top: 0.9rem;
        border: 1px solid rgba(34, 34, 34, 0.08);
        border-radius: 14px;
        padding: 1rem;
        display: none;
    }

    .rsvp-success {
        text-align: center;
    }

    .rsvp-success i {
        font-size: 3rem;
    }

    @media (max-width: 768px) {
        .rsvp-status-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $title = $event->title ?: $event->name;
    $locationName = $event->location_name ?: ($event->address ?? '');
    $primaryDescription = $event->description ?: $event->description_en;
    $secondaryDescription = $event->description_en ?: $event->description;

    $hasResponded = $guest->status !== 'pending';
    $isAccepted = $guest->status === 'accepted';
    $isMaybe = $guest->status === 'maybe';

    /**
     * Resolve the hero image URL for this page.
     *
     * Priority:
     *  1. Event's own header_image_path — only if the file exists on disk.
     *  2. Company branding header_image_url — fallback when no event image.
     *  3. Empty string → CSS gradient banner is used instead (no <img> rendered).
     *
     * We verify local paths exist before using them so the hero section never
     * shows a broken image placeholder.
     */
    $resolveHeroImage = function (string $src): string {
        $src = trim($src);
        if ($src === '') {
            return '';
        }

        // External URL — trust it as-is (CDN / remote host).
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            return $src;
        }

        // Local path: starts with / (e.g. /uploads/event-images/headers/abc.jpg).
        // Verify the file actually exists before returning the URL.
        if (str_starts_with($src, '/')) {
            $absPath = public_path(ltrim($src, '/'));
            return is_file($absPath) ? $src : '';
        }

        // Legacy bare relative path (no leading slash) — prefix with /storage/.
        $absPath = public_path('storage/' . $src);
        return is_file($absPath) ? asset('storage/' . $src) : '';
    };

    // Step 1 — try the event's own header image.
    $heroImage = $resolveHeroImage((string) ($event->header_image_path ?? ''));

    // Step 2 — fall back to company branding header only when the event has no image.
    if ($heroImage === '') {
        $companyBranding = $event->company
            ? \App\Models\CompanyBranding::where('company_id', $event->company_id)->first()
            : null;
        if ($companyBranding && $companyBranding->header_image_url) {
            $heroImage = $resolveHeroImage((string) $companyBranding->header_image_url);
        }
    }
    // Step 3 — $heroImage === '' → gradient banner rendered in the template (no <img>).

    $mapQuery = $locationName ?: $title;
    $mapEmbed = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&t=&z=13&ie=UTF8&iwloc=&output=embed';

    $tokenForSubmit = $guest->invitation_token ?: $guest->token;
@endphp

<div class="container hero">
    <div class="hero-shell">
        @if($heroImage)
            <div class="hero-banner">
                <img src="{{ e($heroImage) }}" alt="{{ $title }}">
            </div>
        @else
            <div class="hero-banner hero-banner--fallback">
                <h3 class="hero-banner-fallback-title">{{ $title }}</h3>
            </div>
        @endif

        <div class="hero-body">
            <h1>{{ $title }}</h1>
            <div class="meta-pills">
                @if($event->date)
                    <div class="meta-pill">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($event->date)->format('l, j F Y') }}
                    </div>
                @endif
                @if($event->from_time || $event->to_time)
                    <div class="meta-pill">
                        <i class="far fa-clock"></i>
                        {{ $event->from_time ?: '--' }} - {{ $event->to_time ?: '--' }}
                    </div>
                @endif
                @if($locationName)
                    <div class="meta-pill">
                        <i class="fas fa-location-dot"></i>
                        {{ $locationName }}
                    </div>
                @endif
            </div>
            @if($primaryDescription)
                <p>{{ $primaryDescription }}</p>
            @else
                <p>{{ __('rsvp.hero_fallback') }}</p>
            @endif
        </div>
    </div>
</div>

<div class="container pb-5" style="padding: 2rem 1rem;">
    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="glass-card mb-4">
                <div class="card-header-section">
                    <span class="card-label">{{ __('rsvp.labels.invitation') }}</span>
                    <h2 class="section-title">{{ __('rsvp.labels.event_details') }}</h2>
                    <p class="section-desc">{{ __('rsvp.labels.event_details_help') }}</p>
                </div>

                @if($primaryDescription)
                    <p class="mb-3" style="font-size: 1.02rem; line-height: 1.8; color: #333;">{{ $primaryDescription }}</p>
                @endif

                @if($secondaryDescription && $secondaryDescription !== $primaryDescription)
                    <p class="mb-0" style="font-size: 1.02rem; line-height: 1.8; color: #555;">{{ $secondaryDescription }}</p>
                @endif
            </div>

            <div class="glass-card" id="locationBox">
                <div class="card-header-section">
                    <span class="card-label">{{ __('rsvp.labels.location') }}</span>
                    <h2 class="section-title">{{ __('rsvp.labels.location') }}</h2>
                </div>

                @if($event->google_map_url)
                    <p class="small mb-2">
                        <a href="{{ $event->google_map_url }}" target="_blank" rel="noopener">{{ __('rsvp.labels.open_map') }}</a>
                    </p>
                @endif

                <p class="text-muted small mb-3">{{ $locationName }}</p>
                <div style="height: 280px; overflow: hidden; border-radius: 16px; margin-top: -0.5rem;">
                    <iframe src="{{ $mapEmbed }}" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <section class="glass-card mb-4 {{ $hasResponded ? '' : 'd-none' }}" id="successSection">
                <div class="rsvp-success">
                    <div class="mb-3">
                        <i id="successIcon" class="@if($isAccepted) fas fa-envelope-circle-check text-success @elseif($isMaybe) fas fa-question-circle text-warning @else far fa-check-circle text-muted @endif"></i>
                    </div>
                    <h3 id="successTitle" class="fw-bold mb-2 {{ $isAccepted ? 'text-success' : ($isMaybe ? 'text-warning' : 'text-secondary') }}">
                        {{ $isAccepted ? __('rsvp.success.attendance_confirmed') : __('rsvp.success.response_recorded') }}
                    </h3>
                    <p id="successMessage" class="mb-0">
                        @if($hasResponded)
                            @if($isAccepted)
                                {{ __('rsvp.success.accepted_message') }}
                            @elseif($isMaybe)
                                {{ __('rsvp.success.maybe_message') }}
                            @else
                                {{ __('rsvp.success.declined_message') }}
                            @endif
                        @endif
                    </p>
                </div>
            </section>

            <section class="glass-card {{ $hasResponded ? 'd-none' : '' }}" id="mainFormCard">
                <div class="card-header-section">
                    <span class="card-label">{{ __('rsvp.labels.rsvp') }}</span>
                    <h2 class="section-title">{{ __('rsvp.labels.attendance_question') }}</h2>
                    <p class="section-desc">{{ __('rsvp.labels.attendance_help') }}</p>
                </div>

                <form
                    id="rsvpForm"
                    method="POST"
                    action="{{ route('rsvp.submit', $tokenForSubmit) }}"
                    data-msg-attendance-confirmed="{{ __('rsvp.success.attendance_confirmed') }}"
                    data-msg-response-recorded="{{ __('rsvp.success.response_recorded') }}"
                    data-msg-accepted="{{ __('rsvp.success.accepted_message') }}"
                    data-msg-maybe="{{ __('rsvp.success.maybe_message') }}"
                    data-msg-declined="{{ __('rsvp.success.declined_message') }}">
                    @csrf
                    <input type="hidden" name="response_status" id="response_status">

                    <div class="rsvp-status-grid">
                        <div class="rsvp-status-tile" id="btnAccept" onclick="selectStatus('accepted')">
                            <i class="fas fa-check-circle"></i>
                            <div class="fw-semibold">{{ __('rsvp.actions.accept') }}</div>
                        </div>
                        <div class="rsvp-status-tile" id="btnMaybe" onclick="selectStatus('maybe')">
                            <i class="fas fa-question-circle"></i>
                            <div class="fw-semibold">{{ __('rsvp.actions.maybe') }}</div>
                        </div>
                        <div class="rsvp-status-tile" id="btnDecline" onclick="selectStatus('declined')">
                            <i class="fas fa-times-circle"></i>
                            <div class="fw-semibold">{{ __('rsvp.actions.decline') }}</div>
                        </div>
                    </div>

                    <div id="guestSection" class="rsvp-action-box">
                        <label class="form-label">{{ __('rsvp.labels.additional_guests') }}</label>
                        <select name="guests_count" class="form-select mb-3">
                            <option value="0">{{ __('rsvp.actions.just_me') }}</option>
                            @for($i = 1; $i <= (int) ($guest->allowed_guests ?? 0); $i++)
                                <option value="{{ $i }}">{{ trans_choice('rsvp.actions.guest_count', $i, ['count' => $i]) }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" id="submitBtn">
                            <span class="btn-text">{{ __('rsvp.actions.confirm_attendance') }}</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>

                    <div id="maybeSection" class="rsvp-action-box" style="background:#fffaf0; border-color:#ffe29f;">
                        <button type="submit" class="btn btn-warning text-white w-100 py-2 fw-bold" id="maybeSubmitBtn">
                            <span class="btn-text">{{ __('rsvp.actions.save_maybe') }}</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>

                    <div id="declineSection" class="rsvp-action-box" style="background:#fff7f7; border-color:#ffd3d3;">
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-bold" id="declineBtn">
                            <span class="btn-text">{{ __('rsvp.actions.confirm_decline') }}</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    guestSection.style.display = 'none';
    maybeSection.style.display = 'none';
    declineSection.style.display = 'none';

    if (status === 'accepted') {
        btnAccept.classList.add('active-accept');
        guestSection.style.display = 'block';
    } else if (status === 'maybe') {
        btnMaybe.classList.add('active-maybe');
        maybeSection.style.display = 'block';
    } else {
        btnDecline.classList.add('active-decline');
        declineSection.style.display = 'block';
    }
}

const form = document.getElementById('rsvpForm');
if (form) {
    const i18n = {
        attendanceConfirmed: form.dataset.msgAttendanceConfirmed,
        responseRecorded: form.dataset.msgResponseRecorded,
        acceptedMessage: form.dataset.msgAccepted,
        maybeMessage: form.dataset.msgMaybe,
        declinedMessage: form.dataset.msgDeclined,
    };

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const status = formData.get('response_status');

        let activeBtn;
        if (status === 'accepted') activeBtn = document.getElementById('submitBtn');
        else if (status === 'maybe') activeBtn = document.getElementById('maybeSubmitBtn');
        else activeBtn = document.getElementById('declineBtn');

        if (!activeBtn) {
            return;
        }

        const btnText = activeBtn.querySelector('.btn-text');
        const spinner = activeBtn.querySelector('.spinner-border');
        activeBtn.disabled = true;
        btnText.style.opacity = '0.5';
        spinner.classList.remove('d-none');

        fetch("{{ route('rsvp.submit', $tokenForSubmit) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed');
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    return;
                }

                // Use classList instead of style.display to work correctly with Bootstrap's d-none (!important).
                document.getElementById('mainFormCard').classList.add('d-none');
                document.getElementById('locationBox').classList.add('d-none');
                const successSection = document.getElementById('successSection');
                const successTitle = document.getElementById('successTitle');
                const successMsg = document.getElementById('successMessage');
                const successIcon = document.getElementById('successIcon');
                successSection.classList.remove('d-none');

                successTitle.classList.remove('text-success', 'text-warning', 'text-secondary');

                if (status === 'accepted') {
                    successTitle.classList.add('text-success');
                    successTitle.innerText = i18n.attendanceConfirmed;
                    successMsg.innerText = i18n.acceptedMessage;
                    successIcon.className = 'fas fa-envelope-circle-check text-success';
                } else if (status === 'maybe') {
                    successTitle.classList.add('text-warning');
                    successTitle.innerText = i18n.responseRecorded;
                    successMsg.innerText = i18n.maybeMessage;
                    successIcon.className = 'fas fa-question-circle text-warning';
                } else {
                    successTitle.classList.add('text-secondary');
                    successTitle.innerText = i18n.responseRecorded;
                    successMsg.innerText = i18n.declinedMessage;
                    successIcon.className = 'far fa-check-circle text-muted';
                }

                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(() => {
                activeBtn.disabled = false;
                btnText.style.opacity = '1';
                spinner.classList.add('d-none');
                form.submit();
            });
    });
}
</script>
@endpush
