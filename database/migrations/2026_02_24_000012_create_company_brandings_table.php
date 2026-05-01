<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBrandingsTable extends Migration
{
    public function up()
    {
        Schema::create('company_brandings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('brand_name')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('header_image_url')->nullable();
            $table->string('primary_color', 20)->default('#DABC9A');
            $table->string('secondary_color', 20)->default('#1F2937');
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('reply_to_email')->nullable();
            $table->longText('header_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_brandings');
    }
}
