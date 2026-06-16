<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id', 'referee_id', 'bonus_amount', 'status', 'paid_at'
    ];

    protected $casts = [
        'bonus_amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}
