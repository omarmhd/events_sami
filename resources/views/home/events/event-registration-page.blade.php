@extends('layouts.public.event')

@section('title', ($event->title ?: $event->name) . ' - ' . __('public-registration.page_title'))

@section('content')
@php
    $eventTitle = $event->title ?: $event->name;
    $eventAddress = $event->location_name ?: ($event->address ?? '');
    $mapUrl = $event->google_map_url ?: ($eventAddress ? 'https://maps.google.com/maps?q=' . urlencode($eventAddress) . '&t=&z=13&ie=UTF8&iwloc=&output=embed' : null);
    $selectedForm = $event->registrationForm;
    
    // Resolve hero image: event header → company branding header → no image.
    $headerImagePath = $event->header_image_path ?? '';
    if ($headerImagePath !== '') {
        // /uploads/... paths are served directly; absolute URLs passed as-is.
        $eventImage = \Illuminate\Support\Str::startsWith($headerImagePath, ['http://', 'https://', '/'])
            ? $headerImagePath
            : asset(ltrim($headerImagePath, '/'));
    } else {
        $companyBranding = $event->company
            ? \App\Models\CompanyBranding::where('company_id', $event->company_id)->first()
            : null;
        $eventImage = ($companyBranding && $companyBranding->header_image_url)
            ? $companyBranding->header_image_url
            : '';
    }

    $hasImage = $eventImage !== '';
@endphp

<div class="container hero">
    <div class="hero-shell">
        @if($hasImage)
            <div class="hero-banner">
                <img src="{{ e($eventImage) }}" alt="{{ $eventTitle }}">
            </div>
        @endif

        <div class="hero-body">
            <h1>{{ $eventTitle }}</h1>
            <div class="meta-pills">
                @if($event->date)
                    <div class="meta-pill">
                        <i class="far fa-calendar-alt"></i>
                        {{ $event->date->format(app()->getLocale() === 'ar' ? 'j F Y' : 'l, j F Y') }}
                    </div>
                @endif
                @if($event->from_time || $event->to_time)
                    <div class="meta-pill">
                        <i class="far fa-clock"></i>
                        {{ $event->from_time ?: '--' }} - {{ $event->to_time ?: '--' }}
                    </div>
                @endif
                @if($eventAddress)
                    <div class="meta-pill">
                        <i class="fas fa-location-dot"></i>
                        {{ $eventAddress }}
                    </div>
                @endif
            </div>
            <p></p>
        </div>
    </div>
</div>

