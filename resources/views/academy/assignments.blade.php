@extends('layouts.academy')

@section('title', 'Academy Assignments - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Course Assignments</h1>
        <p class="text-sm text-brand-gray mt-1">Submit files for evaluation and track status of bootcamp projects.</p>
    </div>

    <!-- Assignment Listing -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">My Projects &amp; Tasks</h3>
        
        <div class="space-y-4">
            <!-- Item 1 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-brand-teal/20 transition-all">
                <div>
                    <span class="rounded bg-brand-teal/10 border border-brand-teal/20 px-2 py-0.5 text-[9px] font-bold uppercase text-brand-cyan">Submitted</span>
                    <h4 class="text-sm font-bold text-brand-white mt-2">Agile Sprints Database Design</h4>
                    <p class="text-[10px] text-brand-gray">Course: Software Engineering Lifecycle · Due: Jun 20, 2026</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-brand-cyan bg-brand-teal/10 border border-brand-teal/25 rounded px-2.5 py-1">Graded: A+</span>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-brand-teal/20 transition-all">
                <div>
                    <span class="rounded bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[9px] font-bold uppercase text-amber-400">Pending Review</span>
                    <h4 class="text-sm font-bold text-brand-white mt-2">Vue.js Real-time Chat Controller</h4>
                    <p class="text-[10px] text-brand-gray">Course: Front-End Architecture · Due: Jun 25, 2026</p>
                </div>
                <div>
                    <span class="text-xs text-brand-gray">Submitted today 14:02</span>
                </div>
            </div>

            <!-- Item 3 -->
            <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-brand-teal/20 transition-all">
                <div>
                    <span class="rounded bg-rose-500/10 border border-rose-500/20 px-2 py-0.5 text-[9px] font-bold uppercase text-rose-400">Not Submitted</span>
                    <h4 class="text-sm font-bold text-brand-white mt-2">Deploying Laravel to AWS Elastic Beanstalk</h4>
                    <p class="text-[10px] text-brand-gray">Course: DevOps Cloud Architecture · Due: Jun 30, 2026</p>
                </div>
                <div>
                    <button @click="alert('Upload channel is active. Attach project zip files below.')" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-extrabold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer font-sans">
                        Upload Files
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
