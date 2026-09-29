<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTask extends Model
{
    protected $table = 'crm_tasks';

    protected $fillable = [
        'title', 'task_type', 'assigned_staff_id', 'due_date', 'priority', 'status', 'crm_lead_id', 'crm_client_id', 'notes'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }
}
