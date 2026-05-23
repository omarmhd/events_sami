@extends('layouts.app')

@section('title', 'نماذج التسجيل')

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   Registration Forms Page — Professional Redesign
═══════════════════════════════════════════════ */

.rfp-wrap {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Page Header ─────────────────────────────── */
.rfp-header {
    background: linear-gradient(135deg, #102a2a 0%, #1a4040 60%, #0f8f83 100%);
    border-radius: var(--radius-lg);
    padding: 1.6rem 1.8rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px -12px rgba(16, 42, 42, .45);
}

.rfp-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 85% -10%, rgba(15,143,131,.35) 0%, transparent 55%),
        radial-gradient(ellipse at 10% 110%, rgba(255,255,255,.04) 0%, transparent 50%);
    pointer-events: none;
}

.rfp-header::after {
    content: '\f0ae';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    left: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 7rem;
    color: rgba(255,255,255,.05);
    pointer-events: none;
    line-height: 1;
}

.rfp-header-text { position: relative; }

.rfp-header-text h1 {
    margin: 0 0 .35rem;
    font-size: clamp(1.35rem, 2.5vw, 1.75rem);
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: .55rem;
    line-height: 1.2;
}

.rfp-header-text p {
    margin: 0;
    color: rgba(255,255,255,.72);
    font-size: .875rem;
}

.rfp-header-actions { position: relative; }

.rfp-btn-create {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: var(--primary-color);
    color: #fff;
    font-weight: 800;
    font-size: .875rem;
    padding: .65rem 1.3rem;
    border-radius: 999px;
    text-decoration: none;
    border: none;
    box-shadow: 0 4px 16px rgba(15,143,131,.4);
    transition: transform .18s ease, box-shadow .18s ease, background .18s;
    white-space: nowrap;
}

.rfp-btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15,143,131,.5);
    background: var(--primary-hover);
    color: #fff;
}

.rfp-btn-create.locked {
    background: #f59e0b;
    box-shadow: 0 4px 16px rgba(245,158,11,.35);
}

.rfp-btn-create.locked:hover {
    background: #d97706;
    box-shadow: 0 8px 24px rgba(245,158,11,.45);
}

.rfp-btn-create.disabled {
    background: rgba(255,255,255,.15);
    box-shadow: none;
    cursor: not-allowed;
    color: rgba(255,255,255,.55);
}

/* ── Stats bar ───────────────────────────────── */
.rfp-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: .75rem;
}

.rfp-stat-card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    gap: .85rem;
    box-shadow: var(--shadow-soft);
}

.rfp-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.rfp-stat-icon.teal { background: var(--primary-soft); color: var(--primary-color); }
.rfp-stat-icon.amber { background: rgba(245,158,11,.1); color: #d97706; }
.rfp-stat-icon.slate { background: rgba(100,116,139,.1); color: #64748b; }

.rfp-stat-num {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}

.rfp-stat-lbl {
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: .28rem;
}

/* ── Toolbar ─────────────────────────────────── */
.rfp-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
}

.rfp-count-badge {
    font-size: .82rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: .4rem;
}

.rfp-count-badge strong { color: var(--text-main); }

.rfp-filter-tabs {
    display: flex;
    gap: .35rem;
    background: var(--surface-soft);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: .3rem;
}

.rfp-filter-tab {
    font-size: .78rem;
    font-weight: 700;
    padding: .35rem .85rem;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all .18s ease;
}

.rfp-filter-tab.active,
.rfp-filter-tab:hover {
    background: var(--surface);
    color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

/* ── Forms Grid ──────────────────────────────── */
.rfp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.1rem;
}

/* ── Form Card ───────────────────────────────── */
.rfc {
    border-radius: var(--radius-md);
    border: 1px solid var(--line);
    background: var(--surface);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: transform .22s ease, box-shadow .22s ease;
}

.rfc:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

/* Card top accent strip */
.rfc-strip {
    height: 5px;
    background: var(--grad-primary);
}

.rfc-strip.paused {
    background: linear-gradient(90deg, #94a3b8, #64748b);
}

/* Card header */
.rfc-head {
    padding: 1rem 1.1rem .7rem;
    border-bottom: 1px solid var(--line);
}

.rfc-chips {
    display: flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .6rem;
    flex-wrap: wrap;
}

.rfc-status {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .24rem .65rem;
    border-radius: 999px;
}

.rfc-status.active {
    background: rgba(31,122,77,.1);
    color: var(--success-color);
    border: 1px solid rgba(31,122,77,.2);
}

.rfc-status.paused {
    background: rgba(100,116,139,.1);
    color: #64748b;
    border: 1px solid rgba(100,116,139,.2);
}

.rfc-fields-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .24rem .65rem;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary-color);
    border: 1px solid rgba(15,143,131,.15);
    margin-right: auto;
}

