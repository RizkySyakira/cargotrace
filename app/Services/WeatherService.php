<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * URL API Open-Meteo (Gratis, No API Key Required)
     */
    protected string $baseUrl = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Ambil data cuaca berdasarkan koordinat latitude & longitude
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        // Kunci cache unik per koordinat (dibulatkan 2 desimal)
        $cacheKey = sprintf('weather_%.2f_%.2f', $latitude, $longitude);

        // Simpan di Cache selama 30 menit agar performa cepat
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($latitude, $longitude) {
            try {
                // Request ke Open-Meteo dengan batas waktu tunggu (timeout) 3 detik
                $response = Http::timeout(3)->get($this->baseUrl, [
                    'latitude'      => $latitude,
                    'longitude'     => $longitude,
                    'current'       => 'temperature_2m,weather_code,wind_speed_10m',
                    'timezone'      => 'auto',
                ]);

                if ($response->successful()) {
                    $current = $response->json('current') ?? [];
                    $weatherCode = $current['weather_code'] ?? 0;

                    return [
                        'temperature_c' => (float) ($current['temperature_2m'] ?? 28.0),
                        'condition'     => $this->mapWeatherCode($weatherCode),
                        'wind_speed'    => (float) ($current['wind_speed_10m'] ?? 0.0),
                        'weather_code'  => $weatherCode,
                        'source'        => 'Open-Meteo Live API',
                    ];
                }
            } catch (\Throwable $e) {
                // Catat log jika internet timeout / error
                Log::warning("WeatherService fallback used for ({$latitude}, {$longitude}): " . $e->getMessage());
            }

            // Fallback aman jika API tidak terjangkau (aplikasi tetap jalan normal!)
            return [
                'temperature_c' => 28.5,
                'condition'     => 'Partly Cloudy',
                'wind_speed'    => 12.0,
                'weather_code'  => 2,
                'source'        => 'Offline Fallback',
            ];
        });
    }

    /**
     * Konversi kode standar meteorologi internasional (WMO Weather Code)
     * ke dalam teks yang mudah dibaca manusia
     */
    protected function mapWeatherCode(int $code): string
    {
        return match ($code) {
            0          => 'Clear Sky',
            1, 2, 3    => 'Partly Cloudy',
            45, 48     => 'Foggy',
            51, 53, 55 => 'Light Drizzle',
            61, 63, 65 => 'Rain Shower',
            71, 73, 75 => 'Snow Flurries',
            80, 81, 82 => 'Violent Rain Showers',
            95, 96, 99 => 'Severe Thunderstorm',
            default    => 'Variable Conditions',
        };
    }
}