<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EventBooking;
use App\Models\StaffQuotation;
use App\Models\ShiftSlot;
use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_successfully_with_operational_data()
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin_test@kingdom.com']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Daily Operations Control Center');
        $response->assertSee('Events Today');
        $response->assertSee('Staff Needed');
        $response->assertSee('Staff Assigned');
        $response->assertSee('Open Vacancies');
        $response->assertSee('On Shift Now');
        $response->assertSee('Operational Workflow Pipeline');
        $response->assertSee('Upcoming Operations Schedule');
        $response->assertSee('Needs Your Attention');
    }
}
