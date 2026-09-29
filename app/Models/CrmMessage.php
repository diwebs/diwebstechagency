<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmMessage extends Model
{
    protected $table = 'crm_messages';

    protected $fillable = [
        'channel', 'direction', 'sender', 'recipient', 'content', 'attachments',
        'crm_lead_id', 'crm_client_id', 'staff_id'
    ];

    protected $casts = [
        'attachments' => 'array'
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
