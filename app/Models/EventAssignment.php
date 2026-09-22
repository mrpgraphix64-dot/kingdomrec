<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'applicant_id', 'status', 'notes'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
