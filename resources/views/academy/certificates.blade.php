@extends('layouts.academy')

@section('title', 'Academy Certificates - Diwebs Academy')

@section('academy_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Earned Certificates</h1>
        <p class="text-sm text-brand-gray mt-1">Download and share your verified training certificates with employers and LinkedIn.</p>
    </div>

    <!-- Certificate drawer -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">My Academic Credentials</h3>
        
        <div class="space-y-4">
            @forelse($enrollments->where('progress', 100) as $enrollment)
                <div class="rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/20 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-brand-teal/20 transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <h4 class="text-sm font-bold text-brand-white leading-snug">{{ $enrollment->course->title }}</h4>
                            <p class="text-[10px] text-brand-gray mt-1">Certified on: {{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : now()->format('M d, Y') }}</p>
                            <span class="font-mono text-[9px] text-brand-cyan mt-1 block">Credential code: {{ $enrollment->certificate_code }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <a href="#" @click.prevent="alert('Downloading certificate PDF stream...')" class="rounded-lg bg-brand-dark-secondary border border-brand-teal/20 hover:border-brand-teal px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                            📥 Download PDF
                        </a>
                        <a href="https://linkedin.com" target="_blank" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2 text-xs font-black text-brand-dark-secondary shadow hover:opacity-90 transition-all font-sans">
                            🔗 Add to LinkedIn
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-brand-teal/20 p-8 text-center text-brand-gray text-xs">
                    You have not completed any bootcamps yet. Complete all lessons in a course to earn your verified training certificate!
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
