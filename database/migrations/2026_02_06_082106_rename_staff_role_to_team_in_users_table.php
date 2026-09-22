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
        // Update existing users with role 'staff' to 'team'
        DB::table('users')
            ->where('role', 'staff')
            ->update(['role' => 'team']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'team' role back to 'staff' (careful if we have legitimate new 'team' roles)
        DB::table('users')
            ->where('role', 'team')
            ->update(['role' => 'staff']);
    }
};
