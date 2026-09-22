<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'event_assignment_id',
        'clock_in',
        'clock_out',
        'total_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function eventAssignment()
    {
        return $this->belongsTo(EventAssignment::class);
    }
}
