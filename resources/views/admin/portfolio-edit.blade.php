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
        <input type="hidden" name="captured_mock_image" id="captured_mock_image" value="">
        
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

                <!-- Live View / Screenshot Auto-Capture Section -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-5" x-data="liveViewWorkspace()">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-cyan border-b border-brand-teal/10 pb-3 flex items-center justify-between">
                        <span>Live Website Preview &amp; Mockup Capture</span>
                        <span class="text-[10px] font-medium text-brand-gray font-mono">Automated Capture Engine</span>
                    </h3>

                    <p class="text-[10px] text-brand-gray">Enter the Live URL in the right-hand metadata panel. The workspace below will allow you to preview the live site and automatically capture it to serve as the portfolio's showcase mockup.</p>

                    <!-- Mock Browser Container -->
                    <div class="relative w-full rounded-xl overflow-hidden border border-brand-teal/25 bg-[#181a1e] flex flex-col shadow-2xl">
                        <!-- Browser Toolbar -->
                        <div class="flex items-center justify-between px-4 py-3 bg-[#131518]/95 border-b border-brand-teal/10 gap-4">
                            <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                <!-- Circles -->
                                <div class="flex gap-1.5">
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500/80"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500/80"></span>
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
                                </div>
                                
                                <!-- Address Bar -->
                                <div class="ml-4 flex-1 max-w-[80%] bg-[#1a1d21] rounded-md px-3 py-1 text-[10px] text-brand-gray font-mono flex items-center gap-1.5 select-none border border-brand-teal/5">
                                    <span class="text-emerald-500/70" x-show="urlValid">🔒</span>
                                    <span class="text-rose-500/70" x-show="!urlValid">⚠️</span>
                                    <span class="truncate" x-text="displayUrl || 'Enter URL on the right...'"></span>
                                </div>
                            </div>

                            <!-- Interactive notice / Iframe warning badge -->
                            <div x-show="showMode === 'live' && urlValid" class="flex items-center gap-1 text-[9px] text-brand-cyan/80 bg-brand-teal/5 px-2.5 py-1 rounded border border-brand-teal/15 select-none cursor-help shrink-0" title="Interactive live view relies on target website security policy (X-Frame-Options). If it displays blank, rest assured that clicking 'Auto-Capture' will still successfully fetch and generate the mockup screenshot.">
                                <svg class="h-3.5 w-3.5 text-brand-cyan shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="hidden md:inline">Frame Notice</span>
                            </div>
                        </div>

                        <!-- Browser Content / Live View Window -->
                        <div class="relative w-full aspect-video bg-[#0f1115] flex flex-col items-center justify-center overflow-hidden">
                            <!-- Empty / Instruction State (No URL entered and no preview image) -->
                            <div x-show="!urlValid && !previewUrl && !loading" class="flex flex-col items-center justify-center p-6 text-center text-brand-gray space-y-2">
                                <span class="text-4xl animate-bounce">🌐</span>
                                <p class="text-xs font-bold text-brand-white">Waiting for Live URL</p>
                                <p class="text-[10px] max-w-sm">Provide a project URL in the Metadata panel to start the live preview and capture process.</p>
                            </div>

                            <!-- Live Interactive Website Iframe -->
                            <div x-show="showMode === 'live' && urlValid && !loading" class="w-full h-full bg-white relative">
                                <iframe :src="iframeUrl" class="w-full h-full border-0 bg-white" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
                            </div>

                            <!-- Live Mockup Image Preview -->
                            <template x-if="previewUrl">
                                <div class="w-full h-full relative flex flex-col" x-show="showMode === 'mockup' && !loading">
                                    <!-- Scrollable Mockup Frame -->
                                    <div class="w-full flex-1 overflow-y-auto scrollbar-premium">
                                        <img :src="previewUrl" alt="Live Mockup Preview" class="w-full h-auto object-contain object-top block">
                                    </div>
                                    <!-- Overlay banner -->
                                    <div class="absolute bottom-3 left-3 right-3 rounded-lg bg-black/70 backdrop-blur-md border border-brand-teal/20 px-3 py-2 flex items-center justify-between z-10">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span class="text-[10px] text-brand-white font-semibold">Captured Mockup Ready</span>
                                        </div>
                                        <span class="text-[9px] text-brand-cyan font-mono">1440x900 (Retina 2x)</span>
                                    </div>
                                </div>
                            </template>

                            <!-- Loading overlay -->
                            <div x-show="loading" class="absolute inset-0 bg-brand-dark-secondary/90 flex flex-col items-center justify-center p-6 space-y-3" style="display: none;">
                                <div class="relative h-12 w-12 flex items-center justify-center">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-cyan/20 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-8 w-8 bg-brand-teal/20 border border-brand-cyan flex items-center justify-center">
                                        <span class="animate-spin rounded-full h-4 w-4 border-2 border-brand-cyan border-t-transparent"></span>
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-brand-white" x-text="loadingStatus">Rendering Mockup...</p>
                                <p class="text-[9px] text-brand-gray max-w-xs text-center">Initializing headless Chromium browser to compile frontend elements, styles, and assets.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions and Status -->
                    <div class="flex items-center justify-between border-t border-brand-teal/10 pt-4 flex-wrap gap-4">
                        <div class="flex items-center gap-3">
                            <!-- Toggle Mode Segmented Control -->
                            <div class="inline-flex rounded-lg p-0.5 bg-brand-dark border border-brand-teal/20 select-none">
                                <button type="button" 
                                        @click="showMode = 'live'"
                                        :class="showMode === 'live' ? 'bg-brand-teal/20 text-brand-cyan border-brand-teal/30' : 'text-brand-gray border-transparent'"
                                        class="px-3 py-1.5 rounded-md text-[10px] font-bold border transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span class="h-1.5 w-1.5 rounded-full" :class="urlValid ? 'bg-emerald-500 animate-pulse' : 'bg-brand-gray'"></span>
                                    Interactive Live View
                                </button>
                                <button type="button" 
                                        @click="showMode = 'mockup'"
                                        :disabled="!previewUrl"
                                        :class="showMode === 'mockup' ? 'bg-brand-teal/20 text-brand-cyan border-brand-teal/30' : 'text-brand-gray border-transparent disabled:opacity-40'"
                                        class="px-3 py-1.5 rounded-md text-[10px] font-bold border transition-all flex items-center gap-1.5 cursor-pointer">
                                    📸 Captured Mockup
                                </button>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-brand-white" x-text="statusText">Status: Idle</span>
                                <span class="text-[9px] text-brand-gray" x-text="subStatusText">No temporary mockup captured yet.</span>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="button" 
                                    @click="captureScreenshot()"
                                    :disabled="!urlValid || loading"
                                    class="rounded-lg bg-brand-teal/10 border border-brand-teal/30 hover:border-brand-cyan/60 hover:bg-brand-teal/20 px-4 py-2 text-xs font-bold text-brand-cyan disabled:opacity-40 disabled:cursor-not-allowed transition-all cubic-bezier(0.4, 0, 0.2, 1) duration-150 cursor-pointer">
                                📸 Auto-Capture Screenshot
                            </button>
                        </div>
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
                        
                        <!-- Dynamic Media Preview Container -->
                        <div id="media-preview-container" class="{{ ($isEdit && $portfolio->mock_image) ? 'block' : 'hidden' }} mb-4 relative w-full aspect-video rounded-lg overflow-hidden border border-brand-teal/25 bg-brand-dark-secondary">
                            <img id="media-preview-img" 
                                 src="{{ ($isEdit && $portfolio->mock_image) ? '/storage/' . $portfolio->mock_image : '' }}" 
                                 alt="Preview" 
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-[10px] font-bold text-brand-white">Currently Selected</span>
                            </div>
                        </div>

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

