<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ── تجريبي ────────────────────────────────────────────────────────────
            [
                'code'                    => 'trial',
                'name'                    => 'تجريبي | Trial',
                'description'             => 'تجربة مجانية لمدة 15 يومًا — فعاليتان كحد أقصى بـ 10 مدعوين لكل فعالية.',
                'annual_price'            => 0,
                'per_event_price'         => 0,
                'annual_event_limit'      => 2,
                'per_event_invitee_limit' => 10,
                'includes_csv_import'     => false,
                'includes_bulk_resend'    => false,
                'includes_customization'  => false,
                'highlight_label'         => null,
                'is_active'               => true,
                'sort_order'              => 0,
            ],

            // ── مبتدئ ─────────────────────────────────────────────────────────────
            [
                'code'                    => 'starter',
                'name'                    => 'مبتدئ | Starter',
                'description'             => 'مثالي للفرق الصغيرة التي تدير فعاليات منتظمة بسير عمل قياسي.',
                'annual_price'            => 4999,
                'per_event_price'         => 650,
                'annual_event_limit'      => 12,
                'per_event_invitee_limit' => 250,
                'includes_csv_import'     => false,
                'includes_bulk_resend'    => false,
                'includes_customization'  => false,
                'highlight_label'         => null,
                'is_active'               => true,
                'sort_order'              => 10,
            ],

            // ── احترافي ───────────────────────────────────────────────────────────
            [
                'code'                    => 'professional',
                'name'                    => 'احترافي | Professional',
                'description'             => 'عمليات عالية الكثافة مع أدوات الأتمتة والإرسال الجماعي والتقارير المتقدمة.',
                'annual_price'            => 11999,
                'per_event_price'         => 450,
                'annual_event_limit'      => 40,
                'per_event_invitee_limit' => 1000,
                'includes_csv_import'     => true,
                'includes_bulk_resend'    => true,
                'includes_customization'  => false,
                'highlight_label'         => 'الأكثر شعبية',
                'is_active'               => true,
                'sort_order'              => 20,
            ],

            // ── مؤسسي ────────────────────────────────────────────────────────────
            [
                'code'                    => 'enterprise',
                'name'                    => 'مؤسسي | Enterprise',
                'description'             => 'تكاملات مخصصة، اتفاقيات مستوى الخدمة، مدير حساب مخصص، ودعم على مدار الساعة.',
                'annual_price'            => 0,
                'per_event_price'         => null,
                'annual_event_limit'      => null,
                'per_event_invitee_limit' => null,
                'includes_csv_import'     => true,
                'includes_bulk_resend'    => true,
                'includes_customization'  => true,
                'highlight_label'         => 'تواصل معنا',
                'is_active'               => true,
                'sort_order'              => 30,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['code' => $plan['code']],
                $plan
            );
        }

        $this->command->info('✅  تم تحديث خطط الاشتراك بنجاح ('.count($plans).' خطط)');
    }
}
