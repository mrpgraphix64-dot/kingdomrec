<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantExperience extends Model
{
    use HasFactory;

    protected $table = 'applicant_experiences';

    protected $fillable = [
        'applicant_id', 'company', 'job_title', 'start_date', 
        'end_date', 'is_current', 'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