<script>
    function liveViewWorkspace() {
        return {
            displayUrl: '',
            urlValid: false,
            loading: false,
            loadingStatus: 'Rendering Mockup...',
            previewUrl: '',
            statusText: 'Status: Idle',
            subStatusText: 'No temporary mockup captured yet.',
            showMode: 'live',
            iframeUrl: '',
            
            init() {
                // Find project_url input
                const urlInput = document.querySelector('input[name="project_url"]');
                if (urlInput) {
                    // Initialize value
                    this.updateUrl(urlInput.value);
                    
                    // Listen to changes
                    urlInput.addEventListener('input', (e) => {
                        this.updateUrl(e.target.value);
                    });

                    // Trigger capture automatically on blur if valid and not captured yet
                    urlInput.addEventListener('blur', () => {
                        if (this.urlValid && !this.previewUrl && !this.loading) {
                            this.captureScreenshot();
                        }
                    });
                }

                // If editing and has existing mock_image, initialize the preview
                @if($isEdit && $portfolio->mock_image)
                    this.previewUrl = "/storage/{{ $portfolio->mock_image }}";
                    this.showMode = "mockup";
                    this.statusText = "Status: Active Mockup Loaded";
                    this.subStatusText = "Currently saved mockup is loaded in workspace.";
                @endif

                // Listen for manual file selection to update preview
                const fileInput = document.querySelector('input[name="mock_image"]');
                if (fileInput) {
                    fileInput.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (event) => {
                                const container = document.getElementById('media-preview-container');
                                const img = document.getElementById('media-preview-img');
                                if (container && img) {
                                    img.src = event.target.result;
                                    container.classList.remove('hidden');
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            },

            updateUrl(val) {
                this.displayUrl = val;
                if (!val) {
                    this.urlValid = false;
                    this.iframeUrl = '';
                    return;
                }
                
                // Automatically test with https:// prepended if it looks like a domain name
                let checkUrl = val;
                if (!/^https?:\/\//i.test(val)) {
                    checkUrl = 'https://' + val;
                }
                
                try {
                    const parsed = new URL(checkUrl);
                    this.urlValid = parsed.protocol === 'http:' || parsed.protocol === 'https:';
                    if (this.urlValid) {
                        this.iframeUrl = checkUrl;
                        if (this.showMode !== 'mockup') {
                            this.showMode = 'live';
                        }
                    } else {
                        this.iframeUrl = '';
                    }
                } catch (_) {
                    this.urlValid = false;
                    this.iframeUrl = '';
                }
            },

            async captureScreenshot() {
                if (!this.urlValid) return;
                
                this.loading = true;
                this.loadingStatus = 'Accessing website...';
                this.statusText = 'Status: Capturing...';
                this.subStatusText = 'Connecting to high-fidelity screenshot API...';
                this.showMode = 'mockup';
                
                const steps = [
                    'Accessing website...',
                    'Compiling DOM elements...',
                    'Executing external scripts...',
                    'Injecting styling context...',
                    'Generating high-resolution viewports...',
                    'Saving mockup asset...'
                ];
                let stepIdx = 0;
                const statusInterval = setInterval(() => {
                    if (stepIdx < steps.length - 1) {
                        stepIdx++;
                        this.loadingStatus = steps[stepIdx];
                    }
                }, 2000);

                let targetUrl = this.displayUrl;
                if (!/^https?:\/\//i.test(targetUrl)) {
                    targetUrl = 'https://' + targetUrl;
                }

                try {
                    const response = await fetch("{{ route('admin.portfolios.capture-screenshot') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ url: targetUrl })
                    });

                    clearInterval(statusInterval);

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        this.previewUrl = data.url;
                        this.loading = false;
                        this.showMode = 'mockup';
                        this.statusText = 'Status: Success';
                        this.subStatusText = 'Mockup captured and synchronized to storage.';
                        
                        // Set the hidden input field
                        document.getElementById('captured_mock_image').value = data.path;

                        // Update the media preview container on the right pane
                        const rightPreviewContainer = document.getElementById('media-preview-container');
                        const rightPreviewImg = document.getElementById('media-preview-img');
                        if (rightPreviewContainer && rightPreviewImg) {
                            rightPreviewImg.src = data.url;
                            rightPreviewContainer.classList.remove('hidden');
                        }
                    } else {
                        throw new Error(data.message || 'Capture failed');
                    }
                } catch (err) {
                    clearInterval(statusInterval);
                    this.loading = false;
                    this.statusText = 'Status: Error';
                    
                    let errorMsg = err.message || 'Failed to capture screenshot.';
                    if (this.displayUrl.includes('localhost') || this.displayUrl.includes('127.0.0.1')) {
                        errorMsg += ' (Note: Public APIs cannot access local development domains like localhost. Please upload the screenshot file manually.)';
                    }
                    
                    this.subStatusText = errorMsg;
                    alert('Screenshot capture failed: ' + errorMsg);
                }
            }
        }
    }
</script>
@endsection
