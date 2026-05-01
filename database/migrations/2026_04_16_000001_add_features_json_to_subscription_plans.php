<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeaturesJsonToSubscriptionPlans extends Migration
{
    public function up()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            // Flexible JSON feature list — replaces hardcoded boolean columns for display+gating
            $table->json('features')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
}
