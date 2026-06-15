<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyAudioLesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'slug', 'instructor_name', 'duration_seconds',
        'audio_url', 'format', 'summary', 'transcript', 'chapters', 'is_downloadable'
    ];

    protected $casts = [
        'chapters' => 'array',
        'is_downloadable' => 'boolean',
        'duration_seconds' => 'integer'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
