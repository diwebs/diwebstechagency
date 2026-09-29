<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmLead extends Model
{
    protected $table = 'crm_leads';

    protected $fillable = [
        'full_name', 'company_name', 'email', 'phone', 'source', 'service_interest',
        'budget_range', 'country', 'status', 'assigned_staff_id', 'lead_score', 'notes'
    ];

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function prospect(): HasOne
    {
        return $this->hasOne(CrmProspect::class, 'crm_lead_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'crm_lead_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'crm_lead_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(CrmMeeting::class, 'crm_lead_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CrmMessage::class, 'crm_lead_id');
    }
}
