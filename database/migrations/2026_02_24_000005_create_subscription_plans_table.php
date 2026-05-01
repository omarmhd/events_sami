<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('annual_price', 10, 2)->default(0);
            $table->decimal('per_event_price', 10, 2)->nullable();
            $table->unsignedInteger('annual_event_limit')->nullable();
            $table->unsignedInteger('per_event_invitee_limit')->nullable();
            $table->boolean('includes_csv_import')->default(false);
            $table->boolean('includes_bulk_resend')->default(false);
            $table->boolean('includes_customization')->default(false);
            $table->string('highlight_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}
