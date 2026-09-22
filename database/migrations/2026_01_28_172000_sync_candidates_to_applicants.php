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
        // Check if candidates table exists and has data
        if (Schema::hasTable('candidates')) {
            $candidates = DB::table('candidates')->get();
            
            foreach ($candidates as $candidate) {
                // Check if applicant with same email already exists to avoid duplicates
                $exists = DB::table('applicants')->where('email', $candidate->email)->exists();
                
                if (!$exists) {
                    DB::table('applicants')->insert([
                        'id' => $candidate->id, // Keep ID if possible, or auto-increment
                        'name' => $candidate->name,
                        'role' => $candidate->role,
                        'email' => $candidate->email,
                        'phone' => $candidate->phone,
                        'location' => $candidate->location,
                        'status' => $candidate->status,
                        'cv_link' => $candidate->cv_link,
                        'image' => $candidate->image,
                        'created_at' => $candidate->created_at,
                        'updated_at' => $candidate->updated_at,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: remove data from applicants? Generally safer to do nothing.
    }
};
