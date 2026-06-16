@extends('layouts.app')

@section('title', 'Diwebs Client Workspace - Enterprise Collaboration Portal')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4" 
     x-data="{
        activeTab: 'dashboard',
        showSignatureModal: false,
        activeContractId: null,
        activeContractTitle: '',
        activeContractContent: '',
        signatureName: '',
        
        // Messaging panel
        selectedDept: 'pm',
        messagesList: [],
        newMessage: '',
        chatLoading: false,
        selectedProject: '{{ $projects->first()?->id ?? '' }}',
        
        // AI assistant
        aiOpen: false,
        aiInput: '',
        aiMessages: [
            { sender: 'ai', text: 'Hello! I am your Diwebs Client AI Assistant. I scan your projects, invoices, and support tickets to answer queries. Try asking me:\n\n*How is my project progressing?* or *Do I have outstanding invoices?*' }
        ],
        aiSending: false,
        
        // Custom budget selectors
        selectedBudget: '$5,000 - $10,000',
        customBudget: '',
        showCreateForm: false,
        
        init() {
            // Read hash parameters to binding tab switching
            if (window.location.hash) {
                const tab = window.location.hash.substring(1);
                if (['dashboard', 'projects', 'requests', 'milestones', 'files', 'contracts', 'billing', 'messages', 'tickets', 'notifications', 'team', 'settings', 'reviews', 'referrals'].includes(tab)) {
                    this.activeTab = tab;
                }
            }
            window.addEventListener('hashchange', () => {
                const tab = window.location.hash.substring(1);
                if (tab) this.activeTab = tab;
            });
            
            // Load messages initially
            this.loadChatMessages();
        },
        
        async loadChatMessages() {
            this.chatLoading = true;
            try {
                const url = `{{ route('portal.chat.messages') }}?project_id=${this.selectedProject}&department=${this.selectedDept}`;
                const res = await fetch(url);
                const data = await res.json();
                this.messagesList = data;
            } catch(e) {
                console.error('Failed to load chat messages:', e);
            } finally {
                this.chatLoading = false;
                this.$nextTick(() => {
                    const box = this.$refs.chatBoxContainer;
                    if (box) box.scrollTop = box.scrollHeight;
                });
            }
        },
        
        async sendChatMessage() {
            if (!this.newMessage.trim()) return;
            const msg = this.newMessage;
            this.newMessage = '';
            
            try {
                const formData = new FormData();
                formData.append('project_id', this.selectedProject);
                formData.append('message', msg);
                formData.append('department', this.selectedDept);
                formData.append('_token', '{{ csrf_token() }}');
                
                await fetch('{{ route('portal.chat.send') }}', {
                    method: 'POST',
                    body: formData
                });
                
                // Reload messages
                await this.loadChatMessages();
            } catch(e) {
                console.error(e);
            }
        },
        
        async askAiAssistant() {
            if (!this.aiInput.trim()) return;
            const query = this.aiInput;
            this.aiMessages.push({ sender: 'user', text: query });
            this.aiInput = '';
            this.aiSending = true;
            
            this.$nextTick(() => {
                const aiBox = this.$refs.aiChatContainer;
                if (aiBox) aiBox.scrollTop = aiBox.scrollHeight;
            });

            try {
                const res = await fetch('{{ route('portal.ai.chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: query })
                });
                const data = await res.json();
                this.aiMessages.push({ sender: 'ai', text: data.message });
            } catch(e) {
                this.aiMessages.push({ sender: 'ai', text: 'Error connecting to the Diwebs AI Engine. Please check your internet connectivity.' });
            } finally {
                this.aiSending = false;
                this.$nextTick(() => {
                    const aiBox = this.$refs.aiChatContainer;
                    if (aiBox) aiBox.scrollTop = aiBox.scrollHeight;
                });
            }
        },
        
        openSignatureModal(id, title, content) {
            this.activeContractId = id;
            this.activeContractTitle = title;
            this.activeContractContent = content;
            this.showSignatureModal = true;
        }
     }">

    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Left Sidebar Navigation Menu -->
        <aside class="w-full lg:w-72 flex-shrink-0">
            <div class="glass-card rounded-2xl p-6 sticky top-24 border border-brand-teal/20 shadow-xl space-y-4">
                <div class="pb-4 border-b border-brand-teal/15">
                    <h3 class="text-base font-bold text-brand-white">Workspace Center</h3>
                    <p class="text-xs text-brand-gray mt-1">Sarah Jenkins (E-Gov Group)</p>
                </div>
                
                <nav class="space-y-1">
                    <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>📊</span> Dashboard Overview
                    </button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>📂</span> My Projects
                    </button>
                    <button @click="activeTab = 'requests'" :class="activeTab === 'requests' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>🚀</span> Service Requests
                    </button>
                    <button @click="activeTab = 'milestones'" :class="activeTab === 'milestones' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>🏆</span> Milestones &amp; Approvals
                    </button>
                    <button @click="activeTab = 'files'" :class="activeTab === 'files' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>💾</span> Files &amp; Deliverables
                    </button>
                    <button @click="activeTab = 'contracts'" :class="activeTab === 'contracts' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>✍️</span> Digital Contracts
                    </button>
                    <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>💳</span> Invoices &amp; Payments
                    </button>
                    <button @click="activeTab = 'messages'" @click.away="" :class="activeTab === 'messages' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>💬</span> Collaboration Hub
                    </button>
                    <button @click="activeTab = 'tickets'" :class="activeTab === 'tickets' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>🎟️</span> Support Helpdesk
                    </button>
                    <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>🔔</span> Notifications
                    </button>
                    <button @click="activeTab = 'team'" :class="activeTab === 'team' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>👥</span> Team Access
                    </button>
                    <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>⚙️</span> Settings &amp; Security
                    </button>
                    <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>⭐</span> Write a Review
                    </button>
                    <button @click="activeTab = 'referrals'" :class="activeTab === 'referrals' ? 'bg-brand-teal/20 text-brand-cyan border-l-4 border-brand-cyan' : 'text-brand-gray hover:bg-brand-teal/5 hover:text-brand-white'" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold rounded-lg transition-all text-left">
                        <span>🤝</span> Referral Program
                    </button>
                </nav>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <main class="flex-grow min-w-0">
            
            <!-- ════════════════════════════════════════
                 TAB: DASHBOARD OVERVIEW
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <!-- Header block -->
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-brand-cyan/10 rounded-full blur-2xl"></div>
                    <h1 class="text-2xl font-extrabold text-brand-white">Workspace Dashboard</h1>
                    <p class="text-xs text-brand-gray mt-1 leading-relaxed">Real-time status monitor of your software development pipelines and account financials.</p>
                </div>

                <!-- KPI Widgets grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Active Projects</span>
                        <div class="text-2xl font-extrabold text-brand-white mt-1">{{ $projects->count() }}</div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Milestone Progress</span>
                        <div class="text-2xl font-extrabold text-brand-cyan mt-1">{{ $taskCompletionRate }}%</div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Outstanding Invoices</span>
                        <div class="text-2xl font-extrabold text-rose-400 mt-1">{{ $unpaidInvoices->count() }}</div>
                    </div>
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">SLA Support Uptime</span>
                        <div class="text-2xl font-extrabold text-emerald-400 mt-1">99.98%</div>
                    </div>
                </div>

                <!-- Main Layout splits -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Progress Telemetry Graphic -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-brand-white">Project Sprint Progress</h3>
                            <p class="text-[11px] text-brand-gray mt-1">Relative sprint completion timeline based on milestone approvals.</p>
                        </div>
                        
                        <!-- Premium SVG Graph Simulation -->
                        <div class="my-6 relative h-40 w-full bg-[#1A1D21]/40 border border-brand-teal/5 rounded-xl overflow-hidden flex items-end">
                            <div class="absolute inset-0 flex items-center justify-between px-6 pointer-events-none">
                                <span class="text-[9px] text-brand-gray border-b border-brand-teal/5 pb-1">Sprint 1</span>
                                <span class="text-[9px] text-brand-gray border-b border-brand-teal/5 pb-1">Sprint 2</span>
                                <span class="text-[9px] text-brand-gray border-b border-brand-teal/5 pb-1">Sprint 3</span>
                                <span class="text-[9px] text-brand-gray border-b border-brand-teal/5 pb-1">Sprint 4</span>
                            </div>
                            
                            <!-- Graph line using SVG -->
                            <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <defs>
                                    <linearGradient id="chartGlow" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#00C2D1" stop-opacity="0.3"></stop>
                                        <stop offset="100%" stop-color="#00C2D1" stop-opacity="0"></stop>
                                    </linearGradient>
                                </defs>
                                <path d="M 0 90 Q 25 70, 50 45 T 100 20 L 100 100 L 0 100 Z" fill="url(#chartGlow)"></path>
                                <path d="M 0 90 Q 25 70, 50 45 T 100 20" fill="none" stroke="#00C2D1" stroke-width="2.5"></path>
                            </svg>
                        </div>

                        <div class="flex items-center justify-between text-xs text-brand-gray">
                            <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-brand-cyan"></span> Telemetry Target: Go-Live Launch</span>
                            <span class="font-bold text-brand-white">Est. Completion: Q3 2026</span>
                        </div>
                    </div>

                    <!-- Quick Operations Hub -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Quick Actions</h3>
                        
                        <button @click="activeTab = 'requests'" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                            🚀 Request Service Proposal
                        </button>
                        
                        <button @click="activeTab = 'files'" class="w-full rounded-xl bg-brand-teal/15 border border-brand-teal/30 text-brand-cyan font-bold text-xs py-3.5 hover:bg-brand-teal/25 transition-all flex items-center justify-center gap-2">
                            💾 Upload Assets &amp; Specs
                        </button>

                        <button @click="activeTab = 'messages'" class="w-full rounded-xl bg-[#25282D] text-brand-white border border-white/5 font-semibold text-xs py-3.5 hover:bg-[#2F3238] transition-all flex items-center justify-center gap-2">
                            💬 Chat PM / Support Desk
                        </button>

                        <button @click="activeTab = 'billing'" class="w-full rounded-xl bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 font-bold text-xs py-3.5 hover:bg-emerald-500/20 transition-all flex items-center justify-center gap-2">
                            💳 Settle Outstanding Balance
                        </button>
                    </div>
                </div>

                <!-- Recent Activities log -->
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h3 class="text-sm font-bold text-brand-white mb-4">Recent Security &amp; Activity Log</h3>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @forelse($auditLogs as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-brand-teal/10" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-brand-teal/10 border border-brand-teal/20 flex items-center justify-center text-xs">
                                                    @if(str_contains($log->event_type, 'login')) 🔒 
                                                    @elseif(str_contains($log->event_type, 'payment')) 💰
                                                    @else ⚙️
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex-grow min-w-0 pt-1.5">
                                                <p class="text-xs text-brand-white font-medium">
                                                    {{ ucfirst(str_replace('_', ' ', $log->event_type)) }}
                                                    <span class="text-brand-gray text-[10px] ml-2 font-mono">({{ $log->ip_address }})</span>
                                                </p>
                                                <p class="text-[10px] text-brand-gray mt-0.5">{{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-xs text-brand-gray text-center py-4">No activity history recorded on account.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: MY PROJECTS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'projects'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-brand-white">My Projects</h2>
                        <p class="text-xs text-brand-gray mt-1">Detailed operational review of currently commissioned platforms.</p>
                    </div>
                    <button @click="showCreateForm = !showCreateForm" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs px-4 py-2.5 hover:opacity-90 transition-all flex items-center gap-1.5 shadow-md">
                        ➕ Create Project Request
                    </button>
                </div>

                <!-- Project Request Form (Alpine conditional) -->
                <div x-show="showCreateForm" x-transition class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4 mb-6">
                    <h3 class="text-base font-bold text-brand-white">New Project Proposal Request</h3>
                    <form action="{{ route('portal.project.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Project Working Title</label>
                                <input type="text" name="title" required placeholder="e.g. Corporate E-Commerce Redesign" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Service Type</label>
                                <select name="service_type" required class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                                    <option value="Website Development">Website Development</option>
                                    <option value="Mobile App Development">Mobile App Development</option>
                                    <option value="SaaS Platform">SaaS Platform</option>
                                    <option value="AI Automation">AI Automation</option>
                                    <option value="Other">Other / Custom Service</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Estimated Budget ($)</label>
                                <input type="number" name="budget" required min="1" placeholder="e.g. 8500" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Timeline Notes</label>
                                <input type="text" placeholder="e.g. Need by end of Q3" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Detailed Scope Description</label>
                            <textarea name="description" required rows="4" placeholder="Describe key feature modules, third-party integrations, and performance goals..." class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white focus:outline-none focus:border-brand-cyan transition-all"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs px-6 py-3 hover:opacity-90 transition-all">
                                🚀 Submit Project Proposal
                            </button>
                            <button type="button" @click="showCreateForm = false" class="rounded-xl bg-brand-dark-secondary border border-brand-teal/15 text-brand-white font-semibold text-xs px-6 py-3 hover:bg-brand-dark transition-all">
                                Cancel
                             </button>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    @forelse($projects as $project)
                        <div class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-6">
                            <div class="flex justify-between items-start gap-4 flex-wrap">
                                <div>
                                     <span class="rounded px-2.5 py-0.5 text-[10px] uppercase font-bold
                                         @if(!$project->is_validated) bg-amber-950 text-amber-400 border border-amber-500/20
                                         @elseif(!$project->payment_made) bg-rose-950 text-rose-400 border border-rose-500/20
                                         @else bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                         @endif">
                                         @if(!$project->is_validated)
                                             Awaiting Validation
                                         @elseif(!$project->payment_made)
                                             Awaiting Initial Payment
                                         @else
                                             {{ strtoupper($project->status) }}
                                         @endif
                                     </span>
                                    <h3 class="text-lg font-bold text-brand-white mt-2">{{ $project->title }}</h3>
                                    <p class="text-xs text-brand-gray mt-1 leading-relaxed max-w-2xl">{{ $project->description }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] uppercase text-brand-gray">Commissioned Budget</span>
                                    <strong class="text-lg font-extrabold text-brand-white">@money($project->budget)</strong>
                                </div>
                            </div>

                            @if($project->is_validated && $project->payment_made)
                                <!-- Success Rate for Web Development -->
                                @if($project->service_type === 'Website Development' || $project->service_type === 'Web Development')
                                    <div class="p-4 bg-brand-dark-secondary/50 border border-brand-teal/15 rounded-xl">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-brand-cyan uppercase">🌐 Web Development Success Rate</span>
                                            <span class="text-xs font-mono font-bold text-emerald-400">{{ $project->success_rate }}%</span>
                                        </div>
                                        <div class="w-full bg-brand-dark rounded-full h-2">
                                            <div class="bg-gradient-to-r from-brand-teal to-brand-cyan h-2 rounded-full transition-all duration-500" style="width: {{ $project->success_rate }}%"></div>
                                        </div>
                                        <p class="text-[10px] text-brand-gray mt-2">This is the official success rate of your web development project, updated in real-time by the project manager.</p>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-brand-teal/10">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-brand-gray block">Assigned Technical Team</span>
                                        <div class="mt-2 space-y-1.5 text-xs text-brand-white">
                                            <div class="flex items-center gap-2">👤 <strong>Jude Carter</strong> <span class="text-[10px] text-brand-gray">(Project Manager)</span></div>
                                            <div class="flex items-center gap-2">👤 <strong>Amina Yusuf</strong> <span class="text-[10px] text-brand-gray">(Tech Lead)</span></div>
                                            <div class="flex items-center gap-2">👤 <strong>Tobi Alabi</strong> <span class="text-[10px] text-brand-gray">(QA Analyst)</span></div>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-brand-gray block">Contract Agreement Details</span>
                                        <div class="mt-2 space-y-1 text-xs text-brand-white">
                                            <div>Agreement Date: <strong class="text-brand-cyan">{{ $project->agreement_signed_at ? $project->agreement_signed_at->format('M d, Y') : 'Pending Signature' }}</strong></div>
                                            <div>Scope Reference: <span class="font-mono text-[10px] text-brand-gray">DIW-CTR-{{ substr($project->id, 0, 8) }}</span></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-end">
                                        <a href="{{ route('portal.project', $project->id) }}" class="rounded-xl bg-brand-teal/10 border border-brand-teal/30 hover:bg-brand-teal/20 text-center py-2.5 text-xs font-bold text-brand-cyan transition-all">
                                            Manage Sprint Telemetry →
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 bg-brand-dark-secondary/50 border border-brand-teal/10 rounded-xl space-y-3">
                                    <p class="text-xs text-brand-gray leading-relaxed">
                                        @if(!$project->is_validated)
                                            🛡️ <strong>This project is currently awaiting administrator validation.</strong> Once the scope and budget are approved by our team, we will generate the service contract agreement and kickoff invoice.
                                        @else
                                             💳 <strong>Project is validated, but payment is outstanding.</strong> Please sign the digital agreement under the <strong>Digital Contracts</strong> tab and pay the initialization invoice under <strong>Invoices &amp; Payments</strong> to activate this project and track milestones.
                                        @endif
                                    </p>
                                    <div class="flex gap-2">
                                        @if($project->is_validated)
                                            <button @click="activeTab = 'contracts'; window.location.hash = 'contracts';" class="rounded-lg bg-brand-teal/15 border border-brand-teal/30 text-brand-cyan text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                                ✍️ Go to Contracts
                                            </button>
                                            <button @click="activeTab = 'billing'; window.location.hash = 'billing';" class="rounded-lg bg-emerald-500/10 border border-emerald-500/35 text-emerald-400 text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                                💳 Go to Payments
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                            No projects registered. Click "+ Create Project Request" above to submit a proposal!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: SERVICE REQUESTS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'requests'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Service Requests System</h2>
                    <p class="text-xs text-brand-gray mt-1">Initiate and request new services or platform additions from Diwebs.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Request form -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Request New Service</h3>
                        
                        <form action="{{ route('portal.service-request.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">Project Working Title</label>
                                <input type="text" name="title" required placeholder="e.g. Real-Time Chat System" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">Service Segment Category</label>
                                    <select name="service_type" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D] px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                        <option value="Website Development">Website Development</option>
                                        <option value="Mobile App Development">Mobile App Development</option>
                                        <option value="SaaS Platform">SaaS Platform</option>
                                        <option value="AI Automation">AI Automation</option>
                                        <option value="CBT Platform">CBT Platform</option>
                                        <option value="API Integration">API Integration</option>
                                        <option value="Cloud Infrastructure">Cloud Infrastructure</option>
                                        <option value="UI/UX Design">UI/UX Design</option>
                                        <option value="Cybersecurity Audit">Cybersecurity Audit</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">Budget Target Scale</label>
                                    <select x-model="selectedBudget" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D] px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                        <option value="$5,000 - $10,000">$5,000 - $10,000</option>
                                        <option value="$10,000 - $25,000">$10,000 - $25,000</option>
                                        <option value="$25,000 - $50,000">$25,000 - $50,000</option>
                                        <option value="$50,000 - $100,000">$50,000 - $100,000</option>
                                        <option value="$100,000+">$100,000+ (Enterprise Scale)</option>
                                        <option value="Other">Other (Custom Amount)</option>
                                    </select>
                                    
                                    <input type="hidden" name="budget_range" :value="selectedBudget === 'Other' ? customBudget : selectedBudget">

                                    <div x-show="selectedBudget === 'Other'" class="mt-3" x-transition>
                                        <label class="block text-[10px] text-brand-cyan font-bold uppercase tracking-wider mb-1">Specify Custom Budget Amount ($)</label>
                                        <input type="text" x-model="customBudget" :required="selectedBudget === 'Other'" placeholder="e.g. $15,000 or 15000" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">Requested Deployment Deadline</label>
                                    <input type="date" name="deadline" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">System Specs / Documents</label>
                                    <input type="file" disabled class="w-full text-xs text-brand-gray py-2.5">
                                    <p class="text-[9px] text-brand-gray mt-1">Upload specification files in the Files tab once project is provisioned.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-brand-gray font-bold uppercase tracking-wider mb-2">Detailed Project Scope Description</label>
                                <textarea name="description" required rows="4" placeholder="Detail the key screens, database needs, and cloud scaling specs..." class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none transition-all"></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                                🚀 Send Service Request to CRM
                            </button>
                        </form>
                    </div>

                    <!-- Right Log of requests -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4 h-[500px] overflow-y-auto">
                        <h3 class="text-sm font-bold text-brand-white">Service Requests Status</h3>
                        
                        <div class="space-y-4">
                            @forelse($serviceRequests as $req)
                                <div class="border-b border-brand-teal/10 pb-4 last:border-0 last:pb-0 space-y-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-xs font-bold text-brand-white">{{ $req->title }}</h4>
                                            <span class="text-[10px] text-brand-gray block">{{ $req->service_type }} • {{ $req->budget_range }}</span>
                                        </div>
                                        <span class="rounded bg-brand-cyan/15 px-2 py-0.5 text-[9px] text-brand-cyan uppercase font-bold">{{ $req->status }}</span>
                                    </div>
                                    <p class="text-[10px] text-brand-gray leading-relaxed">{{ $req->description }}</p>
                                    @if($req->ai_recommendations)
                                        <div class="p-2.5 bg-brand-dark-secondary/70 border border-brand-cyan/15 rounded-xl">
                                            <span class="text-[9px] text-brand-cyan font-bold uppercase tracking-wider block">🤖 AI Recommendation</span>
                                            <p class="text-[9px] text-brand-gray mt-1 leading-relaxed whitespace-pre-line">{{ $req->ai_recommendations }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-brand-gray text-center py-4">No service requests logged.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: MILESTONES & APPROVALS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'milestones'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Milestone Verification &amp; Sign-off</h2>
                    <p class="text-xs text-brand-gray mt-1">Review operational sprint outputs. Approve milestones to trigger next phases and release payments.</p>
                </div>

                <div class="space-y-6">
                    @foreach($projects as $project)
                        <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                            <h3 class="text-sm font-bold text-brand-white">{{ $project->title }}</h3>
                            
                            <div class="space-y-4">
                                @foreach($project->milestones as $milestone)
                                    <div class="border-b border-brand-teal/10 pb-4 last:border-0 last:pb-0">
                                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="h-2 w-2 rounded-full 
                                                        @if($milestone->status === 'approved') bg-emerald-400
                                                        @elseif($milestone->status === 'working') bg-brand-cyan animate-pulse
                                                        @else bg-brand-gray
                                                        @endif">
                                                    </span>
                                                    <h4 class="text-xs font-bold text-brand-white">{{ $milestone->title }}</h4>
                                                </div>
                                                <p class="text-[10px] text-brand-gray max-w-xl leading-relaxed">{{ $milestone->description }}</p>
                                                <span class="text-[9px] text-brand-gray block">Due Date: {{ $milestone->due_date ? $milestone->due_date->format('M d, Y') : 'TBD' }} • Value: @money($milestone->amount)</span>
                                            </div>

                                            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                                                @if($milestone->status === 'working' || $milestone->status === 'pending')
                                                    <!-- Actions -->
                                                    <form action="{{ route('portal.milestone.action', $milestone->id) }}" method="POST" class="flex gap-2 w-full md:w-auto">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approved">
                                                        <button type="submit" class="rounded-lg bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                                            Approve Stage
                                                        </button>
                                                    </form>
                                                    
                                                    <button @click="const comment = prompt('Provide revision feedback:'); if(comment) { 
                                                        const f = document.createElement('form'); f.method='POST'; f.action='{{ route('portal.milestone.action', $milestone->id) }}';
                                                        const c = document.createElement('input'); c.name='comments'; c.value=comment; f.appendChild(c);
                                                        const a = document.createElement('input'); a.name='action'; a.value='revision_requested'; f.appendChild(a);
                                                        const t = document.createElement('input'); t.name='_token'; t.value='{{ csrf_token() }}'; f.appendChild(t);
                                                        document.body.appendChild(f); f.submit();
                                                     }" class="rounded-lg bg-rose-500/10 border border-rose-500/30 hover:bg-rose-500/20 text-rose-400 text-[10px] font-bold uppercase tracking-wider px-3.5 py-2 transition-all">
                                                        Request Revision
                                                    </button>
                                                @else
                                                    <span class="text-[10px] uppercase font-bold text-emerald-400 flex items-center gap-1">✔ signed off</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Milestone logs / audit trails -->
                                        @if($milestone->logs->isNotEmpty())
                                            <div class="mt-3 pl-4 border-l border-brand-teal/15 space-y-1.5">
                                                <span class="text-[9px] uppercase font-bold text-brand-gray tracking-wider block">Audit Trails</span>
                                                @foreach($milestone->logs as $log)
                                                    <div class="text-[9px] text-brand-gray leading-relaxed">
                                                        <strong class="text-brand-white">{{ $log->action === 'approved' ? 'Approved' : 'Revision Requested' }}</strong> by {{ $log->user->name }} • {{ $log->created_at->format('M d, H:i') }}
                                                        @if($log->comments)
                                                            <div class="text-brand-gray/80 italic mt-0.5">"{{ $log->comments }}"</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: FILES & DELIVERABLES
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'files'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Secure Assets &amp; Deliverables</h2>
                    <p class="text-xs text-brand-gray mt-1">Secure directory tracking source packages, contract pdfs, layouts, database backups, and project specifications.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Upload area -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Upload Project File</h3>
                        
                        <!-- Drag & Drop container simulation -->
                        <form action="{{ route('portal.project.upload', $projects->first()?->id ?? 1) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="border-2 border-dashed border-brand-teal/20 rounded-xl p-6 text-center hover:border-brand-cyan/40 transition-all relative">
                                <input type="file" name="project_file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <span class="text-2xl block mb-2">📁</span>
                                <span class="text-xs font-semibold text-brand-white block">Drag and drop file here</span>
                                <span class="text-[9px] text-brand-gray mt-1 block">Maximum upload file size: 15MB</span>
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Destination Directory Folder</label>
                                <select name="folder" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D] px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                    <option value="assets">Assets &amp; Specification sheets</option>
                                    <option value="contracts">Legal Contracts &amp; Proposals</option>
                                    <option value="deliverables">Sprints Deliverables packages</option>
                                    <option value="reports">Telemetry &amp; Audit reports</option>
                                    <option value="backups">Database &amp; Source Backups</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                                📤 Upload Package
                            </button>
                        </form>
                    </div>

                    <!-- Right: Directory Browser -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Project Folder Directory</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                                    <tr>
                                        <th class="py-3">Name</th>
                                        <th class="py-3">Folder Location</th>
                                        <th class="py-3">Version</th>
                                        <th class="py-3 text-right">Size</th>
                                        <th class="py-3 text-center">Downloads</th>
                                        <th class="py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                                    @forelse($projectFiles as $file)
                                        <tr>
                                            <td class="py-3 font-semibold flex items-center gap-2">
                                                <span>📄</span> {{ $file->filename }}
                                            </td>
                                            <td class="py-3 uppercase font-bold text-brand-cyan text-[10px]">
                                                {{ $file->folder }}
                                            </td>
                                            <td class="py-3 text-[10px] font-mono text-brand-gray">
                                                v{{ $file->version }}
                                            </td>
                                            <td class="py-3 text-right font-mono text-[10px] text-brand-gray">
                                                {{ round($file->file_size / 1024, 1) }} KB
                                            </td>
                                            <td class="py-3 text-center text-brand-gray font-mono text-[10px]">
                                                {{ $file->download_count }}
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('portal.file.download', $file->id) }}" class="rounded bg-brand-teal/10 border border-brand-teal/30 hover:bg-brand-teal/20 px-3 py-1.5 text-[10px] text-brand-cyan transition-all">
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4 text-center text-brand-gray text-xs">No project packages uploaded to directory.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: DIGITAL CONTRACTS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'contracts'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Digital Agreement Desk</h2>
                    <p class="text-xs text-brand-gray mt-1">View, review, and e-sign contracts and project specifications online with IP/browser audit trail tracking.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @forelse($contracts as $ctr)
                        <div class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-bold text-brand-white">{{ $ctr->title }}</h3>
                                    <span class="text-[9px] text-brand-gray font-mono uppercase tracking-wider block">ID: DIW-CTR-{{ $ctr->id }}</span>
                                </div>
                                <span class="rounded px-2.5 py-0.5 text-[10px] uppercase font-bold 
                                    @if($ctr->status === 'signed') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @else bg-rose-500/10 text-rose-400 border border-rose-500/20
                                    @endif">
                                    {{ $ctr->status }}
                                </span>
                            </div>

                            <p class="text-xs text-brand-gray leading-relaxed max-w-3xl whitespace-pre-line">{{ Str::limit($ctr->content, 200) }}</p>

                            <div class="pt-4 border-t border-brand-teal/10 flex justify-between items-center">
                                @if($ctr->status === 'signed')
                                    <div class="text-[10px] text-brand-gray space-y-0.5">
                                        <div>Signed At: <strong class="text-brand-white">{{ $ctr->signed_at->format('M d, Y H:i:s') }}</strong></div>
                                        <div>Legal Signature: <span class="font-mono text-brand-cyan select-all">/{{ $ctr->signature_data }}/</span></div>
                                        <div class="text-[9px]">Audit IP: <span class="font-mono">{{ $ctr->ip_address }}</span> • UA: <span class="font-mono">{{ Str::limit($ctr->user_agent, 40) }}</span></div>
                                    </div>
                                @else
                                    <span class="text-xs text-brand-gray">Review and sign agreement to activate pipeline planning stage.</span>
                                @endif

                                <div class="flex gap-2">
                                    @if($ctr->status !== 'signed')
                                        <button @click="openSignatureModal({{ $ctr->id }}, '{{ $ctr->title }}', `{{ $ctr->content }}`)" class="rounded-xl bg-brand-teal/20 border border-brand-teal/40 hover:bg-brand-teal/30 px-4 py-2 text-xs font-bold text-brand-cyan transition-all">
                                            E-Sign Contract Desk
                                        </button>
                                    @else
                                        <button onclick="alert('Downloading Contract PDF...')" class="rounded-xl bg-brand-dark border border-brand-teal/20 hover:bg-brand-teal/10 px-4 py-2 text-xs font-semibold text-brand-gray transition-all">
                                            Download PDF
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="glass-card rounded-2xl p-8 text-center text-brand-gray text-sm">
                            No digital agreements linked to account workspace.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: INVOICES & PAYMENTS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'billing'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Billing &amp; Payments Center</h2>
                    <p class="text-xs text-brand-gray mt-1">Settle outstanding balances, choose installment schedules, and view complete transaction billing histories.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Unpaid invoices and payment configurations -->
                    <div class="lg:col-span-2 space-y-6">
                        <h3 class="text-sm font-bold text-brand-cyan tracking-wider uppercase">Unpaid Milestone Invoices</h3>
                        
                        @forelse($unpaidInvoices as $invoice)
                            <div class="glass-card rounded-2xl p-6 border border-brand-teal/20 space-y-4" x-data="{ payType: 'full', customAmount: '' }">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        <span class="text-[10px] text-brand-cyan font-bold uppercase tracking-wider">Invoice #{{ $invoice->invoice_number }}</span>
                                        <h4 class="text-xs font-bold text-brand-white mt-1">{{ $invoice->project->title }}</h4>
                                        <p class="text-[10px] text-brand-gray">Due Date: {{ $invoice->due_date->format('M d, Y') }}</p>
                                    </div>
                                    <strong class="text-lg font-mono text-brand-white">@money($invoice->amount)</strong>
                                </div>

                                <!-- Payment type selector -->
                                <div class="p-3.5 bg-brand-dark-secondary/50 border border-brand-teal/10 rounded-xl space-y-3">
                                    <span class="text-[10px] text-brand-gray font-bold uppercase tracking-wider block">Choose Payment Schedule</span>
                                    
                                    <div class="flex flex-wrap gap-4 text-xs">
                                        <label class="flex items-center gap-2 cursor-pointer text-brand-white">
                                            <input type="radio" x-model="payType" name="payment_type_{{ $invoice->id }}" value="full" class="text-brand-cyan focus:ring-brand-cyan bg-brand-dark-primary border-brand-teal/30">
                                            Full Amount (@money($invoice->amount))
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer text-brand-white">
                                            <input type="radio" x-model="payType" name="payment_type_{{ $invoice->id }}" value="installment" class="text-brand-cyan focus:ring-brand-cyan bg-brand-dark-primary border-brand-teal/30">
                                            Installment (50% - @money($invoice->amount / 2))
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer text-brand-white">
                                            <input type="radio" x-model="payType" name="payment_type_{{ $invoice->id }}" value="partial" class="text-brand-cyan focus:ring-brand-cyan bg-brand-dark-primary border-brand-teal/30">
                                            Custom Partial
                                        </label>
                                    </div>

                                    <div x-show="payType === 'partial'" class="pt-2">
                                        <input type="number" x-model="customAmount" placeholder="Enter amount to pay" class="rounded-lg border border-brand-teal/20 bg-brand-dark px-3 py-2 text-xs text-brand-white w-full">
                                    </div>
                                </div>

                                <!-- Pay execution -->
                                <form action="{{ route('portal.invoice.pay', $invoice->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_type" :value="payType">
                                    <input type="hidden" name="partial_amount" :value="customAmount">
                                    
                                    <!-- Display selected gateway button -->
                                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                        💳 Process Payment using Gateway ({{ ucfirst(str_replace('_', ' ', \App\Helpers\PaymentHelper::activeGateway())) }})
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="glass-card rounded-2xl p-6 text-center text-brand-gray text-xs">
                                All billing items settled. Excellent!
                            </div>
                        @endforelse
                    </div>

                    <!-- Right: History and specs -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Invoice History Log</h3>
                        
                        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                            @forelse($invoiceHistory as $inv)
                                <div class="border-b border-brand-teal/5 pb-3 last:border-0 last:pb-0 flex justify-between items-center">
                                    <div>
                                        <span class="text-[9px] text-brand-cyan font-mono block">#{{ $inv->invoice_number }}</span>
                                        <span class="text-[10px] text-brand-white block font-semibold">{{ Str::limit($inv->project->title, 20) }}</span>
                                        <span class="text-[8px] text-brand-gray block">{{ $inv->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono text-xs block text-brand-white">@money($inv->amount)</span>
                                        <span class="text-[9px] uppercase font-bold 
                                            @if($inv->status === 'paid') text-emerald-400
                                            @elseif($inv->status === 'pending' || $inv->status === 'pending_partial') text-brand-cyan animate-pulse
                                            @else text-rose-400
                                            @endif">
                                            {{ $inv->status }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-brand-gray text-center py-2">No transaction history.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: COLLABORATION HUB (MESSAGES)
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'messages'" class="space-y-6" @click.away="">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Live Collaboration Workspace</h2>
                    <p class="text-xs text-brand-gray mt-1">Communicate directly with your Project Manager, support agents, legal, or finance department.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Dept switcher -->
                    <div class="glass-card rounded-2xl p-4 border border-brand-teal/15 space-y-2 h-fit">
                        <span class="text-[10px] uppercase font-bold text-brand-gray block mb-3 px-2">Departments</span>
                        <button @click="selectedDept = 'pm'; loadChatMessages();" :class="selectedDept === 'pm' ? 'bg-brand-cyan/15 text-brand-cyan font-bold' : 'text-brand-gray hover:bg-white/5'" class="w-full text-xs text-left px-3 py-2.5 rounded-lg transition-all flex items-center gap-2">
                            <span>💼</span> Project Manager
                        </button>
                        <button @click="selectedDept = 'technical'; loadChatMessages();" :class="selectedDept === 'technical' ? 'bg-brand-cyan/15 text-brand-cyan font-bold' : 'text-brand-gray hover:bg-white/5'" class="w-full text-xs text-left px-3 py-2.5 rounded-lg transition-all flex items-center gap-2">
                            <span>💻</span> Engineering Team
                        </button>
                        <button @click="selectedDept = 'finance'; loadChatMessages();" :class="selectedDept === 'finance' ? 'bg-brand-cyan/15 text-brand-cyan font-bold' : 'text-brand-gray hover:bg-white/5'" class="w-full text-xs text-left px-3 py-2.5 rounded-lg transition-all flex items-center gap-2">
                            <span>💰</span> Finance &amp; Billing
                        </button>
                        <button @click="selectedDept = 'support'; loadChatMessages();" :class="selectedDept === 'support' ? 'bg-brand-cyan/15 text-brand-cyan font-bold' : 'text-brand-gray hover:bg-white/5'" class="w-full text-xs text-left px-3 py-2.5 rounded-lg transition-all flex items-center gap-2">
                            <span>🛡️</span> Technical Support
                        </button>
                    </div>

                    <!-- Chat Window -->
                    <div class="lg:col-span-3 glass-card rounded-2xl border border-brand-teal/20 flex flex-col h-[520px]">
                        <!-- Head -->
                        <div class="px-6 py-4 border-b border-brand-teal/15 flex justify-between items-center">
                            <div>
                                <h3 class="text-xs font-bold text-brand-white uppercase tracking-wider">
                                    <span x-text="selectedDept === 'pm' ? 'Project Manager Collaboration' : (selectedDept === 'technical' ? 'Engineering & DevOps API' : (selectedDept === 'finance' ? 'Finance Ledger Desk' : 'Help Desk Support'))"></span>
                                </h3>
                                <p class="text-[9px] text-brand-gray mt-0.5">Mock agent polling dispatcher active</p>
                            </div>
                            
                            <div>
                                <select x-model="selectedProject" @change="loadChatMessages()" class="rounded bg-brand-dark-primary text-[10px] text-brand-white border border-brand-teal/15 py-1 px-2 focus:outline-none">
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}">{{ Str::limit($p->title, 20) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Chat boxes -->
                        <div class="flex-grow p-6 overflow-y-auto space-y-4" x-ref="chatBoxContainer">
                            <div x-show="chatLoading" class="text-center py-10 text-xs text-brand-gray animate-pulse">Loading message records...</div>
                            
                            <template x-for="msg in messagesList" :key="msg.id">
                                <div :class="msg.is_client ? 'justify-end' : 'justify-start'" class="flex">
                                    <div :class="msg.is_client ? 'bg-brand-cyan/20 border border-brand-cyan/35 text-brand-white' : 'bg-[#25282D] border border-white/5 text-brand-white'" class="max-w-[70%] rounded-2xl p-4 space-y-1 relative">
                                        <span class="text-[9px] uppercase font-bold text-brand-gray block" x-text="msg.sender_name"></span>
                                        <p class="text-xs leading-relaxed" x-text="msg.message"></p>
                                        <span class="text-[8px] text-brand-gray block text-right mt-1" x-text="msg.created_at"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Chat sender -->
                        <div class="p-4 border-t border-brand-teal/15">
                            <div class="flex gap-2">
                                <input type="text" x-model="newMessage" @keydown.enter="sendChatMessage()" placeholder="Type message to team..." class="flex-grow rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none">
                                <button @click="sendChatMessage()" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary px-5 font-bold text-xs hover:opacity-90 transition-all flex items-center justify-center">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: SUPPORT TICKETS
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'tickets'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Support Helpdesk</h2>
                    <p class="text-xs text-brand-gray mt-1">Submit high-priority engineering, hosting, or billing support tickets directly to SLA managers.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Create ticket -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Open Support Ticket</h3>
                        
                        <form action="{{ route('portal.ticket.create') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Subject / Summary</label>
                                <input type="text" name="subject" required placeholder="Describe core issue" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Incident Priority Level</label>
                                <select name="priority" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D] px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                    <option value="low">Low (General Query)</option>
                                    <option value="medium">Medium (Regular Issue)</option>
                                    <option value="high">High (Sprint blocker)</option>
                                    <option value="critical">Critical (Infrastructure outage)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Detailed Description</label>
                                <textarea name="message" required rows="4" placeholder="Detail browser console messages, server responses, or error details..." class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none transition-all"></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                                🎟️ Open Help Ticket
                            </button>
                        </form>
                    </div>

                    <!-- Log of tickets -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Ticket Logs</h3>
                        
                        <div class="space-y-4 max-h-[450px] overflow-y-auto">
                            @forelse($tickets as $tick)
                                <div class="p-4 bg-brand-dark-secondary/50 border border-brand-teal/10 rounded-2xl flex justify-between items-start">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="rounded px-2 py-0.5 text-[8px] uppercase font-bold 
                                                @if($tick->priority === 'critical') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                                @elseif($tick->priority === 'high') bg-orange-500/10 text-orange-400 border border-orange-500/20
                                                @else bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                                @endif">
                                                {{ $tick->priority }}
                                            </span>
                                            <h4 class="text-xs font-bold text-brand-white">{{ $tick->subject }}</h4>
                                        </div>
                                        <p class="text-[10px] text-brand-gray leading-relaxed">{{ $tick->message }}</p>
                                        <span class="text-[8px] text-brand-gray block">{{ $tick->created_at->format('M d, Y H:i') }}</span>
                                    </div>

                                    <div class="text-right">
                                        <span class="rounded px-2.5 py-1 text-[9px] uppercase font-bold 
                                            @if($tick->status === 'open') bg-brand-cyan/20 text-brand-cyan border border-brand-cyan/35
                                            @elseif($tick->status === 'resolved') bg-emerald-500/20 text-emerald-400 border border-emerald-500/35
                                            @else bg-brand-gray/20 text-brand-gray border border-brand-gray/35
                                            @endif">
                                            {{ $tick->status }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-brand-gray text-center py-6">No help tickets opened.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: NOTIFICATIONS CENTER
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'notifications'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Workspace Notifications</h2>
                    <p class="text-xs text-brand-gray mt-1">In-app notifications channel log of project completions and code deployments.</p>
                </div>

                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                    <div class="divide-y divide-brand-teal/5">
                        <div class="py-4 flex gap-3">
                            <span class="text-lg">💰</span>
                            <div>
                                <h4 class="text-xs font-bold text-brand-white">Milestone Invoice Dispatched</h4>
                                <p class="text-[10px] text-brand-gray mt-0.5">Invoice #INV-2026-002 for $60,000.00 is outstanding. Due date is in 15 days.</p>
                                <span class="text-[8px] text-brand-gray block mt-1">1 hour ago</span>
                            </div>
                        </div>
                        <div class="py-4 flex gap-3">
                            <span class="text-lg">🏆</span>
                            <div>
                                <h4 class="text-xs font-bold text-brand-white">Milestone Stage Signed Off</h4>
                                <p class="text-[10px] text-brand-gray mt-0.5">Jude Carter approved the "System Design &amp; Database Schema Approval" stage.</p>
                                <span class="text-[8px] text-brand-gray block mt-1">2 days ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: TEAM ACCESS MANAGEMENT
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'team'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Team Access &amp; Workspace Share</h2>
                    <p class="text-xs text-brand-gray mt-1">Invite team developers, product owners, or accounting managers to view or manage the workspace.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Invite Collaborator</h3>
                        
                        <form action="{{ route('portal.team.invite') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Member Name</label>
                                <input type="text" name="name" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Email Address</label>
                                <input type="email" name="email" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Workspace Role</label>
                                <select name="role" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D] px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                    <option value="manager">Manager (Read &amp; Write project details)</option>
                                    <option value="reviewer">Reviewer (Approve milestones &amp; view files)</option>
                                    <option value="finance_viewer">Finance Viewer (Pay invoices, block chat)</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                                👥 Invite Collaborator
                            </button>
                        </form>
                    </div>

                    <!-- Invited logs -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Invited Collaborators</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                                    <tr>
                                        <th class="py-3">Name</th>
                                        <th class="py-3">Email</th>
                                        <th class="py-3">Workspace Role</th>
                                        <th class="py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                                    @forelse($teamMembers as $mem)
                                        <tr>
                                            <td class="py-3 font-semibold">{{ $mem->name }}</td>
                                            <td class="py-3 font-mono text-[10px] text-brand-gray">{{ $mem->email }}</td>
                                            <td class="py-3">
                                                <span class="rounded bg-brand-teal/15 px-2 py-0.5 text-[9px] text-brand-cyan font-bold uppercase border border-brand-teal/15">
                                                    {{ $mem->role }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-right">
                                                <form action="{{ route('portal.team.remove', $mem->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold">Revoke</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-brand-gray text-xs">No team guest invites sent.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: SETTINGS & SECURITY
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'settings'" class="space-y-6">
                <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                    <h2 class="text-xl font-bold text-brand-white">Settings &amp; Secure Configuration</h2>
                    <p class="text-xs text-brand-gray mt-1">Configure company credentials, API tokens, and monitor active sessions and trusted device parameters.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Profile and info settings -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Profile Workspace Metadata</h3>
                        
                        <form action="{{ route('portal.settings.update') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Corporate Account Name</label>
                                    <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Register Company</label>
                                    <input type="text" name="company_name" value="{{ cache('client_company_' . auth()->id(), 'E-Gov Group Ltd') }}" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Authorized Email Gateway (Unmodifiable)</label>
                                <input type="email" disabled value="{{ auth()->user()->email }}" class="w-full rounded-xl border border-brand-teal/10 bg-brand-dark-secondary px-4 py-3 text-xs text-brand-gray">
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center">
                                Save Settings Configuration
                            </button>
                        </form>
                    </div>

                    <!-- Right: Trusted devices and logins -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Trusted Sessions</h3>
                        
                        <div class="space-y-3 max-h-[300px] overflow-y-auto">
                            @foreach($devices as $dev)
                                <div class="border-b border-brand-teal/5 pb-3 last:border-0 last:pb-0 flex justify-between items-center text-xs text-brand-white">
                                    <div>
                                        <div class="font-bold flex items-center gap-1">
                                            <span>{{ str_contains(strtolower($dev->os), 'windows') ? '💻' : '📱' }}</span>
                                            {{ $dev->os ?? 'Unknown OS' }} • {{ $dev->browser ?? 'Unknown Browser' }}
                                        </div>
                                        <span class="text-[9px] text-brand-gray block">{{ $dev->ip_address }} • {{ $dev->location ?? 'Unknown Location' }}</span>
                                    </div>
                                    @if($dev->is_trusted)
                                        <span class="text-[8px] uppercase font-bold text-emerald-400">trusted</span>
                                    @else
                                        <span class="text-[8px] uppercase font-bold text-brand-cyan">session active</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: CLIENT REVIEW & RATING
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'reviews'" class="space-y-6" style="display: none;">
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-brand-cyan/10 rounded-full blur-2xl"></div>
                    <h2 class="text-xl font-bold text-brand-white">Client Reviews &amp; Trust Ratings</h2>
                    <p class="text-xs text-brand-gray mt-1 leading-relaxed">Share your experience working with Diwebs Tech. Your review and trust rating will be displayed directly on our homepage.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Write a Review Form -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Write a Review</h3>
                        
                        <form action="{{ route('portal.review.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Company / Organization</label>
                                    <input type="text" name="company_name" placeholder="e.g. E-Gov Group Ltd (Optional)" value="{{ cache('client_company_' . auth()->id()) }}" class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white focus:border-brand-cyan focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Trust Rating</label>
                                    <div class="flex items-center gap-1.5 py-2" x-data="{ currentRating: 5 }">
                                        <input type="hidden" name="rating" :value="currentRating">
                                        <template x-for="i in 5">
                                            <button type="button" @click="currentRating = i" class="text-2xl transition-transform hover:scale-110 focus:outline-none">
                                                <span :class="i <= currentRating ? 'text-brand-cyan' : 'text-brand-gray/30'">★</span>
                                            </button>
                                        </template>
                                        <span class="text-xs text-brand-gray ml-2 font-semibold font-mono" x-text="currentRating + ' / 5 Stars'"></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Your Review &amp; Comments</label>
                                <textarea name="comment" required rows="5" placeholder="Share your experience working with Diwebs Tech. Highlight our deliverables, communication, and software engineering capabilities..." class="w-full rounded-xl border border-brand-teal/20 bg-[#25282D]/60 px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none transition-all"></textarea>
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary font-bold text-xs py-3.5 hover:opacity-90 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                ⭐ Submit Public Review
                            </button>
                        </form>
                    </div>

                    <!-- Submitted Reviews Log -->
                    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">My Reviews</h3>
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
                            @forelse($reviews as $rev)
                                <div class="border-b border-brand-teal/5 pb-4 last:border-0 last:pb-0 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="text-[10px] text-brand-cyan font-bold block">{{ $rev->company_name ?? 'Individual Client' }}</span>
                                            <div class="flex text-xs text-brand-cyan mt-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="rounded bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 text-[9px] text-emerald-400 font-bold uppercase">{{ $rev->status }}</span>
                                    </div>
                                    <p class="text-[10px] text-brand-gray leading-relaxed font-light italic">"{{ $rev->comment }}"</p>
                                    <span class="text-[8px] text-brand-gray block">{{ $rev->created_at->format('M d, Y') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-brand-gray text-center py-4">No reviews submitted yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════
                 TAB: CLIENT REFERRAL PROGRAM
                 ════════════════════════════════════════ -->
            <div x-show="activeTab === 'referrals'" class="space-y-6" style="display: none;"
                 x-data="{ 
                     copySuccess: false,
                     referralLink: '{{ route('register') }}?ref={{ auth()->user()->referral_code }}',
                     copyLink() {
                         navigator.clipboard.writeText(this.referralLink);
                         this.copySuccess = true;
                         setTimeout(() => { this.copySuccess = false; }, 2000);
                     }
                 }">
                <div class="glass-card rounded-2xl p-6 relative overflow-hidden border border-brand-teal/15">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-brand-cyan/10 rounded-full blur-2xl"></div>
                    <h2 class="text-xl font-bold text-brand-white">Client Referral Program</h2>
                    <p class="text-xs text-brand-gray mt-1 leading-relaxed">Refer other businesses or organizations to Diwebs Tech. When they sign up and initiate a project, you'll earn a referral bonus credit!</p>
                </div>

                <!-- Referral Analytics Widgets -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Total Referrals</span>
                        <div class="text-2xl font-extrabold text-brand-white mt-1">{{ $referrals->count() }}</div>
                        <p class="text-[9px] text-brand-gray mt-1">Successfully registered accounts</p>
                    </div>
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Earned &amp; Paid Bonuses</span>
                        <div class="text-2xl font-extrabold text-emerald-400 mt-1">@money($totalBonusEarned)</div>
                        <p class="text-[9px] text-brand-gray mt-1">Settled and credited to billing</p>
                    </div>
                    <div class="glass-card rounded-2xl p-5 border border-brand-teal/10">
                        <span class="text-[10px] uppercase font-bold text-brand-gray">Pending Bonuses</span>
                        <div class="text-2xl font-extrabold text-brand-cyan mt-1">@money($pendingBonus)</div>
                        <p class="text-[9px] text-brand-gray mt-1">Awaiting administrator clearance</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left: Invitation center -->
                    <div class="lg:col-span-1 glass-card rounded-2xl p-6 border border-brand-teal/25 space-y-5">
                        <h3 class="text-sm font-bold text-brand-white">Your Referral Code</h3>
                        
                        <div class="p-4 bg-brand-dark-secondary/50 border border-brand-teal/15 rounded-xl text-center space-y-2">
                            <span class="text-[10px] text-brand-gray uppercase font-bold tracking-wider block">Share unique code</span>
                            <div class="text-xl font-mono font-extrabold text-brand-cyan tracking-wider py-1 bg-brand-dark rounded border border-brand-teal/10">
                                {{ auth()->user()->referral_code }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider">Referral Link</label>
                            <div class="flex gap-2">
                                <input type="text" readonly :value="referralLink" class="flex-grow rounded-lg border border-brand-teal/20 bg-[#25282D]/60 px-3 py-2 text-xs text-brand-gray font-mono focus:outline-none">
                                <button @click="copyLink()" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary px-3 font-bold text-xs hover:opacity-90 transition-all flex items-center justify-center">
                                    <span x-text="copySuccess ? 'Copied!' : 'Copy'"></span>
                                </button>
                            </div>
                            <p class="text-[9px] text-brand-gray leading-relaxed mt-1">Clients onboarding via this link will automatically have your referral code pre-filled during signup.</p>
                        </div>
                    </div>

                    <!-- Right: Referral logs -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-4">
                        <h3 class="text-sm font-bold text-brand-white">Referrals History</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="text-[10px] uppercase font-bold text-brand-gray border-b border-brand-teal/10">
                                    <tr>
                                        <th class="py-3">Invited Client</th>
                                        <th class="py-3">Email Address</th>
                                        <th class="py-3">Date Joined</th>
                                        <th class="py-3">Estimated Bonus</th>
                                        <th class="py-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-teal/5 text-brand-white">
                                    @forelse($referrals as $ref)
                                        <tr>
                                            <td class="py-3 font-semibold">{{ $ref->referee->name }}</td>
                                            <td class="py-3 font-mono text-[10px] text-brand-gray">{{ $ref->referee->email }}</td>
                                            <td class="py-3">{{ $ref->created_at->format('M d, Y') }}</td>
                                            <td class="py-3 font-mono font-bold text-brand-cyan">@money($ref->bonus_amount)</td>
                                            <td class="py-3 text-right">
                                                <span class="rounded px-2.5 py-0.5 text-[9px] uppercase font-bold 
                                                    @if($ref->status === 'paid') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                                    @elseif($ref->status === 'approved') bg-brand-teal/10 text-brand-cyan border border-brand-teal/20
                                                    @elseif($ref->status === 'void') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                                    @else bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse
                                                    @endif">
                                                    {{ $ref->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-6 text-center text-brand-gray text-xs">No referral history logged. Send your code or link to invite clients!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ════════════════════════════════════════
         MODAL: DIGITAL CONTRACT SIGNING
         ════════════════════════════════════════ -->
    <div x-show="showSignatureModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark-secondary/80 backdrop-blur-md"
         style="display:none;"
         x-transition>
        <div class="glass-card rounded-2xl border border-brand-cyan/20 max-w-2xl w-full p-6 space-y-4" @click.away="showSignatureModal = false">
            <div class="flex justify-between items-center border-b border-brand-teal/10 pb-3">
                <h3 class="text-sm font-bold text-brand-white uppercase tracking-wider" x-text="activeContractTitle"></h3>
                <button @click="showSignatureModal = false" class="text-brand-gray hover:text-brand-white text-lg">×</button>
            </div>

            <div class="h-60 overflow-y-auto p-4 bg-brand-dark-secondary/50 border border-brand-teal/10 rounded-xl text-xs text-brand-gray leading-relaxed whitespace-pre-line" x-text="activeContractContent"></div>

            <form :action="`/portal/project/${activeContractId}/sign`" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] text-brand-gray font-bold uppercase tracking-wider mb-2">Signatory Authorized Full Name (E-Sign Validation)</label>
                    <input type="text" name="signature_name" required x-model="signatureName" placeholder="Type your name to digitally seal this agreement" class="w-full rounded-xl border border-brand-teal/20 bg-brand-dark px-4 py-3 text-xs text-brand-white placeholder-brand-gray/30 focus:border-brand-cyan focus:outline-none">
                </div>

                <div class="text-[10px] text-brand-gray leading-relaxed">
                    By typing your name above, you acknowledge this is a binding, digital signature. We record the legal timestamps, browser fingerprint agent, and server IP address.
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showSignatureModal = false" class="rounded-xl border border-brand-teal/20 hover:bg-white/5 px-4 py-2.5 text-xs text-brand-white">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary px-6 py-2.5 text-xs font-bold hover:opacity-90 transition-all">Digitally Seal Agreement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ════════════════════════════════════════
         AI CLIENT ASSISTANT FLOATING TOOL
         ════════════════════════════════════════ -->
    <div class="fixed bottom-24 right-6 z-50">
        <!-- Floating bubble trigger -->
        <button @click="aiOpen = !aiOpen" class="h-14 w-14 rounded-full bg-gradient-to-r from-brand-teal to-brand-cyan shadow-xl hover:scale-105 transition-all flex items-center justify-center text-2xl relative select-none">
            🤖
            <span class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full bg-rose-500 border-2 border-[#1E2125] animate-ping"></span>
        </button>

        <!-- Chat assistant panel -->
        <div x-show="aiOpen" 
             style="display:none;"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-10 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100 scale-100"
             x-transition:leave-end="translate-y-10 opacity-0 scale-95"
             class="absolute bottom-20 right-0 w-80 md:w-96 glass-card rounded-2xl border border-brand-cyan/25 flex flex-col h-[400px] shadow-2xl overflow-hidden">
            
            <!-- Head -->
            <div class="bg-gradient-to-r from-brand-teal/20 to-brand-cyan/20 px-4 py-3 border-b border-brand-teal/15 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🤖</span>
                    <div>
                        <h4 class="text-xs font-bold text-brand-white leading-none">Diwebs Client AI Assistant</h4>
                        <span class="text-[8px] text-brand-cyan font-semibold tracking-wider uppercase">project telemetry engine</span>
                    </div>
                </div>
                <button @click="aiOpen = false" class="text-brand-gray hover:text-brand-white text-lg">×</button>
            </div>

            <!-- Messages area -->
            <div class="flex-grow p-4 overflow-y-auto space-y-3" x-ref="aiChatContainer">
                <template x-for="(msg, i) in aiMessages" :key="i">
                    <div :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'" class="flex">
                        <div :class="msg.sender === 'user' ? 'bg-brand-cyan/15 border border-brand-cyan/20 text-brand-white' : 'bg-[#25282D] border border-white/5 text-brand-white'" class="rounded-2xl p-3 max-w-[85%] text-[11px] leading-relaxed select-text whitespace-pre-line" x-html="msg.text"></div>
                    </div>
                </template>
                <div x-show="aiSending" class="text-left" style="display:none;">
                    <div class="inline-flex items-center gap-1.5 bg-[#25282D] border border-white/5 rounded-2xl px-3 py-2 text-[10px] text-brand-gray">
                        <span class="animate-pulse">Analyzing project metrics...</span>
                    </div>
                </div>
            </div>

            <!-- Sender input -->
            <div class="p-3 border-t border-brand-teal/15 bg-brand-dark/40">
                <div class="flex gap-2">
                    <input type="text" x-model="aiInput" @keydown.enter="askAiAssistant()" placeholder="Ask me about progress or invoices..." class="flex-grow rounded-lg border border-brand-teal/20 bg-brand-dark px-3 py-2 text-xs text-brand-white focus:border-brand-cyan focus:outline-none">
                    <button @click="askAiAssistant()" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan text-brand-dark-secondary px-4 font-bold text-xs hover:opacity-90 transition-all flex items-center justify-center">
                        Ask
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
