<!-- resources/views/emails/invitation-sent.blade.php -->
@php $platformName = \App\Models\SystemSetting::get('platform_name', config('app.name', 'Platform')); @endphp
@component('mail::message')
<h2>{{ $heading }}</h2>

أنت مدعو لحضور:

@component('mail::panel')
## {{ $event->name }}

**التاريخ:** {{ $event->start_datetime->format('Y-m-d H:i') }}

**الموقع:** {{ $event->location_name }}

@if($event->description)
{{ $event->description }}
@endif
@endcomponent

@component('mail::button', ['url' => $actionUrl])
{{ $actionText }}
@endcomponent

إذا كان لديك أي استفسارات، يمكنك الرد على هذا البريد مباشرة.

شكراً،<br>
فريق {{ $platformName }}
@endcomponent