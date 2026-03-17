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
        Schema::create('form_answer_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained('form_answers')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('form_field_options')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['answer_id', 'option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_answer_selections');
    }
};
