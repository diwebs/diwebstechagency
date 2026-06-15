<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'project_id', 'client_id', 'title', 'content', 'status', 'signed_at', 'signature_data', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
