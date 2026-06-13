@extends('layouts.app')

@section('title', 'Secure Registration - Diwebs Digital Ecosystem')

@section('content')
<div x-data="registerForm()" class="mx-auto max-w-xl px-4 py-8">
    <!-- Progress Indicator Tracker -->
    <div class="mb-8 relative">
        <div class="flex items-center justify-between z-10 relative">
            <template x-for="i in 5">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 border"
                         :class="{
                            'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-[0_0_15px_rgba(0,194,209,0.4)]': step === i,
                            'bg-brand-teal text-brand-white border-brand-teal': step > i,
                            'bg-brand-dark-primary text-brand-gray border-brand-teal/20': step < i
                         }"
                         x-text="i"></div>
                    <span class="text-[10px] uppercase font-semibold mt-2 tracking-wider"
                          :class="step === i ? 'text-brand-cyan' : 'text-brand-gray'"
                          x-text="getStepLabel(i)"></span>
                </div>
            </template>
        </div>
        <!-- Progress Bar Background Line -->
        <div class="absolute top-5 left-0 w-full h-[2px] bg-brand-dark-primary -z-10"></div>
        <div class="absolute top-5 left-0 h-[2px] bg-gradient-to-r from-brand-teal to-brand-cyan -z-10 transition-all duration-500"
             :style="'width: ' + ((step - 1) / 4 * 100) + '%'"></div>
    </div>

    <div class="glass-card rounded-3xl p-8 relative overflow-hidden min-h-[450px] transition-all duration-500"
         :class="{ 'border-rose-500/30 shake': errorShake }">
        <div class="absolute inset-0 bg-dot-matrix opacity-25"></div>
        
        <!-- Onboarding Header -->
        <div class="relative z-10 mb-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-brand-white text-glow" x-text="getStepTitle()"></h2>
            <p class="mt-2 text-xs text-brand-gray" x-text="getStepDescription()"></p>
        </div>

        <form id="secure-register-form" action="{{ route('register') }}" method="POST" @submit.prevent="submitForm()" class="relative z-10">
            @csrf
            
            <!-- STEP 1: Account Type Selection -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
                <input type="hidden" name="role" :value="role">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Cards list -->
                    <template x-for="opt in roleOptions">
                        <div @click="role = opt.id"
                             class="glass-card p-5 rounded-2xl cursor-pointer transition-all duration-300 hover:scale-[1.02] border flex items-start gap-4 relative overflow-hidden"
                             :class="role === opt.id ? 'border-brand-cyan bg-brand-teal/10 shadow-[0_0_15px_rgba(0,194,209,0.15)]' : 'border-brand-teal/10 hover:border-brand-teal/30'">
                            <div class="text-3xl" x-text="opt.icon"></div>
                            <div>
                                <h4 class="font-bold text-brand-white" x-text="opt.title"></h4>
                                <p class="text-xs text-brand-gray mt-1 leading-relaxed" x-text="opt.description"></p>
                            </div>
                            <div x-show="role === opt.id" class="absolute right-4 top-4 text-brand-cyan font-bold">✓</div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- STEP 2: Personal Information -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-brand-cyan uppercase">Full Name</label>
                    <input type="text" name="name" id="name" x-model="name" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-brand-cyan uppercase">Email Address</label>
                    <input type="email" name="email" id="email" x-model="email" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-brand-cyan uppercase">Phone Number</label>
                        <input type="text" name="phone" id="phone" x-model="phone" placeholder="+234..." class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                    <div>
                        <label for="country" class="block text-xs font-semibold text-brand-cyan uppercase">Country</label>
                        <input type="text" name="country" id="country" x-model="country" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Security Password</label>
                    <input type="password" name="password" id="password" x-model="password" @input="evaluatePasswordStrength()" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    <!-- Password Strength Meter -->
                    <div class="mt-2">
                        <div class="flex items-center justify-between text-[10px] font-semibold text-brand-gray mb-1 uppercase">
                            <span>Password Strength</span>
                            <span :class="strengthColor" x-text="strengthText"></span>
                        </div>
                        <div class="w-full h-1.5 bg-brand-dark-primary rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" :class="strengthBg" :style="'width: ' + (passwordStrength * 25) + '%'"></div>
                        </div>
                        <p class="text-[10px] text-brand-gray mt-1">Min. 12 characters with uppercase, lowercase, numbers, and symbols.</p>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brand-cyan uppercase">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" x-model="password_confirmation" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>
            </div>

            <!-- STEP 3: Identity Verification (OTP) -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6 text-center">
                <p class="text-sm text-brand-gray">We have sent a secure 6-digit verification code to <strong class="text-brand-white" x-text="email"></strong>.</p>
                
                <div class="flex justify-center gap-2 max-w-xs mx-auto">
                    <!-- Custom verification inputs code block -->
                    <input type="text" maxlength="6" x-model="verification_code" placeholder="000000" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-2xl font-bold tracking-[8px] text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div class="text-xs text-brand-gray space-y-2">
                    <div x-show="otpCountdown > 0">
                        Code expires in <span class="text-brand-cyan font-bold" x-text="formatTime(otpCountdown)"></span>
                    </div>
                    <div x-show="otpCountdown === 0">
                        Code expired. Please request a new one.
                    </div>
                    <button type="button" @click="sendOtp()" :disabled="resendCooldown > 0"
                            class="px-4 py-2 rounded-md border border-brand-teal/20 text-xs font-semibold text-brand-cyan hover:bg-brand-teal/10 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="resendCooldown > 0">Resend OTP in <span x-text="resendCooldown"></span>s</span>
                        <span x-show="resendCooldown === 0">Resend Verification Code</span>
                    </button>
                </div>
            </div>

            <!-- STEP 4: Security Preferences -->
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                <!-- 2FA Preference -->
                <div class="glass-card p-5 rounded-2xl border border-brand-teal/10 flex items-start gap-4">
                    <input type="checkbox" name="enable_2fa" id="enable_2fa" x-model="enable_2fa" value="1" class="mt-1 accent-brand-cyan w-5 h-5 rounded border-brand-teal/20 bg-brand-dark-secondary">
                    <div>
                        <div class="flex items-center gap-2">
                            <label for="enable_2fa" class="font-bold text-brand-white cursor-pointer">Enable Two-Factor Authentication (2FA)</label>
                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Recommended</span>
                        </div>
                        <p class="text-xs text-brand-gray mt-1 leading-relaxed">Adds an extra layer of protection by requiring a temporary secure OTP code in addition to your standard password.</p>
                    </div>
                </div>

                <!-- Passkey Placeholder -->
                <div class="glass-card p-5 rounded-2xl border border-brand-teal/10 flex items-start gap-4">
                    <input type="checkbox" id="passkey_enroll" x-model="passkey_enroll" class="mt-1 accent-brand-cyan w-5 h-5 rounded border-brand-teal/20 bg-brand-dark-secondary">
                    <div>
                        <div class="flex items-center gap-2">
                            <label for="passkey_enroll" class="font-bold text-brand-white cursor-pointer">Register Biometric Passkey Later</label>
                        </div>
                        <p class="text-xs text-brand-gray mt-1 leading-relaxed">Allows passwordless authentication using Windows Hello, Face ID, Apple Touch ID, or external security keys on trusted browsers.</p>
                    </div>
                </div>
            </div>

            <!-- STEP 5: Final Review -->
            <div x-show="step === 5" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                <div class="glass-card p-6 rounded-2xl border border-brand-teal/10 space-y-4">
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Account Type</span>
                        <strong class="text-brand-cyan capitalize text-sm" x-text="role"></strong>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Representative Name</span>
                        <strong class="text-brand-white text-sm" x-text="name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Email Address</span>
                        <strong class="text-brand-white text-sm" x-text="email"></strong>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Verification State</span>
                        <strong class="text-emerald-400 text-sm">✓ Verified</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-brand-gray">Security Profile</span>
                        <strong class="text-brand-cyan text-sm" x-text="enable_2fa ? '2FA Enabled' : 'Standard Protection'"></strong>
                    </div>
                </div>

                <p class="text-[10px] text-brand-gray leading-relaxed text-center">By clicking 'Create Secure Account', your device fingerprint, geolocation details, and audit parameters are initialized to secure the session token.</p>
            </div>

            <!-- Alert Messages -->
            <div x-show="errorMessage" class="mt-4 p-3 rounded bg-rose-950/40 border border-rose-500/30 text-xs text-rose-400 text-center" x-text="errorMessage"></div>

            <!-- Actions Buttons -->
            <div class="mt-8 flex items-center justify-between gap-4 border-t border-brand-teal/10 pt-6">
                <button type="button" x-show="step > 1" @click="prevStep()" class="rounded-md border border-brand-teal/20 px-6 py-2.5 text-sm font-semibold text-brand-cyan hover:bg-brand-teal/10 transition-all cursor-pointer">Previous</button>
                <div class="flex-1"></div>
                <button type="button" x-show="step < 5" @click="nextStep()" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Continue</button>
                <button type="submit" x-show="step === 5" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Create Secure Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    function registerForm() {
        return {
            step: 1,
            role: 'student',
            name: '',
            email: '',
            phone: '',
            country: 'Nigeria',
            password: '',
            password_confirmation: '',
            verification_code: '',
            enable_2fa: true,
            passkey_enroll: false,
            passwordStrength: 0,
            strengthText: 'Weak',
            strengthColor: 'text-rose-500',
            strengthBg: 'bg-rose-500',
            otpCountdown: 0,
            resendCooldown: 0,
            otpTimerId: null,
            cooldownTimerId: null,
            errorMessage: '',
            errorShake: false,

            roleOptions: [
                { id: 'student', icon: '🎓', title: 'Student', description: 'Access courses, certifications, exams, and learning dashboard.' },
                { id: 'client', icon: '💼', title: 'Client / SaaS Customer', description: 'Track milestones, view proposals, pay invoices, and direct messaging.' },
                { id: 'candidate', icon: '📝', title: 'CBT Candidate', description: 'Take online/offline locked examinations under biometric security controls.' },
                { id: 'partner', icon: '🤝', title: 'Partner Link', description: 'Manage center nodes, inspect local system devices, and joint contracts.' },
                { id: 'instructor', icon: '👨‍🏫', title: 'Academy Instructor', description: 'Configure syllabus materials, grade candidates, and moderate AI tutors.' }
            ],

            getStepLabel(i) {
                const labels = ['Role', 'Profile', 'Identity', 'Security', 'Review'];
                return labels[i - 1];
            },

            getStepTitle() {
                const titles = [
                    'Select Account Role',
                    'Personal Information',
                    'Identity Verification',
                    'Security Preferences',
                    'Verify Details & Confirm'
                ];
                return titles[this.step - 1];
            },

            getStepDescription() {
                const desc = [
                    'Choose how you want to interact with the Diwebs platform.',
                    'Please fill in your primary profile information.',
                    'Check your corporate email inbox for a temporary OTP code.',
                    'Configure multi-factor security options for account hardening.',
                    'Review and initialize your premium session footprint.'
                ];
                return desc[this.step - 1];
            },

            evaluatePasswordStrength() {
                let score = 0;
                if (!this.password) {
                    this.passwordStrength = 0;
                    this.strengthText = 'Weak';
                    this.strengthColor = 'text-rose-500';
                    this.strengthBg = 'bg-rose-500';
                    return;
                }
                if (this.password.length >= 12) score++;
                if (/[A-Z]/.test(this.password)) score++;
                if (/[a-z]/.test(this.password)) score++;
                if (/[0-9]/.test(this.password)) score++;
                if (/[^A-Za-z0-9]/.test(this.password)) score++;

                this.passwordStrength = Math.min(score, 4);

                switch (this.passwordStrength) {
                    case 0:
                    case 1:
                        this.strengthText = 'Weak';
                        this.strengthColor = 'text-rose-500';
                        this.strengthBg = 'bg-rose-500';
                        break;
                    case 2:
                        this.strengthText = 'Fair';
                        this.strengthColor = 'text-amber-500';
                        this.strengthBg = 'bg-amber-500';
                        break;
                    case 3:
                        this.strengthText = 'Strong';
                        this.strengthColor = 'text-brand-teal';
                        this.strengthBg = 'bg-brand-teal';
                        break;
                    case 4:
                        this.strengthText = 'Very Strong';
                        this.strengthColor = 'text-emerald-500';
                        this.strengthBg = 'bg-emerald-500';
                        break;
                }
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m}:${s < 10 ? '0' : ''}${s}`;
            },

            triggerShake() {
                this.errorShake = true;
                setTimeout(() => { this.errorShake = false; }, 500);
            },

            async nextStep() {
                this.errorMessage = '';
                
                if (this.step === 1) {
                    this.step = 2;
                    return;
                }

                if (this.step === 2) {
                    // Profile validation
                    if (!this.name || !this.email || !this.password) {
                        this.errorMessage = 'Please complete all required fields.';
                        this.triggerShake();
                        return;
                    }
                    if (this.password !== this.password_confirmation) {
                        this.errorMessage = 'Password confirmation does not match.';
                        this.triggerShake();
                        return;
                    }
                    if (this.passwordStrength < 3) {
                        this.errorMessage = 'Password does not meet minimal complexity requirements (Strength must be at least Strong).';
                        this.triggerShake();
                        return;
                    }

                    // Send OTP before advancing
                    const success = await this.sendOtp();
                    if (success) {
                        this.step = 3;
                    }
                    return;
                }

                if (this.step === 3) {
                    if (!this.verification_code || this.verification_code.length !== 6) {
                        this.errorMessage = 'Verification code must be exactly 6 digits.';
                        this.triggerShake();
                        return;
                    }

                    // Verify OTP via AJAX
                    const success = await this.verifyOtp();
                    if (success) {
                        this.step = 4;
                    }
                    return;
                }

                if (this.step === 4) {
                    this.step = 5;
                    return;
                }
            },

            prevStep() {
                this.errorMessage = '';
                this.step--;
            },

            async sendOtp() {
                try {
                    const response = await fetch('{{ route("register.otp.send") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: this.email })
                    });
                    const data = await response.json();
                    
                    if (response.ok) {
                        // Reset OTP Timers
                        this.otpCountdown = 300; // 5 mins
                        this.resendCooldown = 60; // 60s
                        this.startOtpTimer();
                        this.startCooldownTimer();
                        return true;
                    } else {
                        this.errorMessage = data.message || 'Failed to dispatch registration OTP.';
                        this.triggerShake();
                        return false;
                    }
                } catch (err) {
                    this.errorMessage = 'Network error. Please try again.';
                    this.triggerShake();
                    return false;
                }
            },

            async verifyOtp() {
                try {
                    const response = await fetch('{{ route("register.otp.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: this.email, code: this.verification_code })
                    });
                    const data = await response.json();

                    if (response.ok) {
                        return true;
                    } else {
                        this.errorMessage = data.message || 'Verification failed. Try again.';
                        this.triggerShake();
                        return false;
                    }
                } catch (err) {
                    this.errorMessage = 'Network error during validation.';
                    this.triggerShake();
                    return false;
                }
            },

            startOtpTimer() {
                if (this.otpTimerId) clearInterval(this.otpTimerId);
                this.otpTimerId = setInterval(() => {
                    if (this.otpCountdown > 0) {
                        this.otpCountdown--;
                    } else {
                        clearInterval(this.otpTimerId);
                    }
                }, 1000);
            },

            startCooldownTimer() {
                if (this.cooldownTimerId) clearInterval(this.cooldownTimerId);
                this.cooldownTimerId = setInterval(() => {
                    if (this.resendCooldown > 0) {
                        this.resendCooldown--;
                    } else {
                        clearInterval(this.cooldownTimerId);
                    }
                }, 1000);
            },

            async submitForm() {
                const form = document.getElementById('secure-register-form');
                
                // Submit form standard POST
                // Since user is fully verified, submit the fields to register route
                form.submit();
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
