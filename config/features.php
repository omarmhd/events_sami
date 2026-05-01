<?php

/**
 * ══════════════════════════════════════════════════════════════════════════════
 *  Feature Constraints & Metadata
 *  ─────────────────────────────────────────────────────────────────────────────
 *  Single place to configure every aspect of gated platform features.
 *
 *  HOW TO ADD OR TWEAK A FEATURE:
 *    1. Add / adjust the entry below.
 *    2. Add the key to FeatureRegistry::FEATURES so the admin plan builder
 *       can toggle it per plan.
 *    3. That's it — middleware, controllers, and views read from here.
 *
 *  LIMITS:
 *    null  → unlimited / not enforced
 *    int   → hard cap
 *
 *  IMAGE CONSTRAINTS (for upload validation):
 *    mimes      → accepted MIME types sent to Laravel's 'mimes' rule
 *    max_kb     → maximum file size in kilobytes
 *    min_width  → minimum pixel width  (null = not enforced)
 *    min_height → minimum pixel height (null = not enforced)
 *    max_width  → maximum pixel width  (null = not enforced)
 *    max_height → maximum pixel height (null = not enforced)
 *    ideal_w    → ideal / recommended width  (shown in UI hint)
 *    ideal_h    → ideal / recommended height (shown in UI hint)
 * ══════════════════════════════════════════════════════════════════════════════
 */

return [

    // ──────────────────────────────────────────────────────────────────────────
    //  REGISTRATION FORMS
    // ──────────────────────────────────────────────────────────────────────────
    'registration_forms' => [
        /*
         * Default maximum number of forms a company may create.
         * Overridden by the plan's JSON features key "registration_forms_limit".
         * Set to null for unlimited.
         */
        'default_limit' => null,

        /*
         * Message shown when the company has reached its form limit.
         * Supports basic HTML.
         */
        'limit_reached_message' => 'لقد وصلت إلى الحد الأقصى لعدد نماذج التسجيل المسموح بها في خطتك الحالية.',
    ],

    // ──────────────────────────────────────────────────────────────────────────
    //  TEAMS
    // ──────────────────────────────────────────────────────────────────────────
    'teams' => [
        /*
         * Route names that belong to the teams feature.
         * CheckFeatureAccess middleware matches incoming route names against
         * this list and blocks access when the feature is disabled.
         */
        'guarded_routes' => [
            'team.index',
            'team.store',
            'team.role.update',
            'team.destroy',
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    //  VISUAL IDENTITY (Email Branding)
    // ──────────────────────────────────────────────────────────────────────────
    'visual_identity' => [
        /*
         * Route names guarded by this feature.
         */
        'guarded_routes' => [
            'email-settings.index',
            'email-settings.branding',
            'email-settings.template',
            'email-settings.preview',
        ],

        /*
         * When visual identity is disabled, emails fall back to these
         * platform-level defaults (pulled from system settings when available).
         */
        'fallback' => [
            /*
             * Setting key inside the admin system_settings table / config.
             * The branding service reads: SystemSetting::get('platform_sender_name')
             * If not found, these static defaults are used.
             */
            'sender_name'  => env('MAIL_FROM_NAME',    config('mail.from.name',    'MaanInvite')),
            'sender_email' => env('MAIL_FROM_ADDRESS',  config('mail.from.address', 'noreply@maaninvite.com')),
            'brand_name'   => env('APP_NAME', 'MaanInvite'),

            /*
             * Platform header image used when company has no branding.
             * Relative to public/ or a full URL.
             */
            'header_image_url' => null, // null → use colour-block header instead
            'primary_color'    => '#0f8f83',
            'secondary_color'  => '#1F2937',
        ],
    ],

    // ──────────────────────────────────────────────────────────────────────────
    //  COMPANY LOGO IMAGE (used in email branding / visual identity)
    // ──────────────────────────────────────────────────────────────────────────
    'company_logo_image' => [
        'mimes'          => 'jpg,jpeg,png,webp,svg',
        'max_kb'         => 2048,
        'storage_folder' => 'uploads/logos',
    ],

    // ──────────────────────────────────────────────────────────────────────────
    //  EVENT HEADER IMAGE
    // ──────────────────────────────────────────────────────────────────────────
    'event_header_image' => [
        /*
         * Accepted file formats.
         * These are passed directly to Laravel's 'mimes' validation rule.
         */
        'mimes' => 'jpg,jpeg,png,webp',

        /*
         * Maximum file size in kilobytes.
         * 2 048 KB = 2 MB
         */
        'max_kb' => 2048,

        /*
         * Minimum dimensions (px). Set to null to skip enforcement.
         */
        'min_width'  => 800,
        'min_height' => 200,

        /*
         * Maximum dimensions (px). Set to null to skip enforcement.
         */
        'max_width'  => null,
        'max_height' => null,

        /*
         * Ideal / recommended dimensions shown in the UI upload hint.
         */
        'ideal_w' => 1200,
        'ideal_h' => 400,

        /*
         * Storage folder relative to public/ directory.
         */
        'storage_folder' => 'uploads/event-images/headers',

        /*
         * Fallback when the feature is disabled or no image is uploaded.
         * 'color_block' → render a solid colour div using the brand primary colour
         * 'none'        → omit the header region entirely
         */
        'fallback_mode' => 'color_block',
    ],

    // ──────────────────────────────────────────────────────────────────────────
    //  EVENT FOOTER IMAGE
    // ──────────────────────────────────────────────────────────────────────────
    'event_footer_image' => [
        'mimes'   => 'jpg,jpeg,png,webp',
        'max_kb'  => 1024,

        'min_width'  => 600,
        'min_height' => 100,
        'max_width'  => null,
        'max_height' => null,

        'ideal_w' => 1200,
        'ideal_h' => 260,

        'storage_folder' => 'uploads/event-images/footers',

        'fallback_mode' => 'color_block',
    ],

];
