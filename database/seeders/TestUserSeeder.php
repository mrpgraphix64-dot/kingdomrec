<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Applicant;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Master Admin']);

        // 2. Partner User
        $partner = User::updateOrCreate(
            ['email' => 'partner@test.com'],
            [
                'name' => 'Partner User',
                'company_name' => 'Test Partner Corp',
                'password' => Hash::make('password'),
                'role' => 'partner',
                'email_verified_at' => now(),
            ]
        );

        // 3. Applicant User
        $applicant = User::updateOrCreate(
            ['email' => 'applicant@test.com'],
            [
                'name' => 'Applicant User',
                'password' => Hash::make('password'),
                'role' => 'applicant',
                'email_verified_at' => now(),
            ]
        );
        
        // Ensure Applicant profile exists for the applicant
        Applicant::updateOrCreate(
            ['email' => 'applicant@test.com'],
            [
                'name' => 'Applicant User',
                'phone' => '1234567890',
                'location' => 'London',
                'role' => 'applicant',
                'status' => 'Pending',
            ]
        );
    }
}
