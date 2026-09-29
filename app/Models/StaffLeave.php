<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffLeave extends Model
{
    protected $table = 'staff_leaves';

    protected $fillable = [
        'staff_id', 'leave_type', 'start_date', 'end_date', 'days', 'reason', 'status', 'admin_notes', 'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'integer',
        'reviewed_at' => 'datetime'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
