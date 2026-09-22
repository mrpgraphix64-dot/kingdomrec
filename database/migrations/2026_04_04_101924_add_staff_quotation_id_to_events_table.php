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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_quotation_id')->nullable()->after('id');
            // Assuming staff_quotations is the table name
            $table->foreign('staff_quotation_id')
                  ->references('id')
                  ->on('staff_quotations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['staff_quotation_id']);
            $table->dropColumn('staff_quotation_id');
        });
    }
};
