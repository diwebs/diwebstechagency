<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id', 'question_text', 'question_type', 'options', 'correct_answers', 'explanation', 'difficulty'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
