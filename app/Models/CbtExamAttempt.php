<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtExamAttempt extends Model
{
    protected $fillable = [
        'cbt_exam_session_id', 'question_id', 'submitted_answers', 'is_correct', 'time_spent_seconds'
    ];

    protected $casts = [
        'submitted_answers' => 'array',
        'is_correct' => 'boolean'
    ];

    public function session()
    {
        return $this->belongsTo(CbtExamSession::class, 'cbt_exam_session_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
