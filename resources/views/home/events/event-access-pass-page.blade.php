@extends('layouts.front')

@section('title', 'Event Access Pass')

@push('styles')
<style>
    .pass-page {
        min-height: calc(100vh - 3rem);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pass-card {
        width: min(520px, 92vw);
        border-radius: 24px;
        border: 1px solid #dce9e5;
        background: #ffffff;
        box-shadow: 0 24px 48px -30px rgba(16, 42, 42, 0.45);
        overflow: hidden;
    }

    .pass-head {
        padding: 1.2rem;
        background: linear-gradient(135deg, #0f8f83 0%, #0f766e 100%);
        color: #fff;
    }

    .token-chip {
        display: inline-block;
        margin-top: 0.65rem;
        font-size: 0.78rem;
        color: #57716d;
        background: #f3fbf9;
        border: 1px solid #d8e9e4;
        border-radius: 999px;
        padding: 6px 10px;
        word-break: break-all;
    }
</style>
@endpush

@section('content')
<div class="pass-page">
    <div class="pass-card">
        <div class="pass-head">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Event Access Pass</h5>
                <i class="fas fa-shield-check"></i>
            </div>
        </div>

        <div class="p-4 text-center">
            <p class="text-muted mb-2">Holder</p>
            <h4 class="fw-bold mb-3">{{ $pass->holder_name }}</h4>

            <img src="{{ $qr }}" alt="QR Code" class="img-fluid" style="max-width: 240px; border:1px dashed #d0e2dd; border-radius: 14px; padding:10px; background:#f8fcfb;">

            <div class="mt-3">
                @if($pass->is_used)
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-3 py-2">Already Used</span>
                @else
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">Valid</span>
                @endif
            </div>

            <div class="token-chip">Token: {{ $pass->token }}</div>
        </div>
    </div>
</div>
@endsection
