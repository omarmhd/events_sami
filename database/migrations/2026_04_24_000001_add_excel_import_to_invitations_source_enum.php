<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand the source enum to include excel_import alongside existing values
        DB::statement("ALTER TABLE `event_invitations` MODIFY COLUMN `source` ENUM('manual', 'csv_import', 'excel_import', 'resend') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        // Revert excel_import rows to manual before shrinking enum
        DB::statement("UPDATE `event_invitations` SET `source` = 'manual' WHERE `source` = 'excel_import'");
        DB::statement("ALTER TABLE `event_invitations` MODIFY COLUMN `source` ENUM('manual', 'csv_import', 'resend') NOT NULL DEFAULT 'manual'");
    }
};
