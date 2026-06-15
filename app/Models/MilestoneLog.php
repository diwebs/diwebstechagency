<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilestoneLog extends Model
{
    protected $fillable = [
        'milestone_id', 'user_id', 'action', 'comments'
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
