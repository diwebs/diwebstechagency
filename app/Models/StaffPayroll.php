<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPayroll extends Model
{
    protected $table = 'staff_payrolls';

    protected $fillable = [
        'staff_id', 'month', 'base_salary', 'bonuses', 'deductions', 'tax', 'net_salary', 'payment_status', 'payment_method', 'paid_at'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
