<div class="sidebar-wrapper">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div>
            <div class="brand-text">لوحة<span> الإدارة</span></div>
            <div class="badge bg-danger mt-1" style="font-size: 0.6rem; padding: 2px 6px;">System Admin</div>
        </div>
    </div>

    {{-- ── Core ── --}}
    <span class="sidebar-headline">الرئيسية</span>

    <a href="{{ route('system.dashboard') }}" class="nav-link-custom {{ request()->routeIs('system.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i>
        <span>لوحة التحكم</span>
    </a>

    {{-- ── Subscribers ── --}}
    <span class="sidebar-headline mt-3">المشتركون</span>

    <a href="{{ route('system.companies') }}" class="nav-link-custom {{ request()->routeIs('system.companies*') ? 'active' : '' }}">
        <i class="fas fa-building"></i>
        <span>المنظمات</span>
    </a>

    <a href="{{ route('system.subscriptions') }}" class="nav-link-custom {{ request()->routeIs('system.subscriptions') ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i>
        <span>الاشتراكات</span>
    </a>

    <a href="{{ route('system.invoices') }}" class="nav-link-custom {{ request()->routeIs('system.invoices*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>الفواتير</span>
    </a>

    @php $pendingCount = \App\Models\BillingContactRequest::where('status','pending')->count(); @endphp
    <a href="{{ route('system.renewal-requests') }}" class="nav-link-custom {{ request()->routeIs('system.renewal-requests*') ? 'active' : '' }}">
        <i class="fas fa-rotate-right"></i>
        <span>طلبات التجديد</span>
        @if($pendingCount > 0)
        <span style="background:#f43f5e;color:#fff;border-radius:999px;padding:1px 7px;font-size:.65rem;font-weight:700;margin-right:auto;">{{ $pendingCount }}</span>
        @endif
    </a>

    {{-- ── Configuration ── --}}
    <span class="sidebar-headline mt-3">الإعدادات</span>

    <a href="{{ route('system.plans') }}" class="nav-link-custom {{ request()->routeIs('system.plans*') ? 'active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>إدارة الخطط</span>
    </a>

    <a href="{{ route('system.settings') }}" class="nav-link-custom {{ request()->routeIs('system.settings*') ? 'active' : '' }}">
        <i class="fas fa-sliders"></i>
        <span>إعدادات النظام</span>
    </a>

    <a href="{{ route('system.users') }}" class="nav-link-custom {{ request()->routeIs('system.users*') ? 'active' : '' }}">
        <i class="fas fa-user-shield"></i>
        <span>مستخدمو النظام</span>
    </a>

    {{-- ── Access badge ── --}}
    <div class="sidebar-plan-card" style="background: linear-gradient(135deg,#1e1b4b,#312e81);">
        <p class="sidebar-plan-title">صلاحية الوصول</p>
        <p class="sidebar-plan-value" style="font-size: 0.85rem;">/admin</p>
        <small style="color: rgba(255,255,255,0.5); font-size: 0.68rem;">نطاق النظام فقط</small>
    </div>

    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-right-from-bracket"></i>
                <span>{{ __('ui.sidebar.sign_out') }}</span>
            </button>
            <p class="sidebar-footer-note">{{ __('ui.sidebar.logout_hint') }}</p>
        </form>
    </div>
</div>
