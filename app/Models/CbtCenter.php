<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCenter extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'city', 'capacity', 'contact_email', 'contact_phone', 'status',
        'owner_id', 'center_type', 'has_physical_location', 'systems_count', 'internet_quality', 'power_backup', 'commission_rate', 'revenue'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function devices()
    {
        return $this->hasMany(CbtCenterDevice::class, 'cbt_center_id');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
