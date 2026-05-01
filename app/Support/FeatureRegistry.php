<?php

namespace App\Support;

/**
 * FeatureRegistry
 *
 * Single source of truth for all feature keys used throughout the application.
 * Admin configures features in the plan builder using these keys.
 * Controllers, middleware, and Blade directives check features by these keys.
 *
 * To add a new feature:
 *   1. Add it here in FEATURES and ALIASES (if it has legacy names)
 *   2. Add a preset button for it in plan-form.blade.php
 *   3. Check it in the controller/view using: @feature('your_key')
 *      or in PHP: $plan->hasFeature('your_key')
 */
class FeatureRegistry
{
    /**
     * All canonical feature keys with their metadata.
     *
     * Structure: [
     *   'key' => [
     *     'label'       => string  — Arabic display name
     *     'icon'        => string  — Font Awesome class
     *     'description' => string  — Short description for admin UI
     *     'category'    => string  — Grouping: 'core' | 'communication' | 'analytics' | 'enterprise'
     *   ]
     * ]
     */
    public const FEATURES = [
        // ── Core Features ─────────────────────────────────────────────────────
        'csv_import' => [
            'label'       => 'استيراد CSV الجماعي',
            'icon'        => 'fas fa-file-csv',
            'description' => 'رفع قوائم المدعوين من ملف CSV دفعةً واحدة',
            'category'    => 'core',
        ],
        'bulk_resend' => [
            'label'       => 'إعادة الإرسال الجماعي',
            'icon'        => 'fas fa-paper-plane',
            'description' => 'إرسال الدعوات مرة أخرى لعدة أشخاص في آنٍ واحد',
            'category'    => 'core',
        ],
        'customization' => [
            'label'       => 'تخصيص احترافي كامل',
            'icon'        => 'fas fa-wand-magic-sparkles',
            'description' => 'تخصيص ألوان وشعار وقوالب البريد والصفحات',
            'category'    => 'core',
        ],

        // ── Communication ─────────────────────────────────────────────────────
        'sms' => [
            'label'       => 'إشعارات SMS',
            'icon'        => 'fas fa-mobile-screen',
            'description' => 'إرسال إشعارات رسائل نصية للمدعوين',
            'category'    => 'communication',
        ],
        'whatsapp' => [
            'label'       => 'رسائل واتساب',
            'icon'        => 'fab fa-whatsapp',
            'description' => 'إرسال الدعوات عبر واتساب',
            'category'    => 'communication',
        ],

        // ── Analytics ────────────────────────────────────────────────────────
        'advanced_analytics' => [
            'label'       => 'تقارير متقدمة',
            'icon'        => 'fas fa-chart-line',
            'description' => 'تقارير تفصيلية وإحصاءات الحضور والتفاعل',
            'category'    => 'analytics',
        ],
        'export_reports' => [
            'label'       => 'تصدير التقارير',
            'icon'        => 'fas fa-file-export',
            'description' => 'تصدير التقارير والإحصاءات بصيغ متعددة',
            'category'    => 'analytics',
        ],

        // ── Platform Features (gated per plan) ───────────────────────────────
        'registration_forms' => [
            'label'       => 'نماذج التسجيل',
            'icon'        => 'fas fa-wpforms',
            'description' => 'إنشاء نماذج تسجيل مخصصة لفعالياتك وربطها بالأحداث',
            'category'    => 'platform',
            'limit_key'   => 'registration_forms_limit', // numeric plan limit (null = unlimited)
        ],
        'teams' => [
            'label'       => 'إدارة الفريق',
            'icon'        => 'fas fa-users',
            'description' => 'إضافة أعضاء الفريق وتعيين أدوارهم للتعاون في إدارة الفعاليات',
            'category'    => 'platform',
        ],
        'visual_identity' => [
            'label'       => 'الهوية البصرية',
            'icon'        => 'fas fa-palette',
            'description' => 'تخصيص شعار المنظمة وألوانها وبيانات المُرسِل في جميع رسائل البريد الإلكتروني',
            'category'    => 'platform',
        ],
        'event_header_image' => [
            'label'       => 'صورة رأس الفعالية',
            'icon'        => 'fas fa-image',
            'description' => 'رفع صورة مخصصة لرأس رسائل البريد الإلكتروني الخاصة بكل فعالية',
            'category'    => 'platform',
        ],
        'event_footer_image' => [
            'label'       => 'صورة تذييل الفعالية',
            'icon'        => 'fas fa-file-image',
            'description' => 'رفع صورة مخصصة لتذييل رسائل البريد الإلكتروني الخاصة بكل فعالية',
            'category'    => 'platform',
        ],

        // ── Public Presence ───────────────────────────────────────────────────
        'custom_subdomain' => [
            'label'       => 'نطاق فرعي مخصص للدعوات',
            'icon'        => 'fas fa-globe',
            'description' => 'استخدام النطاق الفرعي الخاص بالمنظمة في روابط الدعوات والصفحات العامة',
            'category'    => 'enterprise',
        ],

        // ── Enterprise ────────────────────────────────────────────────────────
        'account_manager' => [
            'label'       => 'مدير حساب مخصص',
            'icon'        => 'fas fa-headset',
            'description' => 'مدير حساب شخصي متاح طوال ساعات العمل',
            'category'    => 'enterprise',
        ],
        'sla' => [
            'label'       => 'اتفاقية مستوى الخدمة SLA',
            'icon'        => 'fas fa-shield-halved',
            'description' => 'ضمان اتفاقية مستوى خدمة مكتوبة',
            'category'    => 'enterprise',
        ],
        'white_label' => [
            'label'       => 'وايت لابل',
            'icon'        => 'fas fa-tag',
            'description' => 'إخفاء شعار المنصة والعلامة التجارية بالكامل',
            'category'    => 'enterprise',
        ],
        'api_access' => [
            'label'       => 'وصول API',
            'icon'        => 'fas fa-code',
            'description' => 'وصول كامل لـ REST API لربط الأنظمة الخارجية',
            'category'    => 'enterprise',
        ],
        'sso' => [
            'label'       => 'تسجيل دخول موحد SSO',
            'icon'        => 'fas fa-key',
            'description' => 'تكامل مع أنظمة SSO وهوية المؤسسة (SAML/OAuth)',
            'category'    => 'enterprise',
        ],
    ];

