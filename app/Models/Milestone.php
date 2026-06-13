<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'project_id', 'title', 'description', 'due_date', 'status', 'amount'
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
