<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'instructor_name', 'price', 'cover_image', 'syllabus',
        'difficulty', 'category'
    ];

    protected $casts = [
        'syllabus' => 'array',
        'price' => 'decimal:2'
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
}
