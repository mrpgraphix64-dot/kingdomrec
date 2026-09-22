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
        Schema::create('shift_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_quotation_id');
            $table->unsignedBigInteger('applicant_id')->nullable(); // Can be unassigned initially
            $table->string('role_name')->nullable();
            $table->date('shift_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('break_mins')->default(30);
            $table->decimal('rate', 8, 2)->default(0);
            $table->string('status')->default('Unassigned'); // Unassigned, Assigned, Completed
            $table->timestamps();

            // Foreign Keys
            $table->foreign('staff_quotation_id')->references('id')->on('staff_quotations')->onDelete('cascade');
            $table->foreign('applicant_id')->references('id')->on('applicants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_slots');
    }
};
