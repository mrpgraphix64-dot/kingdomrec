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
        Schema::create('staff_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('client');
            $table->string('role');
            $table->integer('quantity')->default(1);
            $table->string('amount'); // e.g. "£4,500" - string for now to match UI format
            $table->string('date'); // e.g. "Oct 12, 2024"
            $table->string('status')->default('Draft'); // Draft, Sent, Approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_quotations');
    }
};
