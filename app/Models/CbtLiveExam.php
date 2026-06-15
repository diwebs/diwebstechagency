<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtLiveExam extends Model
{
    protected $fillable = [
        'exam_id', 'scheduled_at', 'proctor_id', 'camera_required', 'mic_required', 'browser_lock_required', 'status'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'camera_required' => 'boolean',
        'mic_required' => 'boolean',
        'browser_lock_required' => 'boolean'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function proctor()
    {
        return $this->belongsTo(User::class, 'proctor_id');
    }

    public function sessions()
    {
        return $this->hasMany(CbtExamSession::class, 'cbt_live_exam_id');
    }
}
