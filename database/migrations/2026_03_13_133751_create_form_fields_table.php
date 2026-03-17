<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();

            $table->string('type');
            // short_text, long_text, number, email, dropdown, radio, checkbox, date, rating, file

            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->string('placeholder')->nullable();

            $table->unsignedInteger('sort_order')->default(1);

            // JSON for dynamic validation rules, min/max, regex, file config, etc.
            $table->json('validation_rules')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['form_id', 'sort_order']);
            $table->index(['form_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
