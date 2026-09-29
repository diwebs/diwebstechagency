<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description', 'head_id'];

    public function head(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'head_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(StaffRole::class);
    }

    public function staffMembers(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}
