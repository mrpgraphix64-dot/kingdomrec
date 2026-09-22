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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id'); // from users table
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('event_assignment_id')->nullable();
            $table->tinyInteger('rating')->default(0); // 1-5
            $table->text('review')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->foreign('event_assignment_id')->references('id')->on('event_assignments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
