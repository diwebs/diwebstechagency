@extends('layouts.app')

@section('title', 'Secure Registration - Diwebs Digital Ecosystem')

@section('content')
@php
    $sessionEmail = session('validated_register_email');
    $oldEmail = old('email');
    $emailVerified = !empty($sessionEmail) && strtolower(trim($sessionEmail)) === strtolower(trim($oldEmail));
    $initialStep = 1;
    if ($errors->any()) {
        if ($emailVerified) {
            $initialStep = 5; // Skip to step 5 (Final Review) since email is already verified
        } else {
            $initialStep = 2; // Go to step 2 to correct personal info
        }
    }
    $hasOldInput = old('_token') !== null;
    $enable2faDefault = $hasOldInput ? (old('enable_2fa') ? 'true' : 'false') : 'true';
@endphp
<div x-data="registerForm()" class="mx-auto max-w-xl px-4 py-8">
    <!-- Progress Indicator Tracker -->
    <div class="mb-8 relative">
        <div class="flex items-center justify-between z-10 relative">
            <template x-for="i in 5">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 border step-circle cursor-pointer"
                         :class="{
                            'bg-brand-cyan text-brand-dark-secondary border-brand-cyan shadow-[0_0_15px_rgba(0,194,209,0.4)]': step === i,
                            'bg-brand-teal text-brand-white border-brand-teal': step > i,
                            'bg-brand-dark-primary text-brand-gray border-brand-teal/20': step < i
                         }"
                         x-text="i"
                         @click="if (i < step) step = i"></div>
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
            <h2 class="text-3xl font-extrabold tracking-tight text-brand-white text-glow" x-text="getStepTitle()"></h2>
            <p class="mt-2 text-sm sm:text-base text-brand-gray" x-text="getStepDescription()"></p>
        </div>

        <form id="secure-register-form" action="{{ route('register') }}" method="POST" @submit.prevent="submitForm()" class="relative z-10">
            @csrf
            
            <!-- STEP 1: Account Type Selection -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
                <input type="hidden" name="role" :value="role">
                <div class="grid grid-cols-1 gap-4">
                    <!-- Cards list -->
                    <template x-for="(opt, index) in roleOptions">
                        <div @click="role = opt.id"
                             class="glass-card p-6 rounded-2xl cursor-pointer hover:scale-[1.01] border flex items-start gap-4 relative overflow-hidden transition-all duration-300"
                             :class="[
                                 role === opt.id ? 'border-brand-cyan bg-brand-teal/10 shadow-[0_0_20px_rgba(0,194,209,0.15)]' : 'border-brand-teal/10 hover:border-brand-teal/30',
                                 'animate-fade-in-up stagger-' + (index + 1)
                             ]">

                            <div class="h-10 w-10 rounded-xl bg-brand-teal/15 border border-brand-teal/30 flex items-center justify-center text-brand-cyan flex-shrink-0 mt-0.5">
                                <template x-if="opt.id === 'student'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                </template>
                                <template x-if="opt.id === 'client'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </template>
                                <template x-if="opt.id === 'candidate'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </template>
                                <template x-if="opt.id === 'instructor'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                </template>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-bold text-brand-white" x-text="opt.title"></h4>
                                <p class="text-xs sm:text-sm text-brand-gray mt-1 leading-relaxed" x-text="opt.description"></p>
                            </div>
                            <div x-show="role === opt.id" class="text-brand-cyan font-bold flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
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
                        <input type="text" name="phone" id="phone" x-model="phone" @input="updateCountryFromPhone()" placeholder="+234..." class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                    </div>
                    <div>
                        <label for="country" class="block text-xs font-semibold text-brand-cyan uppercase">Country &amp; Billing Region</label>
                        <select name="country" id="country" x-model="country" @change="updatePhoneCountryCode()" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                            <option value="South Africa" class="bg-[#1A1D21] text-brand-white">South Africa 🇿🇦 (ZAR R)</option>
                            <option value="Nigeria" class="bg-[#1A1D21] text-brand-white">Nigeria 🇳🇬 (NGN ₦)</option>
                            <option value="Kenya" class="bg-[#1A1D21] text-brand-white">Kenya 🇰🇪 (KES KSh)</option>
                            <option value="Ghana" class="bg-[#1A1D21] text-brand-white">Ghana 🇬🇭 (GHS GH₵)</option>
                            <option value="Egypt" class="bg-[#1A1D21] text-brand-white">Egypt 🇪🇬 (EGP E£)</option>
                            <option value="Rwanda" class="bg-[#1A1D21] text-brand-white">Rwanda 🇷🇼 (KES KSh)</option>
                            <option value="Tanzania" class="bg-[#1A1D21] text-brand-white">Tanzania 🇹🇿 (KES KSh)</option>
                            <option value="Uganda" class="bg-[#1A1D21] text-brand-white">Uganda 🇺🇬 (KES KSh)</option>
                            <option value="Ethiopia" class="bg-[#1A1D21] text-brand-white">Ethiopia 🇪🇹 (KES KSh)</option>
                            <option value="Morocco" class="bg-[#1A1D21] text-brand-white">Morocco 🇲🇦 (EGP E£)</option>
                            <option value="United States" class="bg-[#1A1D21] text-brand-white">United States 🇺🇸 (Global - USD $)</option>
                            <option value="United Kingdom" class="bg-[#1A1D21] text-brand-white">United Kingdom 🇬🇧 (UK - GBP £)</option>
                            <option value="Canada" class="bg-[#1A1D21] text-brand-white">Canada 🇨🇦 (CAD C$)</option>
                            <option value="Germany" class="bg-[#1A1D21] text-brand-white">Germany 🇩🇪 (Europe - EUR €)</option>
                            <option value="France" class="bg-[#1A1D21] text-brand-white">France 🇫🇷 (Europe - EUR €)</option>
                            <option value="India" class="bg-[#1A1D21] text-brand-white">India 🇮🇳 (INR ₹)</option>
                            <option value="United Arab Emirates" class="bg-[#1A1D21] text-brand-white">United Arab Emirates 🇦🇪 (USD $)</option>
                            <option value="Australia" class="bg-[#1A1D21] text-brand-white">Australia 🇦🇺 (AUD A$)</option>
                            <option value="Saudi Arabia" class="bg-[#1A1D21] text-brand-white">Saudi Arabia 🇸🇦 (USD $)</option>
                            <option value="China" class="bg-[#1A1D21] text-brand-white">China 🇨🇳 (USD $)</option>
                            <option value="Japan" class="bg-[#1A1D21] text-brand-white">Japan 🇯🇵 (USD $)</option>
                            <option value="Brazil" class="bg-[#1A1D21] text-brand-white">Brazil 🇧🇷 (USD $)</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Regional Payment Plan Badge -->
                <div class="mt-3 p-3 rounded-xl bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-between text-xs text-brand-white">
                    <div class="flex items-center gap-2">
                        <span class="text-brand-cyan">🌍 Regional Billing:</span>
                        <strong x-text="getRegionalCurrencyLabel()"></strong>
                    </div>
                    <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">Localized Rate Active</span>
                </div>

                <!-- Password with show/hide toggle -->
                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-semibold text-brand-cyan uppercase">Password</label>
                        <span class="text-[10px] text-brand-gray">or use biometric below</span>
                    </div>
                    <div class="relative mt-2">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" x-model="password" @input="evaluatePasswordStrength()" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 pr-10 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                        <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-brand-gray hover:text-brand-cyan transition-colors"
                                :title="showPassword ? 'Hide password' : 'Show password'">
                            <!-- Eye-off icon -->
                            <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <!-- Eye icon -->
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Password Strength Meter -->
                    <div class="mt-2">
                        <div class="flex items-center justify-between text-[10px] font-semibold text-brand-gray mb-1 uppercase">
                            <span>Password Strength</span>
                            <span :class="strengthColor" x-text="strengthText"></span>
                        </div>
                        <div class="w-full h-1.5 bg-brand-dark-primary rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-500 rounded-full" :class="strengthBg" :style="'width: ' + (passwordStrength * 25) + '%'"></div>
                        </div>
                        <p class="text-[10px] text-brand-gray mt-1">Must be at least 8 characters and include uppercase letters, lowercase letters, numbers, and a special character (e.g. @, #, !).</p>
                    </div>
                </div>

                <!-- Confirm Password with show/hide toggle -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brand-cyan uppercase">Confirm Password</label>
                    <div class="relative mt-2">
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" x-model="password_confirmation" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 pr-10 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                        <button type="button" @click="showConfirm = !showConfirm" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-brand-gray hover:text-brand-cyan transition-colors"
                                :title="showConfirm ? 'Hide password' : 'Show password'">
                            <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Biometric Quick-Fill Button -->
                <div x-show="biometricAvailable" class="pt-1">
                    <button type="button" @click="triggerBiometric()"
                            :disabled="biometricStatus === 'loading'"
                            class="w-full flex items-center justify-center gap-3 rounded-xl border border-brand-teal/30 bg-brand-dark-secondary/60 px-4 py-3 text-sm font-semibold text-brand-white hover:border-brand-cyan hover:bg-brand-teal/10 hover:text-brand-cyan active:scale-95 transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed group"
                            id="biometric-btn">
                        <!-- Fingerprint icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-teal group-hover:text-brand-cyan transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C9.51 2 7.27 3.07 5.7 4.8M12 2c2.49 0 4.73 1.07 6.3 2.8M2 12c0-1.85.5-3.58 1.37-5.06M22 12c0-1.85-.5-3.58-1.37-5.06M12 22c-1.57 0-3.06-.4-4.36-1.1M12 22c1.57 0 3.06-.4 4.36-1.1M8 12a4 4 0 018 0M6 12a6 6 0 0112 0M4 12a8 8 0 0116 0" />
                        </svg>
                        <span x-text="biometricStatus === 'loading' ? 'Verifying biometric...' : biometricStatus === 'success' ? '✓ Biometric Verified' : 'Use Fingerprint / Face ID to fill password'"></span>
                        <!-- Pulse ring when loading -->
                        <span x-show="biometricStatus === 'loading'" class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-cyan"></span>
                        </span>
                    </button>
                    <p class="text-[10px] text-brand-gray mt-1.5 text-center" x-show="biometricStatus !== 'success'">
                        Your device will prompt for biometric confirmation. Your biometric data never leaves your device.
                    </p>
                    <p class="text-[10px] text-emerald-400 mt-1.5 text-center font-semibold" x-show="biometricStatus === 'success'">
                        ✓ Identity confirmed — password fields have been filled automatically.
                    </p>
                    <p class="text-[10px] text-rose-400 mt-1.5 text-center" x-show="biometricStatus === 'failed'">
                        ✗ Biometric check failed or was cancelled. Please enter your password manually.
                    </p>
                </div>

                <div x-show="role === 'client'" class="pt-2">
                    <label for="company_name" class="block text-xs font-semibold text-brand-cyan uppercase">Company Name (Optional)</label>
                    <input type="text" name="company_name" id="company_name" x-model="company_name" placeholder="e.g. Acme Corp" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div x-show="role === 'client'" class="pt-2">
                    <label for="referral_code" class="block text-xs font-semibold text-brand-cyan uppercase">Referral Code (Optional)</label>
                    <input type="text" name="referral_code" id="referral_code" x-model="referral_code" placeholder="e.g. REF-SARAH1" class="mt-2 block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                </div>
            </div>

            <!-- STEP 3: Identity Verification (OTP) -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6 text-center">
                <p class="text-sm text-brand-gray">We have sent a 6-digit confirmation code to <strong class="text-brand-white" x-text="email"></strong>. Please check your inbox (and spam folder) and enter the code below.</p>
                
                <div class="flex justify-center gap-2 max-w-xs mx-auto">
                    <!-- Custom verification inputs code block -->
                    <input type="text" maxlength="6" x-model="verification_code" placeholder="000000" class="block w-full rounded-md border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-3 text-2xl font-bold tracking-[8px] text-center text-brand-cyan focus:border-brand-cyan focus:outline-none transition-all">
                </div>

                <div class="text-xs text-brand-gray space-y-2">
                    <div x-show="otpCountdown > 0">
                        This code will expire in <span class="text-brand-cyan font-bold" x-text="formatTime(otpCountdown)"></span>
                    </div>
                    <div x-show="otpCountdown === 0">
                        The code has expired. Please request a new one.
                    </div>
                    <button type="button" @click="sendOtp()" :disabled="resendCooldown > 0"
                            class="px-4 py-2 rounded-md border border-brand-teal/20 text-xs font-semibold text-brand-cyan hover:bg-brand-teal/10 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="resendCooldown > 0">Send new code in <span x-text="resendCooldown"></span>s</span>
                        <span x-show="resendCooldown === 0">Send a New Code</span>
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
                            <label for="enable_2fa" class="font-bold text-brand-white cursor-pointer">Double Login Protection</label>
                            <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Recommended</span>
                        </div>
                        <p class="text-xs text-brand-gray mt-1 leading-relaxed">Each time you log in, we will also send a one-time code to your email as an extra safety check — so even if someone else knows your password, they still cannot access your account.</p>
                    </div>
                </div>

                <!-- Passkey / Biometric Enroll -->
                <div class="glass-card p-5 rounded-2xl border transition-all duration-300"
                     :class="passkeyRegistered ? 'border-emerald-500/40 bg-emerald-950/20' : 'border-brand-teal/10'">
                    <div class="flex items-start gap-4">
                        <input type="checkbox" id="passkey_enroll" x-model="passkey_enroll" class="mt-1 accent-brand-cyan w-5 h-5 rounded border-brand-teal/20 bg-brand-dark-secondary">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <label for="passkey_enroll" class="font-bold text-brand-white cursor-pointer">Enable Fingerprint / Face ID Login</label>
                                <span x-show="passkeyRegistered" class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Registered ✓</span>
                            </div>
                            <p class="text-xs text-brand-gray mt-1 leading-relaxed">Register your biometric now so you can log in using your fingerprint, face recognition, or Windows Hello — no password needed.</p>

                            <!-- Register Biometric CTA -->
                            <div x-show="passkey_enroll && biometricAvailable" class="mt-3">
                                <button type="button" @click="registerPasskey()"
                                        :disabled="passkeyRegistered || passkeyLoading"
                                        class="flex items-center gap-2 rounded-lg border border-brand-cyan/40 bg-brand-dark-primary/60 px-4 py-2 text-xs font-bold text-brand-cyan hover:bg-brand-teal/10 active:scale-95 transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C9.51 2 7.27 3.07 5.7 4.8M12 2c2.49 0 4.73 1.07 6.3 2.8M2 12c0-1.85.5-3.58 1.37-5.06M22 12c0-1.85-.5-3.58-1.37-5.06M12 22c-1.57 0-3.06-.4-4.36-1.1M12 22c1.57 0 3.06-.4 4.36-1.1M8 12a4 4 0 018 0M6 12a6 6 0 0112 0M4 12a8 8 0 0116 0" />
                                    </svg>
                                    <span x-text="passkeyLoading ? 'Registering...' : passkeyRegistered ? '✓ Biometric Registered' : 'Register My Biometric Now'"></span>
                                </button>
                                <p class="text-[10px] text-brand-gray mt-2" x-show="!passkeyRegistered">This uses your device's secure enclave — your biometric data never leaves your device or reaches our servers.</p>
                                <p class="text-[10px] text-emerald-400 mt-2 font-semibold" x-show="passkeyRegistered">✓ Biometric credential saved. You can use it to log in after registration.</p>
                            </div>
                            <div x-show="passkey_enroll && !biometricAvailable" class="mt-2">
                                <p class="text-[10px] text-amber-400">⚠ Your current browser or device does not support biometric authentication. You can set this up later from your security settings.</p>
                            </div>
                        </div>
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
                        <span class="text-xs text-brand-gray">Your Name</span>
                        <strong class="text-brand-white text-sm" x-text="name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Email Address</span>
                        <strong class="text-brand-white text-sm" x-text="email"></strong>
                    </div>
                    <div x-show="role === 'client' && company_name" class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Company Name</span>
                        <strong class="text-brand-white text-sm" x-text="company_name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-brand-teal/10 pb-3">
                        <span class="text-xs text-brand-gray">Email Confirmed</span>
                        <strong class="text-emerald-400 text-sm">✓ Yes, Confirmed</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-brand-gray">Login Protection</span>
                        <strong class="text-brand-cyan text-sm" x-text="enable_2fa ? 'Double Protection On' : 'Basic Protection'"></strong>
                    </div>
                </div>

                <p class="text-[10px] text-brand-gray leading-relaxed text-center">By clicking 'Create My Account', you agree to our Terms of Service. Your information is kept safe and will only be used to provide you with our services.</p>
            </div>

            <!-- Alert Messages -->
            <div x-show="errorMessage" class="mt-4 p-3 rounded bg-rose-950/40 border border-rose-500/30 text-xs text-rose-400 text-center" x-text="errorMessage"></div>

            <!-- Actions Buttons -->
            <div class="mt-8 flex items-center justify-between gap-4 border-t border-brand-teal/10 pt-6">
                <button type="button" x-show="step > 1" @click="prevStep()" class="rounded-md border border-brand-teal/20 px-6 py-2.5 text-sm font-semibold text-brand-cyan hover:bg-brand-teal/10 transition-all cursor-pointer">← Back</button>
                <div class="flex-1"></div>
                <button type="button" x-show="step < 5" @click="nextStep()" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Next Step →</button>
                <button type="submit" x-show="step === 5" class="rounded-md bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-2.5 text-sm font-bold text-brand-dark-secondary shadow-md hover:opacity-90 transition-all cursor-pointer">Create My Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    function registerForm() {
        return {
            step: {{ $initialStep }},
            role: '{{ old('role') }}' || (new URLSearchParams(window.location.search).get('ref') ? 'client' : 'student'),
            name: '{{ old('name') }}',
            company_name: '{{ old('company_name') }}',
            email: '{{ old('email') }}',
            phone: '{{ old('phone') ?? '+234' }}',
            country: '{{ old('country') ?? 'Nigeria' }}',
            countryDialCodes: {
                'Nigeria': '+234',
                'United States': '+1',
                'United Kingdom': '+44',
                'Canada': '+1',
                'Ghana': '+233',
                'Kenya': '+254',
                'South Africa': '+27',
                'Germany': '+49',
                'France': '+33',
                'India': '+91',
                'United Arab Emirates': '+971',
                'Australia': '+61',
                'Saudi Arabia': '+966',
                'Egypt': '+20',
                'Rwanda': '+250',
                'China': '+86',
                'Japan': '+81',
                'Brazil': '+55'
            },

            updatePhoneCountryCode() {
                const dialCode = this.countryDialCodes[this.country];
                if (dialCode) {
                    if (!this.phone) {
                        this.phone = dialCode;
                    } else {
                        const match = this.phone.match(/^\+(\d+)/);
                        if (match) {
                            const existingDial = match[0];
                            this.phone = dialCode + this.phone.slice(existingDial.length);
                        } else {
                            this.phone = dialCode + this.phone;
                        }
                    }
                }
            },

            updateCountryFromPhone() {
                if (this.phone && this.phone.startsWith('+')) {
                    let bestMatch = null;
                    let longestLength = 0;
                    for (const [country, code] of Object.entries(this.countryDialCodes)) {
                        if (this.phone.startsWith(code)) {
                            if (code.length > longestLength) {
                                longestLength = code.length;
                                bestMatch = country;
                            }
                        }
                    }
                    if (bestMatch) {
                        this.country = bestMatch;
                    }
                }
            },

            getRegionalCurrencyLabel() {
                const c = (this.country || '').toLowerCase();
                if (['south africa', 'za', 'namibia', 'botswana', 'lesotho', 'eswatini', 'zimbabwe'].includes(c)) {
                    return 'South Africa (ZAR R) — Local EFT & Paystack';
                }
                if (['kenya', 'ke', 'tanzania', 'uganda', 'rwanda', 'ethiopia'].includes(c)) {
                    return 'Kenya & East Africa (KES KSh) — Mobile Money & Flutterwave';
                }
                if (['ghana', 'gh'].includes(c)) {
                    return 'Ghana (GHS GH₵) — Mobile Money & Paystack';
                }
                if (['egypt', 'eg', 'morocco', 'tunisia', 'algeria'].includes(c)) {
                    return 'Egypt & North Africa (EGP E£) — Card & Stripe';
                }
                if (['nigeria', 'ng', 'cameroon', 'cote d\'ivoire', 'senegal'].includes(c)) {
                    return 'Nigeria & West Africa (NGN ₦) — Local Bank & Paystack';
                }
                if (['united kingdom', 'uk', 'gb', 'great britain'].includes(c)) {
                    return 'United Kingdom (GBP £) — UK Faster Payments';
                }
                if (['germany', 'france', 'italy', 'spain', 'netherlands', 'belgium', 'ireland', 'europe'].includes(c)) {
                    return 'Europe (EUR €) — EU SEPA Transfer';
                }
                if (['canada', 'ca'].includes(c)) {
                    return 'Canada (CAD C$) — Interac & Credit Card';
                }
                if (['australia', 'au'].includes(c)) {
                    return 'Australia (AUD A$) — Credit Card & PayPal';
                }
                if (['india', 'in'].includes(c)) {
                    return 'India (INR ₹) — Razorpay & UPI';
                }
                return 'United States & Global (USD $) — Stripe & Cards';
            },
            password: '',
            password_confirmation: '',
            showPassword: false,
            showConfirm: false,
            referral_code: '{{ old('referral_code') }}' || (new URLSearchParams(window.location.search).get('ref') || ''),
            verification_code: '',
            enable_2fa: {{ $enable2faDefault }},
            passkey_enroll: false,
            passkeyRegistered: false,
            passkeyLoading: false,
            biometricAvailable: false,
            biometricStatus: 'idle', // idle | loading | success | failed
            _biometricPassword: null, // stores temp strong random password set by biometric
            passwordStrength: 0,
            strengthText: 'Weak',
            strengthColor: 'text-rose-500',
            strengthBg: 'bg-rose-500',
            otpCountdown: 0,
            resendCooldown: 0,
            otpTimerId: null,
            cooldownTimerId: null,
            errorMessage: '{{ $errors->first() }}',
            errorShake: false,

            roleOptions: [
                { id: 'student', icon: '🎓', title: 'Student', description: 'I want to learn — access video courses, take tests, earn certificates, and track my progress.' },
                { id: 'client', icon: '💼', title: 'Client', description: 'I hired Diwebs for a project — view my project updates, approve work, pay invoices, and chat with the team.' },
                { id: 'candidate', icon: '📝', title: 'Exam Candidate', description: 'I am here to take a computer-based test or exam assigned to me.' },
                { id: 'instructor', icon: '👨‍🏫', title: 'Instructor / Tutor', description: 'I teach at Diwebs Academy — manage course content, grade students, and run live classes.' }
            ],

            // ── Biometric helpers ────────────────────────────────────────────────
            _randomPassword(len = 24) {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                const arr = new Uint8Array(len);
                crypto.getRandomValues(arr);
                return Array.from(arr).map(b => chars[b % chars.length]).join('');
            },

            async _checkBiometricAvailability() {
                if (!window.PublicKeyCredential) {
                    this.biometricAvailable = false;
                    return;
                }
                try {
                    const available = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
                    this.biometricAvailable = !!available;
                } catch (e) {
                    this.biometricAvailable = false;
                }
            },

            async triggerBiometric() {
                if (this.biometricStatus === 'loading') return;
                this.biometricStatus = 'loading';
                this.errorMessage = '';

                try {
                    // Use a simple get() assertion with a unique challenge to prove user-verifying platform authenticator
                    const challenge = crypto.getRandomValues(new Uint8Array(32));
                    const credential = await navigator.credentials.get({
                        publicKey: {
                            challenge,
                            timeout: 60000,
                            userVerification: 'required',
                            rpId: window.location.hostname,
                        }
                    });

                    if (credential) {
                        // Biometric succeeded — generate a cryptographically strong password and fill both fields
                        const pwd = this._randomPassword(24);
                        this.password = pwd;
                        this.password_confirmation = pwd;
                        this._biometricPassword = pwd;
                        this.evaluatePasswordStrength();
                        this.biometricStatus = 'success';
                    } else {
                        this.biometricStatus = 'failed';
                    }
                } catch (err) {
                    if (err.name === 'NotAllowedError') {
                        // No passkey registered yet — fall back: generate password and prompt
                        try {
                            const challenge = crypto.getRandomValues(new Uint8Array(32));
                            const userId = crypto.getRandomValues(new Uint8Array(16));
                            await navigator.credentials.create({
                                publicKey: {
                                    challenge,
                                    rp: { name: 'Diwebs Tech Agency', id: window.location.hostname },
                                    user: { id: userId, name: this.email || 'user', displayName: this.name || 'User' },
                                    pubKeyCredParams: [{ type: 'public-key', alg: -7 }, { type: 'public-key', alg: -257 }],
                                    authenticatorSelection: { authenticatorAttachment: 'platform', userVerification: 'required' },
                                    timeout: 60000,
                                }
                            });
                            const pwd = this._randomPassword(24);
                            this.password = pwd;
                            this.password_confirmation = pwd;
                            this._biometricPassword = pwd;
                            this.evaluatePasswordStrength();
                            this.biometricStatus = 'success';
                        } catch (e2) {
                            this.biometricStatus = 'failed';
                            this.errorMessage = 'Biometric authentication was cancelled or not supported. Please enter your password manually.';
                        }
                    } else {
                        this.biometricStatus = 'failed';
                        this.errorMessage = 'Biometric check failed. Please enter your password manually.';
                    }
                }
            },

            async registerPasskey() {
                if (this.passkeyRegistered || this.passkeyLoading) return;
                this.passkeyLoading = true;
                try {
                    const challenge = crypto.getRandomValues(new Uint8Array(32));
                    const userId = crypto.getRandomValues(new Uint8Array(16));
                    const cred = await navigator.credentials.create({
                        publicKey: {
                            challenge,
                            rp: { name: 'Diwebs Tech Agency', id: window.location.hostname },
                            user: { id: userId, name: this.email || 'user', displayName: this.name || 'User' },
                            pubKeyCredParams: [{ type: 'public-key', alg: -7 }, { type: 'public-key', alg: -257 }],
                            authenticatorSelection: { authenticatorAttachment: 'platform', userVerification: 'required', residentKey: 'preferred' },
                            timeout: 60000,
                        }
                    });
                    if (cred) {
                        this.passkeyRegistered = true;
                    }
                } catch (e) {
                    this.errorMessage = 'Biometric registration was cancelled. You can retry or set it up later from your account settings.';
                } finally {
                    this.passkeyLoading = false;
                }
            },

            async init() {
                await this._checkBiometricAvailability();
            },

            getStepLabel(i) {
                const labels = ['Account Type', 'Your Details', 'Confirm Email', 'Safety', 'Review'];
                return labels[i - 1];
            },

            getStepTitle() {
                const titles = [
                    'Who are you signing up as?',
                    'Tell Us About Yourself',
                    'Confirm Your Email Address',
                    'Keep Your Account Safe',
                    'Almost Done — Review Your Details'
                ];
                return titles[this.step - 1];
            },

            getStepDescription() {
                const desc = [
                    'Pick the option that best describes why you are joining Diwebs.',
                    'Fill in your name, email address, and create a password.',
                    'We sent a 6-digit code to your email. Enter it here to prove it is yours.',
                    'Choose how you want to protect your account from unauthorised access.',
                    'Check your details below and click the button to finish creating your account.'
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
                if (this.password.length >= 8) score++;
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
                        this.errorMessage = 'Please fill in all the fields before continuing.';
                        this.triggerShake();
                        return;
                    }
                    if (this.password !== this.password_confirmation) {
                        this.errorMessage = 'The passwords you entered do not match. Please re-enter them.';
                        this.triggerShake();
                        return;
                    }
                    if (this.passwordStrength < 3) {
                        this.errorMessage = 'Your password is not strong enough. Please make it at least 12 characters long and include uppercase letters, lowercase letters, numbers, and symbols.';
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
                        this.errorMessage = 'Please enter the 6-digit code we sent to your email.';
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
                        this.errorMessage = data.message || 'We could not send the confirmation code. Please try again.';
                        this.triggerShake();
                        return false;
                    }
                } catch (err) {
                    this.errorMessage = 'Connection problem. Please check your internet and try again.';
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
                        this.errorMessage = data.message || 'That code did not work. Please check it and try again.';
                        this.triggerShake();
                        return false;
                    }
                } catch (err) {
                    this.errorMessage = 'Connection problem. Please check your internet and try again.';
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
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(3deg); }
    }
    
    @keyframes bounceSuccess {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.12); }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    
    .animate-float {
        animation: float 5s infinite ease-in-out;
    }
    
    .animate-bounce-success {
        animation: bounceSuccess 1.5s infinite ease-in-out;
    }
    
    .stagger-1 { animation-delay: 50ms; }
    .stagger-2 { animation-delay: 120ms; }
    .stagger-3 { animation-delay: 190ms; }
    .stagger-4 { animation-delay: 260ms; }
    
    .step-circle {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .step-circle:hover {
        transform: scale(1.15);
    }
</style>
@endsection
