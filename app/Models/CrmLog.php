<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmLog extends Model
{
    protected $table = 'crm_logs';

    protected $fillable = [
        'staff_id', 'action', 'description', 'ip_address'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
