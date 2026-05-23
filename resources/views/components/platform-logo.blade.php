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

/* Sidebar / compact variants — these are enabled when the component is
   used with an extra class like `sidebar-platform-logo` or `mobile-platform-logo` */
.sidebar-platform-logo .platform-logo-accent,
.mobile-platform-logo .platform-logo-accent {
    display: block;
    width: 6px;
    height: 36px;
    border-radius: 6px;
    background: var(--primary-color, #0f8f83);
}
.sidebar-platform-logo .platform-logo-accent { height: 42px; }
.sidebar-platform-logo .platform-logo-img,
.mobile-platform-logo .platform-logo-img { box-shadow: none; border: none; }
.sidebar-platform-logo .platform-logo-img { max-height: 42px; }
.sidebar-platform-logo .platform-logo-initial,
.mobile-platform-logo .platform-logo-initial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(15,143,131,.12), rgba(15,143,131,.06));
    color: var(--primary-color, #0f8f83);
    font-weight: 800;
}
.sidebar-platform-logo .platform-logo-text { font-size: 1rem; }
</style>
