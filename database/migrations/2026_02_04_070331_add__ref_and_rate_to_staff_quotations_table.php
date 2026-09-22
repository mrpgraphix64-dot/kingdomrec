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
            $table->string('quotation_ref')->nullable()->after('id');
            $table->string('rate')->nullable()->after('total_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_quotations', function (Blueprint $table) {
            $table->dropColumn(['quotation_ref', 'rate']);
        });
    }
};
