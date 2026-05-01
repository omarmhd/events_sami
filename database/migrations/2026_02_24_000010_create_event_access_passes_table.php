<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAccessPassesTable extends Migration
{
    public function up()
    {
        Schema::create('event_access_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->morphs('passable');
            $table->string('holder_name');
            $table->string('holder_email')->nullable();
            $table->enum('type', ['main', 'guest'])->default('main');
            $table->uuid('token')->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'is_used']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_access_passes');
    }
}
