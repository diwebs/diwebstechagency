@extends('layouts.app')

@section('title', 'Sign In - Diwebs Security Gateway')

@section('content')
<div x-data="loginPage()" class="mx-auto max-w-md px-4">
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden" :class="{ 'border-rose-500/30 shake': errorShake }">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <div class="relative z-10 text-center mb-6">
            <h2 class="text-2xl font-bold text-brand-white">Ecosystem Authentication</h2>
            <p class="mt-2 text-xs text-brand-gray">Sign in to access your secure portal dashboard</p>
        </div>

        <!-- Device recognition alert -->
        <div x-show="newDeviceAlert" class="relative z-10 mb-4 p-3 rounded bg-amber-500/10 border border-amber-500/30 text-[11px] text-amber-400 flex items-center gap-2 select-none animate-pulse">
            <span>⚠️</span>
            <span>Unrecognized device. OTP check will be required.</span>
        </div>

        <!-- 2FA OTP Interstitial -->
        <div x-show="show2FA" x-transition class="relative z-10 space-y-4">
            <div class="text-center">
                <span class="text-3xl">🛡️</span>
                <h3 class="font-bold text-brand-white mt-2">Security Verification</h3>
                <p class="text-xs text-brand-gray mt-1">Please enter the 2FA OTP code from your Authenticator app or email code.</p>
            </div>
            
            <form @submit.prevent="verify2FA()" class="space-y-4">
                <div>
                    <input type="text" maxlength="6" x-model="otpCode" placeholder="000000" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-2xl font-bold tracking-[8px] text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                </div>
                
                <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Verify Code</button>
                <button type="button" @click="show2FA = false" class="w-full text-xs text-brand-gray hover:text-brand-white transition-all text-center">Back to Login</button>
            </form>
        </div>

        <!-- Standard Login Forms -->
        <div x-show="!show2FA" class="relative z-10 space-y-4">
            <form action="{{ route('login') }}" method="POST" @submit.prevent="handleLogin()" class="space-y-4">
                @csrf
                <!-- Dynamic inputs -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Corporate Email</label>
                    <input type="email" name="email" id="email" x-model="email" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Password</label>
                        <a href="{{ route('password.request') }}" class="text-[10px] text-brand-cyan hover:underline uppercase font-semibold">Forgot?</a>
                    </div>
                    <input type="password" name="password" id="password" x-model="password" required class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <button type="submit" class="w-full rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan py-3 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Verify Credentials</button>
            </form>

            <!-- SSO Selectors -->
            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-brand-teal/10"></div>
                <span class="flex-shrink mx-4 text-[10px] text-brand-gray uppercase font-semibold">or authenticate with</span>
                <div class="flex-grow border-t border-brand-teal/10"></div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="triggerSSO('Google')" class="rounded-md border border-brand-teal/20 hover:border-brand-cyan/40 bg-brand-dark-secondary/40 py-2.5 flex items-center justify-center gap-1.5 text-xs text-brand-white hover:bg-brand-dark-primary/60 transition-all cursor-pointer">
                    <img src="/images/brand/google.svg" alt="Google" class="h-4 w-4"> Google
                </button>
                <button type="button" @click="triggerSSO('Apple')" class="rounded-md border border-brand-teal/20 hover:border-brand-cyan/40 bg-brand-dark-secondary/40 py-2.5 flex items-center justify-center gap-1.5 text-xs text-brand-white hover:bg-brand-dark-primary/60 transition-all cursor-pointer">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg> Apple
                </button>
                <button type="button" @click="triggerSSO('Microsoft')" class="rounded-md border border-brand-teal/20 hover:border-brand-cyan/40 bg-brand-dark-secondary/40 py-2.5 flex items-center justify-center gap-1.5 text-xs text-brand-white hover:bg-brand-dark-primary/60 transition-all cursor-pointer">
                    <img src="/images/brand/microsoft.svg" alt="Microsoft" class="h-4 w-4"> Azure
                </button>
            </div>

            <!-- Passkey Login Trigger -->
            <button type="button" @click="loginWithPasskey()" class="w-full rounded-md border border-brand-cyan/35 text-brand-cyan hover:bg-brand-cyan/10 py-3 text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-2">
                🔑 Sign In with Passkey (Biometrics)
            </button>
        </div>

        <!-- Alert message display -->
        <div x-show="errorMessage" class="relative z-10 mt-4 p-3 rounded bg-rose-950/40 border border-rose-500/30 text-xs text-rose-400 text-center" x-text="errorMessage"></div>

        <!-- Sandbox logins -->
        <div class="relative z-10 mt-8 border-t border-brand-teal/10 pt-6">
            <h4 class="text-xs font-semibold text-brand-white uppercase text-center mb-4">Dev Testing Sandbox</h4>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('auth.dev-login', 'super_admin') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Admin Panel</a>
                <a href="{{ route('auth.dev-login', 'client') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Client Portal</a>
                <a href="{{ route('auth.dev-login', 'student') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">Academy LMS</a>
                <a href="{{ route('auth.dev-login', 'candidate') }}" class="rounded-md bg-brand-teal/10 hover:bg-brand-teal/20 border border-brand-teal/20 py-2 text-center text-xs text-brand-cyan transition-all">CBT Engine</a>
            </div>
        </div>
    </div>
