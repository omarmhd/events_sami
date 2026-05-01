<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrUpdateEventsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('event_slug')->nullable();
                $table->string('name')->nullable();
                $table->string('title')->nullable();
                $table->enum('event_type', ['private', 'public'])->default('private');
                $table->enum('registration_mode', ['private_invites', 'public_link'])->default('private_invites');
                $table->date('date')->nullable();
                $table->time('from_time')->nullable();
                $table->time('to_time')->nullable();
                $table->string('location_name')->nullable();
                $table->string('google_map_url')->nullable();
                $table->text('description')->nullable();
                $table->text('description_en')->nullable();
                $table->string('footer_image_path')->nullable();
                $table->string('invitation_email_subject')->nullable();
                $table->longText('invitation_email_body')->nullable();
                $table->string('confirmation_email_subject')->nullable();
                $table->longText('confirmation_email_body')->nullable();
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'event_type']);
                $table->index(['company_id', 'status']);
                $table->unique(['company_id', 'event_slug']);
            });

            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('events', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('events', 'event_slug')) {
                $table->string('event_slug')->nullable();
            }

            if (!Schema::hasColumn('events', 'title')) {
                $table->string('title')->nullable();
            }

            if (!Schema::hasColumn('events', 'event_type')) {
                $table->enum('event_type', ['private', 'public'])->default('private');
            }

            if (!Schema::hasColumn('events', 'registration_mode')) {
                $table->enum('registration_mode', ['private_invites', 'public_link'])->default('private_invites');
            }

            if (!Schema::hasColumn('events', 'location_name')) {
                $table->string('location_name')->nullable();
            }

            if (!Schema::hasColumn('events', 'google_map_url')) {
                $table->string('google_map_url')->nullable();
            }

            if (!Schema::hasColumn('events', 'description_en')) {
                $table->text('description_en')->nullable();
            }

            if (!Schema::hasColumn('events', 'footer_image_path')) {
                $table->string('footer_image_path')->nullable();
            }

            if (!Schema::hasColumn('events', 'invitation_email_subject')) {
                $table->string('invitation_email_subject')->nullable();
            }

            if (!Schema::hasColumn('events', 'invitation_email_body')) {
                $table->longText('invitation_email_body')->nullable();
            }

            if (!Schema::hasColumn('events', 'confirmation_email_subject')) {
                $table->string('confirmation_email_subject')->nullable();
            }

            if (!Schema::hasColumn('events', 'confirmation_email_body')) {
                $table->longText('confirmation_email_body')->nullable();
            }

            if (!Schema::hasColumn('events', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            }

            if (!Schema::hasColumn('events', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }

            if (!Schema::hasColumn('events', 'created_at') && !Schema::hasColumn('events', 'updated_at')) {
                $table->timestamps();
            }
        });

        // Existing deployments may already have indexes managed manually.
        // We avoid adding index constraints here to keep migration idempotent.
    }

    public function down()
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            if (Schema::hasColumn('events', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }

            $columns = [
                'event_slug',
                'title',
                'event_type',
                'registration_mode',
                'location_name',
                'google_map_url',
                'description_en',
                'footer_image_path',
                'invitation_email_subject',
                'invitation_email_body',
                'confirmation_email_subject',
                'confirmation_email_body',
                'status',
                'published_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
