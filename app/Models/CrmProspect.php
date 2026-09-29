<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmProspect extends Model
{
    protected $table = 'crm_prospects';

    protected $fillable = [
        'crm_lead_id', 'qualification_score', 'service_requirement_analysis', 'budget_analysis', 'probability_scoring', 'conversion_tracking'
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }
}
