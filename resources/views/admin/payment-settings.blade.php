@extends('layouts.admin')

@section('title', 'Payment Gateway Settings - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Payment Gateway &amp; Currency</h1>
        <p class="text-sm text-brand-gray mt-1">Configure payment methods, API credentials, default currency, and billing settings for the entire platform.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-3.5 text-sm font-medium text-emerald-400 flex items-center gap-2.5">
            <span class="text-lg">✅</span> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-500/30 bg-red-500/10 px-5 py-3.5 text-sm text-red-400 space-y-1">
            @foreach ($errors->all() as $error)
                <div>⚠️ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.payment-settings.update') }}" method="POST" id="payment-settings-form">
        @csrf

        {{-- ── SECTION 1: Active Gateway Selector ── --}}
        <div class="glass-card rounded-2xl border border-brand-teal/15 p-6 mb-6">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-1">Active Payment Gateway</h2>
            <p class="text-[11px] text-brand-gray mb-5">Select which payment processor will be used for all new transactions across the platform.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3" id="gateway-selector">
                @php
                    $gateways = [
                        ['id' => 'stripe',        'label' => 'Stripe',        'icon_img' => '/images/brand/stripe.svg'],
                        ['id' => 'paystack',      'label' => 'Paystack',      'icon_img' => '/images/brand/paystack.svg'],
                        ['id' => 'flutterwave',   'label' => 'Flutterwave',   'icon_img' => '/images/brand/flutterwave.svg'],
                        ['id' => 'paypal',        'label' => 'PayPal',        'icon_img' => '/images/brand/paypal.svg'],
                        ['id' => 'bank_transfer', 'label' => 'Bank Wire',     'icon_img' => '/images/brand/bank.svg'],
                        ['id' => 'crypto',        'label' => 'Crypto',        'icon_img' => '/images/brand/bitcoin.svg'],
                    ];
                @endphp

                @foreach($gateways as $gw)
                    <label for="gw_{{ $gw['id'] }}"
                           class="gateway-card relative flex flex-col items-center gap-2 rounded-xl border p-4 cursor-pointer transition-all
                                  {{ $paymentSettings['active_gateway'] === $gw['id']
                                     ? 'border-brand-cyan bg-brand-cyan/10 shadow-lg shadow-brand-cyan/10'
                                     : 'border-brand-teal/20 bg-brand-dark-secondary/40 hover:border-brand-teal/50' }}">
                        <input type="radio" name="active_gateway" id="gw_{{ $gw['id'] }}" value="{{ $gw['id'] }}"
                               {{ $paymentSettings['active_gateway'] === $gw['id'] ? 'checked' : '' }}
                               class="sr-only" />
                        <div class="h-10 flex items-center justify-center">
                            <img src="{{ $gw['icon_img'] }}" alt="{{ $gw['label'] }}" class="h-10 w-10 object-contain">
                        </div>
                        <span class="text-[11px] font-bold text-brand-white text-center leading-tight">{{ $gw['label'] }}</span>
                        @if($paymentSettings['active_gateway'] === $gw['id'])
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-brand-cyan"></span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── SECTION 2: Currency & Billing Settings ── --}}
        <div class="glass-card rounded-2xl border border-brand-teal/15 p-6 mb-6">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan mb-1">Currency &amp; Billing Configuration</h2>
            <p class="text-[11px] text-brand-gray mb-5">Configure the platform's default currency, invoice prefix, and tax settings.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Currency Code --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Default Currency <span class="text-red-400">*</span></label>
                    <select name="default_currency"
                            class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors">
                        @php
                            $currencies = [
                                'USD' => '🇺🇸 US Dollar (USD)',
                                'EUR' => '🇪🇺 Euro (EUR)',
                                'GBP' => '🇬🇧 British Pound (GBP)',
                                'NGN' => '🇳🇬 Nigerian Naira (NGN)',
                                'GHS' => '🇬🇭 Ghanaian Cedi (GHS)',
                                'KES' => '🇰🇪 Kenyan Shilling (KES)',
                                'ZAR' => '🇿🇦 South African Rand (ZAR)',
                                'CAD' => '🇨🇦 Canadian Dollar (CAD)',
                                'AUD' => '🇦🇺 Australian Dollar (AUD)',
                                'JPY' => '🇯🇵 Japanese Yen (JPY)',
                                'CNY' => '🇨🇳 Chinese Yuan (CNY)',
                                'INR' => '🇮🇳 Indian Rupee (INR)',
                                'AED' => '🇦🇪 UAE Dirham (AED)',
                                'SAR' => '🇸🇦 Saudi Riyal (SAR)',
                                'BTC' => '₿ Bitcoin (BTC)',
                                'USDT'=> '💲 Tether (USDT)',
                            ];
                        @endphp
                        @foreach($currencies as $code => $label)
                            <option value="{{ $code }}" {{ $paymentSettings['default_currency'] === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Currency Symbol --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Currency Symbol <span class="text-red-400">*</span></label>
                    <input type="text" name="currency_symbol" maxlength="5" required
                           value="{{ old('currency_symbol', $paymentSettings['currency_symbol']) }}"
                           class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                           placeholder="$, £, ₦, €" />
                </div>

                {{-- Symbol Position --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Symbol Position <span class="text-red-400">*</span></label>
                    <select name="currency_position"
                            class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors">
                        <option value="before" {{ $paymentSettings['currency_position'] === 'before' ? 'selected' : '' }}>Before amount ($100)</option>
                        <option value="after"  {{ $paymentSettings['currency_position'] === 'after'  ? 'selected' : '' }}>After amount (100$)</option>
                    </select>
                </div>

                {{-- Invoice Prefix --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Invoice Number Prefix <span class="text-red-400">*</span></label>
                    <input type="text" name="invoice_prefix" maxlength="20" required
                           value="{{ old('invoice_prefix', $paymentSettings['invoice_prefix']) }}"
                           class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                           placeholder="INV, DWB, DIWEBS" />
                </div>

                {{-- Tax Rate --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Tax Rate (%) <span class="text-red-400">*</span></label>
                    <input type="number" name="tax_rate" min="0" max="100" step="0.01" required
                           value="{{ old('tax_rate', $paymentSettings['tax_rate']) }}"
                           class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                           placeholder="7.5" />
                </div>

                {{-- Tax Label --}}
                <div>
                    <label class="block text-xs font-bold text-brand-gray mb-1.5">Tax Label <span class="text-red-400">*</span></label>
                    <input type="text" name="tax_label" maxlength="20" required
                           value="{{ old('tax_label', $paymentSettings['tax_label']) }}"
                           class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/50 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                           placeholder="VAT, GST, Sales Tax" />
                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Gateway Credentials ── --}}
        <div class="space-y-4">

            {{-- STRIPE --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-indigo-950/30 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <img src="/images/brand/stripe.svg" alt="Stripe" class="h-9 w-9 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Stripe</h3>
                            <p class="text-[10px] text-brand-gray">Global card processing — Visa, Mastercard, Amex, and local methods.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="stripe_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['stripe_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Publishable Key</label>
                        <input type="text" name="stripe_public_key"
                               value="{{ old('stripe_public_key', $paymentSettings['stripe_public_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="pk_live_..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Secret Key</label>
                        <input type="password" name="stripe_secret_key"
                               value="{{ old('stripe_secret_key', $paymentSettings['stripe_secret_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="sk_live_..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Webhook Signing Secret</label>
                        <input type="password" name="stripe_webhook_secret"
                               value="{{ old('stripe_webhook_secret', $paymentSettings['stripe_webhook_secret']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="whsec_..." />
                    </div>
                </div>
            </div>

            {{-- PAYSTACK --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-emerald-950/30 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <img src="/images/brand/paystack.svg" alt="Paystack" class="h-9 w-9 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Paystack</h3>
                            <p class="text-[10px] text-brand-gray">Africa-first payments — Nigeria, Ghana, Kenya &amp; South Africa.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="paystack_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['paystack_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Public Key</label>
                        <input type="text" name="paystack_public_key"
                               value="{{ old('paystack_public_key', $paymentSettings['paystack_public_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="pk_live_..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Secret Key</label>
                        <input type="password" name="paystack_secret_key"
                               value="{{ old('paystack_secret_key', $paymentSettings['paystack_secret_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="sk_live_..." />
                    </div>
                </div>
            </div>

            {{-- FLUTTERWAVE --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-orange-950/30 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <img src="/images/brand/flutterwave.svg" alt="Flutterwave" class="h-9 w-9 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Flutterwave</h3>
                            <p class="text-[10px] text-brand-gray">Pan-African payments — 30+ African countries, mobile money &amp; USSD.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="flw_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['flw_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Public Key</label>
                        <input type="text" name="flw_public_key"
                               value="{{ old('flw_public_key', $paymentSettings['flw_public_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="FLWPUBK_TEST-..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Secret Key</label>
                        <input type="password" name="flw_secret_key"
                               value="{{ old('flw_secret_key', $paymentSettings['flw_secret_key']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="FLWSECK_TEST-..." />
                    </div>
                </div>
            </div>

            {{-- PAYPAL --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-blue-950/30 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <img src="/images/brand/paypal.svg" alt="PayPal" class="h-9 w-9 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">PayPal</h3>
                            <p class="text-[10px] text-brand-gray">Global checkout via PayPal wallet, credit/debit cards. 200+ markets.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="paypal_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['paypal_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Client ID</label>
                        <input type="text" name="paypal_client_id"
                               value="{{ old('paypal_client_id', $paymentSettings['paypal_client_id']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="AXk3..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Client Secret</label>
                        <input type="password" name="paypal_secret"
                               value="{{ old('paypal_secret', $paymentSettings['paypal_secret']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="EJk9..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Environment Mode</label>
                        <select name="paypal_mode"
                                class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors">
                            <option value="sandbox" {{ $paymentSettings['paypal_mode'] === 'sandbox' ? 'selected' : '' }}>🧪 Sandbox (Testing)</option>
                            <option value="live"    {{ $paymentSettings['paypal_mode'] === 'live'    ? 'selected' : '' }}>🚀 Live (Production)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- BANK TRANSFER --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-slate-800/50 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <img src="/images/brand/bank.svg" alt="Bank Wire" class="h-9 w-9 flex-shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Bank Wire Transfer</h3>
                            <p class="text-[10px] text-brand-gray">Direct bank-to-bank payments. Clients wire funds manually using your account details.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="bank_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['bank_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Bank Name</label>
                        <input type="text" name="bank_name"
                               value="{{ old('bank_name', $paymentSettings['bank_name']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                               placeholder="GTBank, Zenith Bank..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Account Name</label>
                        <input type="text" name="bank_account_name"
                               value="{{ old('bank_account_name', $paymentSettings['bank_account_name']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors"
                               placeholder="Diwebs Tech Agency Ltd" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Account Number</label>
                        <input type="text" name="bank_account_number"
                               value="{{ old('bank_account_number', $paymentSettings['bank_account_number']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="0123456789" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Routing / Sort Code</label>
                        <input type="text" name="bank_routing_number"
                               value="{{ old('bank_routing_number', $paymentSettings['bank_routing_number']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="021000021" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">SWIFT / BIC Code</label>
                        <input type="text" name="bank_swift_code"
                               value="{{ old('bank_swift_code', $paymentSettings['bank_swift_code']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-sm text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="GTBINGLAXXX" />
                    </div>
                </div>
            </div>

            {{-- CRYPTO --}}
            <div class="glass-card rounded-2xl border border-brand-teal/15 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 bg-yellow-950/30 border-b border-brand-teal/10">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <img src="/images/brand/bitcoin.svg" alt="Bitcoin" class="h-8 w-8">
                            <img src="/images/brand/usdt.svg" alt="USDT" class="h-8 w-8">
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Cryptocurrency Wallets</h3>
                            <p class="text-[10px] text-brand-gray">Accept Bitcoin and USDT (TRC20/ERC20) directly to your on-chain wallet addresses.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="crypto_enabled" value="1" class="sr-only peer"
                               {{ $paymentSettings['crypto_enabled'] ? 'checked' : '' }}>
                        <div class="w-10 h-5 bg-brand-dark-secondary border border-brand-teal/30 rounded-full peer peer-checked:bg-brand-cyan peer-checked:border-brand-cyan after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                        <span class="ml-2 text-[10px] font-bold text-brand-gray peer-checked:text-brand-cyan">Enabled</span>
                    </label>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">Bitcoin (BTC) Wallet Address</label>
                        <input type="text" name="crypto_wallet_btc"
                               value="{{ old('crypto_wallet_btc', $paymentSettings['crypto_wallet_btc']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="bc1q..." />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand-gray mb-1.5">USDT (TRC20 or ERC20) Wallet Address</label>
                        <input type="text" name="crypto_wallet_usdt"
                               value="{{ old('crypto_wallet_usdt', $paymentSettings['crypto_wallet_usdt']) }}"
                               class="w-full rounded-lg border border-brand-teal/20 bg-brand-dark-secondary/60 px-4 py-2.5 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan focus:outline-none focus:ring-1 focus:ring-brand-cyan transition-colors font-mono"
                               placeholder="TR7N..." />
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SAVE BUTTON ── --}}
        <div class="mt-8 flex items-center justify-between">
            <p class="text-[11px] text-brand-gray">Changes are persisted to the platform cache and take effect immediately.</p>
            <button type="submit"
                    class="inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-7 py-3 text-sm font-bold text-brand-dark-secondary shadow-lg shadow-brand-teal/25 hover:opacity-90 hover:scale-[1.02] active:scale-[0.99] transition-all">
                💾 Save Payment Settings
            </button>
        </div>

    </form>
</div>

<script>
    // Gateway card selection highlight
    document.querySelectorAll('#gateway-selector input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.gateway-card').forEach(card => {
                card.classList.remove('border-brand-cyan', 'bg-brand-cyan/10', 'shadow-lg', 'shadow-brand-cyan/10');
                card.classList.add('border-brand-teal/20', 'bg-brand-dark-secondary/40');
                const dot = card.querySelector('.w-2.h-2');
                if (dot) dot.remove();
            });
            const selected = radio.closest('.gateway-card');
            selected.classList.remove('border-brand-teal/20', 'bg-brand-dark-secondary/40');
            selected.classList.add('border-brand-cyan', 'bg-brand-cyan/10', 'shadow-lg', 'shadow-brand-cyan/10');
            const dot = document.createElement('span');
            dot.className = 'absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-brand-cyan';
            selected.appendChild(dot);
        });
    });
</script>
@endsection
