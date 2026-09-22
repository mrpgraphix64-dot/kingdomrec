<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_post_id');
            $table->unsignedBigInteger('applicant_id');
            $table->string('status')->default('Pending'); // Pending, Reviewed, Qualified, Rejected
            $table->text('cover_letter')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('cascade');
            $table->unique(['job_post_id', 'applicant_id']); // Prevent duplicate applications
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
