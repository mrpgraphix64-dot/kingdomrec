<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'capacity', 'image', 'status', 'description', 'contact_info', 'city', 'postcode', 'country', 'email'
    ];
}
