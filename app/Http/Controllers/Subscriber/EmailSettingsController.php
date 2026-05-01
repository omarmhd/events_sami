<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\CompanyBranding;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\SystemSetting;
use App\Services\EmailTemplateService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmailSettingsController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    public function index(Request $request, EmailTemplateService $templateService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        // Check visual identity feature access.
        $visualIdentityEnabled = $this->subscriptionService->featureEnabled($company, 'visual_identity');

        // Platform-level defaults (from admin settings) used when the company
        // has no branding record yet or when visual_identity feature is disabled.
        $platformName  = SystemSetting::get('platform_name',      config('app.name', 'Platform'));
        $platformEmail = SystemSetting::get('platform_sender_email',
                            config('mail.from.address', 'noreply@' . config('app.domain', 'platform.com')));
        $platformColor = SystemSetting::get('primary_color', '#0f8f83');

        $branding = CompanyBranding::firstOrCreate(
            ['company_id' => $company->id],
            [
                // Defaults are scoped to THIS company — always use company data,
                // never hardcoded platform brand names.
                'brand_name'       => $company->name,
                'header_image_url' => null,
                'primary_color'    => $platformColor,
                'secondary_color'  => SystemSetting::get('secondary_color', '#1F2937'),
                'sender_name'      => $company->name,
                'sender_email'     => $company->billing_email
                                        ?: $company->contact_email
                                        ?: $platformEmail,
            ]
        );

        $templateTypes = [
            EmailTemplate::TYPE_INVITATION,
            EmailTemplate::TYPE_TICKET,
            EmailTemplate::TYPE_PUBLIC_ACCEPTED,
            EmailTemplate::TYPE_PUBLIC_REJECTED,
        ];

        $templateMeta = [
            EmailTemplate::TYPE_INVITATION => [
                'title' => 'دعوة حضور الفعالية',
                'description' => 'هذه الرسالة تصل للمدعو قبل الرد على الدعوة، ويفضل أن تكون مختصرة وواضحة.',
            ],
            EmailTemplate::TYPE_TICKET => [
                'title' => 'تأكيد الحضور وإرسال رموز QR',
                'description' => 'هذه الرسالة تصل بعد قبول الدعوة، وقد تحتوي أكثر من رمز QR إذا كان مع المدعو مرافقون.',
            ],
            EmailTemplate::TYPE_PUBLIC_ACCEPTED => [
                'title' => 'قبول التسجيل العام',
                'description' => 'تستخدم لإشعار المسجلين في الفعاليات العامة بعد قبول طلبهم.',
            ],
            EmailTemplate::TYPE_PUBLIC_REJECTED => [
                'title' => 'اعتذار عن قبول التسجيل',
                'description' => 'رسالة مهذبة عند رفض التسجيل العام مع الحفاظ على صورة العلامة التجارية.',
            ],
        ];

        $templates = [];
        foreach ($templateTypes as $type) {
            $record = EmailTemplate::query()
                ->where('company_id', $company->id)
                ->whereNull('event_id')
                ->where('template_type', $type)
                ->first();

            $defaults = $templateService->defaultTemplates($type);
            $templates[$type] = [
                'subject_template' => $record->subject_template ?? $defaults['subject_template'],
                'body_template' => $record->body_template ?? $defaults['body_template'],
                'is_active' => $record ? (bool) $record->is_active : true,
            ];
        }

        $events = Event::query()
            ->where('company_id', $company->id)
            ->orderByDesc('id')
            ->get(['id', 'title', 'name']);

        return view('subscriber.email-settings.index', [
            'branding'             => $branding,
            'templates'            => $templates,
            'templateTypes'        => $templateTypes,
            'templateDefaults'     => collect($templateTypes)
                ->mapWithKeys(fn(string $type) => [$type => $templateService->defaultTemplates($type)])
                ->all(),
            'templateMeta'         => $templateMeta,
            'availableVariables'   => $templateService->availableVariables(),
            'events'               => $events,
            // Feature gating flags
            'visualIdentityEnabled' => $visualIdentityEnabled,
            'visualIdentityFallback' => [],
        ]);
    }

    public function saveBranding(Request $request)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        // Block branding changes when visual identity is not available.
        if (!$this->subscriptionService->featureEnabled($company, 'visual_identity')) {
            return redirect()->route('feature.unavailable', ['feature' => 'visual_identity']);
        }

        // Pull image constraints from config/features.php so they can be
        // changed in one place without touching controller code.
        $headerCfg = config('features.event_header_image');
        $logoCfg   = config('features.company_logo_image', [
            'mimes'          => 'jpg,jpeg,png,webp,svg',
            'max_kb'         => 2048,
            'storage_folder' => 'uploads/logos',
        ]);

        $data = $request->validate([
            'brand_name'        => ['nullable', 'string', 'max:120'],
            'logo_file'         => [
                'nullable',
                'file',
                'mimes:' . ($logoCfg['mimes'] ?? 'jpg,jpeg,png,webp,svg'),
                'max:'   . ($logoCfg['max_kb'] ?? 2048),
            ],
            'header_image_file' => [
                'nullable',
                'image',
                'mimes:' . ($headerCfg['mimes'] ?? 'jpg,jpeg,png,webp'),
                'max:'   . ($headerCfg['max_kb'] ?? 4096),
            ],
            'primary_color'     => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'secondary_color'   => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'sender_name'       => ['nullable', 'string', 'max:120'],
            'sender_email'      => ['nullable', 'email', 'max:190'],
            'reply_to_email'    => ['nullable', 'email', 'max:190'],
            'header_html'       => ['nullable', 'string'],
            'footer_html'       => ['nullable', 'string'],
        ]);

        $existingBranding = CompanyBranding::where('company_id', $company->id)->first();

        // ── Handle X-button deletes ───────────────────────────────────────────────
        if ($request->input('clear_logo') === '1') {
            if ($existingBranding?->logo_url) {
                $this->deletePublicFile(
                    (string) $existingBranding->logo_url,
                    config('features.company_logo_image.storage_folder', 'uploads/logos')
                );
            }
            $data['logo_url'] = null;
        }

        if ($request->input('clear_header') === '1') {
            if ($existingBranding?->header_image_url) {
                $this->deletePublicFile(
                    (string) $existingBranding->header_image_url,
                    config('features.event_header_image.storage_folder', 'uploads/event-images/headers')
                );
            }
            $data['header_image_url'] = null;
        }

        // ── Handle logo file upload ──────────────────────────────────────────────
        if ($request->hasFile('logo_file')) {
            $logoFolder = $logoCfg['storage_folder'] ?? 'uploads/logos';
            $uploadDir  = public_path($logoFolder);
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Delete the old logo file if it was previously managed by this system.
            if ($existingBranding && $existingBranding->logo_url) {
                $oldRelative = ltrim(parse_url($existingBranding->logo_url, PHP_URL_PATH), '/');
                $oldAbsolute = public_path($oldRelative);
                if (str_contains($oldRelative, $logoFolder) && file_exists($oldAbsolute)) {
                    @unlink($oldAbsolute);
                }
            }

            $file     = $request->file('logo_file');
            $filename = uniqid('logo_') . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['logo_url'] = asset($logoFolder . '/' . $filename);
        }
        unset($data['logo_file']);

        // ── Handle header image file upload ──────────────────────────────────────
        // Storage folder is read from config/features.php → 'event_header_image.storage_folder'
        // so it can be changed without touching this controller.
        if ($request->hasFile('header_image_file')) {
            $storageFolder = $headerCfg['storage_folder'] ?? 'uploads/branding';
            $uploadDir     = public_path($storageFolder);
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Delete previous uploaded file if it lives in our managed folder.
            if ($existingBranding && $existingBranding->header_image_url) {
                $oldRelative = ltrim(parse_url($existingBranding->header_image_url, PHP_URL_PATH), '/');
                $oldAbsolute = public_path($oldRelative);
                if (str_contains($oldRelative, $storageFolder) && file_exists($oldAbsolute)) {
                    @unlink($oldAbsolute);
                }
            }

            $file     = $request->file('header_image_file');
            $filename = uniqid('header_') . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $data['header_image_url'] = asset($storageFolder . '/' . $filename);
        }
        unset($data['header_image_file']);

        CompanyBranding::updateOrCreate(
            ['company_id' => $company->id],
            $data
        );

        return back()->with('success', 'Email branding updated successfully.');
    }

    public function saveTemplate(Request $request, EmailTemplateService $templateService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return redirect()->route('system.dashboard');
        }

        $data = $request->validate([
            'template_type' => ['required', Rule::in([
                EmailTemplate::TYPE_INVITATION,
                EmailTemplate::TYPE_TICKET,
                EmailTemplate::TYPE_PUBLIC_ACCEPTED,
                EmailTemplate::TYPE_PUBLIC_REJECTED,
            ])],
            'scope' => ['required', Rule::in(['company', 'event'])],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'subject_template' => ['nullable', 'string', 'max:255'],
            'body_template' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $eventId = null;
        if ($data['scope'] === 'event') {
            $event = Event::query()
                ->where('company_id', $company->id)
                ->where('id', $data['event_id'])
                ->firstOrFail();
            $eventId = $event->id;
        }

        EmailTemplate::updateOrCreate(
            [
                'company_id' => $company->id,
                'event_id' => $eventId,
                'template_type' => $data['template_type'],
            ],
            [
                'name' => ucfirst(str_replace('_', ' ', $data['template_type'])) . ' Template',
                'subject_template' => $data['subject_template'] ?? $templateService->defaultTemplates($data['template_type'])['subject_template'],
                'body_template' => $data['body_template'],
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        $templateService->clearTemplateCache($company, $eventId ? Event::find($eventId) : null, $data['template_type']);

        return back()->with('success', 'Email template saved successfully.');
    }

    public function preview(Request $request, EmailTemplateService $templateService)
    {
        $company = $request->user()->company;
        if (!$company) {
            return response()->json(['message' => 'Company context required'], 422);
        }

        $data = $request->validate([
            'template_type' => ['required', 'string'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'guest_name' => ['nullable', 'string'],
            'guest_email' => ['nullable', 'string'],
            'invitation_link' => ['nullable', 'url'],
            'subject_template' => ['nullable', 'string'],
            'body_template' => ['nullable', 'string'],
        ]);

        $event = null;
        if (!empty($data['event_id'])) {
            $event = Event::query()
                ->where('company_id', $company->id)
                ->findOrFail($data['event_id']);
        }

        $compiled = $templateService->compile($company, $event, $data['template_type'], [
            'guest_name' => $data['guest_name'] ?? 'Guest',
            'guest_email' => $data['guest_email'] ?? 'guest@example.com',
            'invitation_link' => $data['invitation_link'] ?? url('/rsvp/sample'),
            'tickets_html' => $this->buildSampleTicketsHtml($templateService, $event),
            'tickets_count' => 3,
        ]);

        if (!empty($data['subject_template']) || !empty($data['body_template'])) {
            $variables = $compiled['variables'];
            if (!empty($data['subject_template'])) {
                $compiled['subject'] = $templateService->renderTemplate($data['subject_template'], $variables);
            }
            if (!empty($data['body_template'])) {
                $body = $templateService->renderTemplate($data['body_template'], $variables);

                $branding = $company->branding ?: CompanyBranding::where('company_id', $company->id)->first();
                $headerHtml = $branding && $branding->header_html
                    ? $templateService->renderTemplate($branding->header_html, $variables)
                    : $templateService->renderTemplate('<img src="{{header_image_url}}" style="width:100%;display:block;">', $variables);

                $footerHtml = $branding && $branding->footer_html
                    ? $templateService->renderTemplate($branding->footer_html, $variables)
                    : $templateService->renderTemplate('<div style="text-align:center;font-size:12px;color:#6e8783;">&copy; {{current_year}} {{app_name}}</div>', $variables);

                $primary = e($variables['primary_color'] ?? '#DABC9A');
                $logo = e($variables['logo_url'] ?? asset('Logo-SAMI.png'));
                $brandName = e($variables['brand_name'] ?? $company->name);

                $compiled['html'] = '<!DOCTYPE html><html><body style="background:#f3f8f6;margin:0;padding:24px 12px;font-family:Segoe UI,Tahoma,Arial,sans-serif;">'
                    . '<table width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #dde9e6;">'
                    . '<tr><td style="padding:16px 22px;background:#f8fcfb;border-bottom:1px solid #e2eeea;">'
                    . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr>'
                    . '<td><img src="' . $logo . '" alt="Logo" style="height:34px;max-width:160px;object-fit:contain;"></td>'
                    . '<td align="right" style="font-size:12px;color:#54716d;font-weight:600;">' . $brandName . '</td>'
                    . '</tr></table>'
                    . '</td></tr>'
                    . '<tr><td>' . $headerHtml . '</td></tr>'
                    . '<tr><td style="padding:24px;border-top:4px solid ' . $primary . ';color:#244542;line-height:1.7;">' . $body . '</td></tr>'
                    . '<tr><td style="padding:20px;background:#f8fcfb;border-top:1px solid #e5efec;">' . $footerHtml . '</td></tr>'
                    . '</table></body></html>';
            }
        }

        return response()->json([
            'subject' => $compiled['subject'],
            'html' => $compiled['html'],
        ]);
    }

    protected function buildSampleTicketsHtml(EmailTemplateService $templateService, ?Event $event): string
    {
        $sampleInvitation = new EventInvitation([
            'invitee_name' => 'Ahmed Example',
            'invitee_email' => 'guest@example.com',
            'invitee_position' => 'Attendee',
            'status' => 'accepted',
        ]);

        $sampleTickets = [
            [
                'label' => 'Main',
                'qr' => $this->placeholderQrDataUri('MAIN'),
                'qr_token' => 'MAIN-QR-SAMPLE',
            ],
            [
                'label' => 'Guest 1',
                'qr' => $this->placeholderQrDataUri('GUEST 1'),
                'qr_token' => 'GUEST1-QR-SAMPLE',
            ],
            [
                'label' => 'Guest 2',
                'qr' => $this->placeholderQrDataUri('GUEST 2'),
                'qr_token' => 'GUEST2-QR-SAMPLE',
            ],
        ];

        return $templateService->ticketCardsHtml($sampleTickets, $sampleInvitation, $event);
    }

    public function clearLogo(\Illuminate\Http\Request $request)
    {
        $company = $request->user()->company;
        if (!$company) {
            return back()->with('error', 'لم يتم العثور على الشركة.');
        }

        $branding = \App\Models\CompanyBranding::where('company_id', $company->id)->first();

        if ($branding && $branding->logo_url) {
            $this->deletePublicFile(
                (string) $branding->logo_url,
                config('features.company_logo_image.storage_folder', 'uploads/logos')
            );
            $branding->logo_url = null;
            $branding->save();
        }

        return back()->with('success', 'تم حذف الشعار بنجاح.');
    }

    public function clearHeaderImage(\Illuminate\Http\Request $request)
    {
        $company = $request->user()->company;
        if (!$company) {
            return back()->with('error', 'لم يتم العثور على الشركة.');
        }

        $branding = \App\Models\CompanyBranding::where('company_id', $company->id)->first();

        if ($branding && $branding->header_image_url) {
            $this->deletePublicFile(
                (string) $branding->header_image_url,
                config('features.event_header_image.storage_folder', 'uploads/event-images/headers')
            );
            $branding->header_image_url = null;
            $branding->save();
        }

        return back()->with('success', 'تم حذف صورة الرأس بنجاح.');
    }

    /**
     * Delete a file stored under public/$managedFolder/ by resolving its URL path.
     * Only deletes files that live inside the given managed folder to prevent
     * accidental deletion of external URLs or files outside our control.
     */
    private function deletePublicFile(string $url, string $managedFolder): void
    {
        if (empty($url)) {
            return;
        }

        // Extract the path component from the full URL (handles http://host/path or /path)
        $urlPath = ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        // Only proceed if the file lives in our managed upload folder.
        // Normalise the folder path to avoid double-slash issues.
        $folder = trim($managedFolder, '/');

        if (!str_starts_with($urlPath, $folder . '/')) {
            return;
        }

        $absolutePath = public_path($urlPath);
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    protected function placeholderQrDataUri(string $label): string
    {
        $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="220" height="220" viewBox="0 0 220 220">'
            . '<rect width="220" height="220" fill="#f8fcfb"/>'
            . '<rect x="12" y="12" width="196" height="196" fill="#fff" stroke="#cfe2dc" stroke-width="2"/>'
            . '<rect x="32" y="32" width="52" height="52" fill="#2f5d57"/>'
            . '<rect x="136" y="32" width="52" height="52" fill="#2f5d57"/>'
            . '<rect x="32" y="136" width="52" height="52" fill="#2f5d57"/>'
            . '<rect x="102" y="102" width="16" height="16" fill="#2f5d57"/>'
            . '<rect x="122" y="122" width="16" height="16" fill="#2f5d57"/>'
            . '<rect x="142" y="102" width="16" height="16" fill="#2f5d57"/>'
            . '<text x="110" y="205" text-anchor="middle" font-size="12" fill="#476f69" font-family="Arial, sans-serif">' . $safeLabel . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}



