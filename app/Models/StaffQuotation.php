<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffQuotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_ref',
        'user_id',
        'event_booking_id',
        'client',
        'category',
        'sub_category',
        'quantity',
        'amount',
        'rate',
        'date',
        'shift_hours',
        'calculated_hours',
        'shift',
        'shift_label',
        'start_date',
        'end_date',
        'shift_date',
        'start_time',
        'end_time',
        'total_days',
        'venue',
        'event_name',
        'special_requirements',
        'preferred_staff',
        'status',
        'booked_at',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Check if booking is still within the edit window.
     * Default edit window is 3 hours if not configured by the admin global settings.
     */
    public function isEditable(): bool
    {
        // If linked to an EventBooking, delegate editability to the parent
        if ($this->eventBooking) {
            return $this->eventBooking->isEditable();
        }
        if (!$this->booked_at) return false;
        
        $hoursWindow = \App\Models\Setting::get('quotation_edit_window_hours', 3);
        
        return $this->booked_at->diffInHours(now()) <= $hoursWindow;
    }

    /**
     * The parent event booking this shift belongs to.
     */
    public function eventBooking()
    {
        return $this->belongsTo(EventBooking::class);
    }

    /**
     * Get the user who made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the events created for this booking.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the shift slots for this booking.
     */
    public function shiftSlots()
    {
        return $this->hasMany(ShiftSlot::class);
    }

    /**
     * Job postings created to recruit staff for this shift/role, when the
     * existing applicant pool couldn't fully cover it.
     */
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    /**
     * How many staff are still needed on this shift (quantity minus filled slots).
     */
    public function getVacancyCountAttribute(): int
    {
        $filled = $this->shiftSlots()->whereNotNull('applicant_id')->where('applicant_id', '!=', 0)->count();
        return max(0, (int) $this->quantity - $filled);
    }

    public function getRefAttribute()
    {
        return $this->quotation_ref;
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('M d, Y') : '';
    }

    public function getPartnerAttribute()
    {
        return $this->client;
    }

    public function getStaffCategoryAttribute()
    {
        return $this->category;
    }

    public function getStaffTypeAttribute()
    {
        return $this->sub_category;
    }

    public function getDaysAttribute()
    {
        return $this->total_days;
    }
}
