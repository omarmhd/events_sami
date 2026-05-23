<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f3f8f6;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#183532;">
@php
    use App\Models\SystemSetting;

    $company = optional($event)->company ?: optional($invitation)->company;
    $branding = optional($company)->branding;

    $customInviteBody = $event->invitation_email_body ?? null;
    $headerImage = trim((string) ($event->header_image_path ?? ''));
    // footer_image_path column was removed from the events table.
    $footerImage = '';

    // Company branding first, then platform fallback
    $platformName    = trim((string) ($branding->brand_name ?? ''))
        ?: trim((string) ($company->name ?? ''))
        ?: SystemSetting::get('platform_name', config('app.name', 'Platform'));
    $platformLogoUrl = trim((string) ($branding->logo_url ?? ''))
        ?: SystemSetting::get('platform_logo_url', '');
    $primaryColor    = trim((string) ($branding->primary_color ?? ''))
        ?: SystemSetting::get('primary_color', '#0f8f83');
    $secondaryColor  = trim((string) ($branding->secondary_color ?? ''))
        ?: SystemSetting::get('secondary_color', '#1F2937');

    if ($headerImage === '') {
        $headerImage = trim((string) ($branding->header_image_url ?? ''));
    }

    /**
     * Resolve an image source for use inside an email.
     *
     * Strategy (in order):
     *  1. Already a data: URI — use as-is (already embedded).
     *  2. Local file path (/uploads/... or storage/...) — embed as base64 data URI
     *     so the image works even on localhost / behind a firewall where external
     *     mail servers cannot fetch the URL.
     *  3. External http(s) URL — use the URL directly (works on production).
     *  4. File not found / empty — return '' so no <img> tag is rendered.
     *
     * Returns '' when no valid image is resolvable.
     */
    $resolveImageSrc = function (string $src): string {
        $src = trim($src);
        if ($src === '') {
            return '';
        }

        // Already a base64 data URI — pass through unchanged.
        if (str_starts_with($src, 'data:image/')) {
            return $src;
        }

        // Derive the absolute filesystem path for local paths like /uploads/...
        // The path stored in the DB is always a public-relative URL (e.g. /uploads/event-images/headers/abc.jpg).
        $urlPath = parse_url($src, PHP_URL_PATH) ?: '';
        if ($urlPath !== '') {
            $absPath = public_path(ltrim($urlPath, '/'));
            if (is_file($absPath)) {
                // Use the public URL for event banners so the email matches the
                // core template behavior and renders consistently in mail clients.
                return asset(ltrim($urlPath, '/'));
            }
        }

        // External https URL (e.g. branding logo stored on a CDN) — use directly.
        // These work on production; on localhost they may not load in the email client
        // but at least won't show a broken-image placeholder.
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            return $src;
        }

        // Cannot resolve — suppress the image entirely.
        return '';
    };

    // Event header image takes priority over company branding header image.
    // $headerImage was already set above: event path first, branding fallback second.
    $headerDisplaySrc = $resolveImageSrc($headerImage);
    $logoDisplaySrc   = $resolveImageSrc($platformLogoUrl);

    // Boolean flags used in the template for clean conditionals.
    $headerImageValid = $headerDisplaySrc !== '';
    $logoValid        = $logoDisplaySrc !== '';
@endphp
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #ddeae6;border-radius:16px;overflow:hidden;">
    @if($headerImageValid)
    {{-- Event header image — only rendered when the file is confirmed to exist --}}
    <tr>
        <td style="padding:0;line-height:0;font-size:0;">
            <img src="{{ $headerDisplaySrc }}"
                 alt="{{ $event->title ?: $event->name }}"
                 style="width:100%;max-width:680px;display:block;border:0;">
        </td>
    </tr>
    @else
    {{-- No header image: full-width gradient banner centred on event title --}}
    <tr>
        <td style="padding:48px 32px;background:linear-gradient(135deg,{{ $primaryColor }} 0%,#0d6e64 100%);text-align:center;">
            <p style="margin:0 0 10px 0;font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.12em;">
                Event Invitation
            </p>
            <p style="margin:0;font-size:26px;font-weight:700;color:#ffffff;line-height:1.3;letter-spacing:.01em;">
                {{ $event->title ?: $event->name }}
            </p>
        </td>
    </tr>
    @endif

    <tr>
        <td style="padding:28px 24px 18px;border-top:4px solid {{ $primaryColor }};">
            <p style="margin:0 0 8px 0;font-size:13px;color:#5f7a76;text-transform:uppercase;letter-spacing:.06em;">Event Invitation</p>
            <h1 style="margin:0 0 8px 0;font-size:24px;line-height:1.35;color:#102a2a;">{{ $event->title ?: $event->name }}</h1>
            <p style="margin:0;font-size:16px;color:#334f4b;">Dear {{ $invitation->invitee_name }},</p>
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 8px;">
            @if(!empty($customInviteBody))
                <p style="margin:0 0 14px 0;line-height:1.85;color:#395a56;">{!! nl2br(e($customInviteBody)) !!}</p>
            @elseif(!empty($event->description_en))
                <p style="margin:0 0 12px 0;line-height:1.75;color:#395a56;">{{ $event->description_en }}</p>
            @endif

            @if(!empty($event->description))
                <p style="margin:0 0 14px 0;line-height:1.85;color:{{ $primaryColor }};font-weight:600;" dir="rtl">{{ $event->description }}</p>
            @endif
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 4px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6fbfa;border:1px solid #e2eeea;border-radius:12px;">
                <tr>
                    <td style="padding:12px 14px;font-size:14px;color:#3b5a56;">
                        <strong>Date:</strong> {{ optional($event->date)->format('Y-m-d') ?: '-' }}<br>
                        <strong>Time:</strong> {{ $event->from_time ?: '-' }} - {{ $event->to_time ?: '-' }}<br>
                        <strong>Location:</strong> {{ $event->location_name ?: ($event->address ?? '-') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:24px;">
            <a href="{{ $invitationLink }}" style="display:inline-block;padding:14px 26px;border-radius:10px;background:{{ $primaryColor }};color:#ffffff;text-decoration:none;font-weight:700;">
                Respond to Invitation
            </a>
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 24px;">
            <p style="margin:0;font-size:12px;line-height:1.7;color:#6b8480;word-break:break-all;">
                If the button does not work, use this link:<br>
                <a href="{{ $invitationLink }}" style="color:{{ $primaryColor }};">{{ $invitationLink }}</a>
            </p>
        </td>
    </tr>

    <tr>
        <td style="padding:20px 24px 24px;background:#f8fcfb;border-top:1px solid #e5efec;text-align:center;">
            @if(!empty($footerImage))
                <img src="{{ $footerImage }}" alt="Event Footer" style="display:block;max-width:100%;height:auto;margin:0 auto 14px auto;">
            @endif

            {{-- Platform logo / brand in footer --}}
            {{-- Logo validity is checked server-side so no broken images reach the email client --}}
            <div style="margin-bottom:10px;">
                @if($logoValid)
                    <img src="{{ $logoDisplaySrc }}"
                         alt="{{ $platformName }}"
                         style="height:28px;max-width:140px;object-fit:contain;display:inline-block;">
                @else
                    <span style="font-family:'Segoe UI',Tahoma,Arial,sans-serif;font-size:15px;font-weight:800;color:{{ $primaryColor }};letter-spacing:-.01em;">
                        {{ $platformName }}
                    </span>
                @endif
            </div>

            <p style="margin:0;font-size:11px;color:{{ $secondaryColor }};line-height:1.6;">
                &copy; {{ date('Y') }} {{ $platformName }}. All rights reserved.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
