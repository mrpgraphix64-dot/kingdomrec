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
        Schema::table('ratings', function (Blueprint $table) {
            // Make applicant_id nullable to support event-wide ratings
            $table->unsignedBigInteger('applicant_id')->nullable()->change();
            
            // Add new columns
            $table->unsignedBigInteger('event_booking_id')->nullable()->after('event_assignment_id');
            $table->tinyInteger('staff_performance_rating')->nullable()->after('rating');

            // Add foreign key
            $table->foreign('event_booking_id')->references('id')->on('event_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['event_booking_id']);
            $table->dropColumn(['event_booking_id', 'staff_performance_rating']);
            $table->unsignedBigInteger('applicant_id')->nullable(false)->change();
        });
    }
};
