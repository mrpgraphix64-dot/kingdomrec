<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;

class FeaturedApplicantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing dummy applicants to avoid duplicate key conflicts
        Applicant::whereIn('email', [
            'alexandra.davies@example.com',
            'marcus.sterling@example.com',
            'elena.rostova@example.com',
            'david.chen@example.com'
        ])->delete();

        User::whereIn('email', [
            'alexandra.davies@example.com',
            'marcus.sterling@example.com',
            'elena.rostova@example.com',
            'david.chen@example.com'
        ])->delete();

        $dummyApplicants = [
            [
                'name' => 'Alexandra Davies',
                'email' => 'alexandra.davies@example.com',
                'role' => 'Event Coordinator',
                'sub_category' => 'Events',
                'location' => 'London',
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
                'phone' => '07700900077',
            ],
            [
                'name' => 'Marcus Sterling',
                'email' => 'marcus.sterling@example.com',
                'role' => 'SIA Security Supervisor',
                'sub_category' => 'Security',
                'location' => 'Birmingham',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=800&q=80',
                'phone' => '07700900088',
            ],
            [
                'name' => 'Elena Rostova',
                'email' => 'elena.rostova@example.com',
                'role' => 'Senior Hospitality Lead',
                'sub_category' => 'Hospitality',
                'location' => 'Manchester',
                'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80',
                'phone' => '07700900099',
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@example.com',
                'role' => 'Head Chef',
                'sub_category' => 'Hospitality',
                'location' => 'London',
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=800&q=80',
                'phone' => '07700900111',
            ],
        ];

        foreach ($dummyApplicants as $data) {
            // Create user
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'applicant',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            // Create applicant profile
            Applicant::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'sub_category' => $data['sub_category'],
                    'location' => $data['location'],
                    'image' => $data['image'],
                    'phone' => $data['phone'],
                    'status' => 'Shortlisted',
                    'image_position' => 'center',
                ]
            );
        }
    }
}
