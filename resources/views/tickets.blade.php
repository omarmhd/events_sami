<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tickets PDF</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #FDFBF7;
            margin: 0;
            padding: 0;
            text-align: right;
            color: #2D3748;
        }

        @page { margin: 0px; padding: 0px; }

        .container { padding: 40px; max-width: 700px; margin: 0 auto; }

        /* تنسيق الأيقونات البديلة */
        .icon-img {
            width: 16px;
            height: 16px;
            vertical-align: middle;
            margin-left: 5px;
        }

        /* --- باقي التنسيقات --- */
        .header-banner { width: 100%; height: 200px; background-color: #eee; margin-bottom: 30px; }
        .header-banner img { width: 100%; height: 100%; object-fit: cover; }

        .ticket-card {
            background-color: #ffffff;
            border: 1px solid #F0EAE0;
            border-radius: 15px;
            margin-bottom: 30px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .card-top-strip { height: 8px; width: 100%; }
        .card-body { padding: 30px; text-align: center; }

        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-box {
            background-color: #FAFAFA;
            border: 1px dashed #DCC8A8;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .qr-container {
            display: inline-block;
            padding: 15px;
            background-color: #FAFAFA;
            border: 1px dashed #DCC8A8;
            border-radius: 10px;
            margin-top: 10px;
        }
        .qr-img { width: 150px; height: 150px; }

        h1, h2, h3 { margin: 0; color: #2D3748; }
        p { margin: 5px 0; color: #718096; font-size: 14px; }

        .badge {
            background-color: #FEF9EF; color: #C5A065; padding: 5px 15px;
            border-radius: 20px; border: 1px solid #F0EAE0; font-size: 12px;
            text-transform: uppercase; display: inline-block; margin-bottom: 10px;
        }
        .footer { text-align: center; font-size: 10px; color: #aaa; margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>

<div class="header-banner">
    <img src="{{ public_path('top-banner.png') }}" alt="Banner">
</div>

<div class="container">

    <div style="text-align: center; margin-bottom: 40px;">
        <h2>Thank you for accepting.</h2>
           <h3> Your tickets are below.</h3>
    </div>

    @foreach ($tickets as $ticket)
        <div class="ticket-card">
            <div class="card-top-strip" style="background-color: {{ $ticket['label'] === 'Main' ? '#C5A065' : '#E2E8F0' }};"></div>

            <div class="card-body">
                <span class="badge" style="{{ $ticket['label'] !== 'Main' ? 'color: #718096; background: #F7FAFC;' : '' }}">
                    {{ $ticket['label'] === 'Main' ? 'Main Ticket' : 'Guest Ticket' }}
                </span>

                @if($ticket['label'] === 'Main')
                    <h2 style="font-size: 24px; margin: 10px 0;">{{ $invitation->invitee_name }}</h2>
                    <p>{{ $invitation->invitee_position }}</p>

                    <table class="info-table" style="margin-top: 20px;">
                        <tr>
                            <td width="50%" style="padding: 5px;">
                                <div class="info-box">

                                    <div style="font-weight: bold; color: #333; margin-top: 5px;">{{ $event->date }}</div>
                                    <div style="font-size: 12px;">{{ $event->from_time }}</div>
                                </div>
                            </td>
                            <td width="50%" style="padding: 5px;">
                                <div class="info-box">
                                    <div style="font-weight: bold; color: #333; margin-top: 5px;">Location</div>
                                    <div style="font-size: 12px;">{{ Str::limit($event->address, 20) }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                @else
                    <h3 style="margin: 15px 0; color: #718096;">Guest Ticket #{{ $loop->iteration }}</h3>
                @endif

                <div class="qr-container" style="{{ $ticket['label'] !== 'Main' ? 'border-color: #E2E8F0;' : '' }}">
                    <img src="{{ $ticket['qr'] }}" class="qr-img" alt="QR Code">
                </div>

                <p style="font-size: 10px; margin-top: 10px; letter-spacing: 1px;">PLEASE SCAN AT ENTRANCE</p>
            </div>
        </div>
    @endforeach

    <div class="footer">
        &copy; {{ date('Y') }} SAMI-AEC. All rights reserved.
    </div>
</div>
</body>
</html>
