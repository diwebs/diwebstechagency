<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyLiveSession extends Model
{
    protected $fillable = [
        'title', 'teacher_id', 'meeting_provider', 'meeting_url',
        'date', 'duration_minutes', 'session_type', 'status', 'description', 'target_role'
    ];

    protected $casts = [
        'date' => 'datetime',
        'duration_minutes' => 'integer'
    ];

    public function teacher()
    {
        return $this->belongsTo(AcademyTeacher::class, 'teacher_id');
    }

    public function recordings()
    {
        return $this->hasMany(AcademyRecording::class, 'live_session_id');
    }
}
