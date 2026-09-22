<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('applicant_id');
            $table->string('status')->default('Assigned'); // Assigned, Confirmed, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->unique(['event_id', 'applicant_id']); // Prevent duplicate assignments
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_assignments');
    }
};
