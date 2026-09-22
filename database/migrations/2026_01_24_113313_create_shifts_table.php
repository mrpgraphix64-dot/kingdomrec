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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('time'); // e.g., "08:00 AM"
            $table->date('date')->nullable(); // For specific day shifting, nullable if daily template
            $table->json('staff_names')->nullable(); // Storing array of names for simplicity: ["Sarah C.", "James W."]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