<div class="container pb-5" style="padding: 2rem 1rem;">
    <div class="row g-4 align-items-start">
        <!-- Left Column: Event Details -->
        <div class="col-lg-7">
            <!-- About Event Section -->
            <div class="glass-card mb-4">
                <div class="card-header-section">
                    <span class="card-label">{{ __('public-registration.about_event') }}</span>
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h2 class="section-title">{{ __('public-registration.clear_registration') }}</h2>
                            <p class="section-desc">{{ __('public-registration.designed_for_clarity') }}</p>
                        </div>
                        @if($event->experience_type)
                            <span class="badge-event-type">{{ $event->experience_type }}</span>
                        @endif
                    </div>
                </div>

                @if(app()->getLocale() === 'ar' && $event->description)
                    <p class="mb-0" style="font-size: 1.05rem; line-height: 1.8; color: #333;">{{ $event->description }}</p>
                @elseif($event->description_en)
                    <p class="mb-0" style="font-size: 1.05rem; line-height: 1.8; color: #333;">{{ $event->description_en }}</p>
                @endif
            </div>

            <!-- Schedule Section -->
            @if(!empty($event->schedule_items) && is_array($event->schedule_items) && count($event->schedule_items) > 0)
                <div class="glass-card mb-4">
                    <div class="card-header-section">
                        <span class="card-label">{{ __('public-registration.schedule') }}</span>
                        <h2 class="section-title">{{ __('public-registration.schedule') }}</h2>
                        <p class="section-desc">{{ __('public-registration.designed_for_clarity') }}</p>
                    </div>

                    @foreach($event->schedule_items as $item)
                        <div class="schedule-line">
                            <div class="schedule-time">{{ $item['start_time'] ?? '--:--' }}</div>
                            <div>
                                <div style="font-weight: 600; color: var(--primary-dark); margin-bottom: 0.3rem;">
                                    {{ $item['title'] ?? '-' }}
                                </div>
                                @if(!empty($item['stage']))
                                    <div class="text-muted small" style="margin-bottom: 0.3rem;">{{ $item['stage'] }}</div>
                                @endif
                                @if(!empty($item['description']))
                                    <div class="text-muted small">{{ $item['description'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Location Section -->
            @if($mapUrl)
                <div class="glass-card">
                    <div class="card-header-section">
                        <span class="card-label">{{ __('public-registration.location') }}</span>
                        <h2 class="section-title">{{ __('public-registration.location') }}</h2>
                    </div>
                    <div style="height: 280px; overflow: hidden; border-radius: 16px; margin-top: -1.5rem;">
                        <iframe 
                            src="{{ $mapUrl }}" 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0" 
                            allowfullscreen
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Registration Form -->
        <div class="col-lg-5">
            <div class="glass-card">

                @if(session('success'))
                    {{-- ── Registration complete: hide form, show confirmation ── --}}
                    <div style="text-align:center;padding:2rem 1rem;">
                        <div style="width:64px;height:64px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-check" style="color:#16a34a;font-size:1.6rem;"></i>
                        </div>
                        <h3 style="font-weight:700;color:#166534;margin-bottom:.5rem;">تم التسجيل بنجاح!</h3>
                        <p style="color:#374151;font-size:.95rem;line-height:1.7;margin-bottom:1.5rem;">
                            {{ session('success') }}
                        </p>
                        <p style="color:#6b7280;font-size:.85rem;">
                            تحقق من بريدك الإلكتروني — ستصلك بطاقة الدخول مع QR Code قريباً.
                        </p>
                    </div>

                @elseif(session('info'))
                    {{-- ── Already registered: show info notice, hide form ── --}}
                    <div style="text-align:center;padding:2rem 1rem;">
                        <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="fas fa-info-circle" style="color:#1d4ed8;font-size:1.6rem;"></i>
                        </div>
                        <h3 style="font-weight:700;color:#1e3a8a;margin-bottom:.5rem;">تسجيل موجود مسبقاً</h3>
                        <p style="color:#374151;font-size:.95rem;line-height:1.7;">
                            {{ session('info') }}
                        </p>
                    </div>

                @else
                    {{-- ── Show form ── --}}

                <!-- Form Header -->
                <div class="card-header-section">
                    <span class="card-label">{{ __('public-registration.registration_form') }}</span>
                    <h2 class="section-title">{{ ($selectedForm && $selectedForm->name !== '__default__') ? $selectedForm->name : __('public-registration.registration_details') }}</h2>
                    <p class="section-desc">
                        {{ $selectedForm?->intro_text ?: __('public-registration.share_info') }}
                    </p>
                </div>

                <!-- Validation errors -->
                @if($errors->any())
                    <div class="alert alert-danger mb-3" style="border-radius:10px;font-size:.88rem;">
                        <i class="fas fa-triangle-exclamation me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Registration Form -->
                <form method="POST" action="{{ route('events.public.register', $event->event_slug) }}" class="row g-3">
                    @csrf

                    <!-- Full Name (Fixed) -->
                    <div class="col-12">
                        <label class="form-label">
                            {{ __('public-registration.full_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="guest_name" 
                            class="form-control @error('guest_name') is-invalid @enderror" 
                            value="{{ old('guest_name') }}" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'محمد علي' : 'John Doe' }}"
                            required>
                        @error('guest_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Business Email (Fixed) -->
                    <div class="col-12">
                        <label class="form-label">
                            {{ __('public-registration.business_email') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="guest_email" 
                            class="form-control @error('guest_email') is-invalid @enderror" 
                            value="{{ old('guest_email') }}" 
                            placeholder="email@company.com"
                            required>
                        @error('guest_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <!-- Dynamic Fields Only (exclude built-in name/email fields) -->
                    @php
                        $builtinKeys = ['full_name','email','guest_name','guest_email','name'];
                        $filteredFields = collect($dynamicFields ?? [])->filter(
                            fn($f) => !in_array($f['key'] ?? $f['name'] ?? '', $builtinKeys, true)
                                   && !in_array(strtolower($f['label'] ?? ''), ['الاسم الكامل','full name','email','البريد الإلكتروني','البريد الإلكتروني للعمل','work email','business email'], true)
                        )->values()->all();
                    @endphp
                    @if(!empty($filteredFields))
                        @foreach($filteredFields as $field)
                            @php
                                $widthClass = match($field['width'] ?? 'full') {
                                    'half' => 'col-md-6',
                                    'third' => 'col-md-4',
                                    default => 'col-12',
                                };
                                $fieldName = 'form_payload[' . $field['key'] . ']';
                                $fieldValue = old('form_payload.' . $field['key']);
                            @endphp
                            <div class="{{ $widthClass }}">
                                <label class="form-label">
                                    {{ $field['label'] ?? '' }}
                                    @if($field['required'] ?? false)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if(in_array($field['type'] ?? 'text', ['text', 'email', 'tel', 'number', 'date'], true))
                                    <input 
                                        type="{{ $field['type'] }}" 
                                        name="{{ $fieldName }}" 
                                        class="form-control @error('form_payload.' . $field['key']) is-invalid @enderror" 
                                        value="{{ $fieldValue }}" 
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                @elseif(($field['type'] ?? null) === 'textarea')
                                    <textarea 
                                        name="{{ $fieldName }}" 
                                        rows="3" 
                                        class="form-control @error('form_payload.' . $field['key']) is-invalid @enderror" 
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ $fieldValue }}</textarea>
                                @elseif(($field['type'] ?? null) === 'select')
                                    <select 
                                        name="{{ $fieldName }}" 
                                        class="form-select @error('form_payload.' . $field['key']) is-invalid @enderror"
                                        {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                        <option value="">{{ $field['placeholder'] ?? __('public-registration.registration_details') }}</option>
                                        @foreach($field['options'] ?? [] as $option)
                                            <option value="{{ $option }}" {{ $fieldValue === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif(($field['type'] ?? null) === 'radio')
                                    @foreach($field['options'] ?? [] as $option)
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input" 
                                                type="radio" 
                                                name="{{ $fieldName }}" 
                                                value="{{ $option }}" 
                                                id="{{ $field['key'] . '_' . $loop->index }}"
                                                {{ $fieldValue === $option ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $field['key'] . '_' . $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                @elseif(($field['type'] ?? null) === 'checkbox')
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            name="{{ $fieldName }}" 
                                            value="1" 
                                            id="{{ $field['key'] }}"
                                            {{ $fieldValue ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field['key'] }}">
                                            {{ $field['placeholder'] ?? __('public-registration.confirm') }}
                                        </label>
                                    </div>
                                @endif

                                @if($field['help_text'] ?? null)
                                    <div class="text-muted small mt-1">{{ $field['help_text'] }}</div>
                                @endif

                                @error('form_payload.' . $field['key'])<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                    @endif

                    <!-- Info Alert -->
                    <div class="col-12">
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            {{ __('public-registration.free_trial_info') }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <button 
                            type="submit" 
                            class="btn btn-submit w-100">
                            <span id="btn-text">{{ __('public-registration.submit_registration') }}</span>
                            <span class="loading-spinner" id="btn-spinner">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
                @endif {{-- end @else (form shown only when no success) --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Show loading state
                const btnText = document.getElementById('btn-text');
                const btnSpinner = document.getElementById('btn-spinner');
                if (btnText && btnSpinner) {
                    btnText.style.display = 'none';
                    btnSpinner.classList.add('show');
                }
            });
        }
    });
</script>
@endpush

@endsection