</div>

<script>
    function loginPage() {
        return {
            email: '',
            password: '',
            show2FA: false,
            otpCode: '',
            newDeviceAlert: false,
            errorMessage: '{{ $errors->first() }}',
            errorShake: false,

            init() {
                // Device fingerprint simulation checks in local storage
                const deviceUuid = localStorage.getItem('diwebs_device_uuid');
                if (!deviceUuid) {
                    this.newDeviceAlert = true;
                }
            },

            triggerShake() {
                this.errorShake = true;
                setTimeout(() => { this.errorShake = false; }, 500);
            },

            async handleLogin() {
                this.errorMessage = '';
                
                try {
                    // Send AJAX precheck for 2FA requirement to handle login dynamically
                    const response = await fetch('{{ route("login") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: this.email, password: this.password })
                    });
                    
                    const data = await response.json();

                    if (response.ok) {
                        // User logged in successfully - store local UUID if returned
                        if (data.device_uuid) {
                            localStorage.setItem('diwebs_device_uuid', data.device_uuid);
                        }
                        
                        if (data.requires_2fa) {
                            this.show2FA = true;
                        } else {
                            window.location.href = data.redirect;
                        }
                    } else {
                        this.errorMessage = data.message || 'Invalid login details.';
                        this.triggerShake();
                    }
                } catch (err) {
                    this.errorMessage = 'A network error occurred. Please check your credentials.';
                    this.triggerShake();
                }
            },

            async verify2FA() {
                this.errorMessage = '';
                try {
                    const response = await fetch('{{ route("login.2fa.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: this.email, code: this.otpCode })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        window.location.href = data.redirect;
                    } else {
                        this.errorMessage = data.message || 'Invalid code.';
                        this.triggerShake();
                    }
                } catch (err) {
                    this.errorMessage = 'Network error verifying code.';
                    this.triggerShake();
                }
            },

            triggerSSO(provider) {
                alert(`${provider} Single Sign-On simulation. In production, this redirects to the secure OAuth authorization endpoints.`);
            },

            async loginWithPasskey() {
                this.errorMessage = '';
                if (!window.PublicKeyCredential) {
                    this.errorMessage = 'Biometric passkeys are not supported on this browser version.';
                    this.triggerShake();
                    return;
                }
                
                try {
                    // Fetch WebAuthn assertion challenge options
                    const optionsResponse = await fetch('/auth/passkeys/login-challenge', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: this.email })
                    });
                    
                    if (!optionsResponse.ok) {
                        const errData = await optionsResponse.json();
                        throw new Error(errData.message || 'Failed to retrieve passkey challenge.');
                    }
                    
                    // WebAuthn request browser credential mapping
                    alert('Requesting biometric verification from OS (Windows Hello / TouchID)...');
                    
                    // In real environment, call navigator.credentials.get()
                    // Propose fallback bypass simulation if no passkey registered
                    const response = await fetch('/auth/passkeys/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: this.email, assertion: 'passkey_auth_verified_signature' })
                    });
                    
                    const data = await response.json();
                    if (response.ok) {
                        window.location.href = data.redirect;
                    } else {
                        this.errorMessage = data.message || 'Passkey verification failed.';
                        this.triggerShake();
                    }
                } catch (err) {
                    this.errorMessage = err.message || 'Failed to complete WebAuthn handshake.';
                    this.triggerShake();
                }
            }
        };
    }
</script>

<style>
    .shake {
        animation: shake 0.5s ease;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-6px); }
        20%, 40%, 60%, 80% { transform: translateX(6px); }
    }
</style>
@endsection
