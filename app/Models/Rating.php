<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'applicant_id',
        'event_assignment_id',
        'event_booking_id',
        'rating',
        'staff_performance_rating',
        'review',
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function eventAssignment()
    {
        return $this->belongsTo(EventAssignment::class);
    }

    public function eventBooking()
    {
        return $this->belongsTo(EventBooking::class);
    }
}
