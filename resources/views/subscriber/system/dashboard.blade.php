@extends('layouts.system')

@section('title', 'لوحة تحكم النظام')

@push('styles')
<style>
/* ── Hero ───────────────────────────────────────── */
.admin-hero {
    background: linear-gradient(135deg,#1e1b4b 0%,#312e81 45%,#4c1d95 100%);
    border-radius: var(--radius-xl);
    padding: 2rem 2.5rem;
    color: #fff;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 50px -12px rgba(30,27,75,.55);
}
.admin-hero::before {
    content:'';position:absolute;top:-60px;right:-60px;width:280px;height:280px;border-radius:50%;
    background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);
}
.admin-hero::after {
    content:'';position:absolute;bottom:-40px;left:200px;width:180px;height:180px;border-radius:50%;
    background:radial-gradient(circle,rgba(167,139,250,.15) 0%,transparent 70%);
}
.admin-hero-content { position:relative;z-index:1; }

/* ── KPI Grid ───────────────────────────────────── */
.sys-kpi-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(185px,1fr));gap:1rem;margin-bottom:2rem; }
.sys-kpi {
    background:#fff;border-radius:var(--radius-lg);border:1px solid var(--line);
    padding:1.3rem 1.4rem;position:relative;overflow:hidden;transition:all .25s;
}
.sys-kpi:hover { transform:translateY(-3px);box-shadow:var(--shadow-hover); }
.sys-kpi::after { content:'';position:absolute;bottom:0;left:0;height:3px;border-radius:0;width:100%; }
.sys-kpi.c1::after { background:linear-gradient(90deg,#6366f1,#8b5cf6); }
.sys-kpi.c2::after { background:linear-gradient(90deg,#10b981,#34d399); }
.sys-kpi.c3::after { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.sys-kpi.c4::after { background:linear-gradient(90deg,#14b8a6,#2dd4bf); }
.sys-kpi.c5::after { background:linear-gradient(90deg,#f43f5e,#fb7185); }
.sys-kpi.c6::after { background:linear-gradient(90deg,#0ea5e9,#38bdf8); }
.sys-kpi.c7::after { background:linear-gradient(90deg,#8b5cf6,#a78bfa); }
.sys-kpi.c8::after { background:linear-gradient(90deg,#ec4899,#f9a8d4); }

.sys-icon {
    width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1rem;margin-bottom:.65rem;
}
.sys-lbl { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:.1rem; }
.sys-val { font-size:2rem;font-weight:800;color:var(--text-main);line-height:1; }
.sys-sub { font-size:.75rem;color:var(--text-soft);margin-top:.2rem; }

/* ── Quick Nav ──────────────────────────────────── */
.sys-nav-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.85rem;margin-bottom:2rem; }
.sys-nav-btn {
    display:flex;flex-direction:column;align-items:center;gap:.55rem;padding:1.25rem 1rem;
    background:#fff;border:1px solid var(--line);border-radius:var(--radius-md);
    text-decoration:none;color:var(--text-main);transition:all .25s;
}
.sys-nav-btn:hover { transform:translateY(-3px);box-shadow:var(--shadow-hover);border-color:rgba(99,102,241,.2);color:var(--primary-color); }
.sys-nav-icon {
    width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.15rem;transition:transform .2s;
}
.sys-nav-btn:hover .sys-nav-icon { transform:scale(1.1); }
.sys-nav-lbl { font-size:.78rem;font-weight:700;text-align:center; }

/* ── Revenue Card ───────────────────────────────── */
.rev-card {
    background: linear-gradient(135deg,#065f46,#047857);
    border-radius: var(--radius-lg);
    padding: 1.5rem 1.75rem;
    color: #fff;
    height: 100%;
}
.rev-label { font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;opacity:.75; }
.rev-value { font-size:2.5rem;font-weight:900;letter-spacing:-.02em;line-height:1.1; }

/* ── Recent Companies ───────────────────────────── */
.co-row {
    display:flex;align-items:center;gap:.85rem;padding:.75rem 1rem;border-radius:var(--radius-md);transition:background .15s;
}
.co-row:hover { background:var(--surface-soft); }
.co-avatar {
    width:36px;height:36px;border-radius:10px;background:var(--primary-soft);
    display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;
    color:var(--primary-color);flex-shrink:0;text-transform:uppercase;
}
.co-name { font-weight:600;font-size:.88rem;color:var(--text-main); }
.co-meta { font-size:.73rem;color:var(--text-soft); }

/* ── Plan Distribution ──────────────────────────── */
.plan-dist-row { display:flex;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px solid #f1f5f9; }
.plan-dist-row:last-child { border-bottom:none; }
.plan-dist-name { flex:1;font-size:.82rem;font-weight:600;color:var(--text-main); }
.plan-dist-bar { flex:2;height:6px;border-radius:99px;background:#f1f5f9;overflow:hidden; }
.plan-dist-fill { height:100%;border-radius:99px;background:var(--grad-primary); }
.plan-dist-count { font-size:.82rem;font-weight:700;color:var(--text-soft);min-width:36px;text-align:end; }

/* ── Invoice Summary ────────────────────────────── */
.invoice-stat { display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;border-bottom:1px solid #f1f5f9; }
.invoice-stat:last-child { border-bottom:none; }
.invoice-stat-label { font-size:.82rem;color:var(--text-soft); }
.invoice-stat-val { font-size:.88rem;font-weight:700;color:var(--text-main); }
</style>
@endpush

@section('content')

{{-- ═══ Hero ═══ --}}
<div class="admin-hero animate__animated animate__fadeInDown">
    <div class="admin-hero-content">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <div style="font-size:.72rem;opacity:.7;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:.5rem">
                    <i class="fas fa-shield-halved me-2"></i>لوحة تحكم النظام
                </div>
                <div style="font-size:clamp(1.4rem,2.5vw,2rem);font-weight:800;letter-spacing:-.01em">مركز إدارة المنصة</div>
                <div style="font-size:.85rem;opacity:.8;margin-top:.4rem">
                    <i class="fas fa-calendar-day me-1"></i>{{ now()->locale('ar')->isoFormat('dddd، D MMMM Y') }}
                </div>
            </div>
            <div class="d-flex flex-column align-items-end gap-2">
                <span style="background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.4);border-radius:999px;padding:.3rem .9rem;font-size:.77rem;font-weight:700;">
                    <i class="fas fa-circle me-1" style="font-size:.5rem;color:#34d399;"></i> جميع الأنظمة تعمل
                </span>
                <span style="font-size:.78rem;opacity:.7;">{{ $companiesCount }} منظمة · {{ $usersCount }} مستخدم</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══ KPI Grid ═══ --}}
<div class="sys-kpi-grid animate__animated animate__fadeInUp" style="animation-delay:.08s">

    <div class="sys-kpi c1">
        <div class="sys-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="fas fa-building"></i></div>
        <div class="sys-lbl">المنظمات</div>
        <div class="sys-val" data-count="{{ $companiesCount }}">{{ $companiesCount }}</div>
        <div class="sys-sub">إجمالي المستأجرين</div>
    </div>

    <div class="sys-kpi c2">
        <div class="sys-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="fas fa-circle-check"></i></div>
        <div class="sys-lbl">اشتراكات فعّالة</div>
        <div class="sys-val" data-count="{{ $activePaidCount }}">{{ $activePaidCount }}</div>
        <div class="sys-sub">من {{ $activeSubscriptionsCount }} نشط</div>
    </div>

    <div class="sys-kpi c3">
        <div class="sys-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="fas fa-hourglass-half"></i></div>
        <div class="sys-lbl">في التجربة</div>
        <div class="sys-val" data-count="{{ $activeTrialsCount }}">{{ $activeTrialsCount }}</div>
        <div class="sys-sub">فترة تجريبية</div>
    </div>

    <div class="sys-kpi c4">
        <div class="sys-icon" style="background:rgba(20,184,166,.1);color:#14b8a6"><i class="fas fa-users"></i></div>
        <div class="sys-lbl">المستخدمون</div>
        <div class="sys-val" data-count="{{ $usersCount }}">{{ $usersCount }}</div>
        <div class="sys-sub">{{ $organizerUsersCount }} منظّم</div>
    </div>

    <div class="sys-kpi c5">
        <div class="sys-icon" style="background:rgba(244,63,94,.1);color:#f43f5e"><i class="fas fa-file-invoice-dollar"></i></div>
        <div class="sys-lbl">الفواتير</div>
        <div class="sys-val" data-count="{{ $totalInvoices }}">{{ $totalInvoices }}</div>
        <div class="sys-sub">{{ $unpaidInvoices }} معلّقة</div>
    </div>

    <div class="sys-kpi c6">
        <div class="sys-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9"><i class="fas fa-chart-line"></i></div>
        <div class="sys-lbl">إيرادات الشهر</div>
        <div class="sys-val" style="font-size:1.35rem;">{{ number_format($monthRevenue, 0) }}</div>
        <div class="sys-sub">ر.س هذا الشهر</div>
    </div>

    <div class="sys-kpi c7">
        <div class="sys-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="fas fa-coins"></i></div>
        <div class="sys-lbl">إجمالي الإيرادات</div>
        <div class="sys-val" style="font-size:1.35rem;">{{ number_format($totalRevenue, 0) }}</div>
        <div class="sys-sub">ر.س محصّلة</div>
    </div>

    <div class="sys-kpi c8">
        <div class="sys-icon" style="background:rgba(236,72,153,.1);color:#ec4899"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="sys-lbl">فواتير متأخرة</div>
        <div class="sys-val" data-count="{{ $overdueInvoices }}" style="{{ $overdueInvoices > 0 ? 'color:#ec4899' : '' }}">{{ $overdueInvoices }}</div>
        <div class="sys-sub">تجاوزت الاستحقاق</div>
    </div>

</div>

{{-- ═══ Quick Nav ═══ --}}
<div class="card-surface mb-4 animate__animated animate__fadeInUp" style="animation-delay:.12s">
    <div style="font-size:.92rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
        <i class="fas fa-bolt" style="color:var(--primary-color)"></i> وصول سريع
    </div>
    <div class="sys-nav-grid">
        <a href="{{ route('system.companies') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(99,102,241,.1);color:#6366f1"><i class="fas fa-building"></i></div>
            <span class="sys-nav-lbl">المنظمات</span>
        </a>
        <a href="{{ route('system.subscriptions') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(16,185,129,.1);color:#10b981"><i class="fas fa-credit-card"></i></div>
            <span class="sys-nav-lbl">الاشتراكات</span>
        </a>
        <a href="{{ route('system.invoices') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(245,158,11,.1);color:#f59e0b"><i class="fas fa-file-invoice-dollar"></i></div>
            <span class="sys-nav-lbl">الفواتير</span>
        </a>
        <a href="{{ route('system.plans') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6"><i class="fas fa-layer-group"></i></div>
            <span class="sys-nav-lbl">الخطط</span>
        </a>
        <a href="{{ route('system.settings') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(20,184,166,.1);color:#14b8a6"><i class="fas fa-sliders"></i></div>
            <span class="sys-nav-lbl">الإعدادات</span>
        </a>
        <a href="{{ route('system.users') }}" class="sys-nav-btn">
            <div class="sys-nav-icon" style="background:rgba(244,63,94,.1);color:#f43f5e"><i class="fas fa-user-shield"></i></div>
            <span class="sys-nav-lbl">مستخدمو النظام</span>
        </a>
    </div>
</div>

{{-- ═══ Row 1: Revenue + Monthly Chart + Invoice Summary ═══ --}}
<div class="row g-3 animate__animated animate__fadeInUp" style="animation-delay:.16s;margin-bottom:1.5rem;">

    {{-- Revenue Card --}}
    <div class="col-lg-3">
        <div class="rev-card">
            <div class="rev-label"><i class="fas fa-coins me-1"></i>إجمالي الإيرادات</div>
            <div class="rev-value mt-2">{{ number_format($totalRevenue, 0) }}</div>
            <div style="font-size:.78rem;opacity:.75;margin-top:.3rem;">ريال سعودي — مدفوعات فعلية</div>

            <div style="margin-top:1.25rem;border-top:1px solid rgba(255,255,255,.15);padding-top:1rem;">
                <div class="d-flex justify-content-between mb-2" style="font-size:.78rem">
                    <span style="opacity:.8">هذا الشهر</span>
                    <span class="fw-bold">{{ number_format($monthRevenue, 0) }} ر.س</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:.78rem">
                    <span style="opacity:.8">اشتراكات فعّالة</span>
                    <span class="fw-bold">{{ $activePaidCount }}</span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:.78rem">
                    <span style="opacity:.8">معدل التحويل</span>
                    <span class="fw-bold">
                        @php $conv = $activeSubscriptionsCount > 0 ? round($activePaidCount/$activeSubscriptionsCount*100) : 0; @endphp
                        {{ $conv }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Revenue Chart --}}
    <div class="col-lg-5">
        <div class="card-surface h-100">
            <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
                <i class="fas fa-chart-bar" style="color:var(--primary-color)"></i> الإيرادات الشهرية (آخر 6 أشهر)
            </div>
            <div style="height:190px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Invoice Summary --}}
    <div class="col-lg-4">
        <div class="card-surface h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem">
                    <i class="fas fa-file-invoice-dollar" style="color:var(--primary-color)"></i> ملخص الفواتير
                </div>
                <a href="{{ route('system.invoices') }}" style="font-size:.72rem;color:var(--primary-color);text-decoration:none;font-weight:600;">
                    عرض الكل <i class="fas fa-arrow-left ms-1 fa-xs"></i>
                </a>
            </div>
            <div class="invoice-stat">
                <span class="invoice-stat-label"><i class="fas fa-circle-check me-2" style="color:#10b981;"></i>مدفوعة</span>
                <span class="invoice-stat-val" style="color:#10b981;">{{ $paidInvoices }}</span>
            </div>
            <div class="invoice-stat">
                <span class="invoice-stat-label"><i class="fas fa-clock me-2" style="color:#f59e0b;"></i>معلّقة</span>
                <span class="invoice-stat-val" style="color:#f59e0b;">{{ $unpaidInvoices }}</span>
            </div>
            <div class="invoice-stat">
                <span class="invoice-stat-label"><i class="fas fa-exclamation-triangle me-2" style="color:#f43f5e;"></i>متأخرة</span>
                <span class="invoice-stat-val" style="color:#f43f5e;">{{ $overdueInvoices }}</span>
            </div>
            <div class="invoice-stat">
                <span class="invoice-stat-label"><i class="fas fa-hashtag me-2" style="color:var(--text-soft);"></i>الإجمالي</span>
                <span class="invoice-stat-val">{{ $totalInvoices }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ═══ Row 2: Plan Distribution + Subscription Donut + Recent Companies ═══ --}}
<div class="row g-3 animate__animated animate__fadeInUp" style="animation-delay:.22s;">

    {{-- Plan Distribution --}}
    <div class="col-lg-4">
        <div class="card-surface h-100">
            <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
                <i class="fas fa-layer-group" style="color:var(--primary-color)"></i> توزيع الخطط
            </div>
            @php $maxSubs = $planDistribution->max(fn($p) => ($p->active_count + $p->trial_count)) ?: 1; @endphp
            @forelse($planDistribution as $plan)
                @php $total = $plan->active_count + $plan->trial_count; $pct = round($total / $maxSubs * 100); @endphp
                <div class="plan-dist-row">
                    <div class="plan-dist-name">{{ $plan->name }}</div>
                    <div class="plan-dist-bar">
                        <div class="plan-dist-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                    <div class="plan-dist-count">{{ $total }}</div>
                </div>
            @empty
                <div class="text-center py-4" style="color:var(--text-soft);font-size:.82rem">
                    <i class="fas fa-layer-group fa-lg mb-2 d-block" style="opacity:.2"></i>لا توجد خطط
                </div>
            @endforelse
        </div>
    </div>

    {{-- Subscription Donut --}}
    <div class="col-lg-4">
        <div class="card-surface h-100">
            <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem;margin-bottom:1rem">
                <i class="fas fa-chart-pie" style="color:var(--primary-color)"></i> توزيع الاشتراكات
            </div>
            <div style="height:210px;display:flex;align-items:center;justify-content:center;">
                <canvas id="subDonut"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Companies --}}
    <div class="col-lg-4">
        <div class="card-surface h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem">
                    <i class="fas fa-building" style="color:var(--primary-color)"></i> آخر المنظمات
                </div>
                <a href="{{ route('system.companies') }}" style="font-size:.72rem;color:var(--primary-color);text-decoration:none;font-weight:600;">
                    عرض الكل <i class="fas fa-arrow-left ms-1 fa-xs"></i>
                </a>
            </div>
            @forelse($recentCompanies ?? [] as $co)
            <div class="co-row">
                <div class="co-avatar">{{ substr($co->name, 0, 2) }}</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="co-name text-truncate">{{ $co->name }}</div>
                    <div class="co-meta">{{ $co->created_at->diffForHumans() }}</div>
                </div>
                <a href="{{ route('system.companies.impersonate', $co) }}"
                   style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid var(--line);background:#fff;color:var(--text-soft);text-decoration:none;"
                   title="انتحال">
                    <i class="fas fa-right-to-bracket"></i>
                </a>
            </div>
            @empty
            <div class="text-center py-4" style="color:var(--text-soft);font-size:.82rem">
                <i class="fas fa-building fa-lg mb-2 d-block" style="opacity:.2"></i>لا توجد منظمات بعد
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Pending Renewal Requests ── --}}
@if(($pendingRenewalsCount ?? 0) > 0)
<div class="row mt-3">
    <div class="col-12">
        <div class="card-surface">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="font-size:.9rem;font-weight:700;color:var(--text-main);display:flex;align-items:center;gap:.5rem;">
                    <i class="fas fa-rotate-right" style="color:#f43f5e;"></i>
                    طلبات التجديد المعلقة
                    <span style="background:#f43f5e;color:#fff;border-radius:999px;padding:2px 9px;font-size:.7rem;font-weight:700;">{{ $pendingRenewalsCount }}</span>
                </div>
                <a href="{{ route('system.renewal-requests') }}" style="font-size:.72rem;color:var(--primary-color);text-decoration:none;font-weight:600;">
                    عرض الكل <i class="fas fa-arrow-left ms-1 fa-xs"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0" style="font-size:.83rem;">
                    <thead>
                        <tr>
                            <th>المشترك</th>
                            <th>المنظمة</th>
                            <th>الرسالة</th>
                            <th class="text-center">تاريخ الطلب</th>
                            <th class="text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRenewals ?? [] as $req)
                        <tr>
                            <td>
                                <div class="fw-bold" style="color:var(--text-main);">{{ $req->contact_name }}</div>
                                <div class="small" style="color:var(--text-soft);">{{ $req->contact_email }}</div>
                            </td>
                            <td>{{ optional($req->company)->name ?? '—' }}</td>
                            <td style="color:var(--text-muted);">{{ Str::limit($req->message, 60) }}</td>
                            <td class="text-center small" style="color:var(--text-soft);">
                                {{ $req->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('system.renewal-requests.contacted', $req) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-action btn-action-paid" style="font-size:.75rem;padding:4px 10px;">
                                        <i class="fas fa-phone-volume"></i> تواصلنا
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
// ── Counter animation ─────────────────────────────────────────────────────
document.querySelectorAll('.sys-val[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count);
    if (!target) return;
    let s = 0;
    const dur = 1200, step = 16, inc = target / (dur / step);
    const t = setInterval(() => {
        s += inc;
        if (s >= target) { el.textContent = target.toLocaleString(); clearInterval(t); }
        else el.textContent = Math.floor(s).toLocaleString();
    }, step);
});

// ── Monthly Revenue Bar Chart ─────────────────────────────────────────────
(function () {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;
    const labels  = {!! json_encode($monthlyRevenue->pluck('label')) !!};
    const amounts = {!! json_encode($monthlyRevenue->pluck('amount')) !!};
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'الإيرادات (ر.س)',
                data: amounts,
                backgroundColor: 'rgba(15,143,131,0.15)',
                borderColor: '#0f8f83',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: c => `${c.raw.toLocaleString()} ر.س`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { font: { size: 10 }, callback: v => v.toLocaleString() }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
})();

// ── Subscription Donut ────────────────────────────────────────────────────
(function () {
    const ctx = document.getElementById('subDonut');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['مدفوع', 'تجريبي', 'أخرى'],
            datasets: [{
                data: [
                    {{ $activePaidCount }},
                    {{ $activeTrialsCount }},
                    Math.max(0, {{ $subscriptionsCount }} - {{ $activePaidCount }} - {{ $activeTrialsCount }})
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#e5e7eb'],
                borderWidth: 0,
                spacing: 2,
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11, family: 'Outfit' }, padding: 12, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: { callbacks: { label: c => `${c.label}: ${c.raw}` } }
            }
        }
    });
})();
</script>
@endpush
