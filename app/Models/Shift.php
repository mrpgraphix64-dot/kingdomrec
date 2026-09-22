<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id', 'date', 'start_time', 'end_time', 
        'staff_names', 'status', 'notes'
    ];

    protected $casts = [
        'staff_names' => 'array',
    ];
}
