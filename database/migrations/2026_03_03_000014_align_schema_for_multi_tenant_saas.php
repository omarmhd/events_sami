<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlignSchemaForMultiTenantSaas extends Migration
{
    public function up()
    {
        $this->updateUsersTable();
        $this->updateEventsTable();
        $this->updateEventInvitationsTable();
        $this->updatePublicRegistrationsTable();
        $this->updateEventAccessPassesTable();
        $this->updateTicketsTable();
        $this->updateBrandingTable();
        $this->createTicketCheckinLogsTable();
    }

    public function down()
    {
        if (Schema::hasTable('ticket_checkin_logs')) {
            Schema::drop('ticket_checkin_logs');
        }
    }

    protected function updateUsersTable(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('company_id')->constrained('companies')->nullOnDelete();
            }
        });

        DB::table('users')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updateEventsTable(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('company_id')->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('events', 'slug')) {
                $table->string('slug')->nullable()->after('event_slug');
            }

            if (!Schema::hasColumn('events', 'start_datetime')) {
                $table->dateTime('start_datetime')->nullable()->after('to_time');
            }

            if (!Schema::hasColumn('events', 'end_datetime')) {
                $table->dateTime('end_datetime')->nullable()->after('start_datetime');
            }

            if (!Schema::hasColumn('events', 'capacity')) {
                $table->unsignedInteger('capacity')->nullable()->after('end_datetime');
            }

            if (!Schema::hasColumn('events', 'header_image_path')) {
                $table->string('header_image_path')->nullable()->after('description_en');
            }

            if (!Schema::hasColumn('events', 'requires_manual_approval')) {
                $table->boolean('requires_manual_approval')->default(true)->after('footer_image_path');
            }

            if (!Schema::hasColumn('events', 'allow_reentry')) {
                $table->boolean('allow_reentry')->default(false)->after('requires_manual_approval');
            }

            if (!Schema::hasColumn('events', 'dynamic_form_schema')) {
                $table->json('dynamic_form_schema')->nullable()->after('allow_reentry');
            }

            if (!Schema::hasColumn('events', 'rejection_email_enabled')) {
                $table->boolean('rejection_email_enabled')->default(false)->after('dynamic_form_schema');
            }
        });

        DB::table('events')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updateEventInvitationsTable(): void
    {
        if (!Schema::hasTable('event_invitations')) {
            return;
        }

        Schema::table('event_invitations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_invitations', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('company_id')->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('event_invitations', 'response_token')) {
                $table->uuid('response_token')->nullable()->unique()->after('invitation_token');
            }
        });

        DB::table('event_invitations')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updatePublicRegistrationsTable(): void
    {
        if (!Schema::hasTable('public_event_registrations')) {
            return;
        }

        Schema::table('public_event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('public_event_registrations', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('company_id')->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('public_event_registrations', 'form_payload')) {
                $table->json('form_payload')->nullable()->after('guest_nationality');
            }

            if (!Schema::hasColumn('public_event_registrations', 'guests_count')) {
                $table->unsignedTinyInteger('guests_count')->default(1)->after('status');
            }
        });

        DB::table('public_event_registrations')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updateEventAccessPassesTable(): void
    {
        if (!Schema::hasTable('event_access_passes')) {
            return;
        }

        Schema::table('event_access_passes', function (Blueprint $table) {
            if (!Schema::hasColumn('event_access_passes', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('company_id')->constrained('companies')->cascadeOnDelete();
            }
        });

        DB::table('event_access_passes')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updateTicketsTable(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('tickets', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('organization_id')->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('tickets', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('company_id')->constrained('events')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('tickets', 'source_type')) {
                $table->string('source_type')->nullable()->after('event_id');
            }

            if (!Schema::hasColumn('tickets', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }

            if (!Schema::hasColumn('tickets', 'holder_name')) {
                $table->string('holder_name')->nullable()->after('source_id');
            }

            if (!Schema::hasColumn('tickets', 'holder_email')) {
                $table->string('holder_email')->nullable()->after('holder_name');
            }

            if (!Schema::hasColumn('tickets', 'guest_count')) {
                $table->unsignedTinyInteger('guest_count')->default(1)->after('holder_email');
            }

            if (!Schema::hasColumn('tickets', 'token')) {
                $table->uuid('token')->nullable()->unique()->after('guest_count');
            }

            if (!Schema::hasColumn('tickets', 'status')) {
                $table->string('status', 20)->default('issued')->after('token');
            }

            if (!Schema::hasColumn('tickets', 'checked_in_count')) {
                $table->unsignedInteger('checked_in_count')->default(0)->after('checked_in_at');
            }

            if (!Schema::hasColumn('tickets', 'qr_payload')) {
                $table->longText('qr_payload')->nullable()->after('checked_in_count');
            }
        });

        DB::table('tickets')
            ->whereNull('organization_id')
            ->whereNotNull('company_id')
            ->update(['organization_id' => DB::raw('company_id')]);
    }

    protected function updateBrandingTable(): void
    {
        if (!Schema::hasTable('company_brandings')) {
            return;
        }

        Schema::table('company_brandings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_brandings', 'font_family')) {
                $table->string('font_family', 120)->nullable()->after('secondary_color');
            }

            if (!Schema::hasColumn('company_brandings', 'footer_text')) {
                $table->text('footer_text')->nullable()->after('footer_html');
            }
        });
    }

    protected function createTicketCheckinLogsTable(): void
    {
        if (Schema::hasTable('ticket_checkin_logs')) {
            return;
        }

        Schema::create('ticket_checkin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('result', 30);
            $table->string('scanned_token')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'event_id', 'created_at'], 'ticket_checkin_logs_org_event_time_idx');
        });
    }
}

