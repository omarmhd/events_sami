@extends('layouts.app')

@section('title', 'الفعاليات')

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   Events Page — Professional Redesign
═══════════════════════════════════════════════ */

.ep-wrap {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ── Page Header ─────────────────────────────── */
.ep-header {
    background: var(--grad-primary);
    border-radius: var(--radius-lg);
    padding: 1.6rem 1.8rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 40px -12px rgba(15,143,131,.38);
}

.ep-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 80% -20%, rgba(255,255,255,.1) 0%, transparent 60%),
        radial-gradient(ellipse at 20% 120%, rgba(0,0,0,.08) 0%, transparent 60%);
    pointer-events: none;
}

.ep-header-text { position: relative; }

.ep-header-text h1 {
    margin: 0 0 .35rem;
    font-size: clamp(1.35rem, 2.5vw, 1.75rem);
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: .55rem;
    line-height: 1.2;
}

.ep-header-text p {
    margin: 0;
    color: rgba(255,255,255,.78);
    font-size: .875rem;
}

.ep-header-actions {
    position: relative;
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
}

.ep-btn-create {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: #fff;
    color: var(--primary-color);
    font-weight: 800;
    font-size: .875rem;
    padding: .65rem 1.3rem;
    border-radius: 999px;
    text-decoration: none;
    border: none;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    transition: transform .18s ease, box-shadow .18s ease;
    white-space: nowrap;
}

.ep-btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.18);
    color: var(--primary-hover);
}

/* ── Quota Panel ─────────────────────────────── */
.ep-quota {
    background: var(--surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--line);
    padding: 1.2rem 1.4rem;
    box-shadow: var(--shadow-soft);
}

.ep-quota-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .8rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.ep-quota-title {
    font-weight: 700;
    font-size: .9rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: .5rem;
}

.ep-quota-title i {
    color: var(--primary-color);
}

.ep-quota-plan {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .75rem;
    font-weight: 700;
    padding: .3rem .8rem;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary-color);
    border: 1px solid rgba(15,143,131,.18);
}

.ep-quota-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: .75rem;
    margin-bottom: .85rem;
}

.ep-quota-cell {
    background: var(--surface-soft);
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    padding: .85rem;
    text-align: center;
}

.ep-quota-cell .num {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
}

.ep-quota-cell .lbl {
    margin-top: .35rem;
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 600;
}

.ep-quota-bar-wrap {
    display: flex;
    align-items: center;
    gap: .85rem;
}

.ep-quota-bar-meta {
    font-size: .75rem;
    color: var(--text-muted);
    white-space: nowrap;
}

.ep-quota-bar {
    flex: 1;
    height: 8px;
    border-radius: 99px;
    background: var(--surface-muted);
    overflow: hidden;
}

.ep-quota-fill {
    height: 100%;
    border-radius: 99px;
    background: var(--grad-primary);
    transition: width .6s ease;
}

/* ── Toolbar ─────────────────────────────────── */
.ep-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
}

.ep-count-badge {
    font-size: .82rem;
    color: var(--text-muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: .4rem;
}

.ep-count-badge strong {
    color: var(--text-main);
}

.ep-filter-tabs {
    display: flex;
    gap: .35rem;
    background: var(--surface-soft);
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: .3rem;
}

.ep-filter-tab {
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

.ep-filter-tab.active,
.ep-filter-tab:hover {
    background: var(--surface);
    color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

/* ── Events Grid ─────────────────────────────── */
.ep-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
    gap: 1.1rem;
}

/* ── Event Card ──────────────────────────────── */
.ec {
    border-radius: var(--radius-md);
    border: 1px solid var(--line);
    background: var(--surface);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: transform .22s ease, box-shadow .22s ease;
}

.ec:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

/* Card image banner */
.ec-banner {
    height: 140px;
    background-size: cover;
    background-position: center;
    position: relative;
    flex-shrink: 0;
}

.ec-banner-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.08) 0%, rgba(0,0,0,.52) 100%);
    display: flex;
    align-items: flex-end;
    padding: .85rem 1rem;
    gap: .5rem;
}

.ec-banner-fallback {
    height: 140px;
    background: var(--grad-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
}

.ec-banner-fallback::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 70% 30%, rgba(255,255,255,.12) 0%, transparent 70%);
}

.ec-banner-fallback i {
    font-size: 2.2rem;
    color: rgba(255,255,255,.35);
    position: relative;
}

