<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'features',
        'annual_price',
        'per_event_price',
        'annual_event_limit',
        'per_event_invitee_limit',
        'includes_csv_import',
        'includes_bulk_resend',
        'includes_customization',
        'highlight_label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'annual_price'          => 'float',
        'per_event_price'       => 'float',
        'features'              => 'array',
        'includes_csv_import'   => 'boolean',
        'includes_bulk_resend'  => 'boolean',
        'includes_customization'=> 'boolean',
        'is_active'             => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(CompanySubscription::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Feature Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Returns the display feature list for plan cards on the billing/upgrade page.
     *
     * Priority:
     *  1. JSON `features` column (structured, admin-managed)
     *  2. Comma-separated `description` field — each item becomes a feature line
     *  3. Legacy boolean columns (csv, bulk_resend, customization) fallback
     *
     * Each returned item: ['icon' => 'fas fa-*', 'text' => '...', 'enabled' => true/false]
     */
    public function featureList(): array
    {
        $list = [];

        // ── Auto-computed limit features (always shown first) ─────────────────
        if ($this->annual_event_limit) {
            $list[] = ['icon' => 'fas fa-calendar-check', 'text' => "حتى {$this->annual_event_limit} فعالية سنوياً", 'enabled' => true];
        } else {
            $list[] = ['icon' => 'fas fa-infinity', 'text' => 'فعاليات غير محدودة', 'enabled' => true];
        }

        if ($this->per_event_invitee_limit) {
            $list[] = ['icon' => 'fas fa-users', 'text' => "حتى {$this->per_event_invitee_limit} مدعو لكل فعالية", 'enabled' => true];
        } else {
            $list[] = ['icon' => 'fas fa-users', 'text' => 'مدعوون غير محدودون', 'enabled' => true];
        }

        // ── Structured JSON features (admin-managed) ───────────────────────────
        if (!empty($this->features)) {
            foreach ($this->features as $f) {
                $list[] = [
                    'icon'    => $f['icon']    ?? 'fas fa-check-circle',
                    'text'    => $f['label']   ?? ($f['text'] ?? ''),
                    'enabled' => (bool) ($f['enabled'] ?? true),
                    'key'     => $f['key']     ?? null,
                    'limit'   => $f['limit']   ?? null,
                ];
            }
            return $list;
        }

        // ── Fallback: parse description as comma-separated feature lines ───────
        if ($this->description) {
            $items = array_filter(array_map('trim', explode(',', $this->description)));
            foreach ($items as $item) {
                $list[] = ['icon' => 'fas fa-circle-check', 'text' => $item, 'enabled' => true];
            }
            return $list;
        }

        // ── Legacy boolean columns fallback ────────────────────────────────────
        $list[] = ['icon' => 'fas fa-envelope', 'text' => 'دعوات بريد إلكتروني', 'enabled' => true];
        $list[] = ['icon' => 'fas fa-qrcode', 'text' => 'تسجيل الحضور بـ QR', 'enabled' => true];

        if ($this->includes_csv_import) {
            $list[] = ['icon' => 'fas fa-file-csv', 'text' => 'استيراد CSV الجماعي', 'enabled' => true];
        }
        if ($this->includes_bulk_resend) {
            $list[] = ['icon' => 'fas fa-paper-plane', 'text' => 'إعادة الإرسال الجماعي', 'enabled' => true];
        }
        if ($this->includes_customization) {
            $list[] = ['icon' => 'fas fa-wand-magic-sparkles', 'text' => 'تخصيص احترافي كامل', 'enabled' => true];
            $list[] = ['icon' => 'fas fa-headset', 'text' => 'مدير حساب مخصص', 'enabled' => true];
            $list[] = ['icon' => 'fas fa-shield-halved', 'text' => 'اتفاقية مستوى الخدمة SLA', 'enabled' => true];
        }

        return $list;
    }

    /**
     * Check whether a specific feature key is enabled on this plan.
     * Falls back to legacy boolean columns if no JSON features defined.
     *
     * Usage: $plan->hasFeature('csv_import')
     */
    public function hasFeature(string $key): bool
    {
        // Check JSON features first
        if (!empty($this->features)) {
            foreach ($this->features as $f) {
                if (($f['key'] ?? null) === $key) {
                    return (bool) ($f['enabled'] ?? false);
                }
            }
            // Key not found in JSON → not available
            return false;
        }

        // Legacy boolean column fallback
        return match ($key) {
            'csv_import'    => (bool) $this->includes_csv_import,
            'bulk_resend'   => (bool) $this->includes_bulk_resend,
            'customization' => (bool) $this->includes_customization,
            default         => false,
        };
    }

    /**
     * Get the limit value for a feature (e.g. max SMS per month).
     * Returns null if the feature has no numeric limit.
     */
    public function featureLimit(string $key): ?int
    {
        if (!empty($this->features)) {
            foreach ($this->features as $f) {
                if (($f['key'] ?? null) === $key) {
                    return isset($f['limit']) && $f['limit'] !== null ? (int) $f['limit'] : null;
                }
            }
        }
        return null;
    }
}
