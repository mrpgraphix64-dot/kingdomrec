<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_post_id', 'applicant_id', 'status', 'cover_letter', 'admin_notes', 'requalified_count'
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}
