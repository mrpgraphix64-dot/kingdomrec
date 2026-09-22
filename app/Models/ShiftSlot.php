<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSlot extends Model
{
    protected $fillable = [
        'staff_quotation_id',
        'applicant_id',
        'role_name',
        'shift_date',
        'start_time',
        'end_time',
        'break_mins',
        'rate',
        'status',
    ];

    protected $casts = [
        'shift_date' => 'date',
    ];

    public function staffQuotation()
    {
        return $this->belongsTo(StaffQuotation::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Calculate worked hours, accounting for overnight shifts and breaks.
     */
    public function getHours(): float
    {
        if (!$this->start_time || !$this->end_time) {
            return 0.0;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $diffInMinutes = $start->diffInMinutes($end);
        if ($this->break_mins && $this->break_mins > 0) {
            $diffInMinutes = max(0, $diffInMinutes - (int)$this->break_mins);
        }
        $totalHours = $diffInMinutes / 60;

        return max(0.0, round($totalHours, 2));
    }

    /**
     * Calculate subtotal (Hours * Rate).
     */
    public function getSubtotal(): float
    {
        return $this->getHours() * ($this->rate ?? 0.0);
    }
}

