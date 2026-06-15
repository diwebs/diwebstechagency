<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtCertificate extends Model
{
    protected $fillable = [
        'user_id', 'exam_id', 'certificate_number', 'grade', 'issue_date', 'qr_code_content', 'signature_path'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'grade' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
