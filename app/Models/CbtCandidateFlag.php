<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCandidateFlag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cbt_exam_session_id', 'violation_type', 'details', 'created_at'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($flag) {
            $flag->created_at = now();
        });
    }

    public function session()
    {
        return $this->belongsTo(CbtExamSession::class, 'cbt_exam_session_id');
    }
}
