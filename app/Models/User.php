<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'company_name', 'email', 'password', 'role', 'status', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'referral_code', 'referred_by', 'phone', 'country'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                do {
                    $code = 'REF-' . strtoupper(\Illuminate\Support\Str::random(6));
                } while (static::where('referral_code', $code)->exists());
                $user->referral_code = $code;
            }
        });

        static::created(function ($user) {
            if ($user->role === 'client') {
                \App\Models\CrmLead::firstOrCreate(
                    ['email' => $user->email],
                    [
                        'full_name' => $user->name,
                        'company_name' => $user->company_name ?? null,
                        'email' => $user->email,
                        'phone' => $user->phone ?? null,
                        'country' => $user->country ?? null,
                        'source' => 'Client Registration',
                        'service_interest' => 'Client Portal Account',
                        'status' => 'New',
                        'lead_score' => 25
                    ]
                );
            }
        });
    }

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

    public function getRegionInfoAttribute(): array
    {
        return \App\Helpers\PaymentHelper::getRegionInfo($this->country);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return $this->region_info['symbol'];
    }

    public function getCurrencyCodeAttribute(): string
    {
        return $this->region_info['currency'];
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

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function passkeys()
    {
        return $this->hasMany(UserPasskey::class);
    }

    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'client_id');
    }

    public function teamAccess()
    {
        return $this->hasMany(TeamAccess::class, 'client_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function partnershipRequests()
    {
        return $this->hasMany(PartnershipRequest::class, 'user_id');
    }
}

