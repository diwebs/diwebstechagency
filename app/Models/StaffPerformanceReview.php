<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPerformanceReview extends Model
{
    protected $table = 'staff_performance_reviews';

    protected $fillable = [
        'staff_id', 'reviewer_id', 'review_date', 'productivity_score', 'task_completion_rate', 'client_satisfaction_score', 'collaboration_score', 'review_notes', 'promotion_recommended'
    ];

    protected $casts = [
        'review_date' => 'date',
        'productivity_score' => 'integer',
        'task_completion_rate' => 'integer',
        'client_satisfaction_score' => 'integer',
        'collaboration_score' => 'integer',
        'promotion_recommended' => 'boolean'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
