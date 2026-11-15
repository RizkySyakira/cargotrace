<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\TrackingService;
use App\Services\WeatherService;

class PublicTrackingController extends Controller
{
    protected TrackingService $trackingService;
    protected WeatherService $weatherService;

    public function __construct(TrackingService $trackingService, WeatherService $weatherService)
    {
        $this->trackingService = $trackingService;
        $this->weatherService = $weatherService;
    }

    /**
     * Halaman Utama: Form pencarian resi publik & kargo sampel demo
     */
    public function index()
    {
        // Ambil 4 kargo riil di database untuk sampel demo klik-cepat
        $featuredShipments = Shipment::with(['carrier', 'origin', 'destination'])
            ->orderBy('id', 'asc')
            ->take(4)
            ->get();

        // Hitung statistik kargo untuk indikator dashboard
        $stats = [
            'total_shipments' => Shipment::count(),
            'in_transit'      => Shipment::where('current_status', Shipment::STATUS_IN_TRANSIT)->count(),
            'delivered'       => Shipment::where('current_status', Shipment::STATUS_DELIVERED)->count(),
            'delayed'         => Shipment::where('delay_status', 'DELAYED')->count(),
        ];

        return view('public.index', compact('featuredShipments', 'stats'));
    }

    /**
     * Tangani pengiriman form pencarian resi
     */
    public function search(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:50',
        ]);

        $trackingNumber = strtoupper(trim($request->input('tracking_number')));

        return redirect()->route('tracking.show', ['tracking_number' => $trackingNumber]);
    }

    /**
     * Halaman Detail Pelacakan Kargo & Peta Leaflet.js
     */
    public function show(string $tracking_number)
    {
        $trackingNumber = strtoupper(trim($tracking_number));

        // Cari kargo beserta seluruh relasi data logistiknya
        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->with([
                'carrier',
                'origin',
                'destination',
                'items',
                'trackingEvents' => fn($q) => $q->orderBy('event_time', 'asc'),
                'exceptions'     => fn($q) => $q->orderBy('detected_at', 'desc'),
            ])
            ->first();

        // Jika nomor resi tidak ditemukan
        if (! $shipment) {
            return redirect()->route('tracking.index')
                ->with('error', "Nomor resi kargo '{$trackingNumber}' tidak ditemukan dalam basis data.");
        }

        // Posisi checkpoint terakhir kargo
        $latestEvent = $shipment->trackingEvents->last();

        // Ambil cuaca live terkini jika koordinat GPS tersedia
        $currentWeather = null;
        if ($latestEvent && $latestEvent->latitude && $latestEvent->longitude) {
            $currentWeather = $this->weatherService->getWeather(
                (float) $latestEvent->latitude,
                (float) $latestEvent->longitude
            );
        }

        return view('public.track', compact('shipment', 'latestEvent', 'currentWeather'));
    }
}