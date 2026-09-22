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
        Schema::table('rate_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('rate_cards', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('type');
            }
            if (!Schema::hasColumn('rate_cards', 'venue_id')) {
                $table->unsignedBigInteger('venue_id')->nullable()->after('sub_category');
            }
            if (!Schema::hasColumn('rate_cards', 'hours')) {
                $table->decimal('hours', 8, 2)->nullable()->after('venue_id');
            }
            if (!Schema::hasColumn('rate_cards', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->nullable()->after('hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rate_cards', function (Blueprint $table) {
            $table->dropColumn(['sub_category', 'venue_id', 'hours', 'total_amount']);
        });
    }
};
