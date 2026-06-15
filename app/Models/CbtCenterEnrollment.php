<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCenterEnrollment extends Model
{
    protected $fillable = [
        'user_id', 'organization_name', 'center_type', 'has_physical_location', 'systems_count', 'internet_quality', 'power_backup', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
