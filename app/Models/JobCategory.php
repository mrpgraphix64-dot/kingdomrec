<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'icon', 'status', 'description'
    ];

    public function jobs()
    {
        return $this->hasMany(JobPost::class, 'dept', 'name');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'parent_category', 'name');
    }
}
