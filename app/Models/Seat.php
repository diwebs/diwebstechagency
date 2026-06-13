<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['cbt_center_id', 'seat_number', 'status'];

    public function center()
    {
        return $this->belongsTo(CbtCenter::class, 'cbt_center_id');
    }

    public function device()
    {
        return $this->hasOne(Device::class);
    }
}
