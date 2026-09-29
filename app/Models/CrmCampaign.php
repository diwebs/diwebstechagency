<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmCampaign extends Model
{
    protected $table = 'crm_campaigns';

    protected $fillable = [
        'name', 'campaign_type', 'status', 'budget', 'spent', 'target_audience', 'metrics'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'metrics' => 'array'
    ];
}
