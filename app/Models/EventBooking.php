<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_ref',
        'event_name',
        'client',
        'venue',
        'start_date',
        'end_date',
        'special_requirements',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Check if the event booking is still within the edit window.
     */
    public function isEditable(): bool
    {
        $hoursWindow = \App\Models\Setting::get('quotation_edit_window_hours', 3);
        return $this->created_at->diffInHours(now()) <= $hoursWindow;
    }

    /**
     * The partner who owns this event booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The shifts (formerly quotation rows) that belong to this event.
     */
    public function shifts()
    {
        return $this->hasMany(StaffQuotation::class);
    }

    /**
     * Get all shift slots across all shifts for this event.
     */
    public function shiftSlots()
    {
        return $this->hasManyThrough(ShiftSlot::class, StaffQuotation::class, 'event_booking_id', 'staff_quotation_id');
    }

    /**
     * Get the invoices associated with this booking.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the rating/feedback for this booking.
     */
    public function rating()
    {
        return $this->hasOne(Rating::class, 'event_booking_id');
    }

    /**
     * Check if timesheets are finalized.
     */
    public function areTimesheetsFinalized(): bool
    {
        $slots = $this->shiftSlots()->whereNotNull('applicant_id')->get();
        if ($slots->isEmpty()) {
            return false;
        }
        return $slots->every(fn($slot) => in_array($slot->status, ['Approved', 'Billed']));
    }

    /**
     * Calculate the total amount from all child shifts.
     */
    public function recalculateTotal(): string
    {
        $total = 0;
        foreach ($this->shifts as $shift) {
            $amt = str_replace(['£', ','], '', $shift->amount);
            $total += (float) $amt;
        }
        $formatted = '£' . number_format($total, 2);
        $this->update(['total_amount' => $formatted]);
        return $formatted;
    }
}
