<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyTeacher extends Model
{
    protected $fillable = [
        'user_id', 'name', 'expertise', 'bio', 'certifications',
        'voice_only_enabled', 'video_enabled', 'hourly_rate',
        'role', 'avatar', 'email'
    ];

    protected $casts = [
        'certifications' => 'array',
        'voice_only_enabled' => 'boolean',
        'video_enabled' => 'boolean',
        'hourly_rate' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(AcademyTeacherAvailability::class, 'teacher_id');
    }

    public function bookings()
    {
        return $this->hasMany(AcademyBooking::class, 'teacher_id');
    }

    public function liveSessions()
    {
        return $this->hasMany(AcademyLiveSession::class, 'teacher_id');
    }
}
