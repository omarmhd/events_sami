@php
    use App\Models\SystemSetting;
    $platformName    = SystemSetting::get('platform_name', config('app.name', 'Platform'));
    $platformLogoUrl = SystemSetting::get('platform_logo_url', '');
    $primaryColor    = SystemSetting::get('primary_color', '#0f8f83');
@endphp
<!doctype html>
<html lang="en">
<body style="font-family:'Segoe UI',Arial,sans-serif;background:#f3f8f6;padding:24px 12px;margin:0;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0"
       style="max-width:520px;margin:0 auto;background:#ffffff;border:1px solid #ddeae6;border-radius:16px;overflow:hidden;">

    {{-- Header with brand --}}
    <tr>
        <td style="padding:20px 24px 16px;background:linear-gradient(135deg,{{ $primaryColor }},{{ $primaryColor }}cc);text-align:center;">
           <span style="font-family:'Segoe UI',Arial,sans-serif;font-size:18px;font-weight:800;color:#fff;letter-spacing:-.01em;">
                    {{ $platformName }}
                </span>
        </td>
    </tr>

    {{-- Content --}}
    <tr>
        <td style="padding:28px 24px 8px;">
            <h2 style="margin:0 0 8px 0;font-size:18px;font-weight:700;color:#102a2a;">Verification Code</h2>
            <p style="margin:0 0 20px 0;font-size:14px;color:#5f7a76;line-height:1.6;">
                Use this one-time password to complete your sign-in:
            </p>
            <div style="text-align:center;margin-bottom:20px;">
                <span style="display:inline-block;font-size:34px;letter-spacing:8px;font-weight:800;color:{{ $primaryColor }};
                             background:#f3fbfa;border:2px dashed {{ $primaryColor }}44;border-radius:12px;
                             padding:12px 24px;font-family:'Courier New',monospace;">{{ $code }}</span>
            </div>
            <p style="margin:0 0 6px 0;font-size:13px;color:#7b938f;text-align:center;">
                This code expires in <strong>10 minutes</strong>.
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="padding:16px 24px 20px;background:#f8fcfb;border-top:1px solid #e5efec;text-align:center;">
            <p style="margin:0;font-size:11px;color:#7b938f;line-height:1.6;">
                &copy; {{ date('Y') }} {{ $platformName }}. All rights reserved.
            </p>
        </td>
    </tr>

</table>
</body>
</html>
