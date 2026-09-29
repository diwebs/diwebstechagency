<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmClient extends Model
{
    protected $table = 'crm_clients';

    protected $fillable = [
        'crm_company_id', 'company_name', 'contact_person', 'email', 'phone', 'country',
        'address', 'industry', 'active_services', 'account_status', 'assigned_manager_id'
    ];

    protected $casts = [
        'active_services' => 'array'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }

    public function assignedManager(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_manager_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'crm_client_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'crm_client_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(CrmMeeting::class, 'crm_client_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CrmMessage::class, 'crm_client_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(CrmContract::class, 'crm_client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CrmInvoice::class, 'crm_client_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(CrmTicket::class, 'crm_client_id');
    }
}
