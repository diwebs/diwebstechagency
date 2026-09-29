<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAssignment extends Model
{
    protected $fillable = ['project_id', 'staff_id', 'role'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
