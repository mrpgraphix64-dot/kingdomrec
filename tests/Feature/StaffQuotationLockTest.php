<?php

namespace Tests\Feature;

use App\Models\EventBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffQuotationLockTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_booking_cannot_be_edited_or_deleted(): void
    {
        // 1. Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 2. Create an event booking that is Approved
        $booking = EventBooking::create([
            'booking_ref' => 'BK-TEST1',
            'event_name' => 'Gala Event',
            'client' => 'TechCorp',
            'venue' => 'Grand Hall',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16',
            'status' => 'Approved',
        ]);

        // 3. Try to update the approved booking as Admin
        $responseUpdate = $this->actingAs($admin)
            ->put(route('admin.staff-quotation.update', $booking->id), [
                'type' => 'booking',
                'client' => 'Updated Corp',
                'event_name' => 'Updated Gala',
                'venue' => 'Updated Hall',
                'start_date' => '2026-06-15',
                'status' => 'Approved',
            ]);

        $responseUpdate->assertRedirect();
        $responseUpdate->assertSessionHas('error', 'Approved/Active bookings cannot be edited.');
        $booking->refresh();
        $this->assertEquals('Gala Event', $booking->event_name); // Verify name didn't change

        // 4. Try to delete the approved booking as Admin
        $responseDelete = $this->actingAs($admin)
            ->delete(route('admin.staff-quotation.destroy', $booking->id), [
                'type' => 'booking',
            ]);

        $responseDelete->assertRedirect();
        $responseDelete->assertSessionHas('error', 'Approved/Active bookings cannot be deleted.');
        $this->assertDatabaseHas('event_bookings', ['id' => $booking->id]); // Verify booking still exists
    }

    public function test_pending_booking_can_be_edited_or_deleted(): void
    {
        // 1. Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // 2. Create an event booking that is Pending
        $booking = EventBooking::create([
            'booking_ref' => 'BK-TEST2',
            'event_name' => 'Pending Gala',
            'client' => 'TechCorp',
            'venue' => 'Grand Hall',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16',
            'status' => 'Pending',
        ]);

        // 3. Try to update the pending booking as Admin
        $responseUpdate = $this->actingAs($admin)
            ->put(route('admin.staff-quotation.update', $booking->id), [
                'type' => 'booking',
                'client' => 'TechCorp',
                'event_name' => 'New Name',
                'venue' => 'Grand Hall',
                'start_date' => '2026-06-15',
                'status' => 'Approved',
            ]);

        $responseUpdate->assertRedirect();
        $responseUpdate->assertSessionHas('success');
        $booking->refresh();
        $this->assertEquals('New Name', $booking->event_name);

        // 4. Try to delete the approved booking (it's now Approved, so it should be locked)
        $responseDeleteLocked = $this->actingAs($admin)
            ->delete(route('admin.staff-quotation.destroy', $booking->id), [
                'type' => 'booking',
            ]);
        $responseDeleteLocked->assertSessionHas('error', 'Approved/Active bookings cannot be deleted.');

        // 5. Change back to Pending and delete
        $booking->update(['status' => 'Pending']);
        $responseDelete = $this->actingAs($admin)
            ->delete(route('admin.staff-quotation.destroy', $booking->id), [
                'type' => 'booking',
            ]);

        $responseDelete->assertRedirect();
        $responseDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('event_bookings', ['id' => $booking->id]);
    }

    public function test_shift_slot_hours_and_break_calculation(): void
    {
        $slot = new \App\Models\ShiftSlot([
            'start_time' => '22:00',
            'end_time' => '06:00',
            'break_mins' => 30,
            'rate' => 15.00,
        ]);

        // 8 hours span minus 30 mins break = 7.5 hours
        $this->assertEquals(7.5, $slot->getHours());
        $this->assertEquals(112.5, $slot->getSubtotal());
    }

    public function test_invoice_generation_duplicate_protection(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $booking = EventBooking::create([
            'booking_ref' => 'BK-INV1',
            'event_name' => 'Invoiceable Event',
            'client' => 'Partner Corp',
            'venue' => 'Main Hall',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16',
            'status' => 'Approved',
        ]);

        $shift = \App\Models\StaffQuotation::create([
            'event_booking_id' => $booking->id,
            'quotation_ref' => 'BK-INV1',
            'client' => 'Partner Corp',
            'sub_category' => 'Security Guard',
            'quantity' => 1,
            'rate' => 15.00,
            'amount' => '£120.00',
            'date' => 'Jun 15, 2026',
            'status' => 'Approved',
        ]);

        $slot = \App\Models\ShiftSlot::create([
            'staff_quotation_id' => $shift->id,
            'role_name' => 'Security Guard',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'break_mins' => 0,
            'rate' => 15.00,
            'status' => 'Approved',
        ]);

        // First generation succeeds
        $response1 = $this->actingAs($admin)
            ->post(route('admin.invoices.generate', $booking->id));
        $response1->assertRedirect(route('admin.finance'));
        $response1->assertSessionHas('success');
        $this->assertDatabaseHas('invoices', ['event_booking_id' => $booking->id]);

        // Second generation should be safely blocked without creating another invoice
        $response2 = $this->actingAs($admin)
            ->post(route('admin.invoices.generate', $booking->id));
        $response2->assertRedirect(route('admin.finance'));
        $response2->assertSessionHas('info');

        $this->assertEquals(1, \App\Models\Invoice::where('event_booking_id', $booking->id)->count());
        
        // Assert Option A: ShiftSlot status remains 'Approved'
        $slot->refresh();
        $this->assertEquals('Approved', $slot->status);
    }

    public function test_partner_cannot_access_other_partner_invoice(): void
    {
        $partnerA = User::factory()->create(['role' => 'partner']);
        $partnerB = User::factory()->create(['role' => 'partner']);

        $bookingB = EventBooking::create([
            'user_id' => $partnerB->id,
            'booking_ref' => 'BK-PARTNER-B',
            'event_name' => 'Partner B Gala',
            'client' => $partnerB->name,
            'venue' => 'Exclusive Hall',
            'status' => 'Approved',
        ]);

        $invoiceB = \App\Models\Invoice::create([
            'invoice_ref' => 'INV-2026-9999',
            'event_booking_id' => $bookingB->id,
            'user_id' => $partnerB->id,
            'company_name' => $partnerB->name,
            'subtotal' => 500.00,
            'tax_rate' => 0.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 500.00,
            'amount_paid' => 0.00,
            'status' => 'Unpaid',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

        // Partner A attempts to download Partner B's invoice PDF
        $response = $this->actingAs($partnerA)
            ->get(route('partner.invoices.pdf', $invoiceB->id));

        $response->assertNotFound();
    }

    public function test_unauthorized_user_cannot_access_admin_finance(): void
    {
        $response = $this->get(route('admin.finance'));
        $response->assertRedirect(route('login'));
    }
}
