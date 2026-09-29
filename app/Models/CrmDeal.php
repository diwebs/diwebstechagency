<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmDeal extends Model
{
    protected $table = 'crm_deals';

    protected $fillable = [
        'title', 'crm_client_id', 'crm_lead_id', 'service', 'deal_value', 'expected_close_date',
        'probability_percent', 'assigned_sales_rep_id', 'stage'
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'expected_close_date' => 'date'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CrmClient::class, 'crm_client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }

    public function assignedSalesRep(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_sales_rep_id');
    }
}
