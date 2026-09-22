<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        JobCategory::firstOrCreate(['name' => 'Hospitality'], ['status' => 'Active']);
        JobCategory::firstOrCreate(['name' => 'Promotional'], ['status' => 'Active']);
        JobCategory::firstOrCreate(['name' => 'SIA Security'], ['status' => 'Active']);
    }

    public function test_candidate_can_update_profile_with_existing_category(): void
    {
        $user = User::factory()->create(['role' => 'applicant', 'email' => 'candidate1@test.com', 'name' => 'Alice']);
        $applicant = Applicant::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+44 7123 456789',
            'location' => 'London',
            'role' => 'Hospitality',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), [
            'name' => 'Alice Updated',
            'phone' => '+44 7999 888777',
            'location' => 'Manchester',
            'category' => 'Promotional',
            'sub_category' => null,
        ]);

        $response->assertRedirect(route('applicant.profile'));
        $applicant->refresh();
        $this->assertEquals('Promotional', $applicant->role);
        $this->assertNull($applicant->other_category_description);
        $this->assertEquals('Alice Updated', $applicant->name);
        $this->assertEquals('+44 7999 888777', $applicant->phone);
    }

    public function test_candidate_can_select_other_not_listed_with_description(): void
    {
        $user = User::factory()->create(['role' => 'applicant', 'email' => 'photographer@test.com', 'name' => 'Bob']);
        $applicant = Applicant::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+44 7111 222333',
            'location' => 'Birmingham',
            'role' => 'General Applicant',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), [
            'name' => 'Bob Photographer',
            'phone' => '+44 7111 222333',
            'location' => 'Birmingham',
            'category' => 'Other / Not Listed',
            'other_category_description' => 'Event Photographer',
        ]);

        $response->assertRedirect(route('applicant.profile'));
        $applicant->refresh();
        $this->assertEquals('Other / Not Listed', $applicant->role);
        $this->assertEquals('Event Photographer', $applicant->other_category_description);
    }

    public function test_candidate_cannot_save_other_without_description(): void
    {
        $user = User::factory()->create(['role' => 'applicant', 'email' => 'nodesc@test.com', 'name' => 'Charlie']);
        Applicant::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+44 7111 222333',
            'location' => 'Leeds',
            'role' => 'General Applicant',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), [
            'name' => 'Charlie Empty',
            'phone' => '+44 7111 222333',
            'location' => 'Leeds',
            'category' => 'Other / Not Listed',
            'other_category_description' => '',
        ]);

        $response->assertSessionHasErrors(['other_category_description']);
    }

    public function test_changing_away_from_other_clears_other_description_requirement(): void
    {
        $user = User::factory()->create(['role' => 'applicant', 'email' => 'switching@test.com', 'name' => 'David']);
        $applicant = Applicant::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+44 7111 555666',
            'location' => 'Bristol',
            'role' => 'Other / Not Listed',
            'other_category_description' => 'Event Photographer',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($user)->put(route('applicant.profile.update'), [
            'name' => 'David',
            'phone' => '+44 7111 555666',
            'location' => 'Bristol',
            'category' => 'Hospitality',
            'other_category_description' => '',
        ]);

        $response->assertRedirect(route('applicant.profile'));
        $applicant->refresh();
        $this->assertEquals('Hospitality', $applicant->role);
        $this->assertNull($applicant->other_category_description);
    }

    public function test_admin_can_filter_applicant_by_other_category_description(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_test@test.com']);
        $user = User::factory()->create(['role' => 'applicant', 'email' => 'driver_candidate@test.com', 'name' => 'Edward Driver']);
        Applicant::create([
            'name' => 'Edward Driver',
            'email' => $user->email,
            'phone' => '+44 7000 111222',
            'location' => 'Liverpool',
            'role' => 'Other / Not Listed',
            'other_category_description' => 'Chauffeur Driver',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.applicant', ['role' => 'Chauffeur']));
        $response->assertStatus(200);
        $response->assertSee('Edward Driver');
        $response->assertSee('Other: Chauffeur Driver');
    }

    public function test_admin_can_filter_uncategorized_applicants(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_review@test.com']);
        Applicant::create([
            'name' => 'Uncategorized Chef',
            'email' => 'chef@test.com',
            'phone' => '+44 7000 333444',
            'location' => 'Manchester',
            'role' => 'Other / Not Listed',
            'other_category_description' => 'Head Pastry Chef',
            'status' => 'Pending'
        ]);
        Applicant::create([
            'name' => 'Official Staff',
            'email' => 'staff@test.com',
            'phone' => '+44 7000 555666',
            'location' => 'London',
            'role' => 'Hospitality',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.applicant', ['filter' => 'uncategorized']));
        $response->assertStatus(200);
        $response->assertSee('Uncategorized Chef');
        $response->assertSee('Other: Head Pastry Chef');
        $response->assertDontSee('Official Staff');
    }

    public function test_admin_can_assign_applicant_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_assign@test.com']);
        $applicant = Applicant::create([
            'name' => 'Pending Review Candidate',
            'email' => 'candidate_rev@test.com',
            'phone' => '+44 7000 777888',
            'location' => 'Birmingham',
            'role' => 'Other / Not Listed',
            'other_category_description' => 'Event Photographer',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($admin)->put(route('admin.applicant.assign-category', $applicant->id), [
            'role' => 'Hospitality',
            'sub_category' => 'Photographer',
        ]);

        $response->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('Hospitality', $applicant->role);
        $this->assertEquals('Photographer', $applicant->sub_category);
        // Ensure original requested role is preserved
        $this->assertEquals('Event Photographer', $applicant->other_category_description);
    }

    public function test_dashboard_shows_category_review_attention_item(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_dash@test.com']);
        Applicant::create([
            'name' => 'Pending Category Applicant',
            'email' => 'pending_cat@test.com',
            'phone' => '+44 7000 999000',
            'location' => 'Leeds',
            'role' => 'Other / Not Listed',
            'other_category_description' => 'Sound Engineer',
            'status' => 'Pending'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Applicant Category Request');
        $response->assertSee('Review Requests');
    }
}