.rfc-name {
    font-size: 1rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.35;
    margin: 0 0 .25rem;
}

.rfc-slug {
    font-size: .74rem;
    font-family: monospace;
    color: var(--text-soft);
    display: flex;
    align-items: center;
    gap: .3rem;
}

.rfc-slug i { font-size: .65rem; }

/* Card body */
.rfc-body {
    padding: .85rem 1.1rem;
    display: flex;
    flex-direction: column;
    gap: .55rem;
    flex: 1;
}

.rfc-headline {
    font-size: .875rem;
    font-weight: 700;
    color: var(--text-main);
}

.rfc-intro {
    font-size: .8rem;
    color: var(--text-muted);
    line-height: 1.6;
}

.rfc-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: .78rem;
    padding: .45rem .6rem;
    border-radius: 10px;
    background: var(--surface-soft);
    border: 1px solid var(--line);
}

.rfc-meta-label {
    display: flex;
    align-items: center;
    gap: .45rem;
    color: var(--text-muted);
}

.rfc-meta-label i { color: var(--primary-color); opacity: .75; font-size: .78rem; }

.rfc-meta-val {
    font-weight: 800;
    color: var(--text-main);
}

/* Card footer */
.rfc-foot {
    border-top: 1px solid var(--line);
    padding: .75rem .85rem;
    display: flex;
    gap: .5rem;
}

.rfc-action {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    border-radius: 10px;
    border: 1px solid var(--line);
    background: var(--surface-soft);
    color: var(--text-muted);
    font-size: .78rem;
    font-weight: 700;
    text-align: center;
    padding: .52rem .5rem;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s ease;
}

.rfc-action:hover {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15,143,131,.25);
}

.rfc-action.danger {
    border-color: rgba(179,38,30,.2);
    color: var(--danger-color);
    background: rgba(179,38,30,.05);
}

.rfc-action.danger:hover {
    background: var(--danger-color);
    color: #fff;
    border-color: var(--danger-color);
    box-shadow: 0 4px 12px rgba(179,38,30,.28);
}

/* ── Empty State ─────────────────────────────── */
.rfp-empty {
    border-radius: var(--radius-md);
    border: 2px dashed var(--line);
    background: var(--surface);
    text-align: center;
    padding: 3rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .75rem;
}

.rfp-empty-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: var(--primary-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: .25rem;
}

.rfp-empty h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text-main);
}

.rfp-empty p {
    margin: 0;
    color: var(--text-muted);
    font-size: .875rem;
    max-width: 380px;
}

/* ── Responsive ──────────────────────────────── */
@media (max-width: 767.98px) {
    .rfp-header { padding: 1.2rem; }
    .rfp-stats { grid-template-columns: 1fr; }
    .rfp-grid { grid-template-columns: 1fr; }
}

@media (max-width: 575.98px) {
    .rfp-btn-create { width: 100%; justify-content: center; }
    .rfp-filter-tabs { width: 100%; justify-content: space-between; }
    .rfp-filter-tab { flex: 1; text-align: center; }
}
</style>
@endpush

@section('content')
@php
    $totalForms  = $forms->total();
    $activeForms = $forms->getCollection()->where('is_active', true)->count();
    $pausedForms = $forms->getCollection()->where('is_active', false)->count();
    $totalFields = $forms->getCollection()->sum(fn($f) => count($f->fields ?? []));
    $totalEvents = $forms->getCollection()->sum('events_count');
@endphp

