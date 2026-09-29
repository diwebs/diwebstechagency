<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmContract extends Model
{
    protected $table = 'crm_contracts';

    protected $fillable = [
        'contract_number', 'title', 'contract_type', 'crm_client_id', 'crm_company_id',
        'value', 'start_date', 'end_date', 'status', 'signed_at', 'signed_copy_path', 'signature_data'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }
}
