@extends('layouts.admin')

@section('title', 'User Management - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">User Management</h1>
            <p class="text-sm text-brand-gray mt-1">Manage all ecosystem users, roles, and account statuses.</p>
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
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @foreach($users as $user)
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
                                <span class="flex items-center gap-1.5 text-xs font-semibold">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-brand-gray">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
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
</div>
@endsection
