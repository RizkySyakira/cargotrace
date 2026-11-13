<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TrackingService
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Rekam pos kejadian tracking baru (Append-Only Event Logging)
     */
    public function recordCheckpoint(Shipment $shipment, array $data, ?int $userId = null): TrackingEvent
    {
        $lat = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $lng = isset($data['longitude']) ? (float) $data['longitude'] : null;

        // Ambil data cuaca satelit otomatis jika koordinat GPS tersedia
        $weatherTemp = null;
        $weatherCond = null;

        if ($lat !== null && $lng !== null) {
            $weather = $this->weatherService->getWeather($lat, $lng);
            $weatherTemp = $weather['temperature_c'] ?? null;
            $weatherCond = $weather['condition'] ?? null;
        }

        return TrackingEvent::create([
            'shipment_id'       => $shipment->id,
            'location_id'       => $data['location_id'] ?? null,
            'location_name'     => $data['location_name'],
            'event_type'        => $data['event_type'],
            'event_time'        => !empty($data['event_time']) ? Carbon::parse($data['event_time']) : Carbon::now(),
            'latitude'          => $lat,
            'longitude'         => $lng,
            'description'       => $data['description'],
            'weather_condition' => $data['weather_condition'] ?? $weatherCond,
            'weather_temp_c'    => $data['weather_temp_c'] ?? $weatherTemp,
            'created_by'        => $userId,
        ]);
    }

    /**
     * Ambil seluruh riwayat timeline perjalanan kargo terurut secara kronologis
     */
    public function getTimeline(Shipment $shipment): Collection
    {
        return $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('event_time', 'asc')
            ->get();
    }
}