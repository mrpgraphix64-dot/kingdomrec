<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\StaffQuotation;
use App\Models\EventBooking;
use App\Models\Shift;
use App\Models\ShiftSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantNavigationAndRouteAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_applicant_profile()
    {
        $response = $this->get(route('applicant.profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_my_applications()
    {
        $response = $this->get(route('jobs.applied'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_my_schedule()
    {
        $response = $this->get(route('applicant.schedule'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_profile_edit()
    {
        $response = $this->get(route('applicant.profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_applicant_can_access_own_pages()
    {
        $applicantUser = User::factory()->create([
            'role' => 'applicant',
            'email' => 'candidate_alice@test.com',
            'name' => 'Alice Applicant',
        ]);

        $applicant = Applicant::create([
            'name' => 'Alice Applicant',
            'email' => $applicantUser->email,
            'role' => 'Hospitality',
            'phone' => '+44 7111 222333',
            'location' => 'London',
            'status' => 'Pending',
        ]);

        $this->actingAs($applicantUser);

        // Profile
        $profileResponse = $this->get(route('applicant.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Alice Applicant');

        // Applications
        $applicationsResponse = $this->get(route('jobs.applied'));
        $applicationsResponse->assertStatus(200);

        // Schedule
        $scheduleResponse = $this->get(route('applicant.schedule'));
        $scheduleResponse->assertStatus(200);

        // Profile Edit
        $editResponse = $this->get(route('applicant.profile.edit'));
        $editResponse->assertStatus(200);
    }

    public function test_partner_cannot_access_applicant_personal_pages()
    {
        $partnerUser = User::factory()->create([
            'role' => 'partner',
            'email' => 'partner_bob@test.com',
            'name' => 'Bob Partner',
        ]);

        $this->actingAs($partnerUser);

        // Profile access denied / redirected
        $profileResponse = $this->get(route('applicant.profile'));
        $profileResponse->assertRedirect(route('partner.dashboard'));

        // Applications access denied / redirected
        $applicationsResponse = $this->get(route('jobs.applied'));
        $applicationsResponse->assertRedirect(route('partner.dashboard'));

        // Schedule access denied / redirected
        $scheduleResponse = $this->get(route('applicant.schedule'));
        $scheduleResponse->assertRedirect(route('partner.dashboard'));
    }

    public function test_admin_cannot_access_applicant_personal_pages_as_applicant()
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_super@test.com',
            'name' => 'Super Admin',
        ]);

        $this->actingAs($adminUser);

        // Profile access redirected to admin dashboard
        $profileResponse = $this->get(route('applicant.profile'));
        $profileResponse->assertRedirect(route('admin.dashboard'));

        // Schedule access redirected to admin dashboard
        $scheduleResponse = $this->get(route('applicant.schedule'));
        $scheduleResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_applicant_cannot_access_another_applicants_private_records()
    {
        // Setup Applicant A
        $userA = User::factory()->create([
            'role' => 'applicant',
            'email' => 'applicant_a@test.com',
            'name' => 'Applicant A',
        ]);
        $applicantA = Applicant::create([
            'name' => 'Applicant A',
            'email' => $userA->email,
            'role' => 'Hospitality',
            'status' => 'Pending',
        ]);

        // Setup Applicant B
        $userB = User::factory()->create([
            'role' => 'applicant',
            'email' => 'applicant_b@test.com',
            'name' => 'Applicant B',
        ]);
        $applicantB = Applicant::create([
            'name' => 'Applicant B',
            'email' => $userB->email,
            'role' => 'SIA Security',
            'status' => 'Pending',
        ]);

        // Setup Job and Application for Applicant B
        $jobB = JobPost::create([
            'dept' => 'SIA Security',
            'location' => 'London',
            'type' => 'Full-time',
            'salary' => '£18/hr',
            'description' => 'Secret SIA Event for B Only',
            'company_name' => 'Security Corp',
            'company_email' => 'hr@securitycorp.test',
            'status' => 'Active',
        ]);

        JobApplication::create([
            'job_post_id' => $jobB->id,
            'applicant_id' => $applicantB->id,
            'status' => 'Pending',
            'cover_letter' => 'Confidential letter from Applicant B',
        ]);

        // Setup ShiftSlot assignment for Applicant B
        $booking = EventBooking::create([
            'booking_ref' => 'KR-BK-9999',
            'user_id' => $userB->id,
            'event_name' => 'Private VIP Gala for B',
            'event_type' => 'Private',
            'location' => 'Mayfair',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'Confirmed',
        ]);
        $quotation = StaffQuotation::create([
            'event_booking_id' => $booking->id,
            'client' => 'VIP Client',
            'amount' => 500.00,
            'date' => now()->toDateString(),
            'status' => 'Approved',
        ]);
        ShiftSlot::create([
            'staff_quotation_id' => $quotation->id,
            'applicant_id' => $applicantB->id,
            'role_name' => 'SIA Security',
            'shift_date' => now()->addDays(5)->toDateString(),
            'start_time' => '18:00',
            'end_time' => '23:00',
            'rate' => 25.00,
            'status' => 'Assigned',
        ]);

        // Log in as Applicant A
        $this->actingAs($userA);

        // 1. My Profile loads Applicant A, does NOT show Applicant B
        $profileRes = $this->get(route('applicant.profile'));
        $profileRes->assertStatus(200);
        $profileRes->assertSee('Applicant A');
        $profileRes->assertDontSee('Applicant B');

        // 2. My Applications shows empty or only A's, does NOT show B's job/application
        $appRes = $this->get(route('jobs.applied'));
        $appRes->assertStatus(200);
        $appRes->assertDontSee('Secret SIA Event for B Only');
        $appRes->assertDontSee('Confidential letter from Applicant B');

        // 3. My Schedule shows only A's, does NOT show B's private VIP gala
        $scheduleRes = $this->get(route('applicant.schedule'));
        $scheduleRes->assertStatus(200);
        $scheduleRes->assertDontSee('Private VIP Gala for B');
    }

    public function test_navigation_dropdown_visibility_per_role()
    {
        // 1. Guest: only Overview is visible, personal links hidden
        $guestRes = $this->get(route('home'));
        $guestRes->assertStatus(200);
        $guestRes->assertSee('Overview');
        $guestRes->assertDontSee('My Profile');
        $guestRes->assertDontSee('My Applications');
        $guestRes->assertDontSee('My Schedule');

        // 2. Logged-in Applicant: Overview, My Profile, My Applications, My Schedule all visible
        $applicant = User::factory()->create(['role' => 'applicant', 'email' => 'app_nav@test.com']);
        Applicant::create([
            'name' => $applicant->name,
            'email' => $applicant->email,
            'role' => 'Hospitality',
            'status' => 'Pending',
        ]);

        $appRes = $this->actingAs($applicant)->get(route('home'));
        $appRes->assertStatus(200);
        $appRes->assertSee('Overview');
        $appRes->assertSee('My Profile');
        $appRes->assertSee('My Applications');
        $appRes->assertSee('My Schedule');

        // 3. Logged-in Partner: only Overview visible, personal links hidden
        $partner = User::factory()->create(['role' => 'partner', 'email' => 'partner_nav@test.com']);
        $partnerRes = $this->actingAs($partner)->get(route('home'));
        $partnerRes->assertStatus(200);
        $partnerRes->assertSee('Overview');
        $partnerRes->assertDontSee('My Profile');
        $partnerRes->assertDontSee('My Applications');
        $partnerRes->assertDontSee('My Schedule');

        // 4. Logged-in Admin: only Overview visible, personal links hidden
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_nav@test.com']);
        $adminRes = $this->actingAs($admin)->get(route('home'));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Overview');
        $adminRes->assertDontSee('My Profile');
        $adminRes->assertDontSee('My Applications');
        $adminRes->assertDontSee('My Schedule');
    }
}
