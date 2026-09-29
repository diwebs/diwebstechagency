@extends('layouts.admin')

@section('title', 'Roles & Permissions (RBAC) - Admin Dashboard')

@section('admin_content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-brand-white">Role-Based Access Control (RBAC)</h1>
        <p class="text-xs text-brand-gray mt-1 font-sans">Set operational permissions for positions under each department. Changes sync globally immediately.</p>
    </div>

    <!-- Info Box -->
    <div class="rounded-xl bg-brand-teal/5 border border-brand-teal/15 p-4 text-xs text-brand-gray leading-relaxed">
        💡 **Permission Key Definitions:**
        <ul class="list-disc pl-5 mt-2 space-y-1">
            <li><strong>View Dashboard:</strong> Access to view the personal staff home page and announcements.</li>
            <li><strong>Manage Staff:</strong> Admin HR capability to add/edit staff profiles and approve leaves.</li>
            <li><strong>Manage Finance:</strong> Run monthly payrolls, update salaries, and update slip items.</li>
            <li><strong>Manage Projects:</strong> Assign staff members to corporate projects pipelines.</li>
            <li><strong>Manage Academy:</strong> Direct control of LMS courses, bootcamps and classes.</li>
            <li><strong>Manage CBT:</strong> Edit exam questions and proctor candidate seat coordinates.</li>
            <li><strong>Manage Clients:</strong> Read and record leads and support CRM systems.</li>
            <li><strong>View Reports:</strong> View global financial outlays, growth stats and audit tables.</li>
            <li><strong>Super Admin Access:</strong> Master override privilege. Accesses all features unconditionally.</li>
        </ul>
    </div>

    <!-- Form matrix -->
    <form action="{{ route('admin.staff.rbac.update') }}" method="POST" class="space-y-8">
        @csrf

        @foreach($departments as $dept)
            @if($dept->roles->count() > 0)
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3 flex items-center justify-between">
                        <span>🏢 {{ $dept->name }}</span>
                        <span class="text-[10px] text-brand-gray font-mono">Department Roles Mapping</span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="text-brand-gray border-b border-brand-teal/5">
                                    <th class="py-2.5 font-bold uppercase text-[9px] w-1/4">Operational Role</th>
                                    @foreach([
                                        'view_dashboard' => 'View Dash',
                                        'manage_staff' => 'HR Staff',
                                        'manage_finance' => 'Finance',
                                        'manage_projects' => 'Projects',
                                        'manage_academy' => 'Academy',
                                        'manage_cbt' => 'CBT',
                                        'manage_clients' => 'Clients',
                                        'view_reports' => 'Reports',
                                        'super_admin_access' => '★ Super'
                                    ] as $key => $label)
                                        <th class="py-2.5 px-2 font-bold uppercase text-[9px] text-center font-mono" title="{{ $key }}">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                                @foreach($dept->roles as $role)
                                    @php
                                        $perms = is_array($role->permissions) 
                                            ? $role->permissions 
                                            : json_decode($role->permissions ?? '[]', true);
                                    @endphp
                                    <tr class="hover:bg-brand-teal/5 transition-all">
                                        <td class="py-3.5 font-bold">
                                            {{ $role->title }}
                                        </td>
                                        @foreach([
                                            'view_dashboard',
                                            'manage_staff',
                                            'manage_finance',
                                            'manage_projects',
                                            'manage_academy',
                                            'manage_cbt',
                                            'manage_clients',
                                            'view_reports',
                                            'super_admin_access'
                                        ] as $pKey)
                                            <td class="py-3.5 px-2 text-center">
                                                <input type="checkbox" 
                                                       name="permissions[{{ $role->id }}][]" 
                                                       value="{{ $pKey }}"
                                                       {{ in_array($pKey, $perms) ? 'checked' : '' }}
                                                       class="rounded bg-brand-dark-secondary border-brand-teal/20 text-brand-cyan focus:ring-brand-cyan/40 h-4 w-4 cursor-pointer">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-8 py-3 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer select-none">
                💾 Synchronize Permissions Matrix
            </button>
        </div>
    </form>
</div>
@endsection
