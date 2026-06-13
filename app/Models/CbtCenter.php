<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCenter extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'city', 'capacity', 'contact_email', 'contact_phone', 'status'
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }
}
