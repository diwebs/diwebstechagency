<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'branch_admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }
}
