<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'visitor_name',
        'visitor_email',
        'user_id',
        'user_type',
        'message',
        'admin_reply',
        'page_url',
        'status',
        'replied_at',
        'resolved_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
