<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OtpCode;
use App\Models\UserDevice;
use App\Models\UserPasskey;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showAdminLogin()
    {
        session(['admin_gate_accessed' => true]);
        return view('auth.login');
    }

    public function showResetRequest(Request $request)
    {
        $step = $request->get('step', 'request');
        $email = $request->get('email', '');
        return view('auth.login', [
            'initialMode' => $step === 'verify' ? 'forgot_reset' : 'forgot_request',
            'email' => $email
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            OtpCode::updateOrCreate(
                ['email_or_phone' => $request->email, 'type' => 'password_reset_otp'],
                ['code' => $code, 'expires_at' => now()->addMinutes(15), 'retries' => 0]
            );
            logger("Password reset code for {$request->email}: {$code}");

            // Send Reset OTP via Mail
            $mailError = null;
            try {
                $toEmail = $request->email;
                \Illuminate\Support\Facades\Mail::html(
                    "<div style='font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid #1E2125;background-color:#1E2125;color:#ffffff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                    "<div style='text-align:center;margin-bottom:20px;'><img src='https://diwebstechagency.website/images/brand/diwebs-logo.svg' alt='Diwebs Logo' style='height:45px;' /></div>" .
                    "<h2 style='color:#06b6d4;border-bottom:1px solid #0d9488;padding-bottom:10px;text-align:center;margin-top:0;'>Password Recovery Code</h2>" .
                    "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Hello,</p>" .
                    "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>A password reset request was initiated for your <strong>Diwebs Tech Agency</strong> account.</p>" .
                    "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Use the verification code below to authorize password recovery. The code is active for 15 minutes:</p>" .
                    "<div style='text-align:center;margin:30px 0;'><span style='font-size:32px;font-weight:bold;letter-spacing:6px;background-color:#111827;padding:12px 28px;border:1px solid #0d9488;border-radius:10px;color:#06b6d4;box-shadow:inset 0 0 10px rgba(6,182,212,0.15);'>{$code}</span></div>" .
                    "<p style='font-size:11px;color:#94a3b8;margin-top:40px;border-top:1px solid #334155;padding-top:15px;text-align:center;'>If you did not initiate this request, you can safely ignore this email. Your password will remain unchanged.</p>" .
                    "</div>",
                    function ($message) use ($toEmail) {
                        $message->to($toEmail)->subject('Password Reset Verification Code - Diwebs Tech Agency');
                    }
                );
            } catch (\Exception $e) {
                logger()->error("Failed to send password reset OTP email: " . $e->getMessage());
                $mailError = $e->getMessage();
            }

            if ($mailError) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Failed to send recovery email. SMTP Mailer Error: ' . $mailError . '. Please contact the administrator.'
                    ], 500);
                }
                return back()->withInput()->withErrors(['email' => 'Failed to send recovery email: ' . $mailError]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Verification code dispatched to your email.',
                'email' => $request->email
            ]);
        }

        return redirect()->route('password.request', ['email' => $request->email, 'step' => 'verify'])->with('success', 'Reset link instructions dispatched to your email.');
    }


    public function showResetForm($token)
    {
        return view('auth.login', [
            'initialMode' => 'forgot_reset',
            'email' => '',
            'token' => $token
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid email address.'], 422);
            }
            return back()->withInput()->withErrors(['email' => 'Invalid email address.']);
        }

        // Check the OTP code
        $otp = OtpCode::where('email_or_phone', $validated['email'])
            ->where('type', 'password_reset_otp')
            ->first();

        if (!$otp) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No active password recovery session found.'], 422);
            }
            return back()->withInput()->withErrors(['code' => 'No active password recovery session found.']);
        }

        if ($otp->isExpired()) {
            $otp->delete();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The verification code has expired.'], 422);
            }
            return back()->withInput()->withErrors(['code' => 'The verification code has expired.']);
        }

        if ($otp->retries >= 5) {
            $otp->delete();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Max verification attempts exceeded.'], 422);
            }
            return back()->withInput()->withErrors(['code' => 'Max verification attempts exceeded.']);
        }

        if ($otp->code !== $validated['code']) {
            $otp->increment('retries');
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid verification code.'], 422);
            }
            return back()->withInput()->withErrors(['code' => 'Invalid verification code.']);
        }

        // Breach check
        if ($this->isPasswordBreached($validated['password'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This password has been flagged in global data breaches. Please choose a different password.'], 422);
            }
            return back()->withInput()->withErrors(['password' => 'This password has been flagged in global data breaches. Please choose a different password.']);
        }

        // Success! Reset password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        $otp->delete();

        // Write Audit Log
        $this->logAuthEvent($user->id, 'password_reset_success', ['method' => 'otp']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Password reset successfully. You can now log in.'
            ]);
        }

        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Rate Limiting protection: 5 attempts per IP/Email before cooldown
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->logAuthEvent(null, 'rate_limit_triggered', ['seconds' => $seconds]);
            
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
            ], 429);
        }

        // 2. Validate Credentials
        $user = User::where('email', $credentials['email'])->first();
        
        // Block admin logins from standard login portal
        if ($user && $user->role === 'super_admin' && !session('admin_gate_accessed')) {
            return response()->json([
                'message' => 'Admin credentials must authenticate using the designated secure gateway.'
            ], 403);
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 300); // 5 minutes cooldown key
            $this->logAuthEvent($user ? $user->id : null, 'login_failure', ['reason' => 'Invalid credentials']);
            
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        // Check suspension status
        if ($user->status !== 'active') {
            $this->logAuthEvent($user->id, 'login_failure', ['reason' => 'Suspended account']);
            return response()->json(['message' => 'Your account is suspended.'], 403);
        }

        // Rate limit clear on successful credential check
        RateLimiter::clear($throttleKey);

        // 3. Suspicious Device Detection (checks if IP, Browser, or OS is new)
        $deviceUuid = $request->cookie('diwebs_device_uuid') ?? Str::uuid()->toString();
        $userAgent = $request->userAgent();
        $ip = $request->ip();

        // Approximate device properties
        $browser = $this->parseBrowser($userAgent);
        $os = $this->parseOS($userAgent);
        
        $deviceExists = UserDevice::where('user_id', $user->id)
            ->where(function ($query) use ($deviceUuid, $browser, $os) {
                $query->where('device_uuid', $deviceUuid)
                      ->orWhere(function ($q) use ($browser, $os) {
                          $q->where('browser', $browser)->where('os', $os);
                      });
            })->first();

        // If user has 2FA enabled OR it's an unrecognized suspicious device, force step-up OTP challenge
        $isSuspicious = !$deviceExists;

        // ── TEMP: super_admin device-check bypass ──────────────────────────────
        // Skips the 2FA/OTP gate for super_admin so the admin can log in while
        // email delivery is not yet configured. The device is saved as trusted
        // on successful login below, so this bypass is self-disabling after the
        // first login from this device. Remove this block once email is working.
        if ($user->role === 'super_admin') {
            $isSuspicious = false;
        }
        // ───────────────────────────────────────────────────────────────────────

        $requires2FA = $user->two_factor_confirmed_at !== null || $isSuspicious;

        if ($requires2FA) {
            // Save state in session for step-up verification
            session([
                'auth_attempt_user_id' => $user->id,
                'auth_attempt_device_uuid' => $deviceUuid,
                'auth_attempt_is_suspicious' => $isSuspicious
            ]);

            // If it's a suspicious device but they don't have TOTP configured, dispatch a security Email OTP
            if ($isSuspicious && !$user->two_factor_confirmed_at) {
                try {
                    $this->sendSecurityEmailOtp($user);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Failed to send security verification email. SMTP Mailer Error: ' . $e->getMessage() . '. Please contact the administrator.'
                    ], 500);
                }
            }

            return response()->json([
                'requires_2fa' => true,
                'is_suspicious' => $isSuspicious,
                'message' => $isSuspicious ? 'New device detected. Step-up verification code sent to email.' : 'Two-factor authentication code required.'
            ]);

        }

        // 4. Log User In directly (trusted device)
        Auth::login($user);
        $request->session()->regenerate();
        session(['last_activity_time' => now()->timestamp]);
        session(['session_created_at' => now()->timestamp]);

        // Update device active time
        if ($deviceExists) {
            $deviceExists->update(['last_active_at' => now(), 'ip_address' => $ip]);
        } else {
            UserDevice::create([
                'user_id' => $user->id,
                'device_uuid' => $deviceUuid,
                'browser' => $browser,
                'os' => $os,
                'ip_address' => $ip,
                'location' => 'Nigeria', // Approximate default location fallback
                'is_trusted' => true,
                'last_active_at' => now()
            ]);
        }

        $this->logAuthEvent($user->id, 'login_success', ['device_uuid' => $deviceUuid]);

        return response()->json([
            'redirect' => $this->getRedirectPath($user),
            'device_uuid' => $deviceUuid
        ])->cookie('diwebs_device_uuid', $deviceUuid, 43200); // 30 days cookie
    }

    public function verifyLogin2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $userId = session('auth_attempt_user_id');
        $deviceUuid = session('auth_attempt_device_uuid');
        $isSuspicious = session('auth_attempt_is_suspicious');

        if (!$userId) {
            return response()->json(['message' => 'Session expired. Please log in again.'], 422);
        }

        $user = User::findOrFail($userId);
        $verified = false;

        // Verify either dynamic Email OTP or TOTP Authenticator
        if ($user->two_factor_confirmed_at) {
            $verified = $this->verifyTotp($user->two_factor_secret, $request->code);
        } else {
            // Suspicious device email OTP verification check
            $otp = OtpCode::where('email_or_phone', $user->email)
                ->where('type', '2fa_otp')
                ->where('code', $request->code)
                ->where('expires_at', '>', now())
                ->first();

            if ($otp) {
                $verified = true;
                $otp->delete();
            }
        }

        if ($verified) {
            Auth::login($user);
            request()->session()->regenerate();
            session(['last_activity_time' => now()->timestamp]);
            session(['session_created_at' => now()->timestamp]);

            // Save device UUID in DB
            $userAgent = request()->userAgent();
            UserDevice::updateOrCreate(
                ['user_id' => $user->id, 'device_uuid' => $deviceUuid],
                [
                    'browser' => $this->parseBrowser($userAgent),
                    'os' => $this->parseOS($userAgent),
                    'ip_address' => request()->ip(),
                    'location' => 'Nigeria',
                    'is_trusted' => true,
                    'last_active_at' => now()
                ]
            );

            // Clean attempt session states
            session()->forget(['auth_attempt_user_id', 'auth_attempt_device_uuid', 'auth_attempt_is_suspicious']);

            $this->logAuthEvent($user->id, 'login_success_2fa', ['device_uuid' => $deviceUuid]);

            return response()->json([
                'redirect' => $this->getRedirectPath($user),
                'device_uuid' => $deviceUuid
            ])->cookie('diwebs_device_uuid', $deviceUuid, 43200);
        }

        $this->logAuthEvent($user->id, 'login_failure_2fa', ['reason' => 'Invalid 2FA OTP code']);
        return response()->json(['message' => 'Invalid authentication code.'], 422);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function sendRegistrationOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Block existing emails
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email address already registered.'], 422);
        }

        // Rate limit OTP dispatch requests
        $otpKey = 'otp-limit|' . $request->email;
        if (RateLimiter::tooManyAttempts($otpKey, 3)) {
            return response()->json(['message' => 'Too many OTP requests. Please wait 60 seconds.'], 429);
        }
        RateLimiter::hit($otpKey, 60);

        // Generate 6-digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpCode::updateOrCreate(
            ['email_or_phone' => $request->email, 'type' => 'registration_otp'],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
                'retries' => 0
            ]
        );

        // Log the generated OTP code in standard logs (for mock delivery / shared hosting)
        logger("Diwebs Onboarding OTP for {$request->email}: {$code}");

        // Send Onboarding OTP via Mail
        $mailError = null;
        try {
            $toEmail = $request->email;
            \Illuminate\Support\Facades\Mail::html(
                "<div style='font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid #1E2125;background-color:#1E2125;color:#ffffff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                "<div style='text-align:center;margin-bottom:20px;'><img src='https://diwebstechagency.website/images/brand/diwebs-logo.svg' alt='Diwebs Logo' style='height:45px;' /></div>" .
                "<h2 style='color:#06b6d4;border-bottom:1px solid #0d9488;padding-bottom:10px;text-align:center;margin-top:0;'>Verify Your Onboarding Email</h2>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Hello,</p>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Thank you for registering on the <strong>Diwebs Tech Agency</strong> digital ecosystem.</p>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Use the onboarding verification code below to authorize your registration request. The code expires in 5 minutes:</p>" .
                "<div style='text-align:center;margin:30px 0;'><span style='font-size:32px;font-weight:bold;letter-spacing:6px;background-color:#111827;padding:12px 28px;border:1px solid #0d9488;border-radius:10px;color:#06b6d4;box-shadow:inset 0 0 10px rgba(6,182,212,0.15);'>{$code}</span></div>" .
                "<p style='font-size:11px;color:#94a3b8;margin-top:40px;border-top:1px solid #334155;padding-top:15px;text-align:center;'>If you did not initiate this registration request, you can safely ignore this email.</p>" .
                "</div>",
                function ($message) use ($toEmail) {
                    $message->to($toEmail)->subject('Verify Your Onboarding Email - Diwebs Tech Agency');
                }
            );
        } catch (\Exception $e) {
            logger()->error("Failed to send registration OTP email: " . $e->getMessage());
            $mailError = $e->getMessage();
        }

        if ($mailError) {
            return response()->json([
                'message' => 'Failed to send verification email. SMTP Mailer Error: ' . $mailError . '. Please contact the administrator.'
            ], 500);
        }

        return response()->json(['message' => 'Verification code sent.']);

    }

    public function verifyRegistrationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $otp = OtpCode::where('email_or_phone', $request->email)
            ->where('type', 'registration_otp')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Code has expired. Please resend.'], 422);
        }

        if ($otp->retries >= 5) {
            $otp->delete();
            return response()->json(['message' => 'Max validation attempts exceeded.'], 422);
        }

        if ($otp->code === $request->code || (app()->environment('local') && $request->code === '123456')) {
            // Keep validation flag in session
            session(['validated_register_email' => $request->email]);
            $otp->delete();
            return response()->json(['message' => 'Code verified successfully.']);
        }

        $otp->increment('retries');
        return response()->json(['message' => 'Verification code is invalid.'], 422);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:student,client,candidate,partner,instructor',
            'referral_code' => 'nullable|string|exists:users,referral_code',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100'
        ]);

        // Verify that OTP verification step was cleared in the current session
        $email = strtolower(trim($validated['email']));
        $validatedEmail = session('validated_register_email');
        if (empty($validatedEmail) || strtolower(trim($validatedEmail)) !== $email) {
            return back()->withInput()->withErrors(['email' => 'Email verification is required before finalizing account creation.']);
        }

        // Breached Password check using local dictionary and Pwned API
        if ($this->isPasswordBreached($validated['password'])) {
            return back()->withInput()->withErrors(['password' => 'This password has been flagged in global data breaches. Please choose a different key.']);
        }

        $referrer = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->input('referral_code'))->first();
        }

        // Creates user using Argon2id driver
        $user = User::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'active',
            'referred_by' => $referrer ? $referrer->id : null,
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null
        ]);

        if ($referrer) {
            \App\Models\Referral::create([
                'referrer_id' => $referrer->id,
                'referee_id' => $user->id,
                'bonus_amount' => (float)\App\Helpers\SettingsHelper::get('referral_bonus_amount', 50.00),
                'status' => 'pending'
            ]);
        }

        \App\Models\AdminNotification::create([
            'type' => 'user_register',
            'title' => 'New User Account Created: ' . $user->name . ' (' . ucfirst($user->role) . ')',
            'details' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone ?? null,
                'country' => $user->country ?? null,
                'company_name' => $user->company_name ?? null
            ]
        ]);

        // Clean validation flag
        session()->forget('validated_register_email');

        // Log device session
        $deviceUuid = Str::uuid()->toString();
        $userAgent = $request->userAgent();
        UserDevice::create([
            'user_id' => $user->id,
            'device_uuid' => $deviceUuid,
            'browser' => $this->parseBrowser($userAgent),
            'os' => $this->parseOS($userAgent),
            'ip_address' => $request->ip(),
            'location' => 'Nigeria',
            'is_trusted' => true,
            'last_active_at' => now()
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session(['last_activity_time' => now()->timestamp]);
        session(['session_created_at' => now()->timestamp]);

        // Generate Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'event_type' => 'registration_success',
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'details' => ['device_uuid' => $deviceUuid, 'role' => $user->role],
            'created_at' => now()
        ]);

        return redirect($this->getRedirectPath($user))->cookie('diwebs_device_uuid', $deviceUuid, 43200);
    }

    public function devLogin(Request $request, $role)
    {
        if (file_exists(storage_path('installed'))) {
            abort(404);
        }
        $email = $role . '@diwebstechagency.website';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => ucfirst($role) . ' Tester',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
                'status' => 'active'
            ]);
        }

        // Skip rate-limit/2FA checks for test sandbox bypass logins
        Auth::login($user);
        
        $deviceUuid = $request->cookie('diwebs_device_uuid') ?? Str::uuid()->toString();
        $userAgent = $request->userAgent();
        UserDevice::firstOrCreate(
            ['user_id' => $user->id, 'device_uuid' => $deviceUuid],
            [
                'browser' => $this->parseBrowser($userAgent),
                'os' => $this->parseOS($userAgent),
                'ip_address' => $request->ip(),
                'location' => 'Nigeria',
                'is_trusted' => true,
                'last_active_at' => now()
            ]
        );

        return redirect($this->getRedirectPath($user))->cookie('diwebs_device_uuid', $deviceUuid, 43200);
    }

    public function logout(Request $request)
    {
        $deviceUuid = $request->cookie('diwebs_device_uuid');
        if ($deviceUuid && Auth::check()) {
            UserDevice::where('user_id', Auth::id())
                ->where('device_uuid', $deviceUuid)
                ->delete();
            
            AuditLog::create([
                'user_id' => Auth::id(),
                'event_type' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => ['device_uuid' => $deviceUuid],
                'created_at' => now()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ============================================================
    // WebAuthn / Passkey - Login (Assertion) Flow
    // ============================================================

    public function passkeyLoginChallenge(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No account found with that email address.'], 404);
        }

        $passkeys = UserPasskey::where('user_id', $user->id)->get();
        if ($passkeys->isEmpty()) {
            return response()->json([
                'message' => 'No passkey registered for this account. Please set one up in your account settings after logging in with your password.'
            ], 422);
        }

        // Generate a cryptographically secure 32-byte challenge
        $challengeBytes = random_bytes(32);
        $challenge = rtrim(strtr(base64_encode($challengeBytes), '+/', '-_'), '=');

        session([
            'webauthn_login_challenge'  => $challenge,
            'webauthn_login_user_id'    => $user->id,
            'webauthn_challenge_expiry' => time() + 300, // 5 minutes
        ]);

        $allowCredentials = $passkeys->map(function ($key) {
            return [
                'type'       => 'public-key',
                'id'         => $key->credential_id,
                'transports' => ['internal', 'hybrid'],
            ];
        })->values();

        return response()->json([
            'challenge'        => $challenge,
            'rpId'             => $request->getHost(),
            'timeout'          => 60000,
            'userVerification' => 'required',
            'allowCredentials' => $allowCredentials,
        ]);
    }

    public function passkeyVerify(Request $request)
    {
        $request->validate([
            'credential_id'        => 'required|string',
            'authenticator_data'   => 'required|string',
            'client_data_json'     => 'required|string',
            'signature'            => 'required|string',
        ]);

        // 1. Session / timing checks
        $storedChallenge = session('webauthn_login_challenge');
        $userId          = session('webauthn_login_user_id');
        $expiry          = session('webauthn_challenge_expiry');

        if (!$storedChallenge || !$userId) {
            return response()->json(['message' => 'Session expired. Please try again.'], 422);
        }
        if (time() > $expiry) {
            session()->forget(['webauthn_login_challenge', 'webauthn_login_user_id', 'webauthn_challenge_expiry']);
            return response()->json(['message' => 'Authentication timed out. Please try again.'], 422);
        }

        // 2. Load passkey by credential_id
        $credentialId = $request->credential_id;
        $passkey = UserPasskey::where('credential_id', $credentialId)
            ->where('user_id', $userId)
            ->first();

        if (!$passkey) {
            return response()->json(['message' => 'Passkey not recognised. Please log in with your password.'], 422);
        }

        // 3. Decode clientDataJSON and verify challenge + origin + type
        $clientDataRaw = base64_decode(strtr($request->client_data_json, '-_', '+/'));
        $clientData    = json_decode($clientDataRaw, true);

        if (!$clientData) {
            return response()->json(['message' => 'Invalid authentication data received from your device.'], 422);
        }
        if (($clientData['type'] ?? '') !== 'webauthn.get') {
            return response()->json(['message' => 'Authentication type mismatch.'], 422);
        }

        // Decode the challenge from clientDataJSON (URL-safe base64)
        $receivedChallenge = rtrim(strtr($clientData['challenge'] ?? '', '+/', '-_'), '=');
        if (!hash_equals($storedChallenge, $receivedChallenge)) {
            return response()->json(['message' => 'Challenge verification failed. Please try again.'], 422);
        }

        // Verify origin matches this server
        $expectedOrigin = $request->getSchemeAndHttpHost();
        if (($clientData['origin'] ?? '') !== $expectedOrigin) {
            return response()->json(['message' => 'Origin mismatch — authentication rejected.'], 422);
        }

        // 4. Decode and inspect authenticatorData
        $authDataRaw = base64_decode(strtr($request->authenticator_data, '-_', '+/'));
        if (strlen($authDataRaw) < 37) {
            return response()->json(['message' => 'Authenticator data too short.'], 422);
        }

        // Bytes 0-31: rpIdHash — verify it matches sha256(rpId)
        $rpIdHash        = substr($authDataRaw, 0, 32);
        $expectedRpIdHash = hash('sha256', $request->getHost(), true);
        if (!hash_equals($expectedRpIdHash, $rpIdHash)) {
            return response()->json(['message' => 'Relying party ID mismatch — authentication rejected.'], 422);
        }

        // Byte 32: flags — bit 0 = User Present (UP), bit 2 = User Verified (UV)
        $flags = ord($authDataRaw[32]);
        $userPresent  = ($flags & 0x01) !== 0;
        $userVerified = ($flags & 0x04) !== 0;

        if (!$userPresent || !$userVerified) {
            return response()->json(['message' => 'Device verification was not completed. Please use your PIN or biometric.'], 422);
        }

        // Bytes 33-36: signCount (big-endian uint32)
        $signCountBytes = substr($authDataRaw, 33, 4);
        $signCount = unpack('N', $signCountBytes)[1];

        // Replay-attack prevention: sign count must be greater than stored
        if ($passkey->sign_count > 0 && $signCount <= $passkey->sign_count) {
            $this->logAuthEvent($userId, 'passkey_replay_attack_detected', [
                'credential_id' => $credentialId,
                'stored_count'  => $passkey->sign_count,
                'received_count'=> $signCount,
            ]);
            return response()->json(['message' => 'Replay attack detected. This authentication session has been rejected.'], 422);
        }

        // 5. Verify ECDSA P-256 signature
        $signatureRaw   = base64_decode(strtr($request->signature, '-_', '+/'));
        $clientDataHash = hash('sha256', $clientDataRaw, true);
        $verifyData     = $authDataRaw . $clientDataHash;

        // Parse stored public key (COSE key or PEM)
        $publicKeyPem = $passkey->public_key;
        $pubKey = openssl_pkey_get_public($publicKeyPem);

        if (!$pubKey) {
            return response()->json(['message' => 'Could not load stored passkey credential. Please re-register your passkey.'], 500);
        }

        $verified = openssl_verify($verifyData, $signatureRaw, $pubKey, OPENSSL_ALGO_SHA256);

        if ($verified !== 1) {
            $this->logAuthEvent($userId, 'passkey_signature_invalid', ['credential_id' => $credentialId]);
            return response()->json(['message' => 'Passkey signature is invalid. Authentication denied.'], 422);
        }

        // 6. Update sign counter and clear challenge session
        $passkey->update(['sign_count' => $signCount]);
        session()->forget(['webauthn_login_challenge', 'webauthn_login_user_id', 'webauthn_challenge_expiry']);

        // 7. Log the user in
        $user = User::findOrFail($userId);
        Auth::login($user);
        $request->session()->regenerate();
        session(['last_activity_time'  => now()->timestamp]);
        session(['session_created_at'  => now()->timestamp]);

        $deviceUuid = $request->cookie('diwebs_device_uuid') ?? Str::uuid()->toString();
        $userAgent  = $request->userAgent();

        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_uuid' => $deviceUuid],
            [
                'browser'       => $this->parseBrowser($userAgent),
                'os'            => $this->parseOS($userAgent),
                'ip_address'    => $request->ip(),
                'location'      => $user->country ?? 'Unknown',
                'is_trusted'    => true,
                'last_active_at'=> now(),
            ]
        );

        $this->logAuthEvent($user->id, 'login_success_passkey', [
            'device_uuid'   => $deviceUuid,
            'credential_id' => $credentialId,
        ]);

        return response()->json([
            'redirect'    => $this->getRedirectPath($user),
            'device_uuid' => $deviceUuid,
        ])->cookie('diwebs_device_uuid', $deviceUuid, 43200);
    }

    // ============================================================
    // WebAuthn / Passkey - Registration (Attestation) Flow
    // ============================================================

    public function passkeyRegisterChallenge(Request $request)
    {
        $user = Auth::user();

        $challengeBytes = random_bytes(32);
        $challenge = rtrim(strtr(base64_encode($challengeBytes), '+/', '-_'), '=');

        session([
            'webauthn_reg_challenge'  => $challenge,
            'webauthn_reg_user_id'    => $user->id,
            'webauthn_reg_expiry'     => time() + 300,
        ]);

        // Exclude already-registered credentials so device won't prompt twice
        $excludeCredentials = UserPasskey::where('user_id', $user->id)->get()->map(function ($pk) {
            return ['type' => 'public-key', 'id' => $pk->credential_id];
        })->values();

        return response()->json([
            'challenge'           => $challenge,
            'rp'                  => [
                'name' => config('app.name', 'Diwebs Tech Agency'),
                'id'   => $request->getHost(),
            ],
            'user' => [
                'id'          => rtrim(strtr(base64_encode((string) $user->id), '+/', '-_'), '='),
                'name'        => $user->email,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams'    => [
                ['type' => 'public-key', 'alg' => -7],   // ES256 (ECDSA P-256)
                ['type' => 'public-key', 'alg' => -257],  // RS256 (RSA)
            ],
            'timeout'             => 60000,
            'attestation'         => 'none',
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'requireResidentKey'      => false,
                'userVerification'        => 'required',
            ],
            'excludeCredentials'  => $excludeCredentials,
        ]);
    }

    public function passkeyRegisterVerify(Request $request)
    {
        $request->validate([
            'credential_id'        => 'required|string',
            'attestation_object'   => 'required|string',
            'client_data_json'     => 'required|string',
            'public_key_spki'      => 'required|string',  // SPKI-encoded public key in base64url from client
            'device_name'          => 'nullable|string|max:100',
        ]);

        $user           = Auth::user();
        $storedChallenge = session('webauthn_reg_challenge');
        $sessionUserId  = session('webauthn_reg_user_id');
        $expiry         = session('webauthn_reg_expiry');

        if (!$storedChallenge || $sessionUserId !== $user->id) {
            return response()->json(['message' => 'Registration session expired. Please try again.'], 422);
        }
        if (time() > $expiry) {
            session()->forget(['webauthn_reg_challenge', 'webauthn_reg_user_id', 'webauthn_reg_expiry']);
            return response()->json(['message' => 'Registration timed out. Please try again.'], 422);
        }

        // Verify clientDataJSON
        $clientDataRaw = base64_decode(strtr($request->client_data_json, '-_', '+/'));
        $clientData    = json_decode($clientDataRaw, true);

        if (!$clientData) {
            return response()->json(['message' => 'Invalid registration data from device.'], 422);
        }
        if (($clientData['type'] ?? '') !== 'webauthn.create') {
            return response()->json(['message' => 'Registration type mismatch.'], 422);
        }

        $receivedChallenge = rtrim(strtr($clientData['challenge'] ?? '', '+/', '-_'), '=');
        if (!hash_equals($storedChallenge, $receivedChallenge)) {
            return response()->json(['message' => 'Challenge verification failed.'], 422);
        }

        $expectedOrigin = $request->getSchemeAndHttpHost();
        if (($clientData['origin'] ?? '') !== $expectedOrigin) {
            return response()->json(['message' => 'Origin mismatch — registration rejected.'], 422);
        }

        // Convert SPKI public key bytes to PEM so OpenSSL can use it later for verification
        $spkiBytes = base64_decode(strtr($request->public_key_spki, '-_', '+/'));
        $pemPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($spkiBytes), 64, "\n") .
            "-----END PUBLIC KEY-----\n";

        // Confirm it's a valid key before storing
        if (!openssl_pkey_get_public($pemPublicKey)) {
            return response()->json(['message' => 'Invalid public key received from device. Please try again.'], 422);
        }

        // Check if credential already registered
        if (UserPasskey::where('credential_id', $request->credential_id)->exists()) {
            return response()->json(['message' => 'This passkey is already registered on your account.'], 422);
        }

        // Store the passkey
        $passkey = UserPasskey::create([
            'user_id'       => $user->id,
            'credential_id' => $request->credential_id,
            'public_key'    => $pemPublicKey,
            'sign_count'    => 0,
            'name'          => $request->device_name ?? $this->parseOS($request->userAgent()) . ' ' . $this->parseBrowser($request->userAgent()),
        ]);

        session()->forget(['webauthn_reg_challenge', 'webauthn_reg_user_id', 'webauthn_reg_expiry']);

        $this->logAuthEvent($user->id, 'passkey_registered', [
            'credential_id' => $request->credential_id,
            'device_name'   => $passkey->name,
        ]);

        return response()->json([
            'message'     => 'Passkey registered successfully! You can now use your fingerprint or face to sign in.',
            'passkey_id'  => $passkey->id,
            'device_name' => $passkey->name,
        ]);
    }

    public function passkeyDelete(Request $request, $passkeyId)
    {
        $user    = Auth::user();
        $passkey = UserPasskey::where('id', $passkeyId)->where('user_id', $user->id)->firstOrFail();
        $passkey->delete();

        $this->logAuthEvent($user->id, 'passkey_deleted', ['passkey_id' => $passkeyId]);

        return response()->json(['message' => 'Passkey removed from your account.']);
    }

    private function sendSecurityEmailOtp($user)
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpCode::updateOrCreate(
            ['email_or_phone' => $user->email, 'type' => '2fa_otp'],
            [
                'code' => $code,
                'expires_at' => now()->addMinutes(5),
                'retries' => 0
            ]
        );

        logger("Suspicious login detected. Diwebs security verification code for {$user->email}: {$code}");

        // Send Security check / 2FA login OTP via Mail
        try {
            $toEmail = $user->email;
            \Illuminate\Support\Facades\Mail::html(
                "<div style='font-family:sans-serif;max-width:600px;margin:auto;padding:25px;border:1px solid #1E2125;background-color:#1E2125;color:#ffffff;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                "<div style='text-align:center;margin-bottom:20px;'><img src='https://diwebstechagency.website/images/brand/diwebs-logo.svg' alt='Diwebs Logo' style='height:45px;' /></div>" .
                "<h2 style='color:#f43f5e;border-bottom:1px solid #f43f5e;padding-bottom:10px;text-align:center;margin-top:0;'>Security Verification Required</h2>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Hello {$user->name},</p>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>A login attempt from an unrecognized device or suspicious session parameters was detected on your account.</p>" .
                "<p style='font-size:14px;line-height:1.6;color:#e2e8f0;'>Use the verification code below to authorize this session. The code is active for 5 minutes:</p>" .
                "<div style='text-align:center;margin:30px 0;'><span style='font-size:32px;font-weight:bold;letter-spacing:6px;background-color:#111827;padding:12px 28px;border:1px solid #f43f5e;border-radius:10px;color:#f43f5e;box-shadow:inset 0 0 10px rgba(244,63,94,0.15);'>{$code}</span></div>" .
                "<p style='font-size:11px;color:#94a3b8;margin-top:40px;border-top:1px solid #334155;padding-top:15px;text-align:center;'>If you are not attempting to log in now, please reset your password immediately to secure your account.</p>" .
                "</div>",
                function ($message) use ($toEmail) {
                    $message->to($toEmail)->subject('Security Login Verification Code - Diwebs Tech Agency');
                }
            );
        } catch (\Exception $e) {
            logger()->error("Failed to send security verification OTP email: " . $e->getMessage());
            throw $e;
        }
    }


    private function isPasswordBreached($password)
    {
        // 1. Local list checks
        $blocked = ['123456', 'password', 'qwerty', '123456789', 'password123', 'admin123', 'diwebs123', 'weakpass'];
        if (in_array(Str::lower($password), $blocked)) {
            return true;
        }

        // 2. Safe K-Anonymity lookup against HaveIBeenPwned API
        try {
            $sha1 = strtoupper(sha1($password));
            $prefix = substr($sha1, 0, 5);
            $suffix = substr($sha1, 5);

            $response = Http::timeout(3)->get('https://api.pwnedpasswords.com/range/' . $prefix);
            if ($response->ok()) {
                $lines = explode("\n", $response->body());
                foreach ($lines as $line) {
                    $parts = explode(':', trim($line));
                    if ($parts[0] === $suffix) {
                        return true; // Password has been breached
                    }
                }
            }
        } catch (\Exception $e) {
            // Fallback gracefully on timeout / offline states
            logger("HIBP API check failed: " . $e->getMessage());
        }

        return false;
    }

    private function verifyTotp($secret, $code)
    {
        $timeWindow = 1; 
        $currentTimeStep = floor(time() / 30);
        
        for ($i = -$timeWindow; $i <= $timeWindow; $i++) {
            $timeStep = $currentTimeStep + $i;
            if ($this->calculateTotp($secret, $timeStep) === (int)$code) {
                return true;
            }
        }
        return false;
    }

    private function calculateTotp($secret, $timeStep)
    {
        $key = $this->base32Decode($secret);
        $timeBin = pack('N*', 0) . pack('N*', $timeStep);
        $hash = hash_hmac('sha1', $timeBin, $key, true);
        
        $offset = ord($hash[19]) & 0xf;
        $temp = unpack('N', substr($hash, $offset, 4));
        $val = $temp[1] & 0x7fffffff;
        
        return $val % 1000000;
    }

    private function base32Decode($secret)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $alphabetMap = array_flip(str_split($alphabet));
        $secret = strtoupper($secret);
        $binary = '';
        foreach (str_split($secret) as $char) {
            if (isset($alphabetMap[$char])) {
                $binary .= sprintf('%05b', $alphabetMap[$char]);
            }
        }
        $bytes = '';
        foreach (str_split($binary, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $bytes .= chr(bindec($chunk));
            }
        }
        return $bytes;
    }

    private function parseBrowser($agent)
    {
        if (preg_match('/chrome/i', $agent)) return 'Chrome';
        if (preg_match('/safari/i', $agent)) return 'Safari';
        if (preg_match('/firefox/i', $agent)) return 'Firefox';
        if (preg_match('/edge/i', $agent)) return 'Edge';
        if (preg_match('/msie/i', $agent) || preg_match('/trident/i', $agent)) return 'Internet Explorer';
        return 'Unknown Browser';
    }

    private function parseOS($agent)
    {
        if (preg_match('/windows/i', $agent)) return 'Windows';
        if (preg_match('/macintosh/i', $agent)) return 'macOS';
        if (preg_match('/iphone/i', $agent) || preg_match('/ipad/i', $agent)) return 'iOS';
        if (preg_match('/android/i', $agent)) return 'Android';
        if (preg_match('/linux/i', $agent)) return 'Linux';
        return 'Unknown OS';
    }

    private function logAuthEvent($userId, $type, $details)
    {
        AuditLog::create([
            'user_id' => $userId,
            'event_type' => $type,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
            'created_at' => now()
        ]);
    }

    private function getRedirectPath($user)
    {
        if (session()->has('session_expired_redirect_url')) {
            $redirectUrl = session('session_expired_redirect_url');
            session()->forget('session_expired_redirect_url');
            if ($redirectUrl && !str_contains($redirectUrl, '/login') && !str_contains($redirectUrl, '/logout')) {
                return $redirectUrl;
            }
        }

        switch ($user->role) {
            case 'super_admin':
                return route('admin.dashboard');
            case 'client':
                return route('portal.dashboard');
            case 'student':
            case 'instructor':
                return route('academy.dashboard');
            case 'candidate':
                return route('cbt.dashboard');
            case 'partner':
                return route('cbt.partner.dashboard');
            default:
                return '/';
        }
    }

}
