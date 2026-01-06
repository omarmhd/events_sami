<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .content {
            padding: 30px;
        }
        .ticket-card {
            border: 2px dashed #46AA32; /* لون مطابق لثيم الموقع */
            background-color: #f9fff9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .ticket-header h3 {
            margin: 0;
            color: #2E8B57;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 140px;
        }
        .qr-container {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: #fff;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-main { background-color: #2E8B57; }
        .badge-guest { background-color: #17a2b8; }
        .badge-child { background-color: #ffc107; color: #333; }
    </style>
</head>
<body>

<div class="email-container">
    <img src="{{asset('assets/header.jpg')}}" class="header-image" alt="SAMI-AEC Event Header">

    <div class="content">
        <h1>Hello, {{ $tickets[0]->employee_name }}</h1>

        <p style="font-size: 16px; line-height: 1.5;">
            Thank you for registering for the event: <br>
            <strong>{{ $event->title ?? 'SAMI-AEC 2026 Picnic' }}</strong>
        </p>

        <p>Here is your admission ticket. Please present the QR code at the entrance.</p>

        @foreach ($tickets as $ticket)
            <div class="ticket-card">
                <div class="ticket-header">
                    <h3>ADMISSION TICKET</h3>
                    <small>Ticket #{{ $ticket->id }}</small>
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label">Attendee Name:</td>
                        <td><strong>{{ $ticket->employee_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Ticket Type:</td>
                        <td>
                            @if($ticket->type === 'employee')
                                <span class="badge badge-main">Main Applicant</span>
                            @else
                                <span class="badge badge-guest">Guest</span>
                                @endif

                        </td>
                    </tr>
                    <tr>
                        <td class="label">Position:</td>
                        <td>{{ $ticket->position ?? 'N/A' }}</td>
                    </tr>

                    <tr>
                        <td class="label">Event:</td>
                        <td>{{ $event->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date & Time:</td>
                        <td>
                            {{ $event->date }} <br>
                            {{ $event->from_time }} - {{ $event->to_time }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Location:</td>
                        <td>{{ $event->address }}</td>
                    </tr>
                </table>

                <div class="qr-container">
                    <p style="margin-bottom: 10px; font-size: 14px; color: #666;">Scan at gate / امسح الكود عند الدخول</p>
                    @if($ticket->barcode && \Illuminate\Support\Facades\Storage::disk('public')->exists("qr_codess/".$ticket->barcode))
                        <img width="180" height="180"
                             src="{{ $message->embed(\Illuminate\Support\Facades\Storage::disk('public')->path("qr_codess/".$ticket->barcode)) }}"
                             alt="QR Code">
                    @else
                        <p style="color: red;">QR Code not available</p>
                    @endif
                </div>
            </div>
        @endforeach

        <p>We look forward to seeing you!</p>
        <p>نتطلع لرؤيتكم!</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} SAMI-AEC. All rights reserved.
    </div>
</div>

</body>
</html>
