<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use Notifiable;

    protected $table = 'staff_members';

    protected $fillable = [
        'staff_id', 'name', 'email', 'username', 'password', 'phone', 'address',
        'dob', 'gender', 'nationality', 'profile_picture', 'department_id', 'role_id',
        'employment_type', 'salary_grade', 'base_salary', 'date_hired',
        'reporting_manager_id', 'office_location', 'status', 'two_factor_secret',
        'two_factor_confirmed_at', 'force_password_change'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret'
    ];

    protected $casts = [
        'two_factor_confirmed_at' => 'datetime',
        'force_password_change' => 'boolean',
        'date_hired' => 'date',
        'dob' => 'date',
        'base_salary' => 'decimal:2'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class, 'role_id');
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reporting_manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Staff::class, 'reporting_manager_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(StaffPayroll::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(StaffPerformanceReview::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(StaffLeave::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(StaffActivityLog::class);
    }

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class, 'staff_id');
    }

    // RBAC check helper for blades or controllers
    public function hasPermission(string $permission): bool
    {
        $role = $this->role;
        if (!$role) {
            return false;
        }

        $permissions = is_array($role->permissions) 
            ? $role->permissions 
            : json_decode($role->permissions ?? '[]', true);

        if (in_array('super_admin_access', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }
}
