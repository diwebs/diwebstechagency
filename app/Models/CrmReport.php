<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmReport extends Model
{
    protected $table = 'crm_reports';

    protected $fillable = [
        'name', 'report_type', 'parameters', 'generated_by_id'
    ];

    protected $casts = [
        'parameters' => 'array'
    ];
}
