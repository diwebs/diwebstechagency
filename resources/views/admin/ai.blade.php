@extends('layouts.admin')

@section('title', 'Intelligent AI Settings - Admin Control Center')

@section('admin_content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-white">Intelligent AI Prompt Engineering</h1>
        <p class="text-sm text-brand-gray mt-1">Fine-tune internal AI model parameters, adjust base system prompt guidelines, and track token resources.</p>
    </div>

    <!-- AI metrics overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Model Status</span>
            <strong class="block text-xl font-bold text-emerald-400 mt-1">Active (LLaMA-3 / Gemini Pro)</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">AI Accuracy Rate</span>
            <strong class="block text-xl font-bold text-brand-white mt-1">{{ $aiSettings['accuracy_rate'] }}</strong>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-brand-teal/15">
            <span class="block text-[10px] uppercase font-semibold text-brand-cyan tracking-wider">Total Tokens Consumed</span>
            <strong class="block text-xl font-bold text-brand-white mt-1">{{ $aiSettings['tokens_consumed'] }}</strong>
        </div>
    </div>

    <!-- Configuration Form -->
    <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
        <h3 class="text-sm font-semibold uppercase text-brand-cyan tracking-wider mb-5 border-b border-brand-teal/10 pb-3">Model Hyperparameters</h3>
        
        <form action="{{ route('admin.ai.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Temperature -->
                <div>
                    <label class="block text-xs font-bold text-brand-white uppercase mb-2">Temperature (Creativity)</label>
                    <input type="number" 
                           step="0.1" 
                           min="0" 
                           max="2" 
                           name="temperature" 
                           value="{{ $aiSettings['temperature'] }}" 
                           required 
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <span class="block text-[10px] text-brand-gray mt-1">Lower values are more deterministic; higher values are more creative.</span>
                </div>

                <!-- Max Tokens -->
                <div>
                    <label class="block text-xs font-bold text-brand-white uppercase mb-2">Max Response Tokens</label>
                    <input type="number" 
                           name="max_tokens" 
                           value="{{ $aiSettings['max_tokens'] }}" 
                           required 
                           class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 px-4 py-2.5 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none transition-all">
                    <span class="block text-[10px] text-brand-gray mt-1">Controls the length of individual output generations.</span>
                </div>
            </div>

            <!-- Base system prompt text -->
            <div>
                <label class="block text-xs font-bold text-brand-white uppercase mb-2">Ecosystem Base System Prompt</label>
                <textarea name="prompt" 
                          rows="8" 
                          required 
                          class="w-full rounded-lg bg-brand-dark-secondary border border-brand-teal/15 p-4 text-xs text-brand-white focus:border-brand-cyan/60 focus:outline-none font-mono leading-relaxed transition-all">{{ $aiSettings['prompt'] }}</textarea>
                <span class="block text-[10px] text-brand-gray mt-1">This prompt acts as the root cognitive guidelines for all chatbot and LMS tutorial nodes.</span>
            </div>

            <div class="flex justify-end pt-4 border-t border-brand-teal/10">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-brand-teal to-brand-cyan px-6 py-2.5 text-xs font-bold text-brand-dark-secondary shadow hover:opacity-90 transition-all">
                    Save AI Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
