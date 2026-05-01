<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Clean up legacy __default__ registration forms.
 *
 * The registration page always renders guest_name + guest_email natively.
 * No __default__ form record is needed anymore — events that had one assigned
 * should have registration_form_id = null so they fall back to the native fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Collect IDs of all __default__ forms.
        $defaultFormIds = DB::table('registration_forms')
            ->where('name', '__default__')
            ->pluck('id');

        if ($defaultFormIds->isNotEmpty()) {
            // Detach events that reference a __default__ form.
            DB::table('events')
                ->whereIn('registration_form_id', $defaultFormIds)
                ->update(['registration_form_id' => null]);

            // Delete the __default__ form records.
            DB::table('registration_forms')
                ->where('name', '__default__')
                ->delete();
        }
    }

    public function down(): void
    {
        // Intentionally irreversible — no meaningful rollback.
    }
};
