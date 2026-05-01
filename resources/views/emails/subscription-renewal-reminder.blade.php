<!-- resources/views/emails/subscription-renewal-reminder.blade.php -->
@php
    $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform'));
@endphp
@component('mail::message')
<h2>تذكير تجديد الاشتراك</h2>

مرحباً {{ $company->owner->name }},

نود تذكرك بأن اشتراكك في **{{ $planName }}** سينتهي في **{{ $renewalDate }}**.

@component('mail::panel')
لتجنب أي انقطاع في الخدمة، يرجى تجديد اشتراكك في الوقت المناسب.
@endcomponent

@component('mail::button', ['url' => $renewalUrl])
جدد الاشتراك الآن
@endcomponent

**فوائد التجديد المبكر:**
- عدم توقف الخدمة
- ضمان استمرار عملياتك
- احصل على 5% خصم إذا جددت قبل انتهاء الفترة

إذا كان لديك أي أسئلة أو احتجت مساعدة، لا تتردد في التواصل معنا.

مع أطيب التمنيات،<br>
فريق {{ $platformName }}
@endcomponent