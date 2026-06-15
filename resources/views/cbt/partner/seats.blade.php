@extends('layouts.cbt')

@section('title', 'Seat Terminal Visualizer - Diwebs CBT')

@section('cbt_content')
<div class="space-y-8" x-data="{ selectedDevice: null }">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cbt.partner.centers') }}" class="text-xs text-brand-cyan hover:underline">&larr; Back to Centers</a>
            </div>
            <h1 class="text-2xl font-bold text-brand-white mt-1">Seat & Terminal Visualizer</h1>
            <p class="text-sm text-brand-gray mt-0.5">Real-time status check for Lagos CBT Center Workstations.</p>
        </div>
        
        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-4 bg-brand-dark-secondary/50 border border-brand-teal/10 px-4 py-2.5 rounded-xl text-xs">
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-emerald-500"></span> Online</span>
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-amber-500"></span> Testing</span>
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-rose-500"></span> Offline</span>
            <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-brand-teal"></span> Selected</span>
        </div>
    </div>

    <!-- Seat Dashboard Grid & Details Sidebar Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Seats Interactive Grid -->
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15">
                <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-3 mb-6">Interactive Floor Layout (Terminal Grids)</h3>
                
                <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 gap-4">
                    @forelse($devices as $device)
                        @php
                            $statusColor = 'bg-rose-500/10 border-rose-500/40 text-rose-400';
                            if($device->system_status === 'online') {
                                $statusColor = 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400';
                            } elseif($device->system_status === 'testing') {
                                $statusColor = 'bg-amber-500/10 border-amber-500/40 text-amber-400';
                            }
                        @endphp
                        
                        <div 
                            @click="selectedDevice = {{ json_encode($device) }}"
                            :class="selectedDevice && selectedDevice.id === {{ $device->id }} ? 'ring-2 ring-brand-cyan border-brand-cyan' : ''"
                            class="cursor-pointer border rounded-xl p-3 text-center transition-all select-none hover:scale-105 hover:bg-brand-teal/5 {{ $statusColor }}"
                        >
                            <span class="block text-[10px] font-bold opacity-80 mb-1">SEAT</span>
                            <span class="block text-sm font-extrabold font-mono">{{ str_replace('SEAT-', '', $device->seat_number) }}</span>
                            
                            <div class="flex items-center justify-center gap-1 mt-2 text-[8px] opacity-75">
                                <span>⚡ {{ $device->cpu_usage }}%</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-brand-gray text-xs border border-dashed border-brand-teal/20 rounded-xl">
                            No terminal devices mapped for this location.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Telemetry Diagnostics Inspector Panel -->
        <div class="space-y-6">
            <h3 class="text-xs font-extrabold uppercase text-brand-cyan tracking-wider border-b border-brand-teal/10 pb-2">Hardware Telemetry</h3>
            
            <div class="glass-card rounded-2xl p-6 border border-brand-teal/15 space-y-6">
                <!-- If selected -->
                <div x-show="selectedDevice" class="space-y-4" style="display: none;">
                    <div class="flex justify-between items-center">
                        <h4 class="text-sm font-bold text-brand-white" x-text="selectedDevice ? selectedDevice.seat_number : ''"></h4>
                        <span class="inline-flex items-center gap-1 rounded px-2.5 py-0.5 text-[10px] font-bold text-brand-white" 
                              :class="selectedDevice && selectedDevice.system_status === 'online' ? 'bg-emerald-500/25 text-emerald-400' : 'bg-amber-500/25 text-amber-400'"
                              x-text="selectedDevice ? selectedDevice.system_status.toUpperCase() : ''">
                        </span>
                    </div>

                    <div class="space-y-4 border-t border-brand-teal/5 pt-4 text-xs">
                        <div class="flex justify-between">
                            <span class="text-brand-gray">Device Model:</span>
                            <strong class="text-brand-white" x-text="selectedDevice ? selectedDevice.device_name : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-brand-gray">Local IP:</span>
                            <strong class="text-brand-white font-mono" x-text="selectedDevice ? selectedDevice.ip_address : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-brand-gray">Power Status:</span>
                            <strong class="text-brand-white" x-text="selectedDevice ? 'AC Online (' + selectedDevice.battery_level + '%)' : ''"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-brand-gray">Webcam Node:</span>
                            <strong x-text="selectedDevice && selectedDevice.webcam_status === 'active' ? '🟢 Active Feed' : '🔴 Inactive'"
                                    :class="selectedDevice && selectedDevice.webcam_status === 'active' ? 'text-emerald-400' : 'text-rose-400'"></strong>
                        </div>
                    </div>

                    <!-- Usage meters -->
                    <div class="space-y-3 pt-2">
                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px]">
                                <span class="text-brand-gray">CPU LOAD</span>
                                <span class="text-brand-cyan" x-text="selectedDevice ? selectedDevice.cpu_usage + '%' : '0%'"></span>
                            </div>
                            <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden">
                                <div class="h-full bg-brand-cyan transition-all duration-300" :style="'width: ' + (selectedDevice ? selectedDevice.cpu_usage : 0) + '%'"></div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px]">
                                <span class="text-brand-gray">RAM UTILIZATION</span>
                                <span class="text-purple-400" x-text="selectedDevice ? selectedDevice.ram_usage + '%' : '0%'"></span>
                            </div>
                            <div class="h-1.5 w-full bg-brand-dark-secondary rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 transition-all duration-300" :style="'width: ' + (selectedDevice ? selectedDevice.ram_usage : 0) + '%'"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-brand-teal/5 pt-4 flex gap-2">
                        <button class="flex-1 rounded bg-brand-teal/15 hover:bg-brand-teal/25 py-2 text-xs font-bold text-brand-cyan transition-all border border-brand-teal/30">
                            🔄 Ping Node
                        </button>
                        <button class="flex-1 rounded bg-rose-500/10 hover:bg-rose-500/20 py-2 text-xs font-bold text-rose-400 transition-all border border-rose-500/30">
                            🔌 Lock Shell
                        </button>
                    </div>
                </div>

                <!-- If not selected -->
                <div x-show="!selectedDevice" class="text-center py-12 text-brand-gray text-xs">
                    <span class="text-3xl block mb-2">🖱️</span>
                    Click any workstation slot on the map layout to inspect telemetry diagnostics.
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
