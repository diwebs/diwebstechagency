<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id', 'date', 'clock_in', 'clock_out', 'status', 'late_minutes', 'ip_address', 'device_info'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'late_minutes' => 'integer'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
