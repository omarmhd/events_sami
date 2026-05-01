<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation Confirmed</title>
    <style>
        body { margin: 0; padding: 0; background-color: #F8FAFC; font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; }
        img { border: 0; display: block; line-height: 100%; outline: none; text-decoration: none; }
        a { text-decoration: none; }

        .card-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 25px;
            border: 1px solid #EFE1D0;
        }

        .qr-frame {
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            border: 2px dashed #EFE1D0;
            display: inline-block;
        }

        @media only screen and (max-width: 600px) {
            .mobile-center { text-align: center !important; }
            img { max-width: 100% !important; height: auto !important; }
        }
    </style>
</head>
<body style="background-color: #F8FAFC; margin: 0; padding: 40px 0;">
@php
    $company = optional($event)->company;
    $branding = optional($company)->branding;

    $brandPrimary = trim((string) ($branding->primary_color ?? '')) ?: '#DABC9A';
    $brandSecondary = trim((string) ($branding->secondary_color ?? '')) ?: '#1F2937';
    $brandName = trim((string) ($branding->brand_name ?? '')) ?: (trim((string) ($company->name ?? '')) ?: 'Maan Platform');
    $logoUrl = trim((string) ($branding->logo_url ?? '')) ?: asset('Logo-SAMI.png');

    // Header image: use event-specific image if set, otherwise leave empty for fallback banner
    $headerImage = trim((string) ($event->header_image_path ?? ''));
    if ($headerImage === '') {
        $headerImage = trim((string) ($branding->header_image_url ?? ''));
    }

    // footer_image_path column was removed from the events table.
    $footerImage = '';

    $eventTitle = trim((string) ($event->title ?? '')) ?: (trim((string) ($event->name ?? '')) ?: 'Event');
    $eventDate = optional($event->date)->format('d M Y') ?: '-';
    $eventFrom = trim((string) ($event->from_time ?? '')) ?: '-';
    $eventTo = trim((string) ($event->to_time ?? '')) ?: '-';
    $eventMap = trim((string) ($event->google_map_url ?? '')) ?: '#';

    $customConfirmationBody = trim((string) ($event->confirmation_email_body ?? ''));

    $defaultIntroEn = 'Thank you for accepting our invitation. Enclosed are your admission tickets.';
    $defaultIntroAr = 'تشرفنا بقبولكم الدعوة. مرفق أدناه بطاقات الدخول الخاصة بكم.';

    $introEn = '';
    if ($customConfirmationBody === '') {
        $introEn = trim((string) ($event->description_en ?? ''));
    }
    if ($introEn === '') {
        $introEn = $defaultIntroEn;
    }

    $introAr = '';
    if ($customConfirmationBody === '') {
        $introAr = trim((string) ($event->description ?? ''));
    }
    if ($introAr === '') {
        $introAr = $defaultIntroAr;
    }

    $inlineImageSrc = function (string $src, string $name) {
        if ($src === '' || !isset($message)) {
            return $src;
        }

        if (str_starts_with($src, 'data:image/')) {
            $parts = explode(',', $src, 2);
            if (count($parts) === 2 && $parts[1] !== '') {
                $mime = str_contains($parts[0], 'image/jpeg') ? 'image/jpeg' : 'image/png';
                $binary = base64_decode($parts[1], true);
                if ($binary !== false) {
                    return $message->embedData($binary, $name, $mime);
                }
            }

            return $src;
        }

        $path = parse_url($src, PHP_URL_PATH) ?: '';
        if ($path !== '') {
            $abs = public_path(ltrim($path, '/'));
            if (is_file($abs)) {
                return $message->embed($abs);
            }
        }

        return $src;
    };

    $headerDisplaySrc = $inlineImageSrc($headerImage, 'tickets-header.png');
    $logoDisplaySrc = $inlineImageSrc($logoUrl, 'tickets-logo.png');
@endphp

