<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionNeedsAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_needs_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->unsignedInteger('annual_events');
            $table->unsignedInteger('average_attendance');
            $table->boolean('needs_customization')->default(false);
            $table->string('recommended_plan_code', 32)->nullable();
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->index(['company_id', 'answered_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_needs_assessments');
    }
}