<div class="rfp-wrap">

    {{-- ── Page Header ─────────────────────────────────────────── --}}
    <div class="rfp-header">
        <div class="rfp-header-text">
            <h1>
                <i class="fas fa-rectangle-list"></i>
                نماذج التسجيل
            </h1>
            <p>أنشئ نماذج قابلة لإعادة الاستخدام واربطها بالفعاليات العامة بكل سهولة.</p>

            @if(!($formsEnabled ?? true))
                <span class="badge bg-warning text-dark mt-2" style="font-size:.75rem;">
                    <i class="fas fa-lock me-1"></i>غير متاح في خطتك الحالية
                </span>
            @elseif(isset($formsLimit) && $formsLimit !== null)
                <span class="badge mt-2" style="background:rgba(255,255,255,.15);color:#fff;font-size:.75rem;">
                    <i class="fas fa-layer-group me-1"></i>
                    {{ $formsCount ?? 0 }} / {{ $formsLimit }} نموذج مُستخدم
                </span>
            @endif
        </div>

        <div class="rfp-header-actions">
            @if($canCreate ?? true)
                <a href="{{ route('registration-forms.create') }}" class="rfp-btn-create">
                    <i class="fas fa-plus"></i>
                    <span>نموذج جديد</span>
                </a>
            @elseif(!($formsEnabled ?? true))
                <a href="{{ route('feature.unavailable', ['feature' => 'registration_forms']) }}"
                   class="rfp-btn-create locked">
                    <i class="fas fa-arrow-up"></i>
                    <span>ترقية الخطة</span>
                </a>
            @else
                <span class="rfp-btn-create disabled">
                    <i class="fas fa-ban"></i>
                    <span>الحد الأقصى مكتمل</span>
                </span>
            @endif
        </div>
    </div>

    {{-- ── Stats Row ──────────────────────────────────────────── --}}
    @if($forms->count() > 0)
    <div class="rfp-stats">
        <div class="rfp-stat-card">
            <div class="rfp-stat-icon teal">
                <i class="fas fa-rectangle-list"></i>
            </div>
            <div>
                <div class="rfp-stat-num">{{ $totalForms }}</div>
                <div class="rfp-stat-lbl">إجمالي النماذج</div>
            </div>
        </div>
        <div class="rfp-stat-card">
            <div class="rfp-stat-icon amber">
                <i class="fas fa-list-check"></i>
            </div>
            <div>
                <div class="rfp-stat-num">{{ $totalFields }}</div>
                <div class="rfp-stat-lbl">إجمالي الحقول</div>
            </div>
        </div>
        <div class="rfp-stat-card">
            <div class="rfp-stat-icon slate">
                <i class="fas fa-calendar-link"></i>
            </div>
            <div>
                <div class="rfp-stat-num">{{ $totalEvents }}</div>
                <div class="rfp-stat-lbl">فعالية مرتبطة</div>
            </div>
        </div>
    </div>

    {{-- ── Toolbar ────────────────────────────────────────────── --}}
    <div class="rfp-toolbar">
        <div class="rfp-count-badge">
            <i class="fas fa-rectangle-list"></i>
            <strong>{{ $totalForms }}</strong> نموذج مسجّل
        </div>
        <div class="rfp-filter-tabs" id="rfpFilterTabs">
            <button class="rfp-filter-tab active" data-filter="all">الكل</button>
            <button class="rfp-filter-tab" data-filter="active">نشطة</button>
            <button class="rfp-filter-tab" data-filter="paused">متوقفة</button>
        </div>
    </div>
    @endif

    {{-- ── Forms Grid ──────────────────────────────────────────── --}}
    @if ($forms->count() > 0)
        <div class="rfp-grid" id="rfpGrid">
            @foreach($forms as $form)
                @php
                    $isActive    = $form->is_active;
                    $statusClass = $isActive ? 'active' : 'paused';
                    $statusLabel = $isActive ? 'نشط' : 'متوقف';
                    $statusIcon  = $isActive ? 'fa-circle-check' : 'fa-pause-circle';
                    $fieldCount  = count($form->fields ?? []);
                @endphp

                <article class="rfc" data-status="{{ $statusClass }}">

                    {{-- Accent strip --}}
                    <div class="rfc-strip {{ $statusClass }}"></div>

                    {{-- Head --}}
                    <div class="rfc-head">
                        <div class="rfc-chips">
                            <span class="rfc-status {{ $statusClass }}">
                                <i class="fas {{ $statusIcon }}"></i>
                                {{ $statusLabel }}
                            </span>
                            <span class="rfc-fields-badge">
                                <i class="fas fa-list-check"></i>
                                {{ $fieldCount }} حقل
                            </span>
                        </div>
                        <h3 class="rfc-name">{{ $form->name }}</h3>
                        <div class="rfc-slug">
                            <i class="fas fa-link"></i>
                            /{{ $form->slug }}
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="rfc-body">
                        @if($form->headline)
                            <div class="rfc-headline">{{ $form->headline }}</div>
                        @endif

                        <div class="rfc-intro">
                            {{ \Illuminate\Support\Str::limit($form->intro_text ?: 'لم تتم إضافة نص تمهيدي بعد.', 110) }}
                        </div>

                        <div class="rfc-meta-row">
                            <span class="rfc-meta-label">
                                <i class="fas fa-list-check"></i>
                                الحقول المخصصة
                            </span>
                            <span class="rfc-meta-val">{{ $fieldCount }}</span>
                        </div>
                        <div class="rfc-meta-row">
                            <span class="rfc-meta-label">
                                <i class="fas fa-calendar-check"></i>
                                الفعاليات المرتبطة
                            </span>
                            <span class="rfc-meta-val">{{ $form->events_count }}</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="rfc-foot">
                        @if($formsEnabled ?? true)
                            <a href="{{ route('registration-forms.edit', $form) }}" class="rfc-action">
                                <i class="fas fa-pen-to-square"></i>
                                تعديل
                            </a>
                                                        <form action="{{ route('registration-forms.destroy', $form) }}" method="POST"
                                                                    data-confirm="هل أنت متأكد من حذف هذا النموذج؟">
                                @csrf
                                @method('DELETE')
                                                                <button type="submit" class="rfc-action danger w-100 js-confirm-action" data-confirm="هل أنت متأكد من حذف هذا النموذج؟">
                                    <i class="fas fa-trash"></i>
                                    حذف
                                </button>
                            </form>
                        @else
                            {{-- Feature locked — show read-only indicator --}}
                            <span class="rfc-action" style="opacity:.45;cursor:not-allowed;pointer-events:none;">
                                <i class="fas fa-lock"></i>
                                للقراءة فقط
                            </span>
                            <a href="{{ route('billing.upgrade') }}" class="rfc-action" style="color:var(--primary-color);">
                                <i class="fas fa-arrow-up"></i>
                                ترقية
                            </a>
                        @endif
                    </div>

                </article>
            @endforeach
        </div>

        @if ($forms->hasPages())
            <div class="mt-1">{{ $forms->links() }}</div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="rfp-empty">
            <div class="rfp-empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>لا توجد نماذج تسجيل بعد</h3>

            @if($canCreate ?? true)
                <p>أنشئ أول نموذج ثم اربطه بأي فعالية عامة لبدء استقبال التسجيلات.</p>
                <a href="{{ route('registration-forms.create') }}" class="btn btn-primary rounded-pill px-4 mt-1">
                    <i class="fas fa-plus"></i>
                    <span>إنشاء أول نموذج</span>
                </a>
            @elseif(!($formsEnabled ?? true))
                <p>ميزة نماذج التسجيل غير متاحة في خطتك الحالية. قم بالترقية للوصول إليها.</p>
                <a href="{{ route('feature.unavailable', ['feature' => 'registration_forms']) }}"
                   class="btn btn-warning rounded-pill px-4 mt-1">
                    <i class="fas fa-arrow-up me-1"></i>ترقية الخطة
                </a>
            @endif
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Client-side filter tabs
    var tabs  = document.querySelectorAll('.rfp-filter-tab');
    var cards = document.querySelectorAll('#rfpGrid .rfc');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            var filter = tab.getAttribute('data-filter');

            cards.forEach(function (card) {
                var status = card.getAttribute('data-status'); // 'active' | 'paused'
                var show   = filter === 'all' || filter === status;
                card.style.display = show ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
