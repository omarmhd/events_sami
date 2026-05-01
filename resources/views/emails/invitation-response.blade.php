<!-- resources/views/emails/invitation-response.blade.php -->
@php $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')); @endphp
@component('mail::message')
<h2>تأكيد الحضور</h2>

شكراً على تأكيدك الحضور!

@component('mail::panel')
## رمز الدخول الخاص بك

### {{ $qrToken }}

**هام:** احفظ هذا الرمز وأدخله عند الدخول للفعالية.

**يمكنك أيضاً:**
- عرض الرمز من أي جهاز عند الدخول
- طلب تذكرة إذا فقدت الرمز
@endcomponent

@component('mail::button', ['url' => $eventUrl, 'color' => 'success'])
عرض تفاصيل الفعالية
@endcomponent

@component('mail::subcopy')
إذا لم تتمكن من النقر على الزر، انسخ والصق الرابط هنا:<br>
{{ $eventUrl }}
@endcomponent

مع أطيب التمنيات،<br>
فريق {{ $platformName }}
@endcomponent