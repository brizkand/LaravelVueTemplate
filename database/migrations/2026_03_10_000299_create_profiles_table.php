<?php

use App\Models\System\Division;
use App\Models\System\Position;
use App\Models\System\Section;
use App\Models\System\User;
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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_number')->unique();
            // create a foreign key of user's id in the profiles table
            $table->foreignIdFor(model: User::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name');
            $table->foreignIdFor(Division::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignIdFor(Section::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(Position::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
