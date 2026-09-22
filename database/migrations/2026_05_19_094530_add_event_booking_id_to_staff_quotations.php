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
        Schema::table('staff_quotations', function (Blueprint $table) {
            $table->foreignId('event_booking_id')->nullable()->constrained('event_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_quotations', function (Blueprint $table) {
            $table->dropForeign(['event_booking_id']);
            $table->dropColumn('event_booking_id');
        });
    }
};
