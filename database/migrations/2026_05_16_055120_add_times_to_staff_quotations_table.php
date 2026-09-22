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
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('shift_date')->nullable();
            $table->decimal('calculated_hours', 5, 2)->nullable();
            $table->string('shift_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_quotations', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'shift_date',
                'calculated_hours',
                'shift_label'
            ]);
        });
    }
};
