@extends('layouts.admin')

@section('title', 'Staff Directory - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">Staff Directory</h1>
            <p class="text-xs text-brand-gray mt-1">Review active employee lists, departments, security permissions level, and details profile.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.staff.create') }}" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all select-none">
                ➕ Add Staff Member
            </a>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
        <form action="{{ route('admin.staff.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search field -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Search Staff</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="ID, name, email..."
                       class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white placeholder-brand-gray/40 focus:border-brand-cyan/60 focus:outline-none transition-all">
            </div>

            <!-- Department filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Department</label>
                <select name="department_id" 
                        class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status filter -->
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-brand-gray mb-2">Employment Status</label>
                <select name="status" 
                        class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3 py-2 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="Resigned" {{ request('status') === 'Resigned' ? 'selected' : '' }}>Resigned</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-brand-teal/20 border border-brand-teal/30 hover:bg-brand-teal/30 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.staff.index') }}" class="rounded-lg border border-white/5 px-4 py-2 text-xs font-bold text-brand-gray hover:bg-white/5 transition-all text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Staff Tabular Grid -->
    <div class="glass-card rounded-2xl border border-brand-teal/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-brand-gray border-b border-brand-teal/10 bg-brand-dark-secondary/50">
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Staff Details</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Department</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Position &amp; Type</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px]">Status</th>
                        <th class="px-6 py-4 font-bold uppercase text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                    @forelse($staffMembers as $staff)
                        <tr class="hover:bg-brand-teal/5 transition-all">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar -->
                                    <div class="h-10 w-10 rounded-full border border-brand-teal/30 bg-brand-teal/10 flex items-center justify-center text-xs font-extrabold text-brand-cyan overflow-hidden flex-shrink-0">
                                        @if($staff->profile_picture)
                                            <img src="/storage/{{ $staff->profile_picture }}" alt="Profile" class="h-full w-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($staff->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.staff.profile', $staff->id) }}" class="font-bold text-brand-white hover:text-brand-cyan transition-colors block text-sm">{{ $staff->name }}</a>
                                        <span class="text-[10px] text-brand-cyan font-mono block">{{ $staff->staff_id }}</span>
                                        <span class="text-[10px] text-brand-gray block">{{ $staff->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold block">{{ $staff->department->name ?? 'Unassigned' }}</span>
                                <span class="text-[10px] text-brand-gray font-mono block">Code: {{ $staff->department->code ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold block">{{ $staff->role->title ?? 'Unassigned' }}</span>
                                <span class="text-[10px] text-brand-cyan block">{{ $staff->employment_type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[9px] font-extrabold border {{ $staff->status === 'Active' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' : 'bg-rose-500/10 border-rose-500/30 text-rose-400' }}">
                                    {{ $staff->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.staff.profile', $staff->id) }}" class="p-1 text-brand-gray hover:text-brand-cyan transition-colors" title="View Full Profile">
                                        👁️ View
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="p-1 text-brand-gray hover:text-brand-cyan transition-colors" title="Edit Profile Details">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('admin.staff.delete', $staff->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this staff record?')">
                                        @csrf
                                        <button type="submit" class="p-1 text-brand-gray hover:text-rose-400 transition-colors" title="Delete Profile">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-brand-gray">No staff members match the current filter criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="px-6 py-4 bg-brand-dark-secondary/20 border-t border-brand-teal/10">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
