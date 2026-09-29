@extends('layouts.admin')

@section('title', 'Add Staff Member - Admin Dashboard')

@section('admin_content')
<div class="space-y-8" x-data="staffForm()">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.staff.index') }}" class="text-xs text-brand-cyan hover:underline">← Back to staff list</a>
        <h1 class="text-2xl font-bold text-brand-white mt-2">Register New Staff Member</h1>
        <p class="text-sm text-brand-gray mt-1 font-sans">Enter personal details, assign corporate departments/positions, and configure system security access levels.</p>
    </div>

    <!-- Error/Validation feedback -->
    @if($errors->any())
        <div class="rounded-lg bg-rose-950/40 border border-rose-500/25 p-4 text-xs text-rose-400">
            <h4 class="font-bold mb-1">Please fix the following validation errors:</h4>
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.staff.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Columns: Personal & Employment Details -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Section 1: Personal Information -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">1. Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob') }}"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Gender</label>
                            <select name="gender" 
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Nationality -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Nigerian"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+234..."
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Profile Avatar -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Profile Picture</label>
                            <input type="file" name="profile_picture" accept="image/*"
                                   class="w-full text-xs text-brand-gray file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-teal/15 file:text-brand-cyan file:cursor-pointer hover:file:bg-brand-teal/25 transition-all">
                        </div>
                    </div>

                    <!-- Residential Address -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Residential Address</label>
                        <textarea name="address" rows="3" placeholder="Enter physical street address..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-4 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none leading-relaxed transition-all">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Section 2: Employment Details -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">2. Employment Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Department -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Department</label>
                            <select name="department_id" 
                                    x-model="selectedDept"
                                    @change="updateRoles()"
                                    required
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Title / Position -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Job Title / Position</label>
                            <select name="role_id" 
                                    x-model="selectedRole"
                                    required
                                    :disabled="roles.length === 0"
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all disabled:opacity-40">
                                <option value="">Select Position</option>
                                <template x-for="r in roles" :key="r.id">
                                    <option :value="r.id" x-text="r.title" :selected="selectedRole == r.id"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Employment Type -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Employment Type</label>
                            <select name="employment_type" required
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="Full-time" {{ old('employment_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="Contract" {{ old('employment_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                                <option value="Remote" {{ old('employment_type') === 'Remote' ? 'selected' : '' }}>Remote</option>
                                <option value="Internship" {{ old('employment_type') === 'Internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                        </div>

                        <!-- Date Hired -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Date Hired</label>
                            <input type="date" name="date_hired" value="{{ old('date_hired', date('Y-m-d')) }}"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Salary Grade -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Salary Grade</label>
                            <input type="text" name="salary_grade" value="{{ old('salary_grade') }}" placeholder="e.g. SG-12"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Base Salary -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Base Salary (Monthly ₦)</label>
                            <input type="number" name="base_salary" value="{{ old('base_salary', 0) }}" step="0.01" required
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>

                        <!-- Reporting Manager -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Reporting Manager</label>
                            <select name="reporting_manager_id" 
                                    class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                                <option value="">Select Manager (Optional)</option>
                                @foreach($managers as $mgr)
                                    <option value="{{ $mgr->id }}" {{ old('reporting_manager_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }} ({{ $mgr->role->title ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Office Location -->
                        <div>
                            <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Office Location</label>
                            <input type="text" name="office_location" value="{{ old('office_location') }}" placeholder="e.g. Lagos HQ"
                                   class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: System Access -->
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">3. System Access</h3>
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="e.g. jdoe"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Email Address (Login User) -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">System Login Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="staff@diwebstechagency.website"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Initial Password -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Initial Password</label>
                        <input type="text" name="password" value="{{ old('password', Str::random(10)) }}" required
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="text-[9px] text-brand-gray mt-1 block">Forces passcode renewal upon first authentication.</span>
                    </div>

                    <!-- Account Status -->
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Account Status</label>
                        <select name="status" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Suspended" {{ old('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <!-- Info Alert -->
                    <div class="rounded-lg bg-brand-teal/5 border border-brand-teal/20 p-3.5 text-[10px] text-brand-gray leading-relaxed">
                        ⚠️ **Permissions note:** Custom RBAC permissions are assigned dynamically. Set permissions globally under the **Roles &amp; Permissions** panel.
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('admin.staff.index') }}" class="flex-1 rounded-lg border border-brand-teal/20 px-4 py-2.5 text-center text-xs font-bold text-brand-white hover:bg-brand-teal/10 transition-all select-none">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-center text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer select-none">
                        Register Staff
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function staffForm() {
    return {
        selectedDept: '{{ old('department_id', '') }}',
        selectedRole: '{{ old('role_id', '') }}',
        roles: [],
        async updateRoles() {
            if (!this.selectedDept) {
                this.roles = [];
                return;
            }
            try {
                const response = await fetch(`/admin/staff/ajax-roles/${this.selectedDept}`);
                this.roles = await response.json();
            } catch(e) {
                console.error('Failed to load roles: ', e);
            }
        },
        init() {
            if (this.selectedDept) {
                this.updateRoles();
            }
        }
    }
}
</script>
@endsection
