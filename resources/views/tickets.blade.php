<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تذاكر الحضور</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
        }

        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: bold;
        }

        @page {
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #f7f3ec;
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #1f2937;
        }

        .page {
            padding: 28px 32px 36px;
        }

        .banner {
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 22px;
            background: linear-gradient(135deg, #c7a06b 0%, #6b4f2d 100%);
        }

        .banner img {
            display: block;
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .intro {
            text-align: center;
            margin-bottom: 18px;
        }

        .intro h1 {
            margin: 0;
            font-size: 24px;
            color: #111827;
        }

        .intro p {
            margin: 8px 0 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.8;
        }

        .ticket-card {
            background: #ffffff;
            border: 1px solid #eadfce;
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .ticket-strip {
            height: 10px;
            width: 100%;
        }

        .ticket-body {
            padding: 24px;
        }

        .label {
            display: inline-block;
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 14px;
            background: #f9f5ef;
            color: #8f6a3d;
            border: 1px solid #e8dccb;
        }

        .label.secondary {
            background: #f3f4f6;
            color: #6b7280;
            border-color: #e5e7eb;
        }

        .name {
            margin: 0;
            font-size: 23px;
            color: #111827;
        }

        .position {
            margin: 6px 0 0;
            font-size: 14px;
            color: #6b7280;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        .info-cell {
            width: 50%;
            vertical-align: top;
            padding: 0 4px;
        }

        .info-box {
            background: #fbfaf7;
            border: 1px dashed #dbc3a1;
            border-radius: 12px;
            padding: 12px 10px;
            text-align: center;
            min-height: 72px;
        }

        .info-title {
            font-size: 12px;
            color: #8b6a42;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 13px;
            color: #1f2937;
            line-height: 1.7;
        }

        .qr-wrap {
            display: inline-block;
            margin-top: 18px;
            padding: 14px;
            border-radius: 16px;
            border: 1px dashed #dbc3a1;
            background: #fbfaf7;
        }

        .qr-box {
            width: 170px;
            height: 170px;
        }

        .scan-text {
            margin: 12px 0 0;
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: .04em;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid #e8dccb;
            color: #8b909a;
            font-size: 10px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="banner">
        <img src="{{ public_path('top-banner.png') }}" alt="شعار الفعالية">
    </div>

    <div class="intro">
        <h1>تذاكر الدخول الخاصة بك جاهزة</h1>
        <p>يرجى إظهار رمز الاستجابة السريعة عند بوابة الدخول، وسيتم اعتماد التذكرة إلكترونيًا.</p>
    </div>

    @foreach ($tickets as $ticket)
        @php
            $isMainTicket = ($ticket['label'] ?? 'Main') === 'Main';
            $ticketLabel = $isMainTicket ? 'التذكرة الرئيسية' : 'تذكرة المرافق ' . $loop->iteration;
            $ticketStripColor = $isMainTicket ? '#c7a06b' : '#cbd5e1';
        @endphp

        <div class="ticket-card">
            <div class="ticket-strip" style="background-color: {{ $ticketStripColor }};"></div>

            <div class="ticket-body">
                <span class="label {{ $isMainTicket ? '' : 'secondary' }}">{{ $ticketLabel }}</span>

                @if($isMainTicket)
                    <h2 class="name">{{ $invitation->invitee_name }}</h2>

                    @if(!empty($invitation->invitee_position))
                        <p class="position">{{ $invitation->invitee_position }}</p>
                    @endif

                    <table class="info-table">
                        <tr>
                            <td class="info-cell">
                                <div class="info-box">
                                    <div class="info-title">التاريخ والوقت</div>
                                    <div class="info-value">
                                        {{ $event->date ?? '—' }}<br>
                                        {{ $event->from_time ?? '' }}
                                    </div>
                                </div>
                            </td>
                            <td class="info-cell">
                                <div class="info-box">
                                    <div class="info-title">الموقع</div>
                                    <div class="info-value">
                                        {{ \Illuminate\Support\Str::limit((string) ($event->address ?? ''), 42) ?: '—' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                @else
                    <h3 class="name" style="font-size: 18px; color: #6b7280; margin-top: 2px;">{{ $ticketLabel }}</h3>
                    <p class="position">بطاقة إضافية مرتبطة بنفس الدعوة</p>
                @endif

                <div style="text-align: center;">
                    <div class="qr-wrap" style="{{ $isMainTicket ? '' : 'border-color: #cbd5e1;' }}">
                        <img src="{{ $ticket['qr'] }}" class="qr-box" alt="رمز الاستجابة السريعة">
                    </div>
                    <p class="scan-text">يرجى مسح الرمز عند المدخل</p>
                </div>
            </div>
        </div>
    @endforeach

    <div class="footer">
        &copy; {{ date('Y') }} SAMI-AEC. جميع الحقوق محفوظة.
    </div>
</div>
</body>
</html>
