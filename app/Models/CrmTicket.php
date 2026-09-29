<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTicket extends Model
{
    protected $table = 'crm_tickets';

    protected $fillable = [
        'ticket_number', 'crm_client_id', 'subject', 'category', 'status', 'priority', 'message', 'assigned_staff_id'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }
}
