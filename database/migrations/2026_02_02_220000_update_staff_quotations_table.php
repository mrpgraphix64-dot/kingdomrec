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
            $table->string('category')->nullable()->after('role');
            $table->string('sub_category')->nullable()->after('category');
            $table->string('shift_hours')->nullable()->after('quantity');
            $table->date('start_date')->nullable()->after('amount');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('total_days')->default(0)->after('end_date');
            $table->string('venue')->nullable()->after('total_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_quotations', function (Blueprint $table) {
            $table->dropColumn(['category', 'sub_category', 'shift_hours', 'start_date', 'end_date', 'total_days', 'venue']);
        });
    }
};
