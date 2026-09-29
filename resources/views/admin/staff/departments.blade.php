@extends('layouts.admin')

@section('title', 'Departments & Roles - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white font-sans">Departments &amp; Roles</h1>
            <p class="text-xs text-brand-gray mt-1">Configure corporate organization trees, assign department heads, and register operational roles.</p>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Create Department & Add Role Forms -->
        <div class="space-y-6">
            <!-- Form 1: Create Department -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">➕ Add Department</h3>
                
                <form action="{{ route('admin.staff.departments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Department Name</label>
                        <input type="text" name="name" required placeholder="e.g. Sales Division"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Department Code (Short)</label>
                        <input type="text" name="code" required placeholder="e.g. SLD"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Operational Description</label>
                        <textarea name="description" rows="2" placeholder="e.g. Handles marketing strategies..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-3 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Department Head</label>
                        <select name="head_id" 
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="">No Head Assigned</option>
                            @foreach($staff as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->staff_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer select-none">
                        Save Department
                    </button>
                </form>
            </div>

            <!-- Form 2: Add Role to Department -->
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">➕ Add Role / Position</h3>
                
                <form action="{{ route('admin.staff.roles.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Assign Department</label>
                        <select name="department_id" required
                                class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                            <option value="">Choose Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-white uppercase mb-2">Position Title</label>
                        <input type="text" name="title" required placeholder="e.g. Digital Planner"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 px-4 py-2.5 text-xs font-bold text-brand-cyan transition-all cursor-pointer select-none">
                        Add Operational Role
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Departments and Roles lists -->
        <div class="lg:col-span-2 space-y-6">
            @foreach($departments as $dept)
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                    <!-- Dept Header -->
                    <div class="flex items-start justify-between border-b border-brand-teal/10 pb-3 flex-wrap gap-2">
                        <div>
                            <span class="text-[9px] font-bold text-brand-cyan uppercase tracking-widest font-mono">Code: {{ $dept->code }}</span>
                            <h2 class="text-base font-extrabold text-brand-white mt-1">{{ $dept->name }}</h2>
                            <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">{{ $dept->description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Delete Department -->
                            <form action="{{ route('admin.staff.departments.delete', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department? All child roles will be lost!')">
                                @csrf
                                <button type="submit" class="text-[10px] border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded px-2.5 py-1">
                                    🗑️ Delete Dept
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Details Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-brand-gray text-[9px] uppercase tracking-wider block mb-1">Head of Department</span>
                            <span class="font-bold text-brand-white">
                                {{ $dept->head ? $dept->head->name : 'Unassigned' }}
                            </span>
                            @if($dept->head)
                                <span class="text-[9px] text-brand-cyan font-mono block">ID: {{ $dept->head->staff_id }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-brand-gray text-[9px] uppercase tracking-wider block mb-1">Total Positions</span>
                            <span class="font-mono font-bold text-brand-cyan">{{ $dept->roles->count() }} Predefined Roles</span>
                        </div>
                    </div>

                    <!-- Roles List -->
                    <div class="space-y-2 pt-2">
                        <span class="text-brand-gray text-[9px] uppercase tracking-wider block font-bold">Child Roles &amp; Permissions</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse($dept->roles as $role)
                                <div class="rounded-lg bg-brand-dark border border-brand-teal/10 px-3 py-1.5 flex items-center justify-between gap-3">
                                    <span class="text-[11px] font-bold text-brand-white">{{ $role->title }}</span>
                                    
                                    <form action="{{ route('admin.staff.roles.delete', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this role?')">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-brand-gray hover:text-rose-400 select-none cursor-pointer">
                                            ✖
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <span class="text-[10px] text-brand-gray italic">No roles configured under this department yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
