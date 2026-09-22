<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'dept', 'location', 'type', 'salary',
        'description', 'company_name', 'company_email',
        'company_website', 'status', 'deadline', 'sub_category',
        'staff_quotation_id'
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'dept', 'name');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * The shift/role requirement (StaffQuotation) this posting was created to fill, if any.
     */
    public function staffQuotation()
    {
        return $this->belongsTo(StaffQuotation::class);
    }

    public function getCalculatedStatusAttribute()
    {
        if ($this->status === 'Archived') {
            return 'Archived';
        }

        if ($this->deadline) {
            $deadline = \Carbon\Carbon::parse($this->deadline)->startOfDay();
            $today = now()->startOfDay();
            if ($today->greaterThan($deadline)) {
                $daysDiff = $deadline->diffInDays($today);
                if ($daysDiff >= 7) {
                    return 'Archived';
                }
                return 'Expired';
            }
        }

        if ($this->status === 'Expired' || $this->status === 'Closed') {
            return 'Expired';
        }

        return $this->status ?? 'Active';
    }

    public function scopeActive($query)
    {
        $today = now()->startOfDay()->toDateString();
        return $query->where('status', 'Active')
            ->where(function($q) use ($today) {
                $q->whereNull('deadline')
                  ->orWhereDate('deadline', '>=', $today);
            });
    }

    public function scopeExpired($query)
    {
        $today = now()->startOfDay()->toDateString();
        $sevenDaysAgo = now()->subDays(7)->startOfDay()->toDateString();
        return $query->where(function($q) use ($today, $sevenDaysAgo) {
            $q->whereIn('status', ['Expired', 'Closed'])
              ->orWhere(function($sub) use ($today, $sevenDaysAgo) {
                  $sub->where('status', 'Active')
                      ->whereNotNull('deadline')
                      ->whereDate('deadline', '<', $today)
                      ->whereDate('deadline', '>', $sevenDaysAgo);
              });
        });
    }

    public function scopeArchived($query)
    {
        $sevenDaysAgo = now()->subDays(7)->startOfDay()->toDateString();
        return $query->where(function($q) use ($sevenDaysAgo) {
            $q->where('status', 'Archived')
              ->orWhere(function($sub) use ($sevenDaysAgo) {
                  $sub->where('status', 'Active')
                      ->whereNotNull('deadline')
                      ->whereDate('deadline', '<=', $sevenDaysAgo);
              });
        });
    }
}
