@extends('layouts.app')

@section('title', 'Dashboard Operasional - CargoTrace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- HEADER DASHBOARD & IDENTITAS PENGGUNA -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Portal Operasional Logistik</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $user->company_name ?? 'CargoTrace Global Logistics' }} &bull; Sesi Aktif: {{ now()->format('d M Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold tracking-wider uppercase 
                {{ $user->isAdmin() ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : ($user->isOperator() ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200') }}">
                Hak Akses: {{ $user->role }}
            </span>
        </div>
    </div>

    <!-- 5 KARTU METRIK STATISTIK OPERASIONAL (SLIDE 10) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total Kargo</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Kontainer Terdata</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600 mb-1">Dalam Pelayaran</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-sky-600">{{ $stats['in_transit'] }}</p>
            <span class="text-[10px] text-sky-500 mt-1 block">Status In-Transit</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Berhasil Tiba</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['delivered'] }}</p>
            <span class="text-[10px] text-emerald-500 mt-1 block">Status Delivered</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600 mb-1">Tertunda (Delayed)</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['delayed'] }}</p>
            <span class="text-[10px] text-amber-500 mt-1 block">Melebihi SLA Waktu</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm col-span-2 lg:col-span-1">
            <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600 mb-1">Insiden Aktif</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-rose-600">{{ $stats['open_exceptions'] }}</p>
            <span class="text-[10px] text-rose-500 mt-1 block">Badai / Bea Cukai</span>
        </div>

    </div>

    <!-- FILTER & PENCARIAN (SLIDE 10) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Input Resi -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Nomor Resi</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Contoh: CRG-2026..." 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs uppercase focus:outline-none focus:ring-2 focus:ring-sky-500"
                >
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Kargo</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Status</option>
                    <option value="CREATED" {{ request('status') === 'CREATED' ? 'selected' : '' }}>CREATED</option>
                    <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>BOOKED</option>
                    <option value="PICKED_UP" {{ request('status') === 'PICKED_UP' ? 'selected' : '' }}>PICKED_UP</option>
                    <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>IN_TRANSIT</option>
                    <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                </select>
            </div>

            <!-- Filter Pelayaran / Carrier -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Armada Pelayaran</label>
                <select name="carrier_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Carrier</option>
                    @foreach ($carriers as $c)
                        <option value="{{ $c->id }}" {{ request('carrier_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Keterlambatan -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kondisi SLA</label>
                <select name="delay_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Kondisi</option>
                    <option value="ON_TIME" {{ request('delay_status') === 'ON_TIME' ? 'selected' : '' }}>ON TIME (Tepat Waktu)</option>
                    <option value="DELAYED" {{ request('delay_status') === 'DELAYED' ? 'selected' : '' }}>DELAYED (Tertunda)</option>
                </select>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-grow py-2 px-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                    Filter Data
                </button>
                <a href="{{ route('dashboard') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- TABEL DATA PENGIRIMAN KARGO (SLIDE 10) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Daftar Manifest Pengiriman
            </h2>
            <span class="text-xs text-slate-400">
                Menampilkan {{ $shipments->count() }} kargo
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                        <th class="py-3 px-6">Nomor Resi</th>
                        <th class="py-3 px-6">Pemilik (Customer)</th>
                        <th class="py-3 px-6">Armada Pelayaran</th>
                        <th class="py-3 px-6">Rute (Asal &rarr; Tujuan)</th>
                        <th class="py-3 px-6">Status Siklus</th>
                        <th class="py-3 px-6">Kondisi Waktu (SLA)</th>
                        <th class="py-3 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($shipments as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Nomor Resi -->
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                <a href="{{ route('tracking.show', $s->tracking_number) }}" class="text-sky-600 hover:underline">
                                    {{ $s->tracking_number }}
                                </a>
                            </td>

                            <!-- Customer -->
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-800 block">{{ $s->customer->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $s->customer->company_name ?? '-' }}</span>
                            </td>

                            <!-- Carrier -->
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-800">{{ $s->carrier->name }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 ml-1">{{ $s->carrier->code }}</span>
                            </td>

                            <!-- Rute -->
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-1.5 font-medium">
                                    <span>{{ $s->origin->city }}</span>
                                    <span class="text-slate-400">&rarr;</span>
                                    <span class="font-bold text-slate-900">{{ $s->destination->city }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                @if ($s->current_status === 'DELIVERED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">DELIVERED</span>
                                @elseif ($s->current_status === 'IN_TRANSIT')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">IN TRANSIT</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">{{ $s->current_status }}</span>
                                @endif
                            </td>

                            <!-- Kondisi Delay -->
                            <td class="py-4 px-6">
                                @if ($s->delay_status === 'DELAYED')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        DELAYED (+{{ round($s->delay_duration_minutes / 60) }}h)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        ON TIME
                                    </span>
                                @endif
                            </td>

                            <!-- Tombol Aksi -->
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('tracking.show', $s->tracking_number) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 font-bold text-xs transition">
                                    <span>Lihat Peta</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada kargo yang sesuai dengan filter pencarian.
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

</div>
@endsection