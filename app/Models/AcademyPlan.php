<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class AcademyPlan extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'plan_name',
        'includes_live_class', 'includes_audio', 'includes_mentorship',
        'status', 'expires_at', 'created_by', 'notes',
    ];

    protected $casts = [
        'includes_live_class'  => 'boolean',
        'includes_audio'       => 'boolean',
        'includes_mentorship'  => 'boolean',
        'expires_at'           => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    /**
     * Only plans that are active and not yet expired.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }
}
