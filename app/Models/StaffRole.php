<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffRole extends Model
{
    protected $fillable = ['department_id', 'title', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function staffMembers(): HasMany
    {
        return $this->hasMany(Staff::class, 'role_id');
    }
}
