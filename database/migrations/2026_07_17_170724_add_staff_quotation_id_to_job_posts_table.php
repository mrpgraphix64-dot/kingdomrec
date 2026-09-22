<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Links a job posting to the specific shift/role (StaffQuotation) it was
     * created to fill, so applicants who apply and get Qualified against it
     * can be surfaced when admin assigns staff to that shift's ShiftSlots.
     */
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('job_posts', 'staff_quotation_id')) {
                $table->foreignId('staff_quotation_id')->nullable()->after('id')->constrained('staff_quotations')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            if (Schema::hasColumn('job_posts', 'staff_quotation_id')) {
                $table->dropForeign(['staff_quotation_id']);
                $table->dropColumn('staff_quotation_id');
            }
        });
    }
};
