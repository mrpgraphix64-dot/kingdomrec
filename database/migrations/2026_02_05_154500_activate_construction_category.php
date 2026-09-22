<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('job_categories')
            ->where('name', 'Construction')
            ->update(['status' => 'Active']);
            
        // Also ensure any sub-categories if needed, but the main issue is the category visibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed really, but for correctness
        DB::table('job_categories')
            ->where('name', 'Construction')
            ->update(['status' => 'Review']);
    }
};
