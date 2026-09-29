@extends('layouts.admin')

@section('title', 'Tasks Checklist - Diwebs CRM')

@section('admin_content')
<div class="space-y-8" x-data="{ showAddTask: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-white">CRM Tasks &amp; Follow-Ups</h1>
            <p class="text-xs text-brand-gray mt-1">Operational activities, client calls, proposal checks, and deadlines.</p>
        </div>
        <button @click="showAddTask = !showAddTask" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-bold text-brand-dark-secondary hover:opacity-90 transition-all cursor-pointer">
            + Schedule CRM Task
        </button>
    </div>

    <!-- Task intake form -->
    <div x-show="showAddTask" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-cloak>
        <h3 class="text-sm font-semibold uppercase text-brand-cyan border-b border-brand-teal/10 pb-2">Schedule Activity</h3>
        <form action="{{ route('admin.crm.tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            @csrf
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Task Title</label>
                <input type="text" name="title" required placeholder="Follow up on proposed invoice" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Activity Type</label>
                <select name="task_type" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Call">Phone Call</option>
                    <option value="Email">Email Follow-Up</option>
                    <option value="Meeting">Strategy Meeting</option>
                    <option value="Proposal">Proposal Submission</option>
                    <option value="Demo">System Demo Session</option>
                    <option value="Payment reminder">Payment Reminder</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Assign Staff</label>
                <select name="assigned_staff_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">Leave Unassigned</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Due Date</label>
                <input type="date" name="due_date" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Priority</label>
                <select name="priority" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Lead Link</label>
                <select name="crm_lead_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($leads as $ld)
                        <option value="{{ $ld->id }}">{{ $ld->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Client Link</label>
                <select name="crm_client_id" class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none">
                    <option value="">None</option>
                    @foreach($clients as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->contact_person }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 md:col-span-3">
                <label class="block text-brand-gray mb-1.5 uppercase tracking-wider text-[9px] font-bold">Internal notes</label>
                <textarea name="notes" rows="2" placeholder="Detail the agenda for this action..." class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-3.5 py-2 text-brand-white focus:outline-none"></textarea>
            </div>
            <div class="col-span-1 md:col-span-3 flex justify-end gap-2">
                <button type="button" @click="showAddTask = false" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2 text-brand-gray">Cancel</button>
                <button type="submit" class="rounded-lg bg-brand-cyan text-brand-dark-secondary px-6 py-2 font-bold cursor-pointer">Register Task</button>
            </div>
        </form>
    </div>

    <!-- Tasks List Table -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/10">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan mb-4">Ecosystem Tasks Registry</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="border-b border-brand-teal/10 text-brand-gray uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Task Details</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3">Associated Target</th>
                        <th class="pb-3">Due Date</th>
                        <th class="pb-3">Priority</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Assigned Staff</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-teal/5">
                    @forelse($tasks as $task)
                        <tr class="hover:bg-brand-dark-secondary/35 transition-colors text-brand-white">
                            <td class="py-3.5">
                                <strong class="block text-sm">{{ $task->title }}</strong>
                                <span class="text-brand-gray/60 block max-w-xs truncate">{{ $task->notes ?? 'No details provided' }}</span>
                            </td>
                            <td class="py-3.5 font-bold text-brand-cyan">{{ $task->task_type }}</td>
                            <td class="py-3.5">
                                @if($task->client)
                                    <span class="block">Client: {{ $task->client->contact_person }}</span>
                                @elseif($task->lead)
                                    <span class="block">Lead: {{ $task->lead->full_name }}</span>
                                @else
                                    <span class="text-brand-gray/50">Internal Task</span>
                                @endif
                            </td>
                            <td class="py-3.5 font-mono">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No Date' }}</td>
                            <td class="py-3.5 font-bold">
                                <span class="rounded px-2 py-0.5 text-[9px] uppercase
                                    @if($task->priority === 'Urgent') bg-rose-950 text-rose-400
                                    @elseif($task->priority === 'High') bg-amber-950 text-amber-400
                                    @else bg-brand-teal/10 text-brand-cyan
                                    @endif">
                                    {{ $task->priority }}
                                </span>
                            </td>
                            <td class="py-3.5">
                                <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase
                                    @if($task->status === 'Completed') bg-emerald-950 text-emerald-400
                                    @elseif($task->status === 'Overdue') bg-rose-950 text-rose-400
                                    @else bg-brand-teal/10 text-brand-white
                                    @endif">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td class="py-3.5 text-brand-gray">{{ $task->assignedStaff ? $task->assignedStaff->name : 'Unassigned' }}</td>
                            <td class="py-3.5 text-right">
                                @if($task->status !== 'Completed')
                                    <form action="{{ route('admin.crm.tasks.status', $task->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" class="rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-2 py-1 text-[10px] font-bold cursor-pointer">
                                            Complete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-brand-gray">No activity tasks scheduled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
@endsection
