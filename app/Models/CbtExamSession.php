<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CbtExamSession extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'exam_id', 'user_id', 'cbt_center_id', 'cbt_live_exam_id', 'exam_mode', 'status', 'score', 'anti_cheat_flags', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'score' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($session) {
            if (empty($session->id)) {
                $session->id = (string) Str::uuid();
            }
        });
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(CbtCenter::class, 'cbt_center_id');
    }

    public function liveExam()
    {
        return $this->belongsTo(CbtLiveExam::class, 'cbt_live_exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(CbtExamAttempt::class, 'cbt_exam_session_id');
    }

    public function flags()
    {
        return $this->hasMany(CbtCandidateFlag::class, 'cbt_exam_session_id');
    }

    public function proctorLogs()
    {
        return $this->hasMany(CbtProctorLog::class, 'cbt_exam_session_id');
    }
}