.ec-type-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .26rem .65rem;
    border-radius: 999px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.3);
}

.ec-type-pill.public {
    background: rgba(31, 122, 77, 0.75);
    color: #fff;
}

.ec-type-pill.private {
    background: rgba(16, 42, 42, 0.7);
    color: rgba(255,255,255,.9);
}

.ec-status-pill {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .26rem .65rem;
    border-radius: 999px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.3);
    margin-right: auto;
}

.ec-status-pill.published {
    background: rgba(31,122,77,.8);
    color: #fff;
}

.ec-status-pill.draft {
    background: rgba(80,80,80,.65);
    color: rgba(255,255,255,.88);
}

/* Card body */
.ec-body {
    padding: 1rem 1.1rem .85rem;
    display: flex;
    flex-direction: column;
    gap: .55rem;
    flex: 1;
}

.ec-title {
    font-size: .98rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.4;
    margin: 0;
}

.ec-meta {
    display: flex;
    flex-direction: column;
    gap: .35rem;
    margin-top: .15rem;
}

.ec-meta-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: var(--text-muted);
}

.ec-meta-row i {
    width: 14px;
    text-align: center;
    color: var(--primary-color);
    flex-shrink: 0;
    opacity: .75;
}

.ec-slug {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: var(--surface-soft);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: .28rem .6rem;
    font-size: .74rem;
    font-family: monospace;
    color: var(--text-muted);
    margin-top: .1rem;
}

/* Card footer actions */
.ec-foot {
    border-top: 1px solid var(--line);
    padding: .75rem .85rem;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: .4rem;
}

.ec-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .2rem;
    border-radius: 10px;
    border: 1px solid var(--line);
    background: var(--surface-soft);
    color: var(--text-muted);
    font-size: .65rem;
    font-weight: 700;
    text-align: center;
    padding: .5rem .3rem;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s ease;
    line-height: 1.2;
}

.ec-action i {
    font-size: .82rem;
    display: block;
    margin-bottom: .12rem;
}

.ec-action:hover {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15,143,131,.25);
}

.ec-action.danger {
    border-color: rgba(179,38,30,.2);
    color: var(--danger-color);
    background: rgba(179,38,30,.05);
}

.ec-action.danger:hover {
    background: var(--danger-color);
    color: #fff;
    border-color: var(--danger-color);
    box-shadow: 0 4px 12px rgba(179,38,30,.28);
}

/* ── Empty State ─────────────────────────────── */
.ep-empty {
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

.ep-empty-icon {
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

.ep-empty h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text-main);
}

.ep-empty p {
    margin: 0;
    color: var(--text-muted);
    font-size: .875rem;
    max-width: 380px;
}

/* ── Responsive ──────────────────────────────── */
@media (max-width: 991.98px) {
    .ep-quota-grid {
        grid-template-columns: repeat(2, minmax(0,1fr));
    }
}

