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
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!Schema::hasIndex('job_applications', ['status'])) {
                    $table->index('status');
                }
            });
        }

        if (Schema::hasTable('shift_slots')) {
            Schema::table('shift_slots', function (Blueprint $table) {
                if (!Schema::hasIndex('shift_slots', ['status'])) {
                    $table->index('status');
                }
                if (!Schema::hasIndex('shift_slots', ['shift_date'])) {
                    $table->index('shift_date');
                }
            });
        }

        if (Schema::hasTable('applicants')) {
            Schema::table('applicants', function (Blueprint $table) {
                if (!Schema::hasIndex('applicants', ['status'])) {
                    $table->index('status');
                }
            });
        }

        if (Schema::hasTable('event_bookings')) {
            Schema::table('event_bookings', function (Blueprint $table) {
                if (!Schema::hasIndex('event_bookings', ['status'])) {
                    $table->index('status');
                }
                if (!Schema::hasIndex('event_bookings', ['start_date'])) {
                    $table->index('start_date');
                }
                if (!Schema::hasIndex('event_bookings', ['end_date'])) {
                    $table->index('end_date');
                }
            });
        }

        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                if (!Schema::hasIndex('job_posts', ['status'])) {
                    $table->index('status');
                }
                if (!Schema::hasIndex('job_posts', ['deadline'])) {
                    $table->index('deadline');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications') && Schema::hasIndex('job_applications', ['status'])) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasTable('shift_slots')) {
            Schema::table('shift_slots', function (Blueprint $table) {
                if (Schema::hasIndex('shift_slots', ['status'])) {
                    $table->dropIndex(['status']);
                }
                if (Schema::hasIndex('shift_slots', ['shift_date'])) {
                    $table->dropIndex(['shift_date']);
                }
            });
        }

        if (Schema::hasTable('applicants') && Schema::hasIndex('applicants', ['status'])) {
            Schema::table('applicants', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasTable('event_bookings')) {
            Schema::table('event_bookings', function (Blueprint $table) {
                if (Schema::hasIndex('event_bookings', ['status'])) {
                    $table->dropIndex(['status']);
                }
                if (Schema::hasIndex('event_bookings', ['start_date'])) {
                    $table->dropIndex(['start_date']);
                }
                if (Schema::hasIndex('event_bookings', ['end_date'])) {
                    $table->dropIndex(['end_date']);
                }
            });
        }

        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                if (Schema::hasIndex('job_posts', ['status'])) {
                    $table->dropIndex(['status']);
                }
                if (Schema::hasIndex('job_posts', ['deadline'])) {
                    $table->dropIndex(['deadline']);
                }
            });
        }
    }
};
