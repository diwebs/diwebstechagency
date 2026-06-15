<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'exam_id', 'user_id', 'cbt_center_id', 'device_id', 'status', 'started_at', 'ended_at', 'score', 'anti_cheat_flags', 'logs'
    ];

    protected $casts = [
        'logs' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($session) {
            if (empty($session->id)) {
                $session->id = (string) Str::uuid();
            }
        });

        static::created(function ($session) {
            $cbtSession = CbtExamSession::find($session->id);
            if (!$cbtSession) {
                CbtExamSession::create([
                    'id' => $session->id,
                    'exam_id' => $session->exam_id,
                    'user_id' => $session->user_id,
                    'cbt_center_id' => $session->cbt_center_id,
                    'exam_mode' => 'standard',
                    'status' => $session->status,
                    'score' => $session->score,
                    'anti_cheat_flags' => $session->anti_cheat_flags ?? 0,
                    'started_at' => $session->started_at,
                    'ended_at' => $session->ended_at
                ]);
            }
        });

        static::updated(function ($session) {
            $cbtSession = CbtExamSession::find($session->id);
            if ($cbtSession) {
                $cbtSession->update([
                    'status' => $session->status,
                    'score' => $session->score,
                    'anti_cheat_flags' => $session->anti_cheat_flags ?? 0,
                    'ended_at' => $session->ended_at
                ]);
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

    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }
}
