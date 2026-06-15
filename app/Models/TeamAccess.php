<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAccess extends Model
{
    protected $table = 'team_access';

    protected $fillable = [
        'client_id', 'name', 'email', 'role', 'project_permissions'
    ];

    protected $casts = [
        'project_permissions' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
