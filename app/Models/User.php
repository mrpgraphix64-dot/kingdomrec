<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'company_name',
        'profile_photo_path',
        'is_active',
        'address',
        'website',
    ];

    /**
     * The attributes that should be appended to JSON form.
     *
     * @var list<string>
     */
    protected $appends = ['profile_photo_url'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the applicant profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function applicant()
    {
        return $this->hasOne(\App\Models\Applicant::class, 'email', 'email');
    }

    /**
     * Get the team profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(\App\Models\Team::class, 'user_id');
    }

    /**
     * Get the user's bookings (staff quotations).
     */
    public function bookings()
    {
        return $this->hasMany(\App\Models\StaffQuotation::class);
    }

    /**
     * Get the user's event bookings (master events).
     */
    public function eventBookings()
    {
        return $this->hasMany(\App\Models\EventBooking::class);
    }

    /**
     * Get the user's invoices.
     */
    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    /**
     * Get the user's favourite staff members.
     */
    public function favouriteStaff()
    {
        return $this->belongsToMany(\App\Models\Applicant::class, 'favourite_staff', 'user_id', 'applicant_id')->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's profile photo URL or fallback avatar.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!empty($this->profile_photo_path)) {
            if (str_starts_with($this->profile_photo_path, 'http://') || str_starts_with($this->profile_photo_path, 'https://')) {
                return $this->profile_photo_path;
            }
            $path = ltrim($this->profile_photo_path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            } elseif (str_starts_with($path, 'media/')) {
                $path = substr($path, 6);
            }
            return asset('media/' . $path);
        }

        if ($this->applicant && !empty($this->applicant->image)) {
            return $this->applicant->profile_photo_url;
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=0F1D33&color=fff';
    }
}
