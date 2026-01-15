<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation Response</title>
    <style>
        /* Basics */
        body { margin: 0; padding: 0; background-color: #FDFBF7; font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; -webkit-font-smoothing: antialiased; color: #333333; }
        table { border-collapse: collapse; }
        a { text-decoration: none; }

        /* Colors */
        .text-gold { color: #C5A065; }
        .text-gray { color: #718096; }

        /* Button Style */
        .btn-gold {
            background-color: #C5A065;
            color: #ffffff !important;
            padding: 14px 30px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(197, 160, 101, 0.3);
            transition: all 0.3s ease;
            text-align: center;
        }

        /* Card */
        .main-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-top: 5px solid #C5A065;
            overflow: hidden;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body style="margin: 0; padding: 40px 0; background-color: #FDFBF7;">

<center>

    <div class="main-card">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding: 40px 30px; text-align: center;">

                    <div style="max-width: 600px; margin: 0 auto; text-align: center; padding: 20px;">

                        <div style="margin-bottom: 35px;">
                            <img src="{{asset("Logo-SAMI.png")}}" alt="Logo-SAMI.png" style="width: 200px">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <h2 style="margin: 0 0 5px 0; font-size: 26px; font-weight: 800; color: #2D3748; letter-spacing: -0.5px; line-height: 1.2;">
                                {{$event->title_en}}
                            </h2>
                            <h2 style="margin: 0; font-size: 22px; font-weight: 600; color: #4A5568; line-height: 1.4; font-family: 'Segoe UI', Tahoma, sans-serif;">
                                {{$event->title}}
                            </h2>
                        </div>

                        <div style="width: 50px; height: 3px; background-color: #C5A065; margin: 0 auto 30px auto; border-radius: 2px;"></div>

                        <div style="margin-bottom: 15px;" dir="ltr">
                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #718096;">
                                {{$event->description_en}}
                            </p>
                        </div>

                        <div style="margin-bottom: 10px;" dir="rtl">
                            <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #C5A065; font-weight: 500;">
                                {{$event->description}}
                            </p>
                        </div>

                    </div>

                    <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #4A5568;">
                        Please click the button below to <strong>accept</strong> or <strong>decline</strong> the invitation.
                        <br>
                        <span style="color: #C5A065; font-size: 14px;">نرجو النقر على الزر أدناه للموافقة على الدعوة أو الاعتذار.</span>
                    </p>

                    <div style="margin-bottom: 30px;">
                        <a href="{{ $invitationLink }}" class="btn-gold">
                            Respond to Invitation
                            <br><span style="font-weight: normal; font-size: 13px;">الرد على الدعوة</span>
                        </a>
                        <p>If you faced any issues kindly contact Abdulrahman Al Henaki: +966551858731</p>
                    </div>

                    <p style="margin: 0; font-size: 12px; color: #CBD5E0;">
                        If the button doesn't work, please use this link:<br>
                        <a href="{{ $invitationLink }}" style="color: #A0AEC0; text-decoration: underline; word-break: break-all;">
                            {{ $invitationLink }}
                        </a>
                    </p>

                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 25px; color: #A0AEC0; font-size: 12px;">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>

</center>
</body>
</html>
