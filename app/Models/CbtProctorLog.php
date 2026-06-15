<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtProctorLog extends Model
{
    protected $fillable = [
        'cbt_exam_session_id', 'proctor_id', 'action_type', 'message'
    ];

    public function session()
    {
        return $this->belongsTo(CbtExamSession::class, 'cbt_exam_session_id');
    }

    public function proctor()
    {
        return $this->belongsTo(User::class, 'proctor_id');
    }
}
