<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'role', 'sub_category', 'other_category_description', 'email', 'phone', 'location', 
        'status', 'cv_link', 'image', 'bio', 'linkedin_url', 
        'portfolio_url', 'expected_salary', 'availability',
        'is_featured', 'image_position'
    ];

    protected $appends = ['cv_name', 'profile_photo_url', 'cv_exists'];


    public function educations()
    {
        return $this->hasMany(ApplicantEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(ApplicantExperience::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function eventAssignments()
    {
        return $this->hasMany(EventAssignment::class);
    }

    public function shiftSlots()
    {
        return $this->hasMany(ShiftSlot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Check if the applicant profile is complete.
     * Optionally populates an array with missing fields.
     *
     * @param array $missing
     * @return bool
     */
    public function hasCompleteProfile(&$missing = []): bool
    {
        $missing = [];

        if (empty($this->cv_link)) {
            $missing[] = 'Resume (CV)';
        }
        if (empty($this->phone) || $this->phone === 'Not provided') {
            $missing[] = 'Phone Number';
        }
        if (empty($this->location) || $this->location === 'Not provided') {
            $missing[] = 'Location';
        }
        if (empty($this->role) || $this->role === 'General Applicant' || ($this->role === 'Other / Not Listed' && empty($this->other_category_description))) {
            $missing[] = 'Job Sector';
        }
        if ($this->experiences()->count() === 0 && $this->educations()->count() === 0) {
            $missing[] = 'Education or Work Experience';
        }

        return empty($missing);
    }

    /**
     * Get the clean resume filename (stripping random/timestamp prefix).
     *
     * @return string|null
     */
    public function getCvNameAttribute()
    {
        if (empty($this->cv_link)) {
            return null;
        }
        $base = basename($this->cv_link);
        
        // Strip 12-char random alphanumeric prefix + underscore
        $clean = preg_replace('/^[a-zA-Z0-9]{12}_/', '', $base);
        // Strip 10-digit timestamp prefix + underscore
        $clean = preg_replace('/^\d{10}_/', '', $clean);
        
        return $clean;
    }

    /**
     * Get the formatted profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        $image = $this->image;

        if (empty($image)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'Applicant') . '&background=0F1D33&color=fff';
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        // Clean up any double slashes or leading slash
        $path = ltrim($image, '/');

        // Remove storage/ or media/ prefix if present
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        } elseif (str_starts_with($path, 'media/')) {
            $path = substr($path, 6);
        }

        // If path already starts with photos/ or profile-photos/, leave as is. Otherwise prepend photos/
        if (!str_starts_with($path, 'photos/') && !str_starts_with($path, 'profile-photos/')) {
            $path = 'photos/' . $path;
        }

        // Return the final media URL using asset()
        return asset('media/' . $path);
    }

    /**
     * Check if the CV file exists on disk (either in private or public storage path).
     *
     * @return bool
     */
    public function getCvExistsAttribute()
    {
        if (empty($this->cv_link)) {
            return false;
        }

        $path = str_replace(['storage/app/private/', 'storage/app/'], '', ltrim($this->cv_link, '/'));

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return true;
        }

        if (file_exists(storage_path('app/' . $path))) {
            return true;
        }

        return false;
    }
}



