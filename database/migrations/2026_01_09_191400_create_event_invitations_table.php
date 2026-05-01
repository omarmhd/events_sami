<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('event_invitations')) {
            return;
        }

        Schema::create('event_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained("events")->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('invitee_name');
            $table->string('invitee_email')->nullable();
            $table->string('invitee_phone')->nullable();
            $table->string('invitee_position')->nullable();
            $table->string('invitee_nationality')->nullable();
            $table->uuid('invitation_token')->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedTinyInteger('allowed_guests')->default(1);
            $table->unsignedTinyInteger('selected_guests')->default(0);
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_invitations');
    }
}
