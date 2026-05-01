<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationFormsAndExtendEvents extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('registration_forms')) {
            Schema::create('registration_forms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('organization_id')->nullable()->constrained('companies')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('slug');
                $table->string('headline')->nullable();
                $table->text('intro_text')->nullable();
                $table->json('fields')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['company_id', 'slug']);
            });
        }

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'registration_form_id')) {
                $table->foreignId('registration_form_id')->nullable()->after('dynamic_form_schema')->constrained('registration_forms')->nullOnDelete();
            }

            if (!Schema::hasColumn('events', 'experience_type')) {
                $table->string('experience_type', 60)->nullable()->after('event_type');
            }

            if (!Schema::hasColumn('events', 'schedule_items')) {
                $table->json('schedule_items')->nullable()->after('capacity');
            }
        });

        Schema::table('public_event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('public_event_registrations', 'registration_form_id')) {
                $table->foreignId('registration_form_id')->nullable()->after('event_id')->constrained('registration_forms')->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('public_event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('public_event_registrations', 'registration_form_id')) {
                $table->dropConstrainedForeignId('registration_form_id');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'registration_form_id')) {
                $table->dropConstrainedForeignId('registration_form_id');
            }

            if (Schema::hasColumn('events', 'experience_type')) {
                $table->dropColumn('experience_type');
            }

            if (Schema::hasColumn('events', 'schedule_items')) {
                $table->dropColumn('schedule_items');
            }
        });

        Schema::dropIfExists('registration_forms');
    }
}