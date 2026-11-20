@extends('layouts.app')

@section('title', 'Lacak Kargo: ' . $shipment->tracking_number . ' - CargoTrace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- TOMBOL KEMBALI & HEADER RESI -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('tracking.index') }}" class="inline-flex items-center space-x-1 text-xs font-semibold text-slate-500 hover:text-sky-600 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Pencarian Resi</span>
            </a>
            
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 tracking-tight">
                    {{ $shipment->tracking_number }}
                </h1>
                
                <!-- BADGE STATUS SIKLUS HIDUP (STATE MACHINE) -->
                @if ($shipment->current_status === 'DELIVERED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        DELIVERED (TELAH TIBA)
                    </span>
                @elseif ($shipment->current_status === 'IN_TRANSIT')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-600 animate-ping"></span>
                        <span>IN TRANSIT (DALAM PERJALANAN)</span>
                    </span>
                @elseif ($shipment->current_status === 'BOOKED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                        BOOKED (DIJADWALKAN)
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                        {{ $shipment->current_status }}
                    </span>
                @endif

                <!-- BADGE INDIKATOR KETERLAMBATAN (SLA DELAY ENGINE) -->
                @if ($shipment->delay_status === 'DELAYED')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <span>TERTUNDA (+{{ round($shipment->delay_duration_minutes / 60) }} JAM) &bull; SEVERITY: {{ $shipment->delay_severity }}</span>
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ON TIME (TEPAT WAKTU)
                    </span>
                @endif
            </div>
        </div>

        <!-- ARMADA KAPAL -->
        <div class="bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center text-sky-600 font-bold text-xs">
                {{ substr($shipment->carrier->code, 0, 3) }}
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Perusahaan Pelayaran</span>
                <p class="text-sm font-bold text-slate-900 leading-tight">{{ $shipment->carrier->name }}</p>
            </div>
        </div>
    </div>

    <!-- PANEL OPERATOR: KONTROL STATUS STATE MACHINE (SLIDE 06) -->
    @auth
        @if(Auth::user()->isAdmin() || Auth::user()->isOperator())
            @php
                $allowedNextStatuses = App\Models\Shipment::STATUS_FLOW[$shipment->current_status] ?? [];
            @endphp
            @if(!empty($allowedNextStatuses))
                <div class="mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-lg border border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-500/20 text-sky-400 border border-sky-400/30 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-sky-400">Panel Operator &bull; State Machine Control (Slide 06)</span>
                            <p class="text-xs text-slate-300">Status saat ini: <strong class="text-white">{{ $shipment->current_status }}</strong></p>
                        </div>
                    </div>

                    <form action="{{ route('shipments.status', $shipment->tracking_number) }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <select name="status" required class="px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-sky-500 focus:outline-none">
                            <option value="">-- Pilih Transisi Status Sah --</option>
                            @foreach($allowedNextStatuses as $nextStatus)
                                <option value="{{ $nextStatus }}">Ubah ke {{ $nextStatus }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-slate-950 font-bold text-xs rounded-xl shadow-md transition">
                            Terapkan Transisi
                        </button>
                    </form>
                </div>
            @endif
        @endif
    @endauth

    <!-- BANNER PERINGATAN INSIDEN / EXCEPTIONS JIKA ADA -->
    @if ($shipment->exceptions->where('status', '!=', 'RESOLVED')->count() > 0)
        @foreach ($shipment->exceptions->where('status', '!=', 'RESOLVED') as $exc)
            <div class="mb-6 bg-gradient-to-r from-rose-500/10 via-amber-500/10 to-transparent border-l-4 border-rose-500 p-4 rounded-r-2xl bg-white shadow-sm">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-bold text-rose-900 uppercase tracking-wide">
                                Anomali Operasional Terdeteksi: {{ str_replace('_', ' ', $exc->exception_type) }}
                            </h3>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-rose-200 text-rose-900">
                                Tingkat: {{ $exc->severity }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-700 mt-1 leading-relaxed">{{ $exc->description }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Terdeteksi pada: {{ \Carbon\Carbon::parse($exc->detected_at)->format('d M Y, H:i') }} &bull; Status: {{ $exc->status }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- GRID UTAMA (KIRI: PETA & DETAIL MUATAN | KANAN: CUACA & TIMELINE) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI (7/12) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- KARTU PETA INTERAKTIF LEAFLET -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full bg-sky-500 animate-pulse"></span>
                        <h2 class="text-base font-bold text-slate-900">Visualisasi Rute Pelayaran Satelit</h2>
                    </div>
                    <span class="text-xs text-slate-400 font-mono">OpenStreetMap &bull; Leaflet.js</span>
                </div>

                <!-- WADAH PETA LEAFLET -->
                <div id="shipmentMap" class="w-full h-[420px] rounded-xl border border-slate-200 shadow-inner z-10"></div>

                <!-- KETERANGAN LEGENDA PETA -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-600">
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 border border-white shadow-sm inline-block"></span>
                        <span>Pelabuhan Asal: <strong>{{ $shipment->origin->code }}</strong></span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3 h-3 rounded-full bg-sky-500 border border-white shadow-sm inline-block"></span>
                        <span>Pos Checkpoint Di Laut</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-500 border border-white shadow-sm inline-block"></span>
                        <span>Pelabuhan Tujuan: <strong>{{ $shipment->destination->code }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- RINCIAN MUATAN & MANIFEST KONTAINER -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                    Manifest Peti Kemas & Komoditas Kargo
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase">Tipe Kontainer</span>
                        <p class="text-sm font-bold text-slate-800">{{ $shipment->cargo_type }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Berat</span>
                        <p class="text-sm font-bold text-slate-800">{{ number_format($shipment->total_weight_kg, 2) }} kg</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase">Volume Ruang</span>
                        <p class="text-sm font-bold text-slate-800">{{ $shipment->total_volume_cbm ?? '-' }} CBM</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase">Nilai Pabean</span>
                        <p class="text-sm font-bold text-slate-800">{{ $shipment->currency }} {{ number_format($shipment->declared_value, 2) }}</p>
                    </div>
                </div>

                <!-- TABEL BARANG / ITEMS -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 uppercase font-semibold border-b border-slate-200">
                                <th class="pb-2">Nama Komoditas</th>
                                <th class="pb-2">HS Code</th>
                                <th class="pb-2">Jumlah</th>
                                <th class="pb-2">Berat</th>
                                <th class="pb-2 text-right">Status Bahaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($shipment->items as $item)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-900">{{ $item->item_name }}</td>
                                    <td class="py-3 font-mono font-medium text-sky-700">{{ $item->hs_code ?? '-' }}</td>
                                    <td class="py-3">{{ $item->quantity }} {{ $item->package_type }}</td>
                                    <td class="py-3">{{ number_format($item->weight_kg, 2) }} kg</td>
                                    <td class="py-3 text-right">
                                        @if ($item->is_dangerous_goods)
                                            <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold">HAZMAT / DG</span>
                                        @else
                                            <span class="text-slate-400 font-medium">Non-Hazmat</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (5/12) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- WIDGET CUACA SATELIT (OPEN-METEO API) -->
            <div class="bg-gradient-to-br from-slate-900 to-sky-950 text-white p-6 rounded-2xl shadow-lg border border-slate-800">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800 text-xs">
                    <span class="font-semibold text-sky-300 flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        <span>Kondisi Cuaca Maritim Terkini</span>
                    </span>
                    <span class="text-slate-400 font-mono text-[10px]">Open-Meteo Satellite</span>
                </div>

                @if ($currentWeather)
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-4xl font-extrabold tracking-tight text-white">
                                {{ round($currentWeather['temperature_c'], 1) }}°C
                            </p>
                            <p class="text-sm font-medium text-sky-200 mt-1">
                                {{ $currentWeather['condition'] }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Kecepatan Angin: {{ $currentWeather['wind_speed'] }} km/h
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 block">Titik Pantau Terakhir:</span>
                            <span class="text-xs font-semibold text-slate-200 block max-w-[150px] truncate">
                                {{ $latestEvent->location_name ?? 'Koordinat Pelayaran' }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5">
                                {{ number_format($latestEvent->latitude, 3) }}, {{ number_format($latestEvent->longitude, 3) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mt-4 text-xs text-slate-400">
                        Data cuaca satelit akan otomatis diperbarui saat kapal mengirimkan koordinat GPS berikutnya.
                    </div>
                @endif
            </div>

            <!-- JADWAL SLA & ESTIMASI TIBA -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Jadwal & Komitmen Waktu (SLA)
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Berangkat Riil:</span>
                        <span class="font-bold text-slate-800">
                            {{ $shipment->actual_departure ? \Carbon\Carbon::parse($shipment->actual_departure)->format('d M Y, H:i') : 'Belum Berangkat' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-medium">Janji Kontrak Tiba (SLA):</span>
                        <span class="font-bold text-slate-800">
                            {{ \Carbon\Carbon::parse($shipment->expected_arrival)->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-slate-500 font-medium">Estimasi Tiba Dinamis (ETA):</span>
                        <span class="font-bold {{ $shipment->delay_status === 'DELAYED' ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ \Carbon\Carbon::parse($shipment->estimated_arrival)->format('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- TIMELINE KRONOLOGIS (APPEND-ONLY EVENT LOGGING) -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6 pb-2 border-b border-slate-100">
                    Riwayat Kronologis Perjalanan
                </h3>

                <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach ($shipment->trackingEvents as $index => $event)
                        <div class="relative group">
                            <!-- Bullet Icon -->
                            <div class="absolute -left-[27px] top-1 w-4 h-4 rounded-full bg-white border-2 {{ $loop->last ? 'border-sky-500 bg-sky-500' : 'border-slate-400' }}"></div>

                            <div class="text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-sm">{{ $event->location_name }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">
                                        {{ \Carbon\Carbon::parse($event->event_time)->format('d M, H:i') }}
                                    </span>
                                </div>

                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="font-semibold text-sky-700 uppercase text-[10px] tracking-wider">{{ $event->event_type }}</span>
                                    @if ($event->weather_condition)
                                        <span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                            ⛅ {{ $event->weather_condition }} ({{ $event->weather_temp_c }}°C)
                                        </span>
                                    @endif
                                </div>

                                <p class="text-slate-600 mt-1 leading-relaxed">
                                    {{ $event->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // KUMPULKAN TITIK KOORDINAT PELABUHAN & CHECKPOINT LAUT
    const waypoints = [
        @if($shipment->origin && $shipment->origin->latitude && $shipment->origin->longitude)
        {
            lat: {{ $shipment->origin->latitude }},
            lng: {{ $shipment->origin->longitude }},
            title: "Pelabuhan Asal: {{ addslashes($shipment->origin->name) }}",
            type: "origin",
            time: "Pelabuhan Muat Asal"
        },
        @endif

        @foreach($shipment->trackingEvents as $event)
            @if($event->latitude && $event->longitude)
            {
                lat: {{ $event->latitude }},
                lng: {{ $event->longitude }},
                title: "{{ addslashes($event->location_name) }}",
                type: "event",
                time: "{{ \Carbon\Carbon::parse($event->event_time)->format('d M Y, H:i') }}",
                desc: "{{ addslashes($event->description) }}",
                weather: "{{ $event->weather_condition ? addslashes($event->weather_condition . ' (' . $event->weather_temp_c . '°C)') : '' }}"
            },
            @endif
        @endforeach

        @if($shipment->destination && $shipment->destination->latitude && $shipment->destination->longitude)
        {
            lat: {{ $shipment->destination->latitude }},
            lng: {{ $shipment->destination->longitude }},
            title: "Pelabuhan Tujuan: {{ addslashes($shipment->destination->name) }}",
            type: "destination",
            time: "Pelabuhan Bongkar Tujuan"
        }
        @endif
    ];

    if (waypoints.length > 0) {
        // INISIALISASI PETA LEAFLET
        const map = L.map('shipmentMap').setView([waypoints[0].lat, waypoints[0].lng], 5);

        // TILE OPENSTREETMAP (GRATIS, NO API KEY)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const latLngs = [];

        // PASANG PIN / MARKER PADA SETIAP TITIK
        waypoints.forEach((wp, index) => {
            latLngs.push([wp.lat, wp.lng]);

            let iconColor = '#0284c7';
            let label = (index + 1).toString();

            if (wp.type === 'origin') {
                iconColor = '#10b981'; // Hijau untuk asal
                label = 'A';
            } else if (wp.type === 'destination') {
                iconColor = '#ef4444'; // Merah untuk tujuan
                label = 'B';
            }

            const customMarker = L.divIcon({
                className: 'custom-map-pin',
                html: `<div style="background-color: ${iconColor}; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 11px; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">${label}</div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            let popupContent = `
                <div style="font-family: inherit; min-width: 160px;">
                    <strong style="color: #0f172a; font-size: 13px;">${wp.title}</strong>
                    <div style="color: #64748b; font-size: 11px; margin-top: 2px;">${wp.time}</div>
            `;

            if (wp.desc) {
                popupContent += `<p style="color: #334155; font-size: 11px; margin-top: 6px; line-height: 1.4;">${wp.desc}</p>`;
            }

            if (wp.weather) {
                popupContent += `<div style="color: #0284c7; font-size: 11px; font-weight: bold; margin-top: 4px;">⛅ ${wp.weather}</div>`;
            }

            popupContent += `</div>`;

            L.marker([wp.lat, wp.lng], { icon: customMarker })
                .addTo(map)
                .bindPopup(popupContent);
        });

        // GAMBAR GARIS RUTE PELAYARAN (POLYLINE)
        const polyline = L.polyline(latLngs, {
            color: '#0284c7',
            weight: 4,
            opacity: 0.85,
            dashArray: '6, 8',
            lineJoin: 'round'
        }).addTo(map);

        // ZOOM OTOMATIS MEMPERLIHATKAN SELURUH JALUR PELAYARAN
        map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
    }
});
</script>
@endpush