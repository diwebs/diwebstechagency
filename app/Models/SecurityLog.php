<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    public $timestamps = false; // Custom timestamp field
    
    protected $fillable = [
        'user_id', 'exam_session_id', 'event_type', 'ip_address', 'user_agent', 'details', 'created_at'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($log) {
            $log->created_at = now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class);
    }
}
