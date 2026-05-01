<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add 'terminated' as a valid status for company_subscriptions.
 * The original enum was: trial, active, past_due, expired, canceled
 * We extend it to also include 'terminated'.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `company_subscriptions`
            MODIFY COLUMN `status`
                ENUM('trial','active','past_due','expired','canceled','terminated')
                NOT NULL DEFAULT 'trial'
        ");
    }

    public function down(): void
    {
        // Remove terminated rows first to avoid data truncation on rollback.
        DB::statement("
            UPDATE `company_subscriptions`
            SET `status` = 'canceled'
            WHERE `status` = 'terminated'
        ");

        DB::statement("
            ALTER TABLE `company_subscriptions`
            MODIFY COLUMN `status`
                ENUM('trial','active','past_due','expired','canceled')
                NOT NULL DEFAULT 'trial'
        ");
    }
};
