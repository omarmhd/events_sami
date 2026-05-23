{{--
    Platform Logo Component
    ────────────────────────────────────────────────────────────────
    Renders the PLATFORM logo — always sourced from the admin panel
    (SystemSetting::get('platform_logo_url') / 'platform_name').

    This is intentionally NOT tenant-specific. The platform admin
    controls the logo shown in the app UI (auth pages, sidebar, mobile
    header, onboarding). Tenant branding (CompanyBranding) only affects
    their outgoing email templates, NOT the platform UI chrome.

    Props:
      $size   — 'sm' | 'md' (default) | 'lg'
      $theme  — 'light' (default, on white bg) | 'dark' (on coloured bg)
      $class  — extra CSS classes for the wrapper element
--}}
@php
    // Always read from platform-level admin settings (SystemSetting).
    $logoUrl  = \App\Models\SystemSetting::get('platform_logo_url', '');
    $logoName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));

    // Size mapping
    $sizeMap = [
        'sm' => ['img' => 'logo-img--sm', 'text' => 'logo-text--sm'],
        'md' => ['img' => 'logo-img--md', 'text' => 'logo-text--md'],
        'lg' => ['img' => 'logo-img--lg', 'text' => 'logo-text--lg'],
    ];
    $sizes    = $sizeMap[$size ?? 'md'] ?? $sizeMap['md'];
    $themeVal = $theme ?? 'light';
    $extraCls = $class ?? '';
@endphp

<div class="platform-logo-wrap {{ $extraCls }}" aria-label="{{ $logoName }}">
    @if(!empty($logoUrl))
        {{-- ── Image logo ── --}}
        <span class="platform-logo-accent" aria-hidden="true"></span>
        <img
            src="{{ $logoUrl }}"
            alt="{{ $logoName }}"
            class="platform-logo-img {{ $sizes['img'] }}"
            loading="lazy"
        >
    @else
        {{-- ── Text logo fallback ── --}}
        @php
            // Split the name into two halves for two-tone styling
            $words = explode(' ', trim($logoName));
            $half  = (int) ceil(count($words) / 2);
            $part1 = implode(' ', array_slice($words, 0, $half));
            $part2 = implode(' ', array_slice($words, $half));
        @endphp
        @php
            $initials = collect(explode(' ', trim($logoName)))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
        @endphp
        <span class="platform-logo-initial" aria-hidden="true">{{ $initials }}</span>
        <span class="platform-logo-text {{ $sizes['text'] }} logo-theme--{{ $themeVal }}">
            {{ $part1 }}<span class="logo-accent">{{ $part2 ? ' ' . $part2 : '' }}</span>
        </span>
    @endif
</div>

{{-- Scoped styles (browser deduplicates identical <style> blocks) --}}
<style>
/* ── Platform Logo Component ─────────────────────────────────── */
.platform-logo-wrap {
    display: inline-flex;
    align-items: center;
    line-height: 1;
    gap: .5rem;
}

/* Image variant */
.platform-logo-accent { display: none; }
.platform-logo-img { object-fit: contain; display: block; border-radius: 6px; background: transparent; }
.platform-logo-img.logo-img--sm { height: 28px; max-width: 110px; }
.platform-logo-img.logo-img--md { height: 36px; max-width: 150px; }
.platform-logo-img.logo-img--lg { height: 48px; max-width: 200px; }

.platform-logo-initial { display: none; }


/* Text variant */
.platform-logo-text {
    font-family: 'Cairo', 'Outfit', sans-serif;
    font-weight: 800;
    letter-spacing: -0.01em;
    white-space: nowrap;
}
.platform-logo-text.logo-text--sm { font-size: 1rem; }
.platform-logo-text.logo-text--md { font-size: 1.25rem; }
.platform-logo-text.logo-text--lg { font-size: 1.65rem; }

/* Light theme (on white / light backgrounds) */
.platform-logo-text.logo-theme--light {
    background: linear-gradient(135deg, #0f8f83, #0a6b62);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.platform-logo-text.logo-theme--light .logo-accent {
    opacity: 0.72;
}

/* Dark theme (on coloured / dark backgrounds) */
.platform-logo-text.logo-theme--dark {
    color: #ffffff;
    -webkit-text-fill-color: #ffffff;
}
.platform-logo-text.logo-theme--dark .logo-accent {
    color: #ffd089;
    -webkit-text-fill-color: #ffd089;
}

/* Compact / simple variants: keep visuals minimal (no borders/shadows) */
.platform-logo-accent { display: none !important; }
.platform-logo-img { box-shadow: none !important; border: none !important; border-radius: 0 !important; background: transparent !important; }
.platform-logo-initial { display: inline-block; font-weight: 800; color: var(--text-main, #0b3f3a); }
.platform-logo-text { margin-left: .25rem; }
</style>
