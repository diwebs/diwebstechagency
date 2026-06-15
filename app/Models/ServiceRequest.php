<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'client_id', 'title', 'service_type', 'description', 'budget_range', 'deadline', 'status', 'attachments', 'ai_recommendations'
    ];

    protected $casts = [
        'deadline' => 'date',
        'attachments' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
