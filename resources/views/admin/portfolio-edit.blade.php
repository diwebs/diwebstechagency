@extends('layouts.admin')

@php
    $isEdit = isset($portfolio);
@endphp

@section('title', ($isEdit ? 'Edit Portfolio Project' : 'Add Portfolio Project') . ' - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.portfolios') }}" class="text-xs text-brand-cyan hover:underline">← Back to showcase list</a>
            <h1 class="text-2xl font-bold text-brand-white mt-2">
                {{ $isEdit ? 'Modify Showcase Project' : 'Register New Showcase Project' }}
            </h1>
            <p class="text-sm text-brand-gray mt-1">Configure external system screenshots, project titles, marketing copy descriptions, and deployment links.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-rose-950/40 border border-rose-500/25 p-4 text-xs text-rose-400">
            <h4 class="font-bold mb-1">Please fix the following validation errors:</h4>
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('admin.portfolios.update', $portfolio->id) : route('admin.portfolios.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Pane: Core Content settings -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Project Details</h3>
                    
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Project Title</label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title', $portfolio->title ?? '') }}" 
                               required 
                               placeholder="e.g. Government CBT Assessments Cloud or Fintech SaaS Portal"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Description / Write up -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Project Write-up &amp; Details</label>
                        <p class="text-[10px] text-brand-gray mb-2">Detailed description about what the project does, the technical requirements, the solved problems, etc. Supports formatting and newlines.</p>
                        <textarea name="description" 
                                  rows="10" 
                                  required 
                                  placeholder="Provide what the project is all about..."
                                  class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-4 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none leading-relaxed transition-all">{{ old('description', $portfolio->description ?? '') }}</textarea>
                    </div>
                </div>

            </div>

            <!-- Right Pane: Meta & Upload screenshots -->
            <div class="space-y-6">
                
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3">Media &amp; Metadata</h3>
                    
                    <!-- Live URL -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Live URL / Project Link</label>
                        <input type="url" 
                               name="project_url" 
                               value="{{ old('project_url', $portfolio->project_url ?? '') }}" 
                               placeholder="https://client-project.com"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    </div>

                    <!-- Display Order -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Display Order Sequence</label>
                        <input type="number" 
                               name="order" 
                               value="{{ old('order', $portfolio->order ?? 0) }}" 
                               placeholder="0"
                               class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                        <span class="text-[9px] text-brand-gray mt-1 block">Lower values display first on the showcase page.</span>
                    </div>

                    <!-- Mockup image file upload -->
                    <div>
                        <label class="block text-xs font-bold text-brand-white uppercase mb-2">Mockup Image (Website Screenshot)</label>
                        
                        @if($isEdit && $portfolio->mock_image)
                            <div class="mb-4 relative w-full aspect-video rounded-lg overflow-hidden border border-brand-teal/25 bg-brand-dark-secondary">
                                <img src="{{ asset('storage/' . $portfolio->mock_image) }}" 
                                     alt="Preview" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-[10px] font-bold text-brand-white">Currently Uploaded</span>
                                </div>
                            </div>
                        @endif

                        <input type="file" 
                               name="mock_image" 
                               accept="image/*"
                               class="w-full text-xs text-brand-gray file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-teal/15 file:text-brand-cyan file:cursor-pointer hover:file:bg-brand-teal/25 transition-all">
                        <span class="text-[9px] text-brand-gray mt-1.5 block">Format: PNG, JPG, JPEG, WebP. Recommended ratio: 16:9 (aspect-video). Max size: 5MB.</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('admin.portfolios') }}" class="flex-1 rounded-lg border border-brand-teal/20 px-4 py-2.5 text-center text-xs font-bold text-brand-white hover:bg-brand-teal/10 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-4 py-2.5 text-center text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all cursor-pointer">
                        {{ $isEdit ? 'Update Project' : 'Register Project' }}
                    </button>
                </div>

            </div>

        </div>

    </form>
</div>
@endsection