    /**
     * Legacy / alternate feature code aliases → normalized key.
     * Used to maintain backward compatibility when code uses different names.
     */
    public const ALIASES = [
        'bulk_import_csv'        => 'csv_import',
        'bulk_resend_invitations' => 'bulk_resend',
        'custom_branding'        => 'customization',
        'advanced_analytics'     => 'advanced_analytics', // same
        'sso_integration'        => 'sso',
        'api'                    => 'api_access',
        // platform feature aliases
        'forms'                  => 'registration_forms',
        'team'                   => 'teams',
        'branding'               => 'visual_identity',
        'header_image'           => 'event_header_image',
        'footer_image'           => 'event_footer_image',
        'subdomain'              => 'custom_subdomain',
        'custom_domain'         => 'custom_subdomain',
    ];

    /**
     * Normalize a feature code to its canonical key.
     * Returns the key unchanged if no alias exists.
     */
    public static function normalize(string $key): string
    {
        return self::ALIASES[$key] ?? $key;
    }

    /**
     * Get metadata for a feature key (label, icon, description, category).
     * Returns null if the key is not registered.
     */
    public static function get(string $key): ?array
    {
        return self::FEATURES[self::normalize($key)] ?? null;
    }

    /**
     * Get display label for a feature key.
     * Falls back to the key itself if not registered.
     */
    public static function label(string $key): string
    {
        return self::FEATURES[self::normalize($key)]['label'] ?? $key;
    }

    /**
     * Get icon class for a feature key.
     */
    public static function icon(string $key): string
    {
        return self::FEATURES[self::normalize($key)]['icon'] ?? 'fas fa-circle-check';
    }

    /**
     * Return all features grouped by category.
     * Useful for rendering the plan feature builder UI.
     */
    public static function grouped(): array
    {
        $groups = [];
        foreach (self::FEATURES as $key => $meta) {
            $groups[$meta['category']][$key] = $meta;
        }
        return $groups;
    }

    /**
     * All known canonical keys.
     */
    public static function keys(): array
    {
        return array_keys(self::FEATURES);
    }
}
