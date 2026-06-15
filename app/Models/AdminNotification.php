<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'type', 'title', 'details', 'is_read'
    ];

    protected $casts = [
        'details' => 'array',
        'is_read' => 'boolean'
    ];
}
