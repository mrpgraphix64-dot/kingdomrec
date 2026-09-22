<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RateCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'hourly_rate', 'currency', 'overtime_rate', 
        'type', 'sub_category', 'venue_id', 'hours', 
        'total_amount', 'company_name', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'type', 'name');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Resolve the correct rate card for a given role, with cascading specificity:
     *   1. company_name + sub_category + venue_id + Active  (most specific)
     *   2. company_name + sub_category + Active             (partner-specific, any venue)
     *   3. sub_category + Active                            (global fallback)
     *
     * Always picks the newest active record if duplicates exist (orderBy id desc).
     * Logs a warning when duplicate rows are detected at the matched specificity.
     *
     * @param string      $subCategory  The role / sub-category name
     * @param string|null $companyName  The partner's company_name (or user name)
     * @param int|null    $venueId      Optional venue constraint
     * @return static|null
     */
    public static function resolveRate(string $subCategory, ?string $companyName = null, ?int $venueId = null): ?self
    {
        // Level 1: Partner + Role + Venue
        if ($companyName && $venueId) {
            $query = static::where('sub_category', $subCategory)
                ->where('company_name', $companyName)
                ->where('venue_id', $venueId)
                ->where('status', 'Active')
                ->orderBy('id', 'desc');

            $count = (clone $query)->count();
            if ($count > 0) {
                if ($count > 1) {
                    \Log::warning("Duplicate active rate cards detected: sub_category={$subCategory}, company_name={$companyName}, venue_id={$venueId} ({$count} rows). Using newest.");
                }
                return $query->first();
            }
        }

        // Level 2: Partner + Role (any venue)
        if ($companyName) {
            $query = static::where('sub_category', $subCategory)
                ->where('company_name', $companyName)
                ->where('status', 'Active')
                ->orderBy('id', 'desc');

            $count = (clone $query)->count();
            if ($count > 0) {
                if ($count > 1) {
                    \Log::warning("Duplicate active rate cards detected: sub_category={$subCategory}, company_name={$companyName} ({$count} rows). Using newest.");
                }
                return $query->first();
            }
        }

        // Level 3: Global fallback (role only)
        $query = static::where('sub_category', $subCategory)
            ->where('status', 'Active')
            ->orderBy('id', 'desc');

        $count = (clone $query)->count();
        if ($count > 0) {
            if ($count > 1) {
                \Log::warning("Duplicate active rate cards detected (global): sub_category={$subCategory} ({$count} rows). Using newest.");
            }
            return $query->first();
        }

        \Log::warning("No active rate card found for sub_category={$subCategory}, company_name={$companyName}. Rate will default to 0.");
        return null;
    }
}
