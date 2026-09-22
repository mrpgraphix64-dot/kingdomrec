<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'parent_category', 'description', 'status'
    ];

    public function parent()
    {
        return $this->belongsTo(JobCategory::class, 'parent_category', 'name');
    }
}
