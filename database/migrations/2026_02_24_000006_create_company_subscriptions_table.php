<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->enum('status', ['trial', 'active', 'past_due', 'expired', 'canceled'])->default('trial');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->unsignedInteger('annual_event_quota')->nullable();
            $table->unsignedInteger('annual_events_used')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_subscriptions');
    }
}
