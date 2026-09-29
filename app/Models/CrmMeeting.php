<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmMeeting extends Model
{
    protected $table = 'crm_meetings';

    protected $fillable = [
        'title', 'meeting_type', 'scheduled_at', 'duration_minutes', 'attendees', 'notes',
        'crm_lead_id', 'crm_client_id', 'assigned_staff_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'attendees' => 'array'
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }
}
