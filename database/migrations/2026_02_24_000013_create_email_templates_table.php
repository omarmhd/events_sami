<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->string('template_type', 50);
            $table->string('name')->nullable();
            $table->text('subject_template')->nullable();
            $table->longText('body_template');
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'template_type']);
            $table->unique(['company_id', 'event_id', 'template_type'], 'email_templates_company_event_type_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
}
