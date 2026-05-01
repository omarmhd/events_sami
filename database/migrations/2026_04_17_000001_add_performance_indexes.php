<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds missing performance and uniqueness indexes:
     *   1. Unique composite index on events(company_id, event_slug) — scoped slug uniqueness per tenant
     *   2. Index on event_invitations(company_id) — speeds up company-scoped invitation queries
     *   3. Index on event_invitations(company_id, status) — speeds up status-filtered invitation counts/stats
     *   4. Index on company_brandings(company_id) — already has unique constraint in the create migration,
     *      added here as a safety net for older DB instances that may have missed it
     *   5. Index on events(company_id) — scoped event lookups used on every dashboard page
     */
    public function up(): void
    {
        // ── 1. Unique slug per company on events ─────────────────────────────
        // Skips safely if already present (e.g., fresh installs via the create migration).
        if (Schema::hasTable('events')) {
            $this->addIndexIfMissing('events', ['company_id', 'event_slug'], 'events_company_id_event_slug_unique', unique: true);
            $this->addIndexIfMissing('events', ['company_id'], 'events_company_id_index');
        }

        // ── 2. Invitation indexes ─────────────────────────────────────────────
        if (Schema::hasTable('event_invitations')) {
            $this->addIndexIfMissing('event_invitations', ['company_id'], 'event_invitations_company_id_index');
            $this->addIndexIfMissing('event_invitations', ['company_id', 'status'], 'event_invitations_company_id_status_index');
            $this->addIndexIfMissing('event_invitations', ['event_id'], 'event_invitations_event_id_index');
        }

        // ── 3. Company branding index ─────────────────────────────────────────
        if (Schema::hasTable('company_brandings')) {
            $this->addIndexIfMissing('company_brandings', ['company_id'], 'company_brandings_company_id_unique', unique: true);
        }

        // ── 4. Invitation QR indexes ──────────────────────────────────────────
        if (Schema::hasTable('invitation_qrs')) {
            $this->addIndexIfMissing('invitation_qrs', ['token'], 'invitation_qrs_token_index');
            $this->addIndexIfMissing('invitation_qrs', ['event_invitation_id'], 'invitation_qrs_event_invitation_id_index');
            $this->addIndexIfMissing('invitation_qrs', ['is_used'], 'invitation_qrs_is_used_index');
        }

        // ── 5. Event access passes index ──────────────────────────────────────
        if (Schema::hasTable('event_access_passes')) {
            $this->addIndexIfMissing('event_access_passes', ['token'], 'event_access_passes_token_index');
            $this->addIndexIfMissing('event_access_passes', ['company_id'], 'event_access_passes_company_id_index');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'events_company_id_event_slug_unique');
            $this->dropIndexIfExists($table, 'events_company_id_index');
        });

        Schema::table('event_invitations', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'event_invitations_company_id_index');
            $this->dropIndexIfExists($table, 'event_invitations_company_id_status_index');
            $this->dropIndexIfExists($table, 'event_invitations_event_id_index');
        });

        Schema::table('company_brandings', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'company_brandings_company_id_unique');
        });

        Schema::table('invitation_qrs', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'invitation_qrs_token_index');
            $this->dropIndexIfExists($table, 'invitation_qrs_event_invitation_id_index');
            $this->dropIndexIfExists($table, 'invitation_qrs_is_used_index');
        });

        Schema::table('event_access_passes', function (Blueprint $table) {
            $this->dropIndexIfExists($table, 'event_access_passes_token_index');
            $this->dropIndexIfExists($table, 'event_access_passes_company_id_index');
        });
    }

    /**
     * Add an index only if it doesn't already exist in the DB.
     * Uses information_schema to detect existing indexes safely.
     */
    private function addIndexIfMissing(
        string $table,
        array  $columns,
        string $indexName,
        bool   $unique = false
    ): void {
        $dbName = DB::getDatabaseName();

        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();

        if ($exists) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName, $unique) {
            if ($unique) {
                $blueprint->unique($columns, $indexName);
            } else {
                $blueprint->index($columns, $indexName);
            }
        });
    }

    /**
     * Drop index if it exists, swallowing any "does not exist" errors.
     */
    private function dropIndexIfExists(Blueprint $table, string $indexName): void
    {
        try {
            $table->dropIndex($indexName);
        } catch (\Exception) {
            // Index did not exist — safe to ignore.
        }
    }
};
