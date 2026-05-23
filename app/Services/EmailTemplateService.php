<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyBranding;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class EmailTemplateService
{
    public function compile(
        Company $company,
        ?Event $event,
        string $type,
        array $variables = []
    ): array {
        $branding = $this->resolveBranding($company);
        $template = $this->resolveTemplate($company, $event, $type);

        $baseVariables = $this->buildBaseVariables($company, $event, $branding, $variables);

        $subjectTemplate = $template['subject_template'];
        $bodyTemplate = $template['body_template'];

        $subject = $this->renderTemplate($subjectTemplate, $baseVariables);
        $body = $this->renderTemplate($bodyTemplate, $baseVariables);

        if ($type === EmailTemplate::TYPE_INVITATION) {
            $body = $this->ensureInvitationLinkBlock($body, $baseVariables);
        }

        if ($type === EmailTemplate::TYPE_TICKET) {
            $body = $this->ensureTicketsHtmlBlock($body, $baseVariables);
        }

        // Only include a header image block when a URL is actually available.
        // This prevents an empty <img> tag or broken-image icon in emails.
        $hasHeaderImage = !empty($baseVariables['header_image_url']);

        if ($branding->header_html) {
            $headerHtml = $branding->header_html;
        } elseif ($hasHeaderImage) {
            $headerHtml = $this->defaultHeaderHtml();
        } else {
            $headerHtml = '';
        }

        $footerHtml = $branding->footer_html ?: $this->defaultFooterHtml();

        $wrappedHtml = $this->wrapHtml(
            $headerHtml !== '' ? $this->renderTemplate($headerHtml, $baseVariables) : '',
            $body,
            $this->renderTemplate($footerHtml, $baseVariables),
            $baseVariables
        );

        // Always use the SMTP configured from address to avoid SMTP rejection.
        // Fall back to the platform default configured in admin settings — never a hardcoded domain.
        $platformEmail = SystemSetting::get('platform_sender_email',
            config('mail.from.address', 'noreply@' . config('app.domain', 'platform.com')));

        return [
            'subject'       => $subject,
            'html'          => $wrappedHtml,
            'from_name'     => $branding->brand_name ?: ($company->name ?: config('app.name')),
            'from_email'    => $platformEmail,
            'reply_to'      => $branding->reply_to_email ?: $platformEmail,
            'variables'     => $baseVariables,
            'template_used' => $template,
        ];
    }

    public function availableVariables(): array
    {
        return [
            '{{app_name}}',
            '{{company_name}}',
            '{{company_subdomain}}',
            '{{brand_name}}',
            '{{primary_color}}',
            '{{secondary_color}}',
            '{{logo_url}}',
            '{{header_image_url}}',
            '{{event_title}}',
            '{{event_date}}',
            '{{event_time}}',
            '{{event_location}}',
            '{{event_map_url}}',
            '{{event_description_ar}}',
            '{{event_description_en}}',
            '{{guest_name}}',
            '{{guest_email}}',
            '{{invitee_position}}',
            '{{invitation_link}}',
            '{{invitation_sent_at}}',
            '{{rsvp_status}}',
            '{{response_time}}',
            '{{allowed_guests}}',
            '{{tickets_html}}',
            '{{tickets_count}}',
            '{{qr_code_image}}',
            '{{support_phone}}',
            '{{current_year}}',
        ];
    }

    public function defaultTemplates(string $type): array
    {
        if ($type === EmailTemplate::TYPE_INVITATION) {
            return [
                'subject_template' => 'دعوة: {{event_title}} | {{brand_name}}',
                'body_template' => <<<HTML
<div dir="rtl" style="text-align:right;font-family:Segoe UI, Tahoma, Arial,sans-serif;color:#244542;line-height:1.7;">
    <h2 style="margin:0 0 10px 0;font-size:20px;font-weight:700;">مرحباً {{guest_name}}</h2>

    <p style="margin:0 0 12px 0;font-size:15px;color:#395a56;">يسرّنا دعوتك لحضور الفعالية التالية:</p>

    <div style="background:#f6fbfa;border:1px solid #e2eeea;border-radius:10px;padding:12px 14px;margin:12px 0;font-size:14px;color:#3b5a56;">
        <div><strong>الفعالية:</strong> {{event_title}}</div>
        <div><strong>التاريخ:</strong> {{event_date}}</div>
        <div><strong>الوقت:</strong> {{event_time}}</div>
        <div><strong>الموقع:</strong> {{event_location}}</div>
        <div style="margin-top:6px;color:#6e8783;font-size:12px;"><strong>تاريخ إرسال الدعوة:</strong> {{invitation_sent_at}}</div>
    </div>

    <div style="text-align:center;margin:18px 0;">
        <a href="{{invitation_link}}" style="background:{{primary_color}};color:#fff;padding:12px 22px;border-radius:10px;display:inline-block;font-weight:700;text-decoration:none;">الرد على الدعوة</a>
    </div>

    <p style="font-size:12px;color:#6e8783;word-break:break-all;text-align:center;margin-top:6px;">{{invitation_link}}</p>

    <p style="margin-top:18px;color:#64748b;font-size:14px;">إذا كان لديك أي استفسار، يمكنك الرد على هذا البريد أو التواصل عبر {{reply_to_email}}</p>

</div>
HTML,
            ];
        }

        if ($type === EmailTemplate::TYPE_PUBLIC_ACCEPTED) {
            return [
                'subject_template' => 'تم قبول تسجيلك — {{event_title}} | {{brand_name}}',
                'body_template' => <<<HTML
<h2 style="margin:0 0 10px 0;color:#102a2a;font-size:20px;">مرحباً {{guest_name}}</h2>
<p style="color:#395a56;line-height:1.8;margin:0 0 6px 0;">
    يسعدنا إبلاغك بأن تسجيلك في الفعالية <strong>{{event_title}}</strong> قد تم قبوله.
</p>
<p style="color:#395a56;line-height:1.8;margin:0 0 20px 0;">
    أدناه بطاقة الدخول الخاصة بك. يرجى تقديمها عند مدخل الفعالية.
</p>

{{tickets_html}}
HTML,
            ];
        }

        if ($type === EmailTemplate::TYPE_PUBLIC_REJECTED) {
            return [
                'subject_template' => 'Registration Update - {{event_title}} | {{brand_name}}',
                'body_template' => <<<HTML
<h2 style="margin:0 0 12px 0;color:#102a2a;">Hello {{guest_name}}</h2>
<p style="color:#395a56;line-height:1.7;">Thank you for your interest in <strong>{{event_title}}</strong>.</p>
<p style="color:#395a56;line-height:1.7;">Unfortunately, your registration was not approved this time.</p>
HTML,
            ];
        }

        return [
            'subject_template' => 'Your Tickets - {{event_title}} | {{brand_name}}',
            'body_template' => <<<HTML
<h2 style="margin:0 0 12px 0;color:#102a2a;">{{guest_name}}</h2>
<p style="color:#395a56;line-height:1.7;">Your attendance has been confirmed. Below are your tickets.</p>
<p style="margin:0;color:#395a56;line-height:1.7;">Event: <strong>{{event_title}}</strong></p>
<p style="margin:0;color:#395a56;line-height:1.7;">Date: <strong>{{event_date}}</strong></p>
<p style="margin:0 0 18px 0;color:#395a56;line-height:1.7;">Location: <strong>{{event_location}}</strong></p>
    <p style="margin:0 0 18px 0;color:#395a56;line-height:1.7;">Status: <strong>{{rsvp_status}}</strong> - Response Time: <strong>{{response_time}}</strong></p>
{{tickets_html}}
HTML,
        ];
    }

    public function renderTemplate(string $template, array $variables): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($variables) {
            $key = $matches[1];
            return array_key_exists($key, $variables) ? (string) $variables[$key] : $matches[0];
        }, $template);
    }

    protected function ensureInvitationLinkBlock(string $bodyHtml, array $variables): string
    {
        $invitationLink = trim((string) ($variables['invitation_link'] ?? ''));
        if ($invitationLink === '') {
            return $bodyHtml;
        }

        $hasLink = stripos($bodyHtml, $invitationLink) !== false
            || stripos($bodyHtml, e($invitationLink)) !== false
            || stripos($bodyHtml, '{{invitation_link}}') !== false;

        if ($hasLink) {
            return $bodyHtml;
        }

        $primary = e((string) ($variables['primary_color'] ?? '#0ea5e9'));
        $cta = '<div style="margin:22px 0;text-align:center;">'
            . '<a href="' . e($invitationLink) . '" style="background:' . $primary . ';color:#fff;padding:14px 26px;border-radius:10px;display:inline-block;font-weight:700;text-decoration:none;">Respond to Invitation</a>'
            . '</div>'
            . '<p style="font-size:12px;color:#6e8783;word-break:break-all;">' . e($invitationLink) . '</p>';

        return $bodyHtml . $cta;
    }

    protected function ensureTicketsHtmlBlock(string $bodyHtml, array $variables): string
    {
        $ticketsHtml = trim((string) ($variables['tickets_html'] ?? ''));
        if ($ticketsHtml === '') {
            return $bodyHtml;
        }

        $hasTickets = stripos($bodyHtml, '{{tickets_html}}') !== false
            || stripos($bodyHtml, 'Ticket #') !== false
            || stripos($bodyHtml, 'SCAN AT ENTRANCE') !== false
            || stripos($bodyHtml, 'QR') !== false;

        if ($hasTickets) {
            return $bodyHtml;
        }

        return $bodyHtml
            . '<div style="margin:20px 0 8px 0;">'
            . '<h3 style="margin:0 0 10px 0;color:#102a2a;font-size:16px;">Your Tickets</h3>'
            . $ticketsHtml
            . '</div>';
    }

    public function clearTemplateCache(Company $company, ?Event $event, string $type): void
    {
        Cache::forget($this->templateCacheKey($company->id, $event ? $event->id : 'none', $type));
    }

    protected function resolveBranding(Company $company): CompanyBranding
    {
        // Use platform-level SystemSetting as defaults for new branding records.
        // This ensures admin configuration is respected rather than hardcoded values.
        $platformEmail = SystemSetting::get('platform_sender_email',
            config('mail.from.address', 'noreply@' . config('app.domain', 'platform.com')));

        return CompanyBranding::firstOrCreate(
            ['company_id' => $company->id],
            [
                'brand_name'       => $company->name,
                'header_image_url' => null,
                'primary_color'    => SystemSetting::get('primary_color', '#0f8f83'),
                'secondary_color'  => SystemSetting::get('secondary_color', '#1F2937'),
                'sender_name'      => $company->name,
                'sender_email'     => $company->billing_email
                                        ?: $company->contact_email
                                        ?: $platformEmail,
            ]
        );
    }

    protected function resolveTemplate(Company $company, ?Event $event, string $type): array
    {
        $storedTemplate = $this->resolveStoredTemplate($company, $event, $type);
        if ($storedTemplate) {
            return $storedTemplate;
        }

        $eventTemplate = $this->resolveEventTemplate($event, $type);

        if ($eventTemplate) {
            return $eventTemplate;
        }

        return $this->defaultTemplates($type);
    }

    protected function resolveStoredTemplate(Company $company, ?Event $event, string $type): ?array
    {
        $default = $this->defaultTemplates($type);

        $query = EmailTemplate::query()
            ->where('company_id', $company->id)
            ->where('template_type', $type)
            ->where('is_active', true);

        $template = null;
        if ($event) {
            $template = (clone $query)
                ->where('event_id', $event->id)
                ->latest('id')
                ->first();
        }

        if (!$template) {
            $template = (clone $query)
                ->whereNull('event_id')
                ->latest('id')
                ->first();
        }

        if (!$template) {
            return null;
        }

        $subjectTemplate = trim((string) $template->subject_template);
        $bodyTemplate = trim((string) $template->body_template);

        return [
            'subject_template' => $subjectTemplate !== '' ? $subjectTemplate : ($default['subject_template'] ?? ''),
            'body_template' => $bodyTemplate !== '' ? $bodyTemplate : ($default['body_template'] ?? ''),
        ];
    }

    protected function resolveEventTemplate(?Event $event, string $type): ?array
    {
        if (!$event) {
            return null;
        }

        // Note: invitation_email_subject and confirmation_email_subject columns were removed
        // from the events table. Only the body fields remain for per-event customisation.
        // Subjects are now always derived from the default template or stored EmailTemplate.
        $invitationBody    = is_string($event->invitation_email_body)    ? trim($event->invitation_email_body)    : '';
        $confirmationBody  = is_string($event->confirmation_email_body)  ? trim($event->confirmation_email_body)  : '';

        return match ($type) {
            EmailTemplate::TYPE_INVITATION => $invitationBody !== '' ? [
                'subject_template' => $this->defaultTemplates($type)['subject_template'],
                'body_template'    => $invitationBody,
            ] : null,

            EmailTemplate::TYPE_TICKET,
            EmailTemplate::TYPE_PUBLIC_ACCEPTED,
            EmailTemplate::TYPE_PUBLIC_REJECTED => $confirmationBody !== '' ? [
                'subject_template' => $this->defaultTemplates($type)['subject_template'],
                'body_template'    => $confirmationBody,
            ] : null,

            default => null,
        };
    }

    protected function buildBaseVariables(Company $company, ?Event $event, CompanyBranding $branding, array $variables): array
    {
        // Platform-level color defaults — read from admin settings, never hardcoded.
        $defaultPrimary   = SystemSetting::get('primary_color',   '#0f8f83');
        $defaultSecondary = SystemSetting::get('secondary_color', '#1F2937');

        $base = [
            'app_name'         => config('app.name'),
            'company_name'     => $company->name,
            'company_subdomain' => $company->subdomain ?? '',
            'brand_name'       => $branding->brand_name ?: $company->name,
            'primary_color'    => $branding->primary_color    ?: $defaultPrimary,
            'secondary_color'  => $branding->secondary_color  ?: $defaultSecondary,
            // Logo: use company-uploaded logo only when it exists; empty string renders nothing in emails.
            'logo_url'         => $branding->logo_url         ?: SystemSetting::get('platform_logo_url', ''),
            // Header image: only render when explicitly set — no hardcoded fallback banner.
            'header_image_url' => $branding->header_image_url ?: '',
            'event_title' => $event ? ($event->title ?: $event->name) : '',
            'event_date' => $event && $event->date ? $event->date->format('Y-m-d') : '',
            'event_time' => $event ? trim(($event->from_time ?: '') . ' - ' . ($event->to_time ?: '')) : '',
            'event_location' => $event ? ($event->location_name ?: ($event->address ?? '')) : '',
            'event_map_url' => $event ? ($event->google_map_url ?: '') : '',
            'event_description_ar' => $event ? ($event->description ?? '') : '',
            'event_description_en' => $event ? ($event->description_en ?? '') : '',
            'guest_name' => '',
            'guest_email' => '',
            'invitee_position' => '',
            'invitation_link' => '',
            'invitation_sent_at' => '',
            'rsvp_status' => '',
            'response_time' => '',
            'allowed_guests' => 0,
            'tickets_html' => '',
            'tickets_count' => 0,
            'qr_code_image' => '',
            'support_phone' => $company->phone ?: '',
            'reply_to_email' => $branding->reply_to_email ?: '',
            'reply_to_email_label' => $branding->reply_to_email
                ? ('لأي مشاكل في التواصل، راسلنا على: ' . $branding->reply_to_email)
                : '',
            'current_year' => date('Y'),
        ];

        return array_merge($base, $variables);
    }

    protected function wrapHtml(string $headerHtml, string $bodyHtml, string $footerHtml, array $variables): string
    {
        $primary   = e($variables['primary_color']   ?? '#0f8f83');
        $secondary = e($variables['secondary_color'] ?? '#1F2937');
        $brand     = e($variables['brand_name'] ?? $variables['company_name'] ?? config('app.name'));
        $logoUrl   = trim((string) ($variables['logo_url'] ?? ''));

        // Only render the logo <img> when a URL is actually available.
        $logoHtml = $logoUrl !== ''
            ? '<img src="' . e($logoUrl) . '" alt="Logo" style="height:34px;max-width:160px;object-fit:contain;">'
            : '<span style="font-size:15px;font-weight:700;color:' . $primary . ';">' . $brand . '</span>';

        // Render provided header HTML when present; otherwise render a flexible
        // decorative banner that looks good whether or not a header image was uploaded.
        if (trim(strip_tags($headerHtml)) !== '' || str_contains($headerHtml, '<img')) {
            $headerRow = '<tr><td style="padding:0;">' . $headerHtml . '</td></tr>';
        } else {
            $bannerText = e(trim((string) ($variables['event_title'] ?? '')) ?: $brand);
            $bannerHtml = '<div style="text-align:center;padding:26px 18px;background:linear-gradient(135deg,' . $primary . ',' . $secondary . ');color:#fff;">'
                . '<div style="font-size:20px;font-weight:700;line-height:1.1;">' . $bannerText . '</div>'
                . '</div>';

            $headerRow = '<tr><td style="padding:0;">' . $bannerHtml . '</td></tr>';
        }

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>'
            . '<body style="margin:0;padding:26px 12px;background:#f3f8f6;font-family:Segoe UI,Tahoma,Arial,sans-serif;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #dde9e6;">'
            . '<tr><td style="padding:16px 22px;background:#f8fcfb;border-bottom:1px solid #e2eeea;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>'
            . '<td style="vertical-align:middle;">' . $logoHtml . '</td>'
            . '<td align="right" style="vertical-align:middle;color:#54716d;font-size:12px;font-weight:600;letter-spacing:.04em;">' . $brand . '</td>'
            . '</tr></table>'
            . '</td></tr>'
            . $headerRow
            . '<tr><td style="padding:26px 24px;color:#244542;line-height:1.7;border-top:4px solid ' . $primary . ';border-bottom:1px solid #e5efec;">' . $bodyHtml . '</td></tr>'
            . '<tr><td style="padding:20px 24px;background:#f8fcfb;color:' . $secondary . ';">' . $footerHtml . '</td></tr>'
            . '</table></body></html>';
    }

    protected function defaultHeaderHtml(): string
    {
        // The {{header_image_url}} variable resolves to '' when no image is set.
        // The onerror handler hides the element to prevent a broken image icon.
        // When the src is empty the browser won't even fire a network request.
        return <<<HTML
<div style="text-align:center;background:#ffffff;padding:0;">
    <img src="{{header_image_url}}" alt="" style="width:100%;max-width:680px;display:block;" onerror="this.style.display='none'" onload="if(!this.src||this.src==='')this.style.display='none'">
</div>
HTML;
    }

    protected function defaultFooterHtml(): string
    {
        return <<<HTML
<div style="text-align:center;font-size:12px;line-height:1.7;">
    <div style="margin-bottom:6px;color:#2d4b48;font-weight:600;">{{brand_name}}</div>
    <div style="color:#6e8783;">
        Support: {{support_phone}}<br>
        {{reply_to_email_label}}<br>
        &copy; {{current_year}} {{app_name}}. All rights reserved.
    </div>
</div>
HTML;
    }

    protected function templateCacheKey($companyId, $eventId, string $type): string
    {
        return "email_template:{$companyId}:{$eventId}:{$type}";
    }

    public function ticketCardsHtml(array $tickets, EventInvitation $invitation, ?Event $event): string
    {
        $cards = [];
        foreach ($tickets as $index => $ticket) {
            $label = e($ticket['label'] ?? 'Ticket');
            $img = e($ticket['qr'] ?? '');

            $cards[] = '<div style="display:inline-block;vertical-align:top;width:250px;margin:8px;padding:14px;border:1px solid #dde9e6;border-radius:12px;text-align:center;background:#fff;">'
                . '<div style="font-size:11px;color:#5f7a76;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">' . $label . '</div>'
                . '<div style="font-weight:700;color:#102a2a;margin-bottom:8px;">' . e($invitation->invitee_name) . '</div>'
                . '<img src="' . $img . '" alt="QR" style="width:170px;height:170px;object-fit:contain;border:1px dashed #d0e2dd;padding:8px;border-radius:8px;background:#f8fcfb;">'
                . '<div style="margin-top:8px;font-size:11px;color:#6b8480;">Ticket #' . ($index + 1) . '</div>'
                . '</div>';
        }

        $eventInfo = '';
        if ($event) {
            $eventInfo = '<div style="margin-bottom:14px;color:#395a56;line-height:1.7;">'
                . '<strong>' . e($event->title ?: $event->name) . '</strong><br>'
                . e($event->date ? $event->date->format('Y-m-d') : '') . ' ' . e(trim(($event->from_time ?: '') . ' - ' . ($event->to_time ?: '')))
                . '</div>';
        }

        return $eventInfo . '<div style="text-align:center;">' . implode('', $cards) . '</div>';
    }
}
