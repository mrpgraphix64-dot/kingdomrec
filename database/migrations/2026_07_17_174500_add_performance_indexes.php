<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * These columns are filtered/sorted on constantly across the admin panel
     * (status badges, date-range queries) but were never indexed, so every
     * list page does a full table scan on them. Harmless at today's data
     * volume, but this is exactly what turns into real slowdowns as the
     * tables grow.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('shift_slots', function (Blueprint $table) {
            $table->index('status');
            $table->index('shift_date');
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('event_bookings', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });

        Schema::table('job_posts', function (Blueprint $table) {
            $table->index('status');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('shift_slots', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['shift_date']);
        });

        Schema::table('applicants', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('event_bookings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
        });

        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['deadline']);
        });
    }
};
