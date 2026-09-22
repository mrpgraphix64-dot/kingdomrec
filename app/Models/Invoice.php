<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_ref',
        'event_booking_id',
        'user_id',
        'company_name',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'issue_date',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'float',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
        'amount_paid' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventBooking()
    {
        return $this->belongsTo(EventBooking::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }



    protected static function booted()
    {
        static::updated(function ($invoice) {
            // Auto close event booking when paid
            if ($invoice->status === 'Paid' && $invoice->event_booking_id) {
                $booking = $invoice->eventBooking;
                if ($booking) {
                    // Check if all timesheets/shift slots for this event are approved or billed
                    $allApprovedOrBilled = $booking->shiftSlots()
                        ->whereIn('shift_slots.status', ['Approved', 'Billed'])
                        ->count() === $booking->shiftSlots()->count();

                    if ($allApprovedOrBilled) {
                        $booking->update(['status' => 'Closed']);
                    }
                }
            }
        });
    }
}
