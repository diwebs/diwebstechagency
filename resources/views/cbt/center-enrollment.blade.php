@extends('layouts.cbt')

@section('title', 'Become a Center Partner - Diwebs CBT')

@section('cbt_content')
<div class="max-w-2xl mx-auto space-y-8" x-data="enrollmentFormState()">
    
    <!-- Top Header -->
    <div class="text-center">
        <span class="text-4xl">🏢</span>
        <h1 class="text-2xl font-bold text-brand-white mt-3">Become a CBT Center Partner</h1>
        <p class="text-xs text-brand-gray mt-1">Enroll your physical labs in the Diwebs testing network. Access proctor toolkits and revenue payouts.</p>
    </div>

    <!-- Active Enrollment Check -->
    @if($enrollment)
        <div class="glass-card rounded-3xl p-8 border border-brand-teal/20 text-center space-y-4">
            <span class="text-3xl">⏳</span>
            <h2 class="text-base font-bold text-brand-white">Application Status: <span class="text-brand-cyan uppercase">{{ $enrollment->status }}</span></h2>
            <p class="text-xs text-brand-gray leading-relaxed">
                Your infrastructure questionnaire for <strong class="text-brand-white">{{ $enrollment->organization_name }}</strong> is currently under review by our operations verification team.
            </p>
            <div class="p-4 rounded-xl border border-brand-teal/5 bg-brand-dark-secondary/50 text-left text-xs text-brand-gray space-y-2 max-w-sm mx-auto">
                <div class="flex justify-between"><span>Center Type:</span> <strong class="text-brand-cyan uppercase">{{ str_replace('_', ' ', $enrollment->center_type) }}</strong></div>
                <div class="flex justify-between"><span>Systems Count:</span> <strong class="text-brand-white">{{ $enrollment->systems_count }}</strong></div>
                <div class="flex justify-between"><span>Internet Latency:</span> <strong class="text-brand-white">{{ $enrollment->internet_quality }}</strong></div>
                <div class="flex justify-between"><span>Power Backup:</span> <strong class="text-brand-white">{{ $enrollment->power_backup }}</strong></div>
            </div>
        </div>
    @else
        <!-- Multi-step questionnaire -->
        <div class="glass-card rounded-3xl p-8 border border-brand-teal/15 space-y-6">
            
            <!-- Progress Bar -->
            <div>
                <div class="flex justify-between text-[10px] text-brand-gray uppercase font-bold tracking-wider mb-2">
                    <span x-text="'Questionnaire Step ' + currentStep + ' of 6'"></span>
                    <span x-text="Math.round(((currentStep - 1) / 5) * 100) + '%'"></span>
                </div>
                <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-brand-teal to-brand-cyan transition-all duration-300" :style="'width: ' + (((currentStep - 1) / 5) * 100) + '%'"></div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('cbt.center-enrollment.store') }}" method="POST" id="enrollmentForm">
                @csrf
                
                <!-- Q1: Center Style -->
                <div x-show="currentStep === 1" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 1: What type of center do you want?</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['jamb_style', 'JAMB-style Center', 'Large-scale synchronized national entrance tests.'],
                            ['waec_style', 'WAEC-style Center', 'Regional terminal examination labs.'],
                            ['school_style', 'School Center', 'In-house university and academic exam blocks.'],
                            ['corporate_style', 'Corporate Testing Center', 'Professional certifications and employee screening hubs.'],
                            ['government_style', 'Government Testing Center', 'Public civil service recruitment centers.']
                        ] as [$val, $title, $desc])
                            <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                <input type="radio" name="center_type" value="{{ $val }}" required x-model="formData.center_type" class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary">
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-brand-white">{{ $title }}</span>
                                    <span class="block text-[10px] text-brand-gray mt-0.5">{{ $desc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Q2: Organization Name -->
                <div x-show="currentStep === 2" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 2: Organization Name?</h3>
                    <div class="space-y-2">
                        <label class="block text-[10px] text-brand-gray uppercase font-bold tracking-wider">Legal Entity or Hub Name:</label>
                        <input type="text" name="organization_name" x-model="formData.organization_name" placeholder="Enter company or school name" class="w-full rounded-xl border border-brand-teal/20 bg-[#1E2125] px-4 py-3 text-xs text-brand-white placeholder-[#94A3B8]/35 focus:border-brand-cyan focus:outline-none transition-colors" :required="currentStep === 2">
                    </div>
                </div>

                <!-- Q3: Physical Location -->
                <div x-show="currentStep === 3" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 3: Do you own a physical location?</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['yes', 'Yes', 'Facility is built and ready for immediate deployment.'],
                            ['no', 'No', 'Currently searching for physical workspace sites.'],
                            ['in_progress', 'In Progress', 'Facility is under lease agreement or renovation.']
                        ] as [$val, $title, $desc])
                            <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                <input type="radio" name="has_physical_location" value="{{ $val }}" required x-model="formData.has_physical_location" class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary">
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-brand-white">{{ $title }}</span>
                                    <span class="block text-[10px] text-brand-gray mt-0.5">{{ $desc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Q4: System Count -->
                <div x-show="currentStep === 4" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 4: How many systems available?</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['10-20', '10 – 20 Workstations', 'Ideal for micro local certifications.'],
                            ['20-50', '20 – 50 Workstations', 'Medium-scale regional workspace center.'],
                            ['50-100', '50 – 100 Workstations', 'Standard academic exam center capacity.'],
                            ['100+', '100+ Workstations', 'Enterprise certified national hub size.']
                        ] as [$val, $title, $desc])
                            <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                <input type="radio" name="systems_count" value="{{ $val }}" required x-model="formData.systems_count" class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary">
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-brand-white">{{ $title }}</span>
                                    <span class="block text-[10px] text-brand-gray mt-0.5">{{ $desc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Q5: Internet Quality -->
                <div x-show="currentStep === 5" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 5: Internet quality?</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['basic', 'Basic Broadband (ADSL)', 'Moderate latency, single line backup.'],
                            ['stable', 'Stable Fiber Optic Connection', 'Low latency, dual line network backup.'],
                            ['enterprise', 'Enterprise Dual-Link Fiber Grid', 'Sub-15ms latencies, automated redundancy failovers.']
                        ] as [$val, $title, $desc])
                            <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                <input type="radio" name="internet_quality" value="{{ $val }}" required x-model="formData.internet_quality" class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary">
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-brand-white">{{ $title }}</span>
                                    <span class="block text-[10px] text-brand-gray mt-0.5">{{ $desc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Q6: Power Backup -->
                <div x-show="currentStep === 6" class="space-y-4">
                    <h3 class="text-sm font-extrabold uppercase text-brand-cyan tracking-wider">Question 6: Power backup available?</h3>
                    <div class="space-y-2">
                        @foreach([
                            ['no', 'No Backup Power', 'Standard utility grid dependency.'],
                            ['generator', 'Generator Backup', 'Dedicated backup generator.'],
                            ['inverter', 'Inverter battery bank', 'Instant switch-over backup battery power.'],
                            ['full_redundancy', 'Full Redundancy Grid', 'Industrial UPS with automated secondary generators.']
                        ] as [$val, $title, $desc])
                            <label class="flex items-center gap-4 rounded-xl border border-brand-teal/10 bg-brand-dark-secondary/40 p-4 cursor-pointer hover:border-brand-cyan/40 hover:bg-brand-dark-secondary/80 transition-all">
                                <input type="radio" name="power_backup" value="{{ $val }}" required x-model="formData.power_backup" class="h-4 w-4 border-brand-teal/30 text-brand-cyan focus:ring-brand-cyan bg-brand-dark-secondary">
                                <div class="text-left">
                                    <span class="block text-xs font-bold text-brand-white">{{ $title }}</span>
                                    <span class="block text-[10px] text-brand-gray mt-0.5">{{ $desc }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation buttons -->
                <div class="mt-8 border-t border-brand-teal/10 pt-6 flex justify-between gap-4">
                    <button type="button" 
                            @click="prevStep()" 
                            x-show="currentStep > 1" 
                            class="rounded-xl border border-brand-teal/20 text-center py-3 px-6 text-xs font-bold text-brand-gray hover:text-brand-white transition-all cursor-pointer">
                        Previous Question
                    </button>
                    
                    <button type="button" 
                            @click="nextStep()" 
                            x-show="currentStep < 6" 
                            class="ml-auto rounded-xl bg-gradient-to-r from-brand-teal to-brand-cyan py-3 px-6 text-xs font-bold text-brand-dark-secondary transition-all cursor-pointer">
                        Next Question
                    </button>

                    <button type="submit" 
                            x-show="currentStep === 6" 
                            class="ml-auto rounded-xl bg-emerald-500 hover:bg-emerald-600 py-3 px-6 text-xs font-extrabold text-brand-dark-secondary transition-all cursor-pointer">
                        Submit Questionnaire
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
function enrollmentFormState() {
    return {
        currentStep: 1,
        formData: {
            center_type: '',
            organization_name: '',
            has_physical_location: '',
            systems_count: '',
            internet_quality: '',
            power_backup: ''
        },

        nextStep() {
            // Validate step selections
            if (this.currentStep === 1 && !this.formData.center_type) return alert('Please select a Center Type.');
            if (this.currentStep === 2 && !this.formData.organization_name) return alert('Please enter your Organization Name.');
            if (this.currentStep === 3 && !this.formData.has_physical_location) return alert('Please select your location status.');
            if (this.currentStep === 4 && !this.formData.systems_count) return alert('Please select systems count.');
            if (this.currentStep === 5 && !this.formData.internet_quality) return alert('Please select internet quality.');
            
            if (this.currentStep < 6) {
                this.currentStep++;
            }
        },

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        }
    };
}
</script>
@endsection
