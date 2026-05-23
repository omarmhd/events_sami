<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دعوة حضور فعالية</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f3f6f4;font-family:'Segoe UI',Tahoma,Arial,sans-serif;color:#183532;direction:rtl;">
@php
    use App\Models\SystemSetting;

    $company = optional($event)->company ?: optional($invitation)->company;
    $branding = optional($company)->branding;

    $customInviteBody = $event->invitation_email_body ?? null;
    $headerImage = trim((string) ($event->header_image_path ?? ''));
    // footer_image_path column was removed from the events table.
    $footerImage = '';

    // Company branding first, then platform fallback
    $compiled = $email_vars ?? [];

    $platformName    = trim((string) ($compiled['brand_name'] ?? $branding->brand_name ?? ''))
        ?: trim((string) ($company->name ?? ''))
        ?: SystemSetting::get('platform_name', config('app.name', 'Platform'));
    $platformLogoUrl = trim((string) ($compiled['logo_url'] ?? $branding->logo_url ?? ''))
        ?: SystemSetting::get('platform_logo_url', '');
    $primaryColor    = trim((string) ($compiled['primary_color'] ?? $branding->primary_color ?? ''))
        ?: SystemSetting::get('primary_color', '#0f8f83');
    $secondaryColor  = trim((string) ($compiled['secondary_color'] ?? $branding->secondary_color ?? ''))
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
    {{-- No header image: full-width gradient banner centred on event title (Arabic) --}}
    <tr>
        <td style="padding:40px 24px;background:linear-gradient(135deg,{{ $primaryColor }} 0%,{{ $secondaryColor }} 100%);text-align:center;">
            <p style="margin:0 0 8px 0;font-size:12px;font-weight:700;color:rgba(255,255,255,0.85);letter-spacing:.06em;">
                دعوة لحضور فعالية
            </p>
            <p style="margin:8px 0 0 0;font-size:26px;font-weight:800;color:#ffffff;line-height:1.2;">{{ $event->title ?: $event->name }}</p>
        </td>
    </tr>
    @endif

    <tr>
        <td style="padding:28px 24px 18px;border-top:4px solid {{ $primaryColor }};">
            <p style="margin:0 0 8px 0;font-size:12px;color:#5f7a76;letter-spacing:.08em;">دعوة حضور فعالية</p>
            <h1 style="margin:0 0 8px 0;font-size:24px;line-height:1.35;color:#102a2a;">{{ $event->title ?: $event->name }}</h1>
            <p style="margin:0;font-size:16px;color:#334f4b;">مرحباً {{ $invitation->invitee_name }}،</p>
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 8px;">
            @if(!empty($customInviteBody))
                <p style="margin:0 0 14px 0;line-height:1.85;color:#395a56;">{!! nl2br(e($customInviteBody)) !!}</p>
            @elseif(!empty($event->description))
                <p style="margin:0 0 14px 0;line-height:1.85;color:#395a56;">{{ $event->description }}</p>
            @else
                <p style="margin:0 0 14px 0;line-height:1.85;color:#395a56;">يسرّنا دعوتكم لحضور هذه الفعالية. نرجو تأكيد الحضور عبر الزر أدناه.</p>
            @endif
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 4px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6fbfa;border:1px solid #e2eeea;border-radius:12px;">
                <tr>
                    <td style="padding:12px 14px;font-size:14px;color:#3b5a56;line-height:1.8;">
                        <strong>التاريخ:</strong> {{ optional($event->date)->format('Y-m-d') ?: '-' }}<br>
                        <strong>الوقت:</strong> {{ $event->from_time ?: '-' }} - {{ $event->to_time ?: '-' }}<br>
                        <strong>الموقع:</strong> {{ $event->location_name ?: ($event->address ?? '-') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:24px;">
            <a href="{{ $invitationLink }}" style="display:inline-block;padding:13px 26px;border-radius:12px;background:{{ $primaryColor }};color:#ffffff;text-decoration:none;font-weight:700;letter-spacing:.02em;">
                تأكيد أو تعديل الرد
            </a>
        </td>
    </tr>

    <tr>
        <td style="padding:0 24px 24px;">
            <p style="margin:0;font-size:12px;line-height:1.7;color:#6b8480;word-break:break-all;text-align:center;">
                في حال لم يعمل الزر، استخدم الرابط التالي:<br>
                <a href="{{ $invitationLink }}" style="color:{{ $primaryColor }};" dir="ltr">{{ $invitationLink }}</a>
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
                &copy; {{ date('Y') }} {{ $platformName }}. جميع الحقوق محفوظة.
            </p>
        </td>
    </tr>
</table>
</body>
</html>
