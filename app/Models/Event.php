<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'end_date',
        'time',
        'type',
        'location',
        'attendees',
        'required_sh',
        'description',
        'staff_quotation_id'
    ];

    public function staffQuotation()
    {
        return $this->belongsTo(StaffQuotation::class);
    }

    public function assignments()
    {
        return $this->hasMany(EventAssignment::class);
    }
}
