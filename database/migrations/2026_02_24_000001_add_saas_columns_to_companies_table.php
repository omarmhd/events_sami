<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaasColumnsToCompaniesTable extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('name');
            }

            if (!Schema::hasColumn('companies', 'phone')) {
                $table->string('phone')->nullable()->after('contact_email');
            }

            if (!Schema::hasColumn('companies', 'annual_events_estimate')) {
                $table->unsignedInteger('annual_events_estimate')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('companies', 'trial_started_at')) {
                $table->timestamp('trial_started_at')->nullable()->after('custom_domain');
            }

            if (!Schema::hasColumn('companies', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
            }

            if (!Schema::hasColumn('companies', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('trial_ends_at');
            }

            if (!Schema::hasColumn('companies', 'billing_email')) {
                $table->string('billing_email')->nullable()->after('onboarding_completed_at');
            }

            if (!Schema::hasColumn('companies', 'timezone')) {
                $table->string('timezone', 64)->default('Asia/Riyadh')->after('billing_email');
            }

            if (!Schema::hasColumn('companies', 'current_plan_code')) {
                $table->string('current_plan_code', 32)->nullable()->after('timezone');
            }

            if (!Schema::hasColumn('companies', 'settings')) {
                $table->json('settings')->nullable()->after('current_plan_code');
            }

            if (!Schema::hasColumn('companies', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->after('settings')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'owner_user_id')) {
                $table->dropConstrainedForeignId('owner_user_id');
            }

            $columns = [
                'contact_email',
                'phone',
                'annual_events_estimate',
                'trial_started_at',
                'trial_ends_at',
                'onboarding_completed_at',
                'billing_email',
                'timezone',
                'current_plan_code',
                'settings',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
