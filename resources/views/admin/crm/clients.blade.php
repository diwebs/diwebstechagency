@extends('layouts.admin')

@section('title', 'Client Directory - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddClient: false, editingId: null }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Clients Directory</h1>
            <p class="text-xs text-brand-gray mt-1">Manage core customer profiles, billing relations, and active accounts.</p>
        </div>
        <button @click="showAddClient = !showAddClient" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Onboard New Client
        </button>
    </div>

    <!-- Client onboarding form -->
    <div x-show="showAddClient" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Client Profile Intake</h3>
        <form action="{{ route('admin.crm.clients.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Contact Person</label>
                <input type="text" name="contact_person" required placeholder="Alice Vance" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Email (Workspace ID)</label>
                <input type="email" name="email" required placeholder="alice@vanguard.com" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Phone</label>
                <input type="text" name="phone" placeholder="+234 (803) 000-0000" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Company Name</label>
                <input type="text" name="company_name" placeholder="Vanguard Inc" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">B2B Corp Association</label>
                <select name="crm_company_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">No Corporate Account Link</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->organization_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Industry</label>
                <input type="text" name="industry" placeholder="Logistics &amp; Transport" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Country &amp; Billing Region</label>
                <select name="country" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Nigeria">Nigeria (Africa - NGN ₦)</option>
                    <option value="United States">United States (Global - USD $)</option>
                    <option value="United Kingdom">United Kingdom (UK - GBP £)</option>
                    <option value="Germany">Germany (Europe - EUR €)</option>
                    <option value="France">France (Europe - EUR €)</option>
                    <option value="Canada">Canada (USD $)</option>
                    <option value="Ghana">Ghana (Africa - NGN ₦)</option>
                    <option value="Kenya">Kenya (Africa - NGN ₦)</option>
                    <option value="South Africa">South Africa (Africa - NGN ₦)</option>
                    <option value="Egypt">Egypt (Africa - NGN ₦)</option>
                    <option value="Rwanda">Rwanda (Africa - NGN ₦)</option>
                    <option value="United Arab Emirates">UAE (USD $)</option>
                    <option value="Saudi Arabia">Saudi Arabia (USD $)</option>
                    <option value="India">India (USD $)</option>
                    <option value="China">China (USD $)</option>
                    <option value="Japan">Japan (USD $)</option>
                    <option value="Australia">Australia (USD $)</option>
                    <option value="Brazil">Brazil (USD $)</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Account Status</label>
                <select name="account_status" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Suspended">Suspended</option>
                    <option value="VIP">VIP Account</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Assigned Account Manager</label>
                <select name="assigned_manager_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Leave Unassigned</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Active Services (comma-separated)</label>
                <input type="text" name="active_services" placeholder="Mobile App, System Integration, Cloud Deployment" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Billing Address</label>
                <textarea name="address" rows="2" placeholder="Corporate HQ physical address..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddClient = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Save Client Profile</button>
            </div>
        </form>
    </div>

    <!-- Client Directory Directory Table -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Customer Directory</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Client Info</th>
                        <th class="pb-3">Company &amp; Industry</th>
                        <th class="pb-3">Active Services</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Account Manager</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($clients as $client)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors">
                            <td class="py-4">
                                <strong class="block text-brand-white text-sm">{{ $client->contact_person }}</strong>
                                <span class="text-brand-gray/60 block">{{ $client->email }}</span>
                                <span class="text-brand-gray/60 block text-[10px]">{{ $client->phone ?? 'No Phone' }}</span>
                            </td>
                            <td class="py-4 text-brand-white">
                                <span class="block font-bold">{{ $client->company_name ?? 'Individual Client' }}</span>
                                <span class="text-brand-gray text-[10px]">{{ $client->industry ?? 'N/A' }} | {{ $client->country ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 text-brand-white max-w-xs">
                                <div class="flex flex-wrap gap-1">
                                    @if(is_array($client->active_services))
                                        @foreach($client->active_services as $svc)
                                            <span class="rounded bg-brand-teal/15 border border-brand-teal/25 text-brand-cyan px-1.5 py-0.5 text-[10px]">{{ $svc }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-brand-gray text-[10px]">None registered</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="rounded px-2 py-0.5 text-[9px] font-bold uppercase
                                    @if($client->account_status === 'VIP') bg-emerald-950 text-emerald-400 border border-emerald-500/20
                                    @elseif($client->account_status === 'Active') bg-brand-teal/20 text-brand-cyan
                                    @elseif($client->account_status === 'Suspended') bg-rose-950 text-rose-400
                                    @else bg-brand-dark text-brand-gray
                                    @endif">
                                    {{ $client->account_status }}
                                </span>
                            </td>
                            <td class="py-4 text-brand-gray">
                                {{ $client->assignedManager ? $client->assignedManager->name : 'Unassigned' }}
                            </td>
                            <td class="py-4 text-right">
                                <button @click="editingId = (editingId === {{ $client->id }} ? null : {{ $client->id }})" class="rounded bg-brand-cyan/20 border border-brand-cyan/30 hover:bg-brand-cyan/30 text-brand-cyan px-2.5 py-1 text-[11px] font-bold cursor-pointer">
                                    Edit Profile
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Inline Panel -->
                        <tr x-show="editingId === {{ $client->id }}" x-cloak class="bg-brand-dark-secondary/20">
                            <td colspan="6" class="p-6">
                                <form action="{{ route('admin.crm.clients.update', $client->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                    @csrf
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Contact Person</label>
                                        <input type="text" name="contact_person" value="{{ $client->contact_person }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Email</label>
                                        <input type="email" name="email" value="{{ $client->email }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Phone</label>
                                        <input type="text" name="phone" value="{{ $client->phone }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Corporate Link</label>
                                        <select name="crm_company_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                            <option value="">No Corporate Link</option>
                                            @foreach($companies as $c)
                                                <option value="{{ $c->id }}" {{ $c->id == $client->crm_company_id ? 'selected' : '' }}>{{ $c->organization_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Country &amp; Billing Region</label>
                                        <select name="country" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                            @php $c = $client->country ?? 'Nigeria'; @endphp
                                            <option value="Nigeria" {{ $c == 'Nigeria' ? 'selected' : '' }}>Nigeria (Africa - NGN ₦)</option>
                                            <option value="United States" {{ $c == 'United States' ? 'selected' : '' }}>United States (Global - USD $)</option>
                                            <option value="United Kingdom" {{ $c == 'United Kingdom' ? 'selected' : '' }}>United Kingdom (UK - GBP £)</option>
                                            <option value="Germany" {{ $c == 'Germany' ? 'selected' : '' }}>Germany (Europe - EUR €)</option>
                                            <option value="France" {{ $c == 'France' ? 'selected' : '' }}>France (Europe - EUR €)</option>
                                            <option value="Canada" {{ $c == 'Canada' ? 'selected' : '' }}>Canada (USD $)</option>
                                            <option value="Ghana" {{ $c == 'Ghana' ? 'selected' : '' }}>Ghana (Africa - NGN ₦)</option>
                                            <option value="Kenya" {{ $c == 'Kenya' ? 'selected' : '' }}>Kenya (Africa - NGN ₦)</option>
                                            <option value="South Africa" {{ $c == 'South Africa' ? 'selected' : '' }}>South Africa (Africa - NGN ₦)</option>
                                            <option value="Egypt" {{ $c == 'Egypt' ? 'selected' : '' }}>Egypt (Africa - NGN ₦)</option>
                                            <option value="Rwanda" {{ $c == 'Rwanda' ? 'selected' : '' }}>Rwanda (Africa - NGN ₦)</option>
                                            <option value="United Arab Emirates" {{ $c == 'United Arab Emirates' ? 'selected' : '' }}>UAE (USD $)</option>
                                            <option value="Saudi Arabia" {{ $c == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia (USD $)</option>
                                            <option value="India" {{ $c == 'India' ? 'selected' : '' }}>India (USD $)</option>
                                            <option value="China" {{ $c == 'China' ? 'selected' : '' }}>China (USD $)</option>
                                            <option value="Japan" {{ $c == 'Japan' ? 'selected' : '' }}>Japan (USD $)</option>
                                            <option value="Australia" {{ $c == 'Australia' ? 'selected' : '' }}>Australia (USD $)</option>
                                            <option value="Brazil" {{ $c == 'Brazil' ? 'selected' : '' }}>Brazil (USD $)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Industry</label>
                                        <input type="text" name="industry" value="{{ $client->industry }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Account Status</label>
                                        <select name="account_status" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                            <option value="Active" {{ $client->account_status == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ $client->account_status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="Suspended" {{ $client->account_status == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                            <option value="VIP" {{ $client->account_status == 'VIP' ? 'selected' : '' }}>VIP</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Account Manager</label>
                                        <select name="assigned_manager_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                            <option value="">Leave Unassigned</option>
                                            @foreach($staffMembers as $staff)
                                                <option value="{{ $staff->id }}" {{ $staff->id == $client->assigned_manager_id ? 'selected' : '' }}>{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Active Services (comma-separated)</label>
                                        <input type="text" name="active_services" value="{{ is_array($client->active_services) ? implode(', ', $client->active_services) : '' }}" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">
                                    </div>
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-brand-gray mb-1 uppercase tracking-wider text-[9px] font-bold">Billing Address</label>
                                        <textarea name="address" rows="2" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-1.5 text-brand-white">{{ $client->address }}</textarea>
                                    </div>
                                    <div class="col-span-1 md:col-span-3 flex justify-end gap-2 mt-2">
                                        <button type="button" @click="editingId = null" class="rounded-lg bg-brand-dark border border-brand-teal/10 px-4 py-1.5 text-brand-gray">Cancel</button>
                                        <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-1.5 font-bold cursor-pointer">Save Profile Changes</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-brand-gray">No client records found. Onboard one using the form.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection
