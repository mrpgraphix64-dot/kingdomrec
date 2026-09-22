<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StaffMember;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staff = [];

        foreach ($staff as $s) {
            // Check if user exists
            if (!User::where('email', $s['email'])->exists()) {
                $user = User::create([
                    'name' => $s['name'],
                    'email' => $s['email'],
                    'password' => Hash::make($s['password']),
                    'role' => 'team',
                ]);

                \App\Models\Team::create([
                    'user_id' => $user->id,
                    'name' => $s['name'],
                    'role' => $s['role'],
                    'email' => $s['email'],
                    'phone' => $s['phone'],
                ]);
            }
        }
    }
}
