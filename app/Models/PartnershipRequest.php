<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnershipRequest extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'website',
        'partnership_type',
        'business_description',
        'synergy_goals',
        'expected_contribution',
        'signed_name',
        'signed_at',
        'status',
        'pdf_path'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
