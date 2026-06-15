<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyRecording extends Model
{
    protected $fillable = [
        'live_session_id', 'booking_id', 'title', 'video_url', 'audio_url',
        'notes', 'ai_summary', 'retention_days'
    ];

    protected $casts = [
        'retention_days' => 'integer'
    ];

    public function liveSession()
    {
        return $this->belongsTo(AcademyLiveSession::class, 'live_session_id');
    }

    public function booking()
    {
        return $this->belongsTo(AcademyBooking::class, 'booking_id');
    }
}
