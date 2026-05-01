@extends('layouts.app')

@section('title', 'Send Invitation')
@push('styles')
    <style>
        .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden; }
        .form-label { font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; }
        .input-group-custom { position: relative; }

        .form-control-styled {
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 12px;
            padding: 12px 15px 12px 45px;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s ease;
            height: 50px;
        }
        .form-control-styled:focus {
            background-color: white;
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-control-styled.is-invalid-custom {
            border-color: #ef4444;
            background-color: #fef2f2;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        .form-control-styled.is-invalid-custom:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
            border-color: #ef4444;
        }

        .input-group-custom.has-error .input-icon {
            color: #ef4444 !important; /* تحويل الأيقونة للأحمر */
        }
        .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; pointer-events: none; z-index: 2; transition: 0.3s; }

        .error-bubble {
            background: #fff1f2;
            color: #be123c;
            border-left: 3px solid #e11d48;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            /* أنيميشن ظهور */
            animation: fadeInUp 0.4s ease;
        }
        .error-bubble i { font-size: 1rem; }

        .alert-modern {
            background: #fff1f2;
            border: 1px solid #fda4af;
            color: #9f1239;
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-submit { background: var(--grad-purple); border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); color: white; }
        .btn-cancel { background: white; border: 1px solid var(--input-border); padding: 12px 25px; border-radius: 12px; font-weight: 600; color: var(--text-muted); transition: 0.3s; }
        .btn-cancel:hover { background: #f1f5f9; color: var(--text-main); }
    </style>
@endpush
@push('styles')

    <style>
        .custom-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: var(--shadow-card); overflow: hidden; }
        .form-label { font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; }
        .input-group-custom { position: relative; }
        .form-control-styled { background-color: var(--input-bg); border: 1px solid var(--input-border); border-radius: 12px; padding: 12px 15px 12px 45px; font-size: 0.95rem; width: 100%; transition: all 0.3s ease; height: 50px; }
        .form-control-styled:focus { background-color: white; border-color: var(--input-focus-border); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); outline: none; }
        .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; pointer-events: none; z-index: 2; }
        .btn-submit { background: var(--grad-purple); border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); color: white; }
        .btn-cancel { background: white; border: 1px solid var(--input-border); padding: 12px 25px; border-radius: 12px; font-weight: 600; color: var(--text-muted); transition: 0.3s; }
        .btn-cancel:hover { background: #f1f5f9; color: var(--text-main); }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Send Invitation</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}" class="text-decoration-none text-muted">Invitations</a></li>
                <li class="breadcrumb-item active text-primary">New Invitation</li>
            </ol>
        </nav>
    </div>

    @if ($errors->any())
        <div class="alert-modern">
            <div class="bg-white rounded-circle p-2 d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0">Action Required</h6>
                <small>Please correct the marked fields below to proceed.</small>
            </div>
        </div>
    @endif

    <div class="custom-card">
        <div class="card-body p-4 p-md-5">

            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 50px; height: 50px; font-size: 1.2rem;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Employee Details</h5>
                    <p class="text-muted small mb-0">
                        Enter the information, and an email will be sent to the employee containing an invitation link.
                    </p>                  <p class="text-muted small mb-0">         They can accept or decline and select the number of guests based on their allowed limit.
                    </p>
                </div>
            </div>

            <form action="{{ route('invitations.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="invitee_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group-custom @error('invitee_name') has-error @enderror">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text"
                                   class="form-control-styled @error('invitee_name') is-invalid-custom @enderror"
                                   id="invitee_name"
                                   name="invitee_name"
                                   placeholder="e.g. Ahmed Ali"
                                   value="{{ old('invitee_name') }}">
                        </div>
                        @error('invitee_name')
                        <div class="error-bubble">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    {{-- Email Field --}}
                    <div class="col-md-6">
                        <label for="invitee_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group-custom @error('invitee_email') has-error @enderror">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email"
                                   class="form-control-styled @error('invitee_email') is-invalid-custom @enderror"
                                   id="invitee_email"
                                   name="invitee_email"
                                   placeholder="name@company.com"
                                   value="{{ old('invitee_email') }}">
                        </div>
                        @error('invitee_email')
                        <div class="error-bubble">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    {{-- Position Field --}}
                    <div class="col-md-6">
                        <label for="invitee_position" class="form-label">Job Title / ID</label>
                        <div class="input-group-custom @error('invitee_position') has-error @enderror">
                            <i class="fas fa-briefcase input-icon"></i>
                            <input type="text"
                                   class="form-control-styled @error('invitee_position') is-invalid-custom @enderror"
                                   id="invitee_position"
                                   name="invitee_position"
                                   placeholder="e.g. HR Specialist"
                                   value="{{ old('invitee_position') }}">
                        </div>
                        @error('invitee_position')
                        <div class="error-bubble">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    {{-- Nationality Field --}}
                    <div class="col-md-6">
                        <label for="invitee_nationality" class="form-label">Nationality</label>
                        <div class="input-group-custom @error('invitee_nationality') has-error @enderror">
                            <i class="fas fa-flag input-icon"></i>
                            <input type="text"
                                   class="form-control-styled @error('invitee_nationality') is-invalid-custom @enderror"
                                   id="invitee_nationality"
                                   name="invitee_nationality"
                                   placeholder=""
                                   value="{{ old('invitee_nationality') }}">
                        </div>
                        @error('invitee_nationality')
                        <div class="error-bubble">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>

                    {{-- Allowed Guests Field --}}
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between @error('allowed_guests') border-danger bg-danger bg-opacity-10 @enderror">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-white p-2 rounded-3 text-muted border shadow-sm">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <label for="allowed_guests" class="form-label mb-0">Allowed Guests</label>
                                    <div class="text-muted small">Extra companions allowed.</div>
                                </div>
                            </div>
                            <div style="width: 100px;">
                                <input type="number"
                                       class="form-control-styled text-center px-2 @error('allowed_guests') is-invalid-custom @enderror"
                                       style="padding-left: 10px;"
                                       id="allowed_guests"
                                       name="allowed_guests"
                                       value="{{ old('allowed_guests', 2) }}"
                                       min="2">
                            </div>
                        </div>
                        @error('allowed_guests')
                        <div class="error-bubble">
                            <i class="fas fa-exclamation-circle"></i> <span>{{ $message }}</span>
                        </div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('invitations.index') }}" class="btn-cancel text-decoration-none">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane me-2"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section("infoCard")
    <div class="stats-card d-none d-md-block">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-chart-pie"></i>
            <h6 class="fw-bold mb-0">Quick Tip</h6>
        </div>

        <div class="stat-row">
            Double-check the email
        </div>

        {{--        <div class="mb-1">--}}
        {{--            <div class="d-flex justify-content-between mb-1">--}}
        {{--                <span class="stat-label" style="font-size: 0.75rem;">Tickets Sold</span>--}}
        {{--                <span class="stat-label fw-bold" style="font-size: 0.75rem;">75%</span>--}}
        {{--            </div>--}}
        {{--            <div class="progress-track">--}}
        {{--                <div class="progress-fill" style="width: 75%;"></div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </div>

@endsection
