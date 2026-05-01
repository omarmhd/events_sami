@extends('layouts.app')

@section('title', __('invitations.create.page_title'))

@push('styles')
<style>
.inv-create-page { display: flex; flex-direction: column; gap: 1.25rem; }
.inv-create-head h1 { margin: 0; font-size: clamp(1.35rem, 2.5vw, 1.9rem); font-weight: 800; }
.inv-create-head p { margin: .35rem 0 0; color: var(--text-muted); font-size: .9rem; }

.inv-create-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-soft);
}

.inv-form-label {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: .45rem;
}

.inv-input-wrap { position: relative; }

.inv-input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-soft);
    font-size: .95rem;
    pointer-events: none;
    z-index: 2;
}

.inv-input {
    min-height: 48px;
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: .65rem .8rem .65rem 2.3rem;
    background: var(--surface-soft);
    font-size: .9rem;
}

.inv-input:focus {
    background: #fff;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(15, 143, 131, .12);
}

.inv-error {
    margin-top: .45rem;
    border-radius: 10px;
    padding: .45rem .65rem;
    font-size: .78rem;
    color: #be123c;
    background: #fff1f2;
    border: 1px solid #fecdd3;
}

.inv-submit {
    border-radius: 10px;
    padding: .62rem 1rem;
    font-weight: 700;
}

.inv-cancel {
    border-radius: 10px;
    padding: .62rem 1rem;
    font-weight: 700;
}

@media (max-width: 575.98px) {
    .inv-form-actions { display: grid !important; grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="inv-create-page">
    <div class="inv-create-head">
        <h1>{{ __('invitations.create.page_title') }}</h1>
        <p>{{ __('invitations.create.page_subtitle') }}</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-0">
            <strong>{{ __('invitations.create.validation_title') }}</strong>
            <div class="small mt-1">{{ __('invitations.create.validation_hint') }}</div>
        </div>
    @endif

    <div class="inv-create-card p-4 p-md-5">
        <form action="{{ route('invitations.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12">
                    <label class="inv-form-label" for="event_id">{{ __('invitations.create.fields.event') }} <span class="text-danger">*</span></label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-calendar inv-input-icon"></i>
                        <select class="form-select inv-input @error('event_id') is-invalid @enderror" id="event_id" name="event_id">
                            <option value="">{{ __('invitations.create.fields.event_placeholder') }}</option>
                            @foreach(($events ?? collect()) as $eventItem)
                                <option value="{{ $eventItem->id }}" {{ old('event_id', request('event_id')) == $eventItem->id ? 'selected' : '' }}>
                                    {{ $eventItem->title ?: $eventItem->name }} ({{ $eventItem->event_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('event_id')<div class="inv-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="inv-form-label" for="invitee_name">{{ __('invitations.create.fields.full_name') }} <span class="text-danger">*</span></label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-user inv-input-icon"></i>
                        <input type="text" class="form-control inv-input @error('invitee_name') is-invalid @enderror" id="invitee_name" name="invitee_name" placeholder="{{ __('invitations.create.fields.full_name_placeholder') }}" value="{{ old('invitee_name') }}">
                    </div>
                    @error('invitee_name')<div class="inv-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="inv-form-label" for="invitee_email">{{ __('invitations.create.fields.email') }} <span class="text-danger">*</span></label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-envelope inv-input-icon"></i>
                        <input type="email" class="form-control inv-input @error('invitee_email') is-invalid @enderror" id="invitee_email" name="invitee_email" placeholder="{{ __('invitations.create.fields.email_placeholder') }}" value="{{ old('invitee_email') }}">
                    </div>
                    @error('invitee_email')<div class="inv-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="inv-form-label" for="invitee_position">{{ __('invitations.create.fields.position') }}</label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-briefcase inv-input-icon"></i>
                        <input type="text" class="form-control inv-input @error('invitee_position') is-invalid @enderror" id="invitee_position" name="invitee_position" placeholder="{{ __('invitations.create.fields.position_placeholder') }}" value="{{ old('invitee_position') }}">
                    </div>
                    @error('invitee_position')<div class="inv-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="inv-form-label" for="invitee_nationality">{{ __('invitations.create.fields.nationality') }}</label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-flag inv-input-icon"></i>
                        <input type="text" class="form-control inv-input @error('invitee_nationality') is-invalid @enderror" id="invitee_nationality" name="invitee_nationality" value="{{ old('invitee_nationality') }}">
                    </div>
                    @error('invitee_nationality')<div class="inv-error">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="inv-form-label" for="allowed_guests">{{ __('invitations.create.fields.allowed_guests') }}</label>
                    <div class="inv-input-wrap">
                        <i class="fas fa-users inv-input-icon"></i>
                        <input type="number" min="0" class="form-control inv-input @error('allowed_guests') is-invalid @enderror" id="allowed_guests" name="allowed_guests" value="{{ old('allowed_guests', 0) }}">
                    </div>
                    <div class="text-muted small mt-1">{{ __('invitations.create.fields.allowed_guests_hint') }}</div>
                    @error('allowed_guests')<div class="inv-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 inv-form-actions">
                <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary inv-cancel">{{ __('invitations.create.actions.cancel') }}</a>
                <button type="submit" class="btn btn-primary inv-submit">
                    <i class="fas fa-paper-plane me-1"></i>
                    {{ __('invitations.create.actions.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
