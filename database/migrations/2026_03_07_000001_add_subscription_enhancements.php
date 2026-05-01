<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to company_subscriptions if not exist
        if (Schema::hasTable('company_subscriptions')) {
            Schema::table('company_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('company_subscriptions', 'trial_started_at')) {
                    $table->timestamp('trial_started_at')->nullable();
                }
                if (!Schema::hasColumn('company_subscriptions', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable();
                }
                if (!Schema::hasColumn('company_subscriptions', 'annual_events_limit')) {
                    $table->integer('annual_events_limit')->default(1);
                }
                if (!Schema::hasColumn('company_subscriptions', 'max_invites_per_event')) {
                    $table->integer('max_invites_per_event')->default(10);
                }
            });
        }

        // Create subscription_needs_assessments table
        if (!Schema::hasTable('subscription_needs_assessments')) {
            Schema::create('subscription_needs_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained();
                $table->integer('annual_events_estimate')->nullable();
                $table->integer('average_attendance')->nullable();
                $table->boolean('requires_custom_development')->default(false);
                $table->text('notes')->nullable();
                $table->string('recommended_plan')->nullable();
                $table->timestamp('assessed_at')->nullable();
                $table->timestamps();
            });
        }

        // Create subscription_invoices table
        if (!Schema::hasTable('subscription_invoices')) {
            Schema::create('subscription_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained();
                $table->string('invoice_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('currency')->default('USD');
                $table->string('status')->default('issued'); // issued, paid, overdue, cancelled
                $table->text('description')->nullable();
                $table->timestamp('issued_at')->nullable();
                $table->timestamp('due_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Create feature_access table for feature gating
        if (!Schema::hasTable('feature_access')) {
            Schema::create('feature_access', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained();
                $table->string('feature_code')->unique();
                $table->string('feature_name');
                $table->boolean('is_enabled')->default(false);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_access');
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscription_needs_assessments');

        if (Schema::hasTable('company_subscriptions')) {
            Schema::table('company_subscriptions', function (Blueprint $table) {
                $table->dropColumnIfExists('trial_started_at');
                $table->dropColumnIfExists('trial_ends_at');
                $table->dropColumnIfExists('annual_events_limit');
                $table->dropColumnIfExists('max_invites_per_event');
            });
        }
    }
};
