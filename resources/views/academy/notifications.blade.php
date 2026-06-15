@extends('layouts.academy')

@section('title', 'Academy Notifications - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Academy Notifications</h1>
        <p class="text-sm text-brand-gray mt-1">Stay updated with classroom schedules, exam announcements, and system alerts.</p>
    </div>

    <!-- Alerts listing -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">My In-App Alerts</h3>
        
        <div class="space-y-4">
            <!-- Alert 1 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-4 flex gap-3.5 hover:border-brand-teal/20 transition-all">
                <span class="text-xl">📺</span>
                <div>
                    <h4 class="text-xs font-bold text-brand-white">New Live Session Scheduled</h4>
                    <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">
                        Instructor David Miller scheduled **Advanced AI Engineering Workshop** for tomorrow. Add to calendar to secure your seat.
                    </p>
                    <span class="text-[9px] text-brand-gray/50 block mt-2 font-mono">10 minutes ago</span>
                </div>
            </div>

            <!-- Alert 2 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-4 flex gap-3.5 hover:border-brand-teal/20 transition-all">
                <span class="text-xl">🎓</span>
                <div>
                    <h4 class="text-xs font-bold text-brand-white">Course Complete: Certified Earned</h4>
                    <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">
                        Congratulations! You finished the **Git Version Control Bootcamp** syllabus. Your certificate code is active.
                    </p>
                    <span class="text-[9px] text-brand-gray/50 block mt-2 font-mono">Yesterday 14:02</span>
                </div>
            </div>

            <!-- Alert 3 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-4 flex gap-3.5 hover:border-brand-teal/20 transition-all">
                <span class="text-xl">🔔</span>
                <div>
                    <h4 class="text-xs font-bold text-brand-white">Security Timeout Active</h4>
                    <p class="text-[10px] text-brand-gray mt-1 leading-relaxed">
                        Session timeout shields are configured. Unused tabs will be auto-revoked after 15 minutes of inactivity.
                    </p>
                    <span class="text-[9px] text-brand-gray/50 block mt-2 font-mono">2 days ago</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
