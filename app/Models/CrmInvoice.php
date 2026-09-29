<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmInvoice extends Model
{
    protected $table = 'crm_invoices';

    protected $fillable = [
        'invoice_number', 'crm_client_id', 'crm_company_id', 'title', 'description',
        'amount', 'tax', 'total_amount', 'status', 'due_date', 'paid_at', 'is_recurring', 'billing_interval'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'is_recurring' => 'boolean'
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