@media (max-width: 767.98px) {
    .ep-header {
        padding: 1.2rem;
    }
    .ep-grid {
        grid-template-columns: 1fr;
    }
    .ec-foot {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 575.98px) {
    .ep-quota-grid {
        grid-template-columns: repeat(2, minmax(0,1fr));
    }
    .ep-btn-create {
        width: 100%;
        justify-content: center;
    }
    .ep-filter-tabs {
        width: 100%;
        justify-content: space-between;
    }
    .ep-filter-tab {
        flex: 1;
        text-align: center;
    }
}
</style>
@endpush

@section('content')
@php
    $isArabic = app()->getLocale() === 'ar';
    $planLabel = strtoupper((string) $planName) === 'TRIAL' ? 'تجريبي' : $planName;
    $usagePct = max(0, min(100, (float) $quota['percentageUsed']));
    $total = $events->total();
@endphp

<div class="ep-wrap">

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="ep-header">
        <div class="ep-header-text">
            <h1>
                <i class="fas fa-calendar-star"></i>
                إدارة الفعاليات
            </h1>
            <p>أنشئ الفعاليات، تابع التسجيلات، وأدِر الدعوات من مكان واحد.</p>
        </div>
        <div class="ep-header-actions">
            <a href="{{ route('events.create') }}" class="ep-btn-create">
                <i class="fas fa-plus"></i>
                <span>فعالية جديدة</span>
            </a>
        </div>
    </div>

    {{-- ── Quota Panel ───────────────────────────────────────────── --}}
    <div class="ep-quota">
        <div class="ep-quota-top">
            <span class="ep-quota-title">
                <i class="fas fa-chart-bar"></i>
                إحصاءات الباقة
            </span>
            <span class="ep-quota-plan">
                <i class="fas fa-gem"></i>
                الخطة: {{ $planLabel }}
            </span>
        </div>

        <div class="ep-quota-grid">
            <div class="ep-quota-cell">
                <div class="num">{{ $quota['total'] ?: '∞' }}</div>
                <div class="lbl">الحد السنوي</div>
            </div>
            <div class="ep-quota-cell">
                <div class="num">{{ $quota['used'] }}</div>
                <div class="lbl">المستهلك</div>
            </div>
            <div class="ep-quota-cell">
                <div class="num">{{ $quota['remaining'] ?: ($quota['total'] ? '0' : '∞') }}</div>
                <div class="lbl">المتبقي</div>
            </div>
            <div class="ep-quota-cell">
                <div class="num">{{ $usagePct }}%</div>
                <div class="lbl">نسبة الاستخدام</div>
            </div>
        </div>

        @if ($quota['total'] > 0)
            <div class="ep-quota-bar-wrap">
                <span class="ep-quota-bar-meta">{{ $quota['used'] }} / {{ $quota['total'] }} فعالية</span>
                <div class="ep-quota-bar">
                    <div class="ep-quota-fill" data-width="{{ $usagePct }}"></div>
                </div>
                <span class="ep-quota-bar-meta">{{ $usagePct }}%</span>
            </div>
        @endif
    </div>

    {{-- ── Toolbar ────────────────────────────────────────────────── --}}
    @if($events->count() > 0)
    <div class="ep-toolbar">
        <div class="ep-count-badge">
            <i class="fas fa-calendar-alt"></i>
            <strong>{{ $total }}</strong> فعالية مسجّلة
        </div>
        <div class="ep-filter-tabs" id="epFilterTabs">
            <button class="ep-filter-tab active" data-filter="all">الكل</button>
            <button class="ep-filter-tab" data-filter="published">منشورة</button>
            <button class="ep-filter-tab" data-filter="draft">مسودة</button>
            <button class="ep-filter-tab" data-filter="public">عامة</button>
            <button class="ep-filter-tab" data-filter="private">خاصة</button>
        </div>
    </div>
    @endif

    {{-- ── Events Grid ─────────────────────────────────────────────── --}}
    @if ($events->count() > 0)
        <div class="ep-grid" id="epGrid">
            @foreach ($events as $event)
                @php
                    $type        = $event->event_type === 'public' ? 'public' : 'private';
                    $typeLabel   = $type === 'public' ? 'عام' : 'خاص';
                    $typeIcon    = $type === 'public' ? 'fa-earth-americas' : 'fa-lock';
                    $statusClass = $event->status === 'published' ? 'published' : 'draft';
                    $statusLabel = $event->status === 'published' ? 'منشور' : 'مسودة';
                    $statusIcon  = $event->status === 'published' ? 'fa-circle-check' : 'fa-pen';

                    // Resolve header image for card banner
                    $bannerImg = $event->header_image_path ?? '';
                    if ($bannerImg && !\Illuminate\Support\Str::startsWith($bannerImg, ['http://', 'https://', '/'])) {
                        $bannerImg = asset('storage/' . ltrim($bannerImg, '/'));
                    }
                @endphp

                <article class="ec"
                         data-status="{{ $event->status }}"
                         data-type="{{ $event->event_type }}">

                    {{-- Banner --}}
                    @if($bannerImg)
                        <div class="ec-banner" style="background-image: url('{{ e($bannerImg) }}')">
                            <div class="ec-banner-overlay">
                                <span class="ec-status-pill {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                                <span class="ec-type-pill {{ $type }}">
                                    <i class="fas {{ $typeIcon }}"></i>
                                    {{ $typeLabel }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="ec-banner-fallback">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="ec-banner-overlay">
                                <span class="ec-status-pill {{ $statusClass }}">
                                    <i class="fas {{ $statusIcon }}"></i>
                                    {{ $statusLabel }}
                                </span>
                                <span class="ec-type-pill {{ $type }}">
                                    <i class="fas {{ $typeIcon }}"></i>
                                    {{ $typeLabel }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Body --}}
                    <div class="ec-body">
                        <h3 class="ec-title">{{ $event->title ?: $event->name }}</h3>

                        <div class="ec-meta">
                            @if($event->date)
                                <div class="ec-meta-row">
                                    <i class="far fa-calendar"></i>
                                    <span>{{ optional($event->date)->locale(app()->getLocale())->translatedFormat($isArabic ? 'j F Y' : 'M d, Y') }}</span>
                                </div>
                            @endif
                            @if($event->from_time || $event->to_time)
                                <div class="ec-meta-row">
                                    <i class="far fa-clock"></i>
                                    <span>
                                        {{ $event->from_time ? \Carbon\Carbon::parse($event->from_time)->format('H:i') : '--:--' }}
                                        &mdash;
                                        {{ $event->to_time ? \Carbon\Carbon::parse($event->to_time)->format('H:i') : '--:--' }}
                                    </span>
                                </div>
                            @endif
                            @if($event->location_name)
                                <div class="ec-meta-row">
                                    <i class="fas fa-location-dot"></i>
                                    <span>{{ $event->location_name }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="ec-slug">
                            <i class="fas fa-link" style="font-size:.7rem;opacity:.6;"></i>
                            {{ $event->event_slug }}
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="ec-foot">
                        @if ($event->event_type === 'public')
                            <a href="{{ route('events.registrations.index', $event) }}" class="ec-action">
                                <i class="fas fa-user-check"></i>
                                التسجيلات
                            </a>
                            @php
                                $_evtCompany  = $event->company ?? auth()->user()?->company ?? null;
                                $_publicEvtUrl = $_evtCompany
                                    ? app(\App\Services\PublicUrlService::class)->publicEventUrl($_evtCompany, $event->event_slug)
                                    : route('public.events.register', $event->event_slug);
                            @endphp
                            <a href="{{ $_publicEvtUrl }}" class="ec-action" target="_blank" rel="noopener">
                                <i class="fas fa-share-nodes"></i>
                                صفحة عامة
                            </a>
                        @else
                            <a href="{{ route('events.invitations.index', $event) }}" class="ec-action" style="grid-column: span 2;">
                                <i class="fas fa-envelope-open-text"></i>
                                الدعوات
                            </a>
                        @endif

                        <a href="{{ route('events.edit', $event) }}" class="ec-action">
                            <i class="fas fa-pen-to-square"></i>
                            تعديل
                        </a>

                                                <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                            data-confirm="هل أنت متأكد من حذف الفعالية؟ لا يمكن التراجع عن هذا الإجراء.">
                            @csrf
                            @method('DELETE')
                                                        <button type="submit" class="ec-action danger w-100 js-confirm-action" data-confirm="هل أنت متأكد من حذف الفعالية؟ لا يمكن التراجع عن هذا الإجراء.">
                                <i class="fas fa-trash"></i>
                                حذف
                            </button>
                        </form>
                    </div>

                </article>
            @endforeach
        </div>

        @if ($events->hasPages())
            <div class="mt-1">{{ $events->links() }}</div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="ep-empty">
            <div class="ep-empty-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3>لا توجد فعاليات بعد</h3>
            <p>ابدأ بإنشاء أول فعالية لإدارة الدعوات والتسجيلات بصورة احترافية.</p>
            <a href="{{ route('events.create') }}" class="btn btn-primary rounded-pill px-4 mt-1">
                <i class="fas fa-plus"></i>
                <span>إنشاء أول فعالية</span>
            </a>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Animate quota progress bar
    document.querySelectorAll('.ep-quota-fill[data-width]').forEach(function (el) {
        var w = parseFloat(el.getAttribute('data-width')) || 0;
        el.style.width = '0%';
        setTimeout(function () {
            el.style.width = Math.max(0, Math.min(100, w)) + '%';
        }, 120);
    });

    // Client-side filter tabs
    var tabs = document.querySelectorAll('.ep-filter-tab');
    var cards = document.querySelectorAll('#epGrid .ec');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            var filter = tab.getAttribute('data-filter');

            cards.forEach(function (card) {
                var status = card.getAttribute('data-status');
                var type   = card.getAttribute('data-type');
                var show   = filter === 'all'
                    || filter === status
                    || filter === type;

                card.style.display = show ? '' : 'none';
            });
        });
    });

});
</script>
@endpush
