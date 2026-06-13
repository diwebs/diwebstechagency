<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['seat_id', 'ip_address', 'mac_address', 'device_name', 'system_status'];

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
