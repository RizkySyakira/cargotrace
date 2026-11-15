@extends('layouts.app')

@section('title', 'CargoTrace - Pelacakan Kargo Maritim Global')

@section('content')
<!-- HERO & SEARCH SECTION -->
<section class="bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 text-white pt-12 pb-20 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
    <div class="max-w-4xl mx-auto text-center">
        
        <!-- Badge Status Sistem -->
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-sky-500/10 border border-sky-400/30 text-sky-300 text-xs font-semibold mb-6">
            <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
            <span>Sistem Pemantauan Armada Maritim Terbuka (AIS & GPS Tracking)</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4 leading-tight">
            Lacak Perjalanan Kargo Maritim <br class="hidden sm:inline">
            <span class="bg-gradient-to-r from-sky-400 via-cyan-300 to-teal-300 bg-clip-text text-transparent">
                Secara Real-Time & Transparan
            </span>
        </h1>
        
        <p class="text-slate-300 text-base sm:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
            Visibilitas penuh pengiriman peti kemas ekspor-impor antarpelabuhan dunia lengkap dengan integrasi cuaca satelit dan pelaporan anomali rute.
        </p>

        <!-- FORM PENCARIAN NOMOR RESI -->
        <div class="bg-slate-800/80 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-slate-700/80 shadow-2xl max-w-2xl mx-auto">
            <form action="{{ route('tracking.search') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="tracking_number" 
                        value="{{ old('tracking_number') }}"
                        placeholder="Masukkan nomor resi (contoh: CRG-2026-0001)" 
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-900/90 text-white placeholder-slate-400 border border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm sm:text-base uppercase tracking-wider font-medium"
                        required
                        autofocus
                    >
                </div>
                <button 
                    type="submit" 
                    class="px-6 py-3.5 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-sky-500/25 transition duration-200 flex items-center justify-center space-x-2 text-sm sm:text-base flex-shrink-0"
                >
                    <span>Lacak Kargo</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- TOMBOL SAMPEL RESI DEMO (PINTASAN KHUSUS PENGUJIAN DOSEN) -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-xs">
            <span class="text-slate-400 font-medium">Uji Coba Cepat (Sampel Riil):</span>
            
            <a href="{{ route('tracking.show', 'CRG-2026-0001') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sky-300 font-mono transition flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>CRG-2026-0001 (Tepat Waktu)</span>
            </a>

            <a href="{{ route('tracking.show', 'CRG-2026-0002') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-300 font-mono transition flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                <span>CRG-2026-0002 (Tertunda Badai)</span>
            </a>

            <a href="{{ route('tracking.show', 'CRG-2026-0003') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-200 font-mono transition flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                <span>CRG-2026-0003 (Bea Cukai)</span>
            </a>

            <a href="{{ route('tracking.show', 'CRG-2026-0004') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-emerald-300 font-mono transition flex items-center space-x-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                <span>CRG-2026-0004 (Selesai/Tiba)</span>
            </a>
        </div>

    </div>
</section>

<!-- STATISTIK METRIK LOGISTIK -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-2xl shadow-xl border border-slate-200/80">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Total Muatan Terdaftar</p>
            <p class="text-3xl font-extrabold text-slate-900">{{ $stats['total_shipments'] }} <span class="text-sm font-normal text-slate-500">Kontainer</span></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xl border border-slate-200/80">
            <p class="text-xs font-semibold uppercase tracking-wider text-sky-600 mb-1">Dalam Pelayaran</p>
            <p class="text-3xl font-extrabold text-sky-600">{{ $stats['in_transit'] }} <span class="text-sm font-normal text-slate-500">Kapal Aktif</span></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xl border border-slate-200/80">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 mb-1">Selesai Diantar</p>
            <p class="text-3xl font-extrabold text-emerald-600">{{ $stats['delivered'] }} <span class="text-sm font-normal text-slate-500">Berhasil Tiba</span></p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-xl border border-slate-200/80">
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 mb-1">Keterlambatan Terdeteksi</p>
            <p class="text-3xl font-extrabold text-amber-600">{{ $stats['delayed'] }} <span class="text-sm font-normal text-slate-500">Perlu Pantauan</span></p>
        </div>

    </div>
</section>

<!-- DAFTAR PENGIRIMAN AKTIF DI SISTEM -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Pengiriman Terkini Dalam Pelayaran</h2>
            <p class="text-sm text-slate-500 mt-1">Status pelayaran kontainer maritim internasional yang sedang aktif dipantau.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($featuredShipments as $item)
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <!-- Header Card: Resi & Status -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nomor Resi</span>
                            <p class="text-lg font-mono font-bold text-slate-900">{{ $item->tracking_number }}</p>
                        </div>
                        
                        <!-- Status Badge -->
                        @if ($item->current_status === 'DELIVERED')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">DELIVERED</span>
                        @elseif ($item->delay_status === 'DELAYED')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                <span>DELAYED (+{{ round($item->delay_duration_minutes / 60) }}h)</span>
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-600 animate-ping"></span>
                                <span>IN TRANSIT</span>
                            </span>
                        @endif
                    </div>

                    <!-- Detail Rute Pelabuhan -->
                    <div class="py-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                <span class="text-slate-500 font-medium">Asal:</span>
                                <span class="font-semibold text-slate-800">{{ $item->origin->name }}</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $item->origin->code }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-slate-500 font-medium">Tujuan:</span>
                                <span class="font-semibold text-slate-800">{{ $item->destination->name }}</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $item->destination->code }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-50">
                            <span>Armada Pelayaran: <strong class="text-slate-700">{{ $item->carrier->name }}</strong></span>
                            <span>Muatan: <strong class="text-slate-700">{{ $item->cargo_type }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card: Tombol Lacak -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Estimasi Tiba: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($item->estimated_arrival)->format('d M Y, H:i') }}</strong>
                    </span>
                    <a href="{{ route('tracking.show', $item->tracking_number) }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 px-3 py-2 rounded-xl transition">
                        <span>Buka Peta Live</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection