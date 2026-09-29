@extends('layouts.app')

@section('title', 'Project Checkout - Diwebs Client Workspace')

@section('content')
<div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4 mb-12" x-cloak x-data="{
    paymentMethod: '',
    cardName: '',
    cardNumber: '',
    cardExpiry: '',
    cardCvv: '',
    txid: '',
    isProcessing: false,
    processingStep: 'Connecting to payment provider...',
    submitPayment() {
        this.isProcessing = true;
        
        if (['stripe', 'paypal', 'paystack', 'flutterwave', 'razorpay', 'coinbase'].includes(this.paymentMethod)) {
            // Simulate automatic payment gateway processing steps
            setTimeout(() => {
                this.processingStep = 'Authorizing transaction amount...';
                setTimeout(() => {
                    this.processingStep = 'Payment successful! Initializing development environment...';
                    setTimeout(() => {
                        this.$refs.paymentForm.submit();
                    }, 1200);
                }, 1200);
            }, 1000);
        } else {
            // Manual flow is immediate submission
            this.$refs.paymentForm.submit();
        }
    }
}">
    <!-- Top Stepper Header -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 mb-8">
        <h1 class="text-2xl font-extrabold text-brand-white">Project Activation Workspace</h1>
        <p class="text-xs text-brand-gray mt-1 leading-relaxed">Complete the steps below to sign off on your technical scoping and launch your active development pipeline.</p>
        
        <!-- Step Stepper -->
        <div class="mt-8 relative">
            <!-- Connector Line -->
            <div class="absolute top-5 left-8 right-8 h-0.5 bg-[#25282D] -z-10">
                <div class="h-full bg-gradient-to-r from-brand-teal via-brand-cyan to-brand-teal transition-all duration-500" style="width: 50%;"></div>
            </div>
            
            <div class="grid grid-cols-4 text-center">
                <!-- Step 1: Completed -->
                <div class="flex flex-col items-center">
                    <div class="h-10 w-10 rounded-full bg-brand-teal/20 border-2 border-brand-teal flex items-center justify-center text-brand-cyan shadow-lg shadow-brand-teal/10">
                        <span class="text-xs">✔</span>
                    </div>
                    <span class="text-[11px] font-bold text-brand-cyan mt-2">1. Scope Scoping</span>
                    <span class="text-[9px] text-brand-gray">Completed</span>
                </div>
                
                <!-- Step 2: Active Checkout -->
                <div class="flex flex-col items-center">
                    <div class="h-10 w-10 rounded-full bg-brand-cyan/20 border-2 border-brand-cyan flex items-center justify-center text-brand-cyan shadow-lg shadow-brand-cyan/20 animate-pulse">
                        <span class="text-xs">💳</span>
                    </div>
                    <span class="text-[11px] font-bold text-brand-cyan mt-2">2. Kickoff Deposit</span>
                    <span class="text-[9px] text-brand-white bg-brand-cyan/10 px-2 py-0.5 rounded font-mono uppercase tracking-wider mt-0.5">Active</span>
                </div>
                
                <!-- Step 3: Contract Signature -->
                <div class="flex flex-col items-center">
                    <div class="h-10 w-10 rounded-full bg-[#1A1D21] border-2 border-[#25282D] flex items-center justify-center text-brand-gray">
                        <span class="text-xs">✍</span>
                    </div>
                    <span class="text-[11px] font-semibold text-brand-gray mt-2">3. Sign Agreement</span>
                    <span class="text-[9px] text-brand-gray">Pending</span>
                </div>
                
                <!-- Step 4: Active Development -->
                <div class="flex flex-col items-center">
                    <div class="h-10 w-10 rounded-full bg-[#1A1D21] border-2 border-[#25282D] flex items-center justify-center text-brand-gray">
                        <span class="text-xs">🚀</span>
                    </div>
                    <span class="text-[11px] font-semibold text-brand-gray mt-2">4. Project Active</span>
                    <span class="text-[9px] text-brand-gray">Pending</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Validation Messages -->
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-5 py-3.5 text-sm font-medium text-rose-400">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-5 py-3.5 text-sm text-rose-400 space-y-1">
            @foreach ($errors->all() as $error)
                <div>⚠️ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT: Project Details Summary -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-sm font-bold text-brand-white uppercase tracking-wider mb-4">Service Scoping Details</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-[9px] uppercase font-bold text-brand-gray">Working Title</span>
                        <div class="text-sm font-bold text-brand-white">{{ $serviceRequest->title }}</div>
                    </div>
                    
                    <div>
                        <span class="text-[9px] uppercase font-bold text-brand-gray">Category</span>
                        <div class="text-xs font-semibold text-brand-cyan uppercase">{{ $serviceRequest->service_type }}</div>
                    </div>
                    
                    <div>
                        <span class="text-[9px] uppercase font-bold text-brand-gray">Scoping Description</span>
                        <p class="text-xs text-brand-gray leading-relaxed italic mt-1 bg-brand-dark-secondary/35 p-3 rounded-lg border border-brand-teal/5">
                            "{{ $serviceRequest->description }}"
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-brand-gray">Target Date</span>
                            <div class="text-xs font-bold text-brand-white">{{ $serviceRequest->deadline->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <span class="text-[9px] uppercase font-bold text-brand-gray">Budget Range</span>
                            <div class="text-xs font-bold text-brand-white">{{ $serviceRequest->budget_range }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Box -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 bg-gradient-to-br from-brand-teal/5 to-brand-cyan/5">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Kickoff Deposit</span>
                        <h2 class="text-2xl font-extrabold text-brand-white mt-1">{{ \App\Helpers\PaymentHelper::format($amount) }}</h2>
                    </div>
                    <span class="text-[10px] text-brand-cyan bg-brand-teal/10 px-2.5 py-1 rounded-full border border-brand-teal/20 font-bold uppercase">100% Secure</span>
                </div>
                <p class="text-[10px] text-brand-gray leading-relaxed mt-4">
                    Upon deposit processing, we will dispatch the custom **Service Agreement & Sprints Outline** to your dashboard immediately.
                </p>
            </div>
        </div>

        <!-- RIGHT: Payment Options & Forms -->
        <div class="lg:col-span-2">
            <form action="{{ route('portal.checkout.pay', $serviceRequest->id) }}" method="POST" enctype="multipart/form-data" x-ref="paymentForm" class="space-y-6">
                @csrf
                
                <!-- Gateway Selector Grid -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h3 class="text-sm font-bold text-brand-white uppercase tracking-wider mb-2">Select Payment Method</h3>
                    <p class="text-[11px] text-brand-gray mb-5">Please choose your preferred gateway option to complete the transaction.</p>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($gateways as $id => $gw)
                            @if($gw['enabled'])
                                <label :class="paymentMethod === '{{ $id }}' ? 'border-brand-cyan bg-brand-cyan/10 shadow-lg shadow-brand-cyan/10' : 'border-brand-teal/20 bg-[#25282D]/40 hover:border-brand-teal/50'"
                                       class="relative flex flex-col items-center justify-center gap-2.5 rounded-xl border p-3.5 cursor-pointer transition-all min-w-0 select-none">
                                    <input type="radio" name="payment_method" value="{{ $id }}" x-model="paymentMethod" class="sr-only" required>
                                    
                                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-dark-secondary/80 border border-brand-teal/10 p-1.5 flex-shrink-0">
                                        <img src="{{ $gw['icon'] }}" alt="" class="h-full w-full object-contain">
                                    </div>
                                    
                                    <span class="text-[11px] font-bold text-brand-white text-center truncate w-full leading-tight">{{ $gw['name'] }}</span>
                                    <span x-show="paymentMethod === '{{ $id }}'" class="absolute top-2 right-2 w-2 h-2 rounded-full bg-brand-cyan ring-4 ring-brand-cyan/20"></span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic Form Panel -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15" x-show="paymentMethod !== ''" x-transition>
                    
                    <!-- Automatic Credit Card Gateways (Stripe, Paystack, Flutterwave, PayPal, Razorpay) -->
                    <div x-show="['stripe', 'paystack', 'flutterwave', 'paypal', 'razorpay'].includes(paymentMethod)" class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">💳</span>
                            <h3 class="text-xs font-bold text-brand-cyan uppercase tracking-wider">Gateway Card Details (Mock Integration)</h3>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Cardholder Full Name</label>
                            <input type="text" x-model="cardName" placeholder="Sarah Jenkins" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Card Number</label>
                            <input type="text" x-model="cardNumber" placeholder="4111 2222 3333 4444" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Expiration Date</label>
                                <input type="text" x-model="cardExpiry" placeholder="MM/YY" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all font-mono">
                            </div>
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">CVV / Security Code</label>
                                <input type="password" x-model="cardCvv" placeholder="•••" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all font-mono">
                            </div>
                        </div>
                        
                        <p class="text-[10px] text-brand-gray italic leading-relaxed">
                            💡 **Mock Authorization Sandbox**: Any card details will work in this sandbox. Funds will be verified immediately and redirected to the contract sign-off phase.
                        </p>
                    </div>

                    <!-- Coinbase Commerce (Crypto Automatic Gateway) -->
                    <div x-show="paymentMethod === 'coinbase'" class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">🪙</span>
                            <h3 class="text-xs font-bold text-brand-cyan uppercase tracking-wider">Pay with Coinbase Commerce</h3>
                        </div>
                        <p class="text-xs text-brand-gray leading-relaxed">
                            Coinbase Commerce allows you to make automatic on-chain payments using major assets like BTC, ETH, USDC, or SOL. Click the authorize button below to open the secure payment widget.
                        </p>
                        <p class="text-[10px] text-brand-gray italic">
                            💡 **Mock Sandbox**: Clicking authorize will simulate Coinbase Payment Success immediately.
                        </p>
                    </div>

                    <!-- Bitcoin / On-chain Cryptocurrency (Manual) -->
                    <div x-show="paymentMethod === 'crypto'" class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">₿</span>
                            <h3 class="text-xs font-bold text-brand-cyan uppercase tracking-wider">Direct Cryptocurrency Payment</h3>
                        </div>
                        <p class="text-[11px] text-brand-gray leading-relaxed">
                            Please transfer the exact deposit value to one of the following official wallet addresses. Ensure you choose the correct network.
                        </p>
                        
                        <div class="space-y-3 p-4 bg-brand-dark-secondary/40 border border-brand-teal/15 rounded-xl">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-amber-500">Bitcoin (BTC) Address</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="text" readonly value="{{ \App\Helpers\SettingsHelper::get('payment_crypto_wallet_btc', 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh') }}" class="w-full bg-[#1A1D21] border border-white/5 rounded px-3 py-2 text-xs font-mono text-brand-white focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-brand-cyan">USDT (USDT-TRC20) Address</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="text" readonly value="{{ \App\Helpers\SettingsHelper::get('payment_crypto_wallet_usdt', 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t') }}" class="w-full bg-[#1A1D21] border border-white/5 rounded px-3 py-2 text-xs font-mono text-brand-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-brand-teal/10 pt-4 space-y-4">
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Transaction ID / TxHash <span class="text-red-400">*</span></label>
                                <input type="text" name="payment_txid" :required="paymentMethod === 'crypto'" placeholder="e.g. 5d5a23f...bf7f4" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all font-mono">
                            </div>
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Upload Payment Receipt Screenshot <span class="text-red-400">*</span></label>
                                <input type="file" name="payment_proof" :required="paymentMethod === 'crypto'" class="w-full text-xs text-brand-gray bg-brand-dark rounded-xl border border-brand-teal/15 p-2 focus:outline-none focus:border-brand-cyan">
                                <span class="text-[9px] text-brand-gray mt-1 block">Supported Formats: JPEG, PNG, JPG, PDF (Max 10MB)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Wire Transfer (Manual) -->
                    <div x-show="paymentMethod === 'bank_transfer'" class="space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">🏦</span>
                            <h3 class="text-xs font-bold text-brand-cyan uppercase tracking-wider">Bank Wire Details</h3>
                        </div>
                        <p class="text-[11px] text-brand-gray leading-relaxed">
                            Please execute a manual bank transfer of the deposit amount using the details below, then upload your transaction slip.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 p-4 bg-brand-dark-secondary/40 border border-brand-teal/15 rounded-xl text-xs">
                            <div>
                                <span class="text-[9px] uppercase font-bold text-brand-gray block">Bank Name</span>
                                <strong class="text-brand-white">{{ \App\Helpers\SettingsHelper::get('payment_bank_name', 'Zenith Bank PLC') }}</strong>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-brand-gray block">Account Name</span>
                                <strong class="text-brand-white">{{ \App\Helpers\SettingsHelper::get('payment_bank_account_name', 'Diwebs Tech Agency Ltd') }}</strong>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-brand-gray block">Account Number</span>
                                <strong class="text-brand-white font-mono">{{ \App\Helpers\SettingsHelper::get('payment_bank_account_number', '1017384950') }}</strong>
                            </div>
                            <div>
                                <span class="text-[9px] uppercase font-bold text-brand-gray block">SWIFT Code</span>
                                <strong class="text-brand-white font-mono">{{ \App\Helpers\SettingsHelper::get('payment_bank_swift_code', 'ZENINILAGXX') }}</strong>
                            </div>
                        </div>

                        <div class="border-t border-brand-teal/10 pt-4 space-y-4">
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Transaction Reference ID <span class="text-red-400">*</span></label>
                                <input type="text" name="payment_txid" :required="paymentMethod === 'bank_transfer'" placeholder="e.g. TRN-19830218-09" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all font-mono">
                            </div>
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Upload Transfer Slip / Receipt <span class="text-red-400">*</span></label>
                                <input type="file" name="payment_proof" :required="paymentMethod === 'bank_transfer'" class="w-full text-xs text-brand-gray bg-brand-dark rounded-xl border border-brand-teal/15 p-2 focus:outline-none focus:border-brand-cyan">
                                <span class="text-[9px] text-brand-gray mt-1 block">Supported Formats: JPEG, PNG, JPG, PDF (Max 10MB)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6 border-t border-brand-teal/10 pt-5 flex items-center justify-between">
                        <span class="text-[9px] text-brand-gray">Next Phase: Review &amp; Sign Service Contract</span>
                        <button type="button" @click="submitPayment()"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-7 py-3 text-xs font-bold text-brand-dark-secondary shadow-lg shadow-brand-teal/25 hover:opacity-90 transition-all">
                            💳 Authorize Deposit of {{ \App\Helpers\PaymentHelper::format($amount) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Processing Overlay Loader -->
    <div class="fixed inset-0 bg-brand-dark/95 z-50 flex flex-col items-center justify-center space-y-4" x-show="isProcessing" x-transition>
        <div class="h-12 w-12 border-4 border-brand-cyan border-t-transparent rounded-full animate-spin"></div>
        <h3 class="text-sm font-bold text-brand-white">Processing Transaction...</h3>
        <p class="text-xs text-brand-gray font-mono" x-text="processingStep"></p>
    </div>
</div>
@endsection
