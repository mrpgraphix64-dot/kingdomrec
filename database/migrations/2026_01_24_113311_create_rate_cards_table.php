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
        Schema::create('rate_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('hourly_rate', 8, 2);
            $table->string('currency')->default('GBP');
            $table->decimal('overtime_rate', 8, 2)->nullable();
            $table->string('type'); // Security, Hospitality, Management...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_cards');
    }
};
