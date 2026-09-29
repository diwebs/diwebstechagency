<?php

namespace App\Http\Controllers;

use App\Models\UserPasskey;
use App\Models\UserDevice;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    public function showSecuritySettings()
    {
        $user = Auth::user();

        // 1. Generate TOTP secret if not present
        $totpSecret = $user->two_factor_secret;
        if (!$totpSecret) {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
            $totpSecret = '';
            for ($i = 0; $i < 16; $i++) {
                $totpSecret .= $chars[rand(0, 31)];
            }
        }

        $qrCodeUrl = 'otpauth://totp/' . rawurlencode('Diwebs:' . $user->email) . '?secret=' . $totpSecret . '&issuer=Diwebs';

        // 2. Fetch active devices / sessions
        $devices = UserDevice::where('user_id', $user->id)
            ->orderBy('last_active_at', 'desc')
            ->get()
            ->map(function ($device) {
                return [
                    'id' => $device->id,
                    'browser' => $device->browser,
                    'os' => $device->os,
                    'ip_address' => $device->ip_address,
                    'location' => $device->location,
                    'last_active_at' => $device->last_active_at ? $device->last_active_at->toIso8601String() : null,
                    'is_current' => $device->device_uuid === request()->cookie('diwebs_device_uuid')
                ];
            });

        // 3. Fetch registered passkeys
        $passkeys = UserPasskey::where('user_id', $user->id)
            ->get()
            ->map(function ($key) {
                return [
                    'id' => $key->id,
                    'name' => $key->name,
                    'sign_count' => $key->sign_count,
                    'created_at' => $key->created_at->toIso8601String()
                ];
            });

        return view('profile.security', compact('totpSecret', 'qrCodeUrl', 'devices', 'passkeys'));
    }

    public function enable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'secret' => 'required|string|size:16'
        ]);

        $user = Auth::user();

        if ($this->verifyTotp($request->secret, $request->code)) {
            $user->update([
                'two_factor_secret' => $request->secret,
                'two_factor_confirmed_at' => now()
            ]);

            $this->logAuthEvent('2fa_enabled', ['method' => 'totp']);

            return response()->json(['message' => '2FA enabled successfully.']);
        }

        return response()->json(['message' => 'Invalid OTP code. Please verify and try again.'], 422);
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        if ($this->verifyTotp($user->two_factor_secret, $request->code)) {
            $user->update([
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null
            ]);

            $this->logAuthEvent('2fa_disabled', ['method' => 'totp']);

            return response()->json(['message' => '2FA disabled successfully.']);
        }

        return response()->json(['message' => 'Invalid verification code.'], 422);
    }

    public function passkeyChallenge()
    {
        $user = Auth::user();

        // Cryptographically secure 32-byte challenge
        $challengeBytes = random_bytes(32);
        $challenge = rtrim(strtr(base64_encode($challengeBytes), '+/', '-_'), '=');

        session([
            'webauthn_reg_challenge' => $challenge,
            'webauthn_reg_user_id'   => $user->id,
            'webauthn_reg_expiry'    => time() + 300,
        ]);

        // Exclude already-registered credentials so the device won't prompt to re-register
        $excludeCredentials = UserPasskey::where('user_id', $user->id)->get()->map(function ($pk) {
            return ['type' => 'public-key', 'id' => $pk->credential_id];
        })->values();

        return response()->json([
            'challenge' => $challenge,
            'rp' => [
                'name' => config('app.name', 'Diwebs Tech Agency'),
                'id'   => request()->getHost(),
            ],
            'user' => [
                'id'          => rtrim(strtr(base64_encode((string) $user->id), '+/', '-_'), '='),
                'name'        => $user->email,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],   // ES256 ECDSA P-256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
            'timeout'    => 60000,
            'attestation' => 'none',
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'requireResidentKey'      => false,
                'userVerification'        => 'required',
            ],
            'excludeCredentials' => $excludeCredentials,
        ]);
    }

    public function passkeyStore(Request $request)
    {
        $request->validate([
            'credential_id'      => 'required|string',
            'client_data_json'   => 'required|string',
            'attestation_object' => 'required|string',
            'public_key_spki'    => 'required|string',
            'device_name'        => 'nullable|string|max:100',
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
            return response()->json(['message' => 'Registration timed out. Please start again.'], 422);
        }

        // Decode and verify clientDataJSON
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
            return response()->json(['message' => 'Challenge mismatch — registration rejected.'], 422);
        }

        $expectedOrigin = request()->getSchemeAndHttpHost();
        if (($clientData['origin'] ?? '') !== $expectedOrigin) {
            return response()->json(['message' => 'Origin mismatch — registration rejected.'], 422);
        }

        // Convert SPKI bytes to PEM for storage
        $spkiBytes    = base64_decode(strtr($request->public_key_spki, '-_', '+/'));
        $pemPublicKey = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($spkiBytes), 64, "\n") .
            "-----END PUBLIC KEY-----\n";

        if (!openssl_pkey_get_public($pemPublicKey)) {
            return response()->json(['message' => 'Invalid public key from device. Please try again.'], 422);
        }

        if (UserPasskey::where('credential_id', $request->credential_id)->exists()) {
            return response()->json(['message' => 'This passkey is already registered.'], 422);
        }

        $osName      = $this->parseOsFromAgent(request()->userAgent());
        $browserName = $this->parseBrowserFromAgent(request()->userAgent());

        $passkey = UserPasskey::create([
            'user_id'       => $user->id,
            'credential_id' => $request->credential_id,
            'public_key'    => $pemPublicKey,
            'sign_count'    => 0,
            'name'          => $request->device_name ?? ($osName . ' ' . $browserName),
        ]);

        session()->forget(['webauthn_reg_challenge', 'webauthn_reg_user_id', 'webauthn_reg_expiry']);
        $this->logAuthEvent('passkey_registered', ['name' => $passkey->name]);

        return response()->json([
            'message' => 'Passkey registered! You can now use your fingerprint or face to sign in.',
            'passkey' => [
                'id'         => $passkey->id,
                'name'       => $passkey->name,
                'sign_count' => $passkey->sign_count,
                'created_at' => $passkey->created_at->toIso8601String(),
            ],
        ]);
    }

    public function passkeyDelete($id)
    {
        $passkey = UserPasskey::where('user_id', Auth::id())->findOrFail($id);
        $name = $passkey->name;
        $passkey->delete();

        $this->logAuthEvent('passkey_deleted', ['name' => $name]);

        return response()->json(['message' => 'Passkey deleted.']);
    }

    public function revokeSession($id)
    {
        $device = UserDevice::where('user_id', Auth::id())->findOrFail($id);
        
        // If revoking current device, log user out
        $isCurrent = $device->device_uuid === request()->cookie('diwebs_device_uuid');
        
        // Invalidate DB session matching the IP/agent
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('ip_address', $device->ip_address)
            ->delete();

        $device->delete();

        $this->logAuthEvent('session_revoked', ['ip' => $device->ip_address, 'browser' => $device->browser]);

        if ($isCurrent) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return response()->json(['logout_current' => true]);
        }

        return response()->json(['message' => 'Device session terminated.']);
    }

    public function revokeAllOtherSessions()
    {
        $currentUuid = request()->cookie('diwebs_device_uuid');

        // Delete other DB sessions
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->whereNot('ip_address', request()->ip())
            ->delete();

        // Delete other devices from user_devices
        UserDevice::where('user_id', Auth::id())
            ->whereNot('device_uuid', $currentUuid)
            ->delete();

        $this->logAuthEvent('all_other_sessions_revoked', []);

        return response()->json(['message' => 'All other sessions terminated.']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The provided current password does not match.'], 422);
        }

        // Check password history / reuse (prevent matching last 3 passwords)
        $historicalHashMatches = AuditLog::where('user_id', $user->id)
            ->where('event_type', 'password_change')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->filter(function ($log) use ($request) {
                return isset($log->details['old_hash']) && Hash::check($request->password, $log->details['old_hash']);
            });

        if ($historicalHashMatches->isNotEmpty()) {
            return response()->json(['message' => 'You cannot reuse one of your last 3 passwords.'], 422);
        }

        // Argon2id hashing is configured in config/hashing.php
        $oldHash = $user->password;
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Revoke all other sessions for safety
        $this->revokeAllOtherSessions();

        $this->logAuthEvent('password_change', ['old_hash' => $oldHash]);

        return response()->json(['message' => 'Password changed successfully.']);
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

    private function logAuthEvent($type, $details)
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'event_type' => $type,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details'    => $details,
            'created_at' => now()
        ]);
    }

    private function parseOsFromAgent($agent)
    {
        if (preg_match('/windows/i', $agent)) return 'Windows';
        if (preg_match('/macintosh/i', $agent)) return 'macOS';
        if (preg_match('/iphone|ipad/i', $agent)) return 'iOS';
        if (preg_match('/android/i', $agent)) return 'Android';
        if (preg_match('/linux/i', $agent)) return 'Linux';
        return 'Device';
    }

    private function parseBrowserFromAgent($agent)
    {
        if (preg_match('/edg/i', $agent)) return 'Edge';
        if (preg_match('/chrome/i', $agent)) return 'Chrome';
        if (preg_match('/safari/i', $agent)) return 'Safari';
        if (preg_match('/firefox/i', $agent)) return 'Firefox';
        return 'Browser';
    }
}
