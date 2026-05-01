<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE companies MODIFY COLUMN status ENUM('trial', 'active', 'suspended') NOT NULL DEFAULT 'trial'");
    }

    public function down(): void
    {
        // Preserve reversibility: map trial records to active before shrinking enum.
        DB::statement("UPDATE companies SET status = 'active' WHERE status = 'trial'");
        DB::statement("ALTER TABLE companies MODIFY COLUMN status ENUM('active', 'suspended') NOT NULL");
    }
};
