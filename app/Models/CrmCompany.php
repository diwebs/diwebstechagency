<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCompany extends Model
{
    protected $table = 'crm_companies';

    protected $fillable = [
        'organization_name', 'industry', 'annual_value', 'relationship_stage', 'corporate_contact', 'partnership_level'
    ];

    protected $casts = [
        'annual_value' => 'decimal:2'
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(CrmClient::class, 'crm_company_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(CrmContract::class, 'crm_company_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CrmInvoice::class, 'crm_company_id');
    }
}
