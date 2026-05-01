@extends('layouts.system')

@section('title', 'إدارة الاشتراكات')

@section('content')

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-main);">إدارة الاشتراكات</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('system.dashboard') }}" class="text-decoration-none text-muted">الرئيسية</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: var(--primary-color);">الاشتراكات</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- ── Subscriptions Table ── --}}
    <div class="custom-card">
        <div class="p-4 border-bottom" style="background: linear-gradient(135deg,rgba(15,143,131,.05),rgba(255,255,255,0));">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:rgba(15,143,131,.1);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-credit-card" style="color:var(--primary-color);font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color:var(--text-main);">اشتراكات المنظمات</h5>
                    <p class="mb-0 small" style="color:var(--text-soft);">متابعة دورة الحياة والحصص عبر جميع المستأجرين</p>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>المنظمة</th>
                        <th>الخطة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">تاريخ البدء</th>
                        <th class="text-center">تاريخ الانتهاء</th>
                        <th class="text-center">الفعاليات المُستخدمة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">
                                    {{ optional($subscription->company)->name ?? '—' }}
                                </div>
                                <div class="small" style="color:var(--text-soft);">
                                    {{ optional($subscription->company)->contact_email }}
                                </div>
                            </td>
                            <td>
                                @if(optional($subscription->plan)->name)
                                    <div class="fw-semibold" style="font-size:0.875rem;color:var(--text-main);">
                                        {{ $subscription->plan->name }}
                                    </div>
                                    <code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:0.73rem;">
                                        {{ strtoupper($subscription->plan->code) }}
                                    </code>
                                @else
                                    <span style="color:var(--text-soft);">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($subscription->status === 'active')
                                    <span class="badge-soft badge-success">نشط</span>
                                @elseif($subscription->status === 'trial')
                                    <span class="badge-soft badge-warning">تجريبي</span>
                                @elseif($subscription->status === 'expired')
                                    <span class="badge-soft badge-danger">منتهي</span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="badge-soft badge-danger">ملغى</span>
                                @else
                                    <span class="badge-soft badge-neutral">{{ $subscription->status }}</span>
                                @endif
                            </td>
                            <td class="text-center small" style="color:var(--text-soft);">
                                {{ optional($subscription->starts_at)->format('Y-m-d') ?: '—' }}
                            </td>
                            <td class="text-center small">
                                @php
                                    $endsAt = $subscription->ends_at ?? $subscription->renews_at ?? null;
                                    $isPast = $endsAt && \Carbon\Carbon::parse($endsAt)->isPast();
                                @endphp
                                <span class="{{ $isPast ? 'text-danger fw-bold' : '' }}" style="font-size:0.84rem;">
                                    {{ $endsAt ? \Carbon\Carbon::parse($endsAt)->format('Y-m-d') : '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $used  = $subscription->annual_events_used ?? 0;
                                    $quota = $subscription->annual_event_quota ?? null;
                                @endphp
                                <span class="fw-bold" style="color:var(--text-main);">{{ $used }}</span>
                                @if(!is_null($quota))
                                    <span style="color:var(--text-soft);font-size:0.82rem;"> / {{ $quota }}</span>
                                    @php $pct = $quota > 0 ? round($used / $quota * 100) : 0; @endphp
                                    <div style="height:4px;border-radius:99px;background:#f1f5f9;margin-top:4px;overflow:hidden;">
                                        <div style="height:100%;border-radius:99px;width:{{ min($pct,100) }}%;background:{{ $pct >= 90 ? '#f43f5e' : ($pct >= 70 ? '#f59e0b' : 'var(--grad-primary)') }};"></div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center gap-2" style="color:var(--text-soft);">
                                    <i class="fas fa-credit-card fa-2x opacity-25"></i>
                                    <span class="small">لا توجد اشتراكات مسجّلة</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
            <div class="p-4 border-top" style="background:#f8fafc;">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

@endsection
