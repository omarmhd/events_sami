<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 60)->default('general');
            $table->timestamps();
        });

        // Seed default values
        DB::table('system_settings')->insert([
            ['key' => 'platform_name',     'value' => 'MaanInvite',      'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'platform_logo_url', 'value' => null,              'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'primary_color',     'value' => '#0f8f83',         'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'secondary_color',   'value' => '#f59e0b',         'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'support_email',     'value' => 'support@maaninvite.com', 'group' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'trial_days',        'value' => '14',              'group' => 'limits',   'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode',  'value' => '0',               'group' => 'system',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
