<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffActivityLog extends Model
{
    protected $table = 'staff_activity_logs';

    protected $fillable = [
        'staff_id', 'action', 'description', 'ip_address', 'user_agent'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
