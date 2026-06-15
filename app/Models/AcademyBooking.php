<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyBooking extends Model
{
    protected $fillable = [
        'user_id', 'teacher_id', 'booking_date', 'start_time', 'end_time',
        'call_type', 'meeting_url', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(AcademyTeacher::class, 'teacher_id');
    }

    public function recordings()
    {
        return $this->hasMany(AcademyRecording::class, 'booking_id');
    }
}
