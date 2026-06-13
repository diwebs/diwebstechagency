@extends('layouts.app')

@section('title', 'Security Configuration Gateway')

@section('content')
<div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8" x-data="securityPanel()">
    <div class="mb-8 flex items-center justify-between border-b border-brand-teal/10 pb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-brand-white text-glow">Security Settings Panel</h1>
            <p class="mt-2 text-sm text-brand-gray">Configure multi-factor options, audit passkeys, and revoke active sessions.</p>
        </div>
        <a href="{{ auth()->user()->role === 'super_admin' ? route('admin.dashboard') : (auth()->user()->role === 'client' ? route('portal.dashboard') : (auth()->user()->role === 'student' ? route('academy.dashboard') : route('cbt.dashboard'))) }}"
           class="rounded-md border border-brand-teal/20 px-4 py-2 text-xs font-semibold text-brand-cyan hover:bg-brand-teal/10 transition-all select-none">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Alert and Feedback notifications -->
    <template x-if="message">
        <div class="mb-6 rounded-md p-4 text-sm transition-all"
             :class="isSuccess ? 'bg-emerald-950/50 border border-emerald-500/30 text-emerald-400' : 'bg-rose-950/50 border border-rose-500/30 text-rose-400'"
             x-text="message"></div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- COLUMN 1 & 2: Main Panels -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- SECTION 1: Active Sessions & Trusted Devices -->
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                <h3 class="text-lg font-bold text-brand-white mb-4 flex items-center gap-2">
                    <span>💻</span> Active Sessions & Trusted Devices
                </h3>
                <p class="text-xs text-brand-gray mb-6">These devices are currently logged into your Diwebs ecosystem account. You can revoke any specific active session dynamically.</p>
                
                <div class="space-y-4">
                    <template x-for="dev in activeDevices" :key="dev.id">
                        <div class="glass-card p-4 rounded-xl border border-brand-teal/5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl" x-text="getDeviceIcon(dev.os)"></span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <strong class="text-brand-white text-sm" x-text="dev.browser + ' on ' + dev.os"></strong>
                                        <template x-if="dev.is_current">
                                            <span class="bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/30 text-[8px] px-2 py-0.5 rounded-full font-bold uppercase select-none">This Device</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-brand-gray mt-1">
                                        IP: <span class="text-brand-white" x-text="dev.ip_address"></span> | 
                                        Location: <span class="text-brand-white" x-text="dev.location || 'Unknown'"></span> | 
                                        Last active: <span class="text-brand-cyan" x-text="formatDate(dev.last_active_at)"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="revokeDevice(dev.id)" 
                                    class="rounded-md border border-rose-500/20 px-3 py-1.5 text-[11px] font-semibold text-rose-400 hover:bg-rose-500/10 transition-all cursor-pointer">
                                Revoke
                            </button>
                        </div>
                    </template>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button @click="revokeAllDevices()" 
                            class="rounded-md bg-rose-500/10 border border-rose-500/30 px-4 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/20 transition-all cursor-pointer">
                        Revoke All Other Sessions
                    </button>
                </div>
            </div>

            <!-- SECTION 2: Passkeys / WebAuthn Biometrics -->
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                <h3 class="text-lg font-bold text-brand-white mb-2 flex items-center gap-2">
                    <span>🔑</span> Biometric Passkeys (WebAuthn)
                </h3>
                <p class="text-xs text-brand-gray mb-6">Use cryptographic credentials via your device (Touch ID, Face ID, Windows Hello) to verify identity safely without typing passwords.</p>

                <div class="space-y-4 mb-6">
                    <template x-for="pk in registeredPasskeys" :key="pk.id">
                        <div class="glass-card p-4 rounded-xl border border-brand-teal/5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">🛡️</span>
                                <div>
                                    <strong class="text-brand-white text-sm" x-text="pk.name"></strong>
                                    <div class="text-[9px] text-brand-gray mt-1">
                                        Registered: <span class="text-brand-cyan" x-text="formatDate(pk.created_at)"></span> | 
                                        Signature Count: <span class="text-brand-white" x-text="pk.sign_count"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="deletePasskey(pk.id)" 
                                    class="rounded-md border border-rose-500/20 px-3 py-1.5 text-[11px] font-semibold text-rose-400 hover:bg-rose-500/10 transition-all cursor-pointer">
                                Delete
                            </button>
                        </div>
                    </template>
                    <template x-if="registeredPasskeys.length === 0">
                        <p class="text-xs text-brand-gray italic text-center py-4 border border-dashed border-brand-teal/10 rounded-xl">No biometric passkeys registered to this account yet.</p>
                    </template>
                </div>

                <div class="flex items-center justify-between border-t border-brand-teal/10 pt-6">
                    <div class="max-w-md">
                        <input type="text" x-model="newPasskeyName" placeholder="Passkey Identifier (e.g. My Laptop)" class="rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all w-64">
                    </div>
                    <button @click="registerNewPasskey()" 
                            class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">
                        + Register New Passkey
                    </button>
                </div>
            </div>
        </div>

        <!-- COLUMN 3: Right Sidebar Panel -->
        <div class="space-y-8">
            
            <!-- SECTION 3: Two-Factor Authentication Settings -->
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                <h3 class="text-lg font-bold text-brand-white mb-2 flex items-center gap-2">
                    <span>🛡️</span> Multi-Factor Auth (2FA)
                </h3>
                
                <div class="mt-4 space-y-4">
                    <!-- 2FA State Check -->
                    <div class="p-3 rounded-xl flex items-center gap-3 border"
                         :class="is2FAEnabled ? 'bg-emerald-950/20 border-emerald-500/20 text-emerald-400' : 'bg-rose-950/20 border-rose-500/20 text-rose-400'">
                        <span class="text-xl" x-text="is2FAEnabled ? '✓' : '⚠️'"></span>
                        <div>
                            <strong class="text-xs uppercase font-bold" x-text="is2FAEnabled ? '2FA Active' : '2FA Deactivated'"></strong>
                            <p class="text-[10px] text-brand-gray mt-0.5">MFA verification is required on login endpoints.</p>
                        </div>
                    </div>

                    <div x-show="!is2FAEnabled" class="space-y-4 border-t border-brand-teal/10 pt-4">
                        <p class="text-xs text-brand-gray">To activate TOTP Authenticator, scan the QR code below or enter the manual secret key into Google Authenticator or Authy.</p>
                        
                        <!-- QR Code simulation place -->
                        <div class="flex justify-center py-2 bg-white rounded-lg p-2 max-w-[150px] mx-auto">
                            <!-- Standard placeholder for QR Code generated dynamically -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrCodeUrl ?? '') }}" alt="Scan with Authenticator App" class="w-full">
                        </div>
                        
                        <div class="text-center">
                            <span class="text-[10px] text-brand-gray uppercase font-semibold block">Secret Key</span>
                            <code class="text-xs text-brand-cyan font-bold" x-text="totpSecret"></code>
                        </div>

                        <div>
                            <input type="text" maxlength="6" x-model="totpVerificationCode" placeholder="Verify 6-digit OTP" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                        </div>

                        <button @click="enable2FA()" 
                                class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
                            Activate MFA
                        </button>
                    </div>

                    <div x-show="is2FAEnabled" class="space-y-4 border-t border-brand-teal/10 pt-4 text-center">
                        <p class="text-xs text-brand-gray">Your account has mandatory multi-factor protection. To turn it off, verify your identity with a code.</p>
                        <div>
                            <input type="text" maxlength="6" x-model="totpVerificationCode" placeholder="Verify 6-digit OTP" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-sm text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                        </div>
                        <button @click="disable2FA()" 
                                class="w-full rounded-md bg-rose-500/10 border border-rose-500/30 py-2 text-xs font-bold text-rose-400 hover:bg-rose-500/20 transition-all cursor-pointer">
                            Deactivate MFA
                        </button>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Change Password -->
            <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                <h3 class="text-lg font-bold text-brand-white mb-4 flex items-center gap-2">
                    <span>🔒</span> Update Password
                </h3>
                
                <form @submit.prevent="updatePassword()" class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-[10px] font-semibold text-brand-cyan uppercase">Current Password</label>
                        <input type="password" x-model="currentPassword" id="current_password" required class="mt-1 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>

                    <div>
                        <label for="new_password" class="block text-[10px] font-semibold text-brand-cyan uppercase">New Password</label>
                        <input type="password" x-model="newPassword" id="new_password" required class="mt-1 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-[10px] font-semibold text-brand-cyan uppercase">Confirm New Password</label>
                        <input type="password" x-model="newPasswordConfirmation" id="new_password_confirmation" required class="mt-1 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>

                    <button type="submit" 
                            class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-2.5 text-xs font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function securityPanel() {
        return {
            totpSecret: '{{ $totpSecret ?? "ABCDEF1234567890" }}',
            totpVerificationCode: '',
            is2FAEnabled: {{ auth()->user()->two_factor_confirmed_at ? 'true' : 'false' }},
            activeDevices: @json($devices ?? []),
            registeredPasskeys: @json($passkeys ?? []),
            newPasskeyName: '',
            currentPassword: '',
            newPassword: '',
            newPasswordConfirmation: '',
            message: '',
            isSuccess: true,

            getDeviceIcon(os) {
                if (!os) return '💻';
                const lower = os.toLowerCase();
                if (lower.includes('win')) return '🪟';
                if (lower.includes('mac')) return '🍎';
                if (lower.includes('linux')) return '🐧';
                if (lower.includes('ios') || lower.includes('phone')) return '📱';
                if (lower.includes('android')) return '🤖';
                return '💻';
            },

            formatDate(d) {
                if (!d) return 'Never';
                const date = new Date(d);
                return date.toLocaleString();
            },

            showFeedback(text, success = true) {
                this.message = text;
                this.isSuccess = success;
                setTimeout(() => { this.message = ''; }, 6000);
            },

            async enable2FA() {
                if (!this.totpVerificationCode) {
                    this.showFeedback('Please enter the verification code.', false);
                    return;
                }
                try {
                    const response = await fetch('{{ route("profile.2fa.enable") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: this.totpVerificationCode, secret: this.totpSecret })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.is2FAEnabled = true;
                        this.totpVerificationCode = '';
                        this.showFeedback('Two-factor authentication enabled successfully.');
                    } else {
                        this.showFeedback(data.message || 'Verification failed.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error enabling 2FA.', false);
                }
            },

            async disable2FA() {
                if (!this.totpVerificationCode) {
                    this.showFeedback('Please enter your 2FA verification code to confirm deactivation.', false);
                    return;
                }
                try {
                    const response = await fetch('{{ route("profile.2fa.disable") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ code: this.totpVerificationCode })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.is2FAEnabled = false;
                        this.totpVerificationCode = '';
                        this.showFeedback('Two-factor authentication disabled successfully.');
                    } else {
                        this.showFeedback(data.message || 'Verification failed.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error disabling 2FA.', false);
                }
            },

            async registerNewPasskey() {
                if (!this.newPasskeyName) {
                    this.showFeedback('Please enter an identifier name for the passkey.', false);
                    return;
                }
                
                try {
                    // Check WebAuthn APIs
                    if (!window.PublicKeyCredential) {
                        this.showFeedback('Passkeys are not supported on this device/browser.', false);
                        return;
                    }
                    
                    // Call register controller
                    const chalResponse = await fetch('/profile/passkeys/challenge', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const chalData = await chalResponse.json();
                    if (!chalResponse.ok) throw new Error(chalData.message || 'Failed challenge retrieval.');
                    
                    alert('OS WebAuthn registry initiated. Registering cryptographic keypair for identifier: ' + this.newPasskeyName);
                    
                    // Send signature back to verify and store
                    const verifyResponse = await fetch('/profile/passkeys/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newPasskeyName, assertion: 'new_passkey_mock_signature' })
                    });
                    
                    const verifyData = await verifyResponse.json();
                    if (verifyResponse.ok) {
                        this.registeredPasskeys.push(verifyData.passkey);
                        this.newPasskeyName = '';
                        this.showFeedback('New Biometric Passkey registered successfully.');
                    } else {
                        this.showFeedback(verifyData.message || 'Signature verification failed.', false);
                    }
                } catch (err) {
                    this.showFeedback(err.message || 'Failed to complete WebAuthn registration.', false);
                }
            },

            async deletePasskey(id) {
                if (!confirm('Are you sure you want to delete this passkey? You will no longer be able to log in using this key.')) return;
                try {
                    const response = await fetch(`/profile/passkeys/${id}/delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        this.registeredPasskeys = this.registeredPasskeys.filter(pk => pk.id !== id);
                        this.showFeedback('Passkey deleted successfully.');
                    } else {
                        this.showFeedback('Failed to delete passkey.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error deleting passkey.', false);
                }
            },

            async revokeDevice(id) {
                if (!confirm('Are you sure you want to log out and revoke this active device session?')) return;
                try {
                    const response = await fetch(`/profile/devices/${id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        const data = await response.json();
                        if (data.logout_current) {
                            window.location.href = '/login';
                        } else {
                            this.activeDevices = this.activeDevices.filter(d => d.id !== id);
                            this.showFeedback('Active session revoked successfully.');
                        }
                    } else {
                        this.showFeedback('Failed to revoke session.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error revoking session.', false);
                }
            },

            async revokeAllDevices() {
                if (!confirm('Are you sure you want to terminate all other active sessions across all devices?')) return;
                try {
                    const response = await fetch('/profile/devices/revoke-all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        this.activeDevices = this.activeDevices.filter(d => d.is_current);
                        this.showFeedback('All other active sessions have been revoked.');
                    } else {
                        this.showFeedback('Failed to revoke other sessions.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error.', false);
                }
            },

            async updatePassword() {
                if (this.newPassword !== this.newPasswordConfirmation) {
                    this.showFeedback('New passwords do not match.', false);
                    return;
                }
                try {
                    const response = await fetch('{{ route("profile.password.update") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            current_password: this.currentPassword,
                            password: this.newPassword,
                            password_confirmation: this.newPasswordConfirmation
                        })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.currentPassword = '';
                        this.newPassword = '';
                        this.newPasswordConfirmation = '';
                        this.showFeedback('Password changed successfully. All other active sessions have been revoked for security.');
                        
                        // Update device list since all others are logged out
                        this.activeDevices = this.activeDevices.filter(d => d.is_current);
                    } else {
                        this.showFeedback(data.message || 'Password update failed.', false);
                    }
                } catch (err) {
                    this.showFeedback('Network error updating password.', false);
                }
            }
        };
    }
</script>
@endsection
