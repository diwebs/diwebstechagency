<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title', 'description', 'code', 'duration_minutes', 'total_questions', 'passing_score', 'is_active', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