<center>
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="max-width: 600px;">

        <tr>
            <td align="center" style="padding-bottom: 30px;">

                @if($headerImage !== '')
                {{-- Show event/brand header image when available --}}
                <div style="margin-bottom: 30px; border-radius: 0 0 20px 20px; overflow: hidden; border-bottom: 4px solid {{ $brandPrimary }};">
                    <img src="{{ $headerDisplaySrc }}" alt="Event Banner" style="width: 100%; max-width: 600px; display: block;">
                </div>
                @else
                {{-- Fallback: decorative gradient banner when no image is set --}}
                <div style="margin-bottom: 30px; border-radius: 0 0 20px 20px; overflow: hidden; border-bottom: 4px solid {{ $brandPrimary }}; background: linear-gradient(135deg, {{ $brandPrimary }}, {{ $brandSecondary }}); height: 100px; display: flex; align-items: center; justify-content: center;">
                    <p style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff; padding: 0 20px; text-align: center;">
                        {{ $eventTitle }}
                    </p>
                </div>
                @endif

                <div style="margin-bottom: 12px;">
                    <img src="{{ $logoDisplaySrc }}" alt="{{ $brandName }}" style="max-width: 180px; max-height: 44px; margin: 0 auto; object-fit: contain;">
                </div>

                <div style="margin-bottom: 20px; padding: 0 15px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.4; font-family: 'Segoe UI', Tahoma, sans-serif;">
                        {{ $eventTitle }}
                    </h2>
                </div>

                <div style="width: 80px; height: 4px; background-color: {{ $brandPrimary }}; margin: 0 auto 30px auto; border-radius: 2px;"></div>

                @if($customConfirmationBody !== '')
                    <p style="margin: 0; font-size: 15px; line-height: 1.8; max-width: 90%; color: #64748b;">{!! nl2br(e($customConfirmationBody)) !!}</p>
                @else
                    <p style="margin: 0; font-size: 15px; line-height: 1.6; max-width: 90%; color: #64748b;">
                        {{ $introEn }}
                        <br>
                        <span style="color: {{ $brandPrimary }}; font-size: 16px; font-weight: 600; font-family: Tahoma, sans-serif;">{{ $introAr }}</span>
                    </p>
                @endif
            </td>
        </tr>

        <tr>
            <td>
                @foreach ($tickets as $ticket)

                    @if(($ticket['label'] ?? 'Main') === 'Main')
                        <div class="card-container">
                            <div style="background-color: {{ $brandPrimary }}; height: 10px; width: 100%;"></div>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 35px 20px;">

                                        <span style="background-color: {{ $brandPrimary }}; color: #ffffff; padding: 8px 18px; border-radius: 50px; font-size: 12px; font-weight: bold; letter-spacing: 1px; border: 1px solid {{ $brandPrimary }}; text-transform: uppercase;">
                                         Main Guest
                                        </span>

                                        <h2 style="margin: 20px 0 5px 0; color: #1e293b; font-size: 24px; font-family: 'Segoe UI', Tahoma, sans-serif;">{{ $invitation->invitee_name }}</h2>
                                        @if(!empty($invitation->invitee_position))
                                            <p style="margin: 0 0 25px 0; color: #64748b; font-size: 14px;">{{ $invitation->invitee_position }}</p>
                                        @endif

                                        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 25px 0; background-color: #FAF7F2; border-radius: 12px; border: 1px solid {{ $brandPrimary }}; max-width: 400px;">
                                            <tr>
                                                <td align="center" style="padding: 15px; border-right: 1px solid {{ $brandPrimary }}; width: 50%;">
                                                    <div style="font-size: 20px;">📅</div>
                                                    <div style="font-size: 13px; color: #1e293b; font-weight: bold; margin-top: 5px;">{{ $eventDate }}</div>
                                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">{{ $eventFrom }} - {{ $eventTo }}</div>
                                                </td>
                                                <td align="center" style="padding: 15px; width: 50%;">
                                                    <div style="font-size: 20px;">📍</div>
                                                    <div style="font-size: 13px; color: #1e293b; font-weight: bold; margin-top: 5px;">Location</div>
                                                    <div style="font-size: 12px; margin-top: 2px;">
                                                        <a href="{{ $eventMap }}" style="color: {{ $brandPrimary }}; text-decoration: none; font-weight: 600;">View Map →</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div class="qr-frame">
                                            @php
                                                $qrSrc = (string) ($ticket['qr'] ?? '');
                                                if (str_starts_with($qrSrc, 'data:image/') && isset($message)) {
                                                    $parts = explode(',', $qrSrc, 2);
                                                    if (count($parts) === 2 && $parts[1] !== '') {
                                                        $mime = str_contains($parts[0], 'image/jpeg') ? 'image/jpeg' : 'image/png';
                                                        $binary = base64_decode($parts[1], true);
                                                        if ($binary !== false) {
                                                            $qrSrc = $message->embedData($binary, 'ticket-main-' . ($loop->index + 1) . '.png', $mime);
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $qrSrc }}"
                                                 width="180" height="180" alt="Entrance QR Code" style="display: block;">
                                        </div>
                                        <p style="margin: 15px 0 0 0; font-size: 11px; color: #94a3b8; letter-spacing: 1px; font-weight: 600;">PLEASE SCAN AT ENTRANCE</p>

                                    </td>
                                </tr>
                            </table>
                        </div>

                    @else
                        <div class="card-container" style="border-top: 0; max-width: 480px; margin-left: auto; margin-right: auto;">
                            <div style="background-color: {{ $brandPrimary }}; height: 6px; width: 100%;"></div>

                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 25px 20px;">

                                        <h3 style="margin: 0 0 15px 0; color: #64748b; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">
                                            <span style="color: {{ $brandPrimary }};">Guest Ticket {{ $loop->iteration }}</span> / مرافق
                                        </h3>

                                        <div class="qr-frame" style="border-color: {{ $brandPrimary }}; border-style: solid; padding: 10px;">
                                            @php
                                                $qrSrc = (string) ($ticket['qr'] ?? '');
                                                if (str_starts_with($qrSrc, 'data:image/') && isset($message)) {
                                                    $parts = explode(',', $qrSrc, 2);
                                                    if (count($parts) === 2 && $parts[1] !== '') {
                                                        $mime = str_contains($parts[0], 'image/jpeg') ? 'image/jpeg' : 'image/png';
                                                        $binary = base64_decode($parts[1], true);
                                                        if ($binary !== false) {
                                                            $qrSrc = $message->embedData($binary, 'ticket-guest-' . ($loop->index + 1) . '.png', $mime);
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <img src="{{ $qrSrc }}"
                                                 width="140" height="140" alt="Guest QR Code" style="display: block;">
                                        </div>

                                        <p style="margin: 10px 0 0 0; font-size: 12px; color: #94a3b8;">Guest # {{ $loop->iteration }}</p>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif

                @endforeach
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 30px 15px 40px 15px; border-top: 1px solid {{ $brandPrimary }}; margin-top: 20px;">

                @if($footerImage !== '')
                    <img src="{{ $footerImage }}" alt="Event Footer" style="display:block;max-width:100%;height:auto;margin:0 auto 18px auto;">
                @endif

                <p style="margin: 0; font-size: 13px; color: #64748b; font-weight: 600;">
                    This invitation was sent via {{ $brandName }} Event Management Platform
                </p>
                <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b; font-weight: 600;" dir="rtl">
                    تم إرسال هذه الدعوة عبر منصة <span style="color:{{ $brandPrimary }};">معا</span> لإدارة الفعاليات
                </p>

                <p style="margin: 10px 0 0 0; font-size: 11px; color: #94a3b8; line-height: 1.5;">
                    &copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.<br>
                    <span dir="rtl">جميع الحقوق محفوظة لدى منصة معا</span>
                </p>
            </td>
        </tr>

    </table>
</center>
</body>
</html>
