<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'client_id', 'title', 'description', 'status', 'budget', 'agreement_signed_at'
    ];

    protected $casts = [
        'agreement_signed_at' => 'datetime',
        'budget' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            if (empty($project->id)) {
                $project->id = (string) Str::uuid();
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
