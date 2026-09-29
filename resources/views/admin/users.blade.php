@extends('layouts.admin')

@section('title', 'User Management - Admin Control Center')

@section('admin_content')
<div x-data="{ showResetModal: false, showRegionModal: false, userId: null, userName: '', userCountry: 'Nigeria' }">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">User Management &amp; Regional Billing</h1>
            <p class="text-sm text-brand-gray mt-1">Manage all ecosystem users, roles, account statuses, and regional pricing tiers.</p>
        </div>
        <span class="text-xs text-brand-gray">{{ $users->total() }} total users</span>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-brand-teal/15">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 bg-brand-dark-secondary/60 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Country &amp; Region</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @foreach($users as $user)
                        @php
                            $regionInfo = \App\Helpers\PaymentHelper::getRegionInfo($user->country);
                        @endphp
                        <tr class="hover:bg-brand-dark-secondary/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-brand-teal to-brand-cyan flex items-center justify-center text-brand-dark-secondary text-xs font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-brand-white">{{ $user->name }}</p>
                                        <p class="text-brand-gray/70">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="rounded px-2 py-1 text-[10px] font-bold uppercase
                                    @if($user->role === 'super_admin') bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20
                                    @elseif($user->role === 'client') bg-purple-900/20 text-purple-400 border border-purple-500/20
                                    @elseif($user->role === 'student') bg-blue-900/20 text-blue-400 border border-blue-500/20
                                    @else bg-brand-teal/10 text-brand-teal border border-brand-teal/20
                                    @endif">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[10px] font-bold text-brand-cyan">
                                        {{ $regionInfo['symbol'] }} {{ $regionInfo['currency'] }}
                                    </span>
                                    <span class="text-brand-white text-xs font-semibold">{{ $user->country ?: 'Nigeria (Default)' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-xs font-semibold">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" 
                                        @click="userId = {{ $user->id }}; userName = '{{ addslashes($user->name) }}'; userCountry = '{{ addslashes($user->country ?: 'Nigeria') }}'; showRegionModal = true;"
                                        class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer bg-brand-cyan/10 text-brand-cyan border border-brand-cyan/20 hover:bg-brand-cyan/20 mr-1.5">
                                    🌍 Region
                                </button>
                                <button type="button" 
                                        @click="userId = {{ $user->id }}; userName = '{{ addslashes($user->name) }}'; showResetModal = true;"
                                        class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer bg-brand-teal/10 text-brand-cyan border border-brand-teal/20 hover:bg-brand-teal/20 mr-1.5">
                                    Password
                                </button>
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer
                                            {{ $user->status === 'active' 
                                                ? 'bg-rose-950 text-rose-400 border border-rose-500/20 hover:bg-rose-900/30' 
                                                : 'bg-emerald-950 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-900/30' }}">
                                        {{ $user->status === 'active' ? 'Suspend' : 'Reactivate' }}
                                    </button>
                                </form>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline ml-1.5" onsubmit="return confirm('Are you sure you want to delete user &quot;{{ $user->name }}&quot;? This will permanently delete all associated data and cannot be undone.')">
                                        @csrf
                                        <button type="submit" 
                                                class="rounded px-3 py-1.5 text-[10px] font-bold uppercase transition-all cursor-pointer bg-red-950 text-red-400 border border-red-500/20 hover:bg-red-900/35">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-brand-teal/10">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Change Region Modal -->
    <div x-show="showRegionModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark/85 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="glass-card rounded-3xl p-6 border border-brand-teal/20 w-full max-w-md bg-gradient-to-b from-brand-dark-secondary to-[#1A1D21] shadow-2xl relative" @click.away="showRegionModal = false">
            <button @click="showRegionModal = false" class="absolute top-4 right-4 text-brand-gray hover:text-brand-white text-lg transition-colors">✕</button>
            
            <h3 class="text-sm font-bold text-brand-cyan uppercase tracking-wider mb-2">🌍 Modify Client Region &amp; Billing Tier</h3>
            <p class="text-xs text-brand-gray mb-6">Updating region for: <strong class="text-brand-white" x-text="userName"></strong></p>
            
            <form :action="'/admin/users/' + userId + '/update-region'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Select Country &amp; Billing Region</label>
                    <select name="country" x-model="userCountry" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                        <option value="South Africa">South Africa 🇿🇦 (ZAR R)</option>
                        <option value="Nigeria">Nigeria 🇳🇬 (NGN ₦)</option>
                        <option value="Kenya">Kenya 🇰🇪 (KES KSh)</option>
                        <option value="Ghana">Ghana 🇬🇭 (GHS GH₵)</option>
                        <option value="Egypt">Egypt 🇪🇬 (EGP E£)</option>
                        <option value="Rwanda">Rwanda 🇷🇼 (KES KSh)</option>
                        <option value="Tanzania">Tanzania 🇹🇿 (KES KSh)</option>
                        <option value="Uganda">Uganda 🇺🇬 (KES KSh)</option>
                        <option value="Ethiopia">Ethiopia 🇪🇹 (KES KSh)</option>
                        <option value="Morocco">Morocco 🇲🇦 (EGP E£)</option>
                        <option value="United States">United States 🇺🇸 (Global - USD $)</option>
                        <option value="United Kingdom">United Kingdom 🇬🇧 (UK - GBP £)</option>
                        <option value="Canada">Canada 🇨🇦 (CAD C$)</option>
                        <option value="Germany">Germany 🇩🇪 (Europe - EUR €)</option>
                        <option value="France">France 🇫🇷 (Europe - EUR €)</option>
                        <option value="India">India 🇮🇳 (INR ₹)</option>
                        <option value="United Arab Emirates">UAE 🇦🇪 (USD $)</option>
                        <option value="Saudi Arabia">Saudi Arabia 🇸🇦 (USD $)</option>
                        <option value="Australia">Australia 🇦🇺 (AUD A$)</option>
                        <option value="China">China 🇨🇳 (USD $)</option>
                        <option value="Japan">Japan 🇯🇵 (USD $)</option>
                        <option value="Brazil">Brazil 🇧🇷 (USD $)</option>
                    </select>
                </div>
                <p class="text-[10px] text-brand-gray leading-relaxed">Updating the user's region will automatically reconfigure all client invoices, pricing plans, and checkout currencies across their portal dashboard.</p>
                
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showRegionModal = false" class="rounded-xl border border-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-gray hover:text-brand-white hover:bg-brand-teal/5 transition-all">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all shadow">
                        🌍 Save Region
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div x-show="showResetModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark/85 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="glass-card rounded-3xl p-6 border border-brand-teal/20 w-full max-w-md bg-gradient-to-b from-brand-dark-secondary to-[#1A1D21] shadow-2xl relative" @click.away="showResetModal = false">
            <button @click="showResetModal = false" class="absolute top-4 right-4 text-brand-gray hover:text-brand-white text-lg transition-colors">✕</button>
            
            <h3 class="text-sm font-bold text-brand-cyan uppercase tracking-wider mb-2">Change User Password</h3>
            <p class="text-xs text-brand-gray mb-6">Updating security credentials for user: <strong class="text-brand-white" x-text="userName"></strong></p>
            
            <form :action="'/admin/users/' + userId + '/change-password'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">New Password</label>
                    <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Re-type new password" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-2.5 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                </div>
                
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="showResetModal = false" class="rounded-xl border border-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-gray hover:text-brand-white hover:bg-brand-teal/5 transition-all">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan px-5 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 active:scale-95 transition-all shadow">
                        🔑 Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
