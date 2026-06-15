<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCenterDevice extends Model
{
    protected $fillable = [
        'cbt_center_id', 'seat_number', 'device_name', 'ip_address', 'system_status', 'cpu_usage', 'ram_usage', 'webcam_status', 'battery_level'
    ];

    public function center()
    {
        return $this->belongsTo(CbtCenter::class, 'cbt_center_id');
    }
}
