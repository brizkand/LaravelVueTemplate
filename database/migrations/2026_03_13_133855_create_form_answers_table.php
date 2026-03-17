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
        Schema::create('form_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('form_responses')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('form_fields')->cascadeOnDelete();

            // Normalized answer storage
            $table->longText('value_text')->nullable();
            $table->decimal('value_number', 12, 2)->nullable();
            $table->date('value_date')->nullable();
            $table->string('value_email')->nullable();
            $table->string('value_file_path')->nullable();

            // For checkbox/multi-select answers
            $table->json('value_json')->nullable();

            $table->timestamps();

            $table->unique(['response_id', 'field_id']);
            $table->index('field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_answers');
    }
};
