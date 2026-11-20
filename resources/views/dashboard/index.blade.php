@extends('layouts.app')

@section('title', 'CargoTrace - Operational Intelligence Dashboard')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)] bg-slate-100">

    <!-- ========================================================================= -->
    <!-- 1. DARK LEFT SIDEBAR (PERSIS SEPERTI FOTO LAYAR TV DOSEN) -->
    <!-- ========================================================================= -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 hidden md:flex flex-col justify-between border-r border-slate-800 select-none">
        <div>
            <!-- Header Brand Sidebar -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800 space-x-3">
                <div class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-slate-950 font-black text-sm shadow-md shadow-sky-500/30">
                    <svg class="w-5 h-5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <span class="font-extrabold text-lg text-white tracking-tight">CargoTrace</span>
            </div>

            <!-- Navigasi Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-semibold">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl bg-slate-800 text-sky-400 font-bold border border-slate-700/60 shadow-sm transition">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="#recent-shipments" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Shipments</span>
                </a>

                <a href="{{ url('/') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <span>Live Tracking</span>
                </a>

                <a href="{{ route('dashboard', ['delay_status' => 'DELAYED']) }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Exceptions</span>
                    @if ($stats['open_exceptions'] > 0)
                        <span class="ml-auto px-1.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 text-[10px] font-extrabold">{{ $stats['open_exceptions'] }}</span>
                    @endif
                </a>

                <a href="#carriers" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Carriers</span>
                </a>

                <a href="{{ url('/api/v1/public/track/CRG-2026-0001') }}" target="_blank" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-sky-300 hover:bg-slate-800/60 transition">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>REST API Docs</span>
                </a>
            </nav>
        </div>

        <!-- Footer Sidebar: User Info & Role -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center space-x-3 p-2 bg-slate-800/70 rounded-xl border border-slate-700/50">
                <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-xs uppercase">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ $user->name }}</p>
                    <p class="text-[10px] text-sky-400 uppercase font-mono tracking-wider">{{ $user->role }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ========================================================================= -->
    <!-- 2. MAIN CONTENT AREA -->
    <!-- ========================================================================= -->
    <main class="flex-grow p-4 sm:p-6 lg:p-8 space-y-6 overflow-x-hidden">

        <!-- TOP BAR: HEADER, PENCARIAN & ACTION BUTTON -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->company_name ?? 'Global Maritime Visibility System' }} &bull; Live Telemetry</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Box di Top Bar (seperti di foto TV) -->
                <form action="{{ route('dashboard') }}" method="GET" class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Q Search shipment..." 
                        class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-sm w-48 sm:w-60 uppercase font-medium"
                    >
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>

                <!-- Tombol Tambah Kargo Baru (Khusus Admin / Operator) -->
                @if($user->isAdmin() || $user->isOperator())
                    <a href="{{ route('shipments.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Tambah Kargo</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. TOP 5 METRIC CARDS (PERSIS SEPERTI DI FOTO LAYAR TV) -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Card 1: Total Shipments -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Shipments</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>

            <!-- Card 2: In Transit -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">In Transit</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-sky-600 mt-1">{{ $stats['in_transit'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Delivered -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Delivered</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $stats['delivered'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <!-- Card 4: Delayed -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Delayed</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['delayed'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Card 5: Open Exceptions -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between col-span-2 lg:col-span-1">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Open Exceptions</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">{{ $stats['open_exceptions'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 4. MIDDLE SECTION: 3 WIDGET VISUAL (BAR CHART, DONUT CHART & MINI MAP) -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- WIDGET 1: BAR CHART (Shipments by Status - Slide 10 & 14) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-sm font-bold text-slate-900">Shipments by Status</h2>
                        <span class="text-[11px] font-extrabold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $stats['total'] }} Total</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-4">Overview of shipments by operational lifecycle</p>
                    <div class="relative h-48 w-full">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span><span>In Transit: {{ $stats['in_transit'] }}</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span>Delivered: {{ $stats['delivered'] }}</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span><span>Delayed: {{ $stats['delayed'] }}</span></span>
                </div>
            </div>

            <!-- WIDGET 2: DONUT CHART (Delay Rate by Carrier - Slide 10 & 14) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-sm font-bold text-slate-900">Delay Rate by Carrier</h2>
                        <span class="text-[11px] font-extrabold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-lg">{{ $avgDelayRate }}% Avg Rate</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-4">Distribution of delay occurrences across fleet</p>
                    <div class="relative h-48 w-full flex items-center justify-center">
                        <canvas id="delayChart"></canvas>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-1 text-[11px] text-slate-500 font-medium">
                    @foreach (array_slice($carrierStats, 0, 4) as $cs)
                        <div class="flex items-center justify-between">
                            <span class="truncate max-w-[90px]">{{ $cs['name'] }}:</span>
                            <strong class="text-slate-800">{{ $cs['pct'] }}%</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- WIDGET 3: MINI LEAFLET MAP (Shipment Map) -->
            <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-sky-500 animate-ping"></span>
                            <h2 class="text-sm font-bold text-slate-900">Shipment Map</h2>
                        </div>
                        <span class="text-[10px] font-mono text-slate-400">Leaflet.js</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-3">Real-time shipment routes and vessel coordinates</p>
                    <div id="dashboardMap" class="h-48 w-full rounded-xl border border-slate-200 shadow-inner z-10"></div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span><span>Origin Port</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span><span>In Sea Route</span></span>
                    <span class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span><span>Destination</span></span>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- 5. BOTTOM SECTION: RECENT SHIPMENTS TABLE (PERSIS SEPERTI DI FOTO LAYAR TV) -->
        <!-- ========================================================================= -->
        <div id="recent-shipments" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Shipments</h2>
                    <p class="text-xs text-slate-400">Live operational overview of all maritime consignments</p>
                </div>

                <!-- Filter Controls Mini -->
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                    <select name="status" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 font-medium">
                        <option value="">Semua Status</option>
                        <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>In Transit</option>
                        <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
                    </select>

                    <select name="delay_status" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-700 font-medium">
                        <option value="">Semua SLA</option>
                        <option value="ON_TIME" {{ request('delay_status') === 'ON_TIME' ? 'selected' : '' }}>On Time</option>
                        <option value="DELAYED" {{ request('delay_status') === 'DELAYED' ? 'selected' : '' }}>Delayed</option>
                    </select>

                    @if(request()->hasAny(['status', 'delay_status', 'search']))
                        <a href="{{ route('dashboard') }}" class="px-2 py-1.5 text-slate-400 hover:text-rose-600 font-semibold">Reset</a>
                    @endif
                </form>
            </div>

            <!-- TABEL DATA PENGIRIMAN -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-400 uppercase font-bold text-[10px] border-b border-slate-200 tracking-wider">
                            <th class="py-3 px-5">Shipment No</th>
                            <th class="py-3 px-5">Customer</th>
                            <th class="py-3 px-5">Carrier</th>
                            <th class="py-3 px-5">Origin</th>
                            <th class="py-3 px-5">Destination</th>
                            <th class="py-3 px-5">ETA</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse ($shipments as $s)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Shipment No -->
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                                    <a href="{{ route('tracking.show', $s->tracking_number) }}" class="text-sky-600 hover:underline">
                                        {{ $s->tracking_number }}
                                    </a>
                                </td>

                                <!-- Customer -->
                                <td class="py-3.5 px-5 text-slate-800 font-semibold">
                                    {{ $s->customer->name }}
                                </td>

                                <!-- Carrier -->
                                <td class="py-3.5 px-5">
                                    <span class="text-slate-800">{{ $s->carrier->name }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 ml-1">{{ $s->carrier->code }}</span>
                                </td>

                                <!-- Origin -->
                                <td class="py-3.5 px-5 text-slate-600">
                                    {{ $s->origin->city }}, {{ $s->origin->country }}
                                </td>

                                <!-- Destination -->
                                <td class="py-3.5 px-5 text-slate-900 font-semibold">
                                    {{ $s->destination->city }}, {{ $s->destination->country }}
                                </td>

                                <!-- ETA -->
                                <td class="py-3.5 px-5 text-slate-600 font-mono text-[11px]">
                                    {{ \Carbon\Carbon::parse($s->estimated_arrival)->format('Y-m-d') }}
                                </td>

                                <!-- Status Badge (Slide 06) -->
                                <td class="py-3.5 px-5">
                                    @if ($s->current_status === 'DELIVERED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            DELIVERED
                                        </span>
                                    @elseif ($s->delay_status === 'DELAYED')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 flex items-center space-x-1 max-w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            <span>DELAYED (+{{ round($s->delay_duration_minutes / 60) }}h)</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 flex items-center space-x-1 max-w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                                            <span>IN TRANSIT</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td class="py-3.5 px-5 text-right">
                                    <a href="{{ route('tracking.show', $s->tracking_number) }}" class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 font-bold text-xs transition">
                                        <span>View</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    No shipments matching criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($shipments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $shipments->links() }}
                </div>
            @endif

        </div>

    </main>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // =========================================================================
    // 1. INSIALISASI GRAFIK BATANG (Chart.js Bar Chart - Shipments by Status)
    // =========================================================================
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: ['In Transit', 'Delivered', 'Delayed', 'Exceptions'],
            datasets: [{
                label: 'Shipments',
                data: [
                    {{ $stats['in_transit'] }}, 
                    {{ $stats['delivered'] }}, 
                    {{ $stats['delayed'] }}, 
                    {{ $stats['open_exceptions'] }}
                ],
                backgroundColor: [
                    '#0284c7', // Sky Blue
                    '#10b981', // Emerald Green
                    '#f59e0b', // Amber Orange
                    '#f43f5e'  // Rose Red
                ],
                borderRadius: 8,
                barThickness: 24,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 10 } },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: { font: { size: 10, weight: 'bold' } },
                    grid: { display: false }
                }
            }
        }
    });

    // =========================================================================
    // 2. INSIALISASI GRAFIK DONAT (Chart.js Donut Chart - Delay Rate by Carrier)
    // =========================================================================
    const ctxDelay = document.getElementById('delayChart').getContext('2d');
    new Chart(ctxDelay, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($carrierStats as $cs) '{{ $cs["name"] }}', @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($carrierStats as $cs) {{ $cs["pct"] > 0 ? $cs["pct"] : 15 }}, @endforeach
                ],
                backgroundColor: [
                    '#0284c7', '#38bdf8', '#f59e0b', '#64748b', '#a855f7'
                ],
                borderWidth: 2,
                cutout: '72%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // =========================================================================
    // 3. INSIALISASI MINI PETA LEAFLET (Leaflet.js Map)
    // =========================================================================
    const map = L.map('dashboardMap', { zoomControl: false }).setView([-2.0, 108.0], 4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const waypoints = [
        @foreach($mapShipments as $ms)
            @if($ms->origin && $ms->destination)
                {
                    from: [{{ $ms->origin->latitude }}, {{ $ms->origin->longitude }}],
                    to: [{{ $ms->destination->latitude }}, {{ $ms->destination->longitude }}],
                    name: '{{ $ms->tracking_number }}'
                },
            @endif
        @endforeach
    ];

    const latLngs = [];
    waypoints.forEach(wp => {
        latLngs.push(wp.from);
        latLngs.push(wp.to);

        L.circleMarker(wp.from, { color: '#10b981', radius: 4 }).addTo(map);
        L.circleMarker(wp.to, { color: '#f43f5e', radius: 4 }).addTo(map);

        L.polyline([wp.from, wp.to], {
            color: '#0284c7',
            weight: 2,
            dashArray: '4, 6',
            opacity: 0.7
        }).addTo(map);
    });

    if (latLngs.length > 0) {
        map.fitBounds(L.latLngBounds(latLngs), { padding: [20, 20] });
    }
});
</script>
@endpush
