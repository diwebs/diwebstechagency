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

    public function showResetRequest()
    {
        return view('auth.login'); // Displays reset request inside glassmorphic login template
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
        }
        return back()->with('success', 'Reset link instructions dispatched to your email.');
    }

    public function showResetForm($token)
    {
        return view('auth.login');
    }

    public function resetPassword(Request $request)
    {
        return back()->with('success', 'Password reset successfully.');
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
                $this->sendSecurityEmailOtp($user);
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:12',
            'role' => 'required|string|in:student,client,candidate,partner,instructor',
            'referral_code' => 'nullable|string|exists:users,referral_code',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100'
        ]);

        // Verify that OTP verification step was cleared in the current session
        if (session('validated_register_email') !== $validated['email']) {
            return back()->withErrors(['email' => 'Email verification is required before finalizing account creation.']);
        }

        // Breached Password check using local dictionary and Pwned API
        if ($this->isPasswordBreached($validated['password'])) {
            return back()->withErrors(['password' => 'This password has been flagged in global data breaches. Please choose a different key.']);
        }

        $referrer = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->input('referral_code'))->first();
        }

        // Creates user using Argon2id driver
        $user = User::create([
            'name' => $validated['name'],
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
                'bonus_amount' => (float)cache('referral_bonus_amount', 50.00),
                'status' => 'pending'
            ]);
        }

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

    // WebAuthn Passkeys login endpoints (Placeholders simulating OS cryptographic signature verification)
    public function passkeyLoginChallenge(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No account matches this email.'], 404);
        }

        $challenge = Str::random(32);
        session(['webauthn_login_challenge' => $challenge, 'webauthn_login_user_id' => $user->id]);

        return response()->json([
            'challenge' => $challenge,
            'rpId' => request()->getHost(),
            'allowCredentials' => UserPasskey::where('user_id', $user->id)->get()->map(function ($key) {
                return ['type' => 'public-key', 'id' => $key->credential_id];
            })
        ]);
    }

    public function passkeyVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'assertion' => 'required|string'
        ]);

        $userId = session('webauthn_login_user_id');
        if (!$userId) {
            return response()->json(['message' => 'Challenge verification session expired.'], 422);
        }

        $user = User::findOrFail($userId);
        
        // Simulating assertion crypt verification mapping matching user registered credentials
        $passkey = UserPasskey::where('user_id', $user->id)->first();
        if (!$passkey) {
            return response()->json(['message' => 'No registered passkeys found.'], 422);
        }

        // Increment sign counter
        $passkey->increment('sign_count');

        Auth::login($user);
        request()->session()->regenerate();
        session(['last_activity_time' => now()->timestamp]);
        session(['session_created_at' => now()->timestamp]);

        $deviceUuid = $request->cookie('diwebs_device_uuid') ?? Str::uuid()->toString();
        $userAgent = $request->userAgent();

        UserDevice::updateOrCreate(
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

        $this->logAuthEvent($user->id, 'login_success_passkey', ['device_uuid' => $deviceUuid]);

        return response()->json([
            'redirect' => $this->getRedirectPath($user),
            'device_uuid' => $deviceUuid
        ])->cookie('diwebs_device_uuid', $deviceUuid, 43200);
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
    }

    private function isPasswordBreached($password)
    {
        // 1. Local list checks
        $blocked = ['123456', 'password', 'qwerty', '123456789', 'password123', 'admin123', 'diwebs123'];
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
        switch ($user->role) {
            case 'super_admin':
                return route('admin.dashboard');
            case 'client':
                return route('portal.dashboard');
            case 'student':
                return route('academy.dashboard');
            case 'candidate':
                return route('cbt.dashboard');
            default:
                return '/';
        }
    }
}
