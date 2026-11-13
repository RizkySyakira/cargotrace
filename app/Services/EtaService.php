<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Hitung selisih keterlambatan dan tingkat keparahan (Slide 08)
     */
    public function calculateDelay(Shipment $shipment, ?Carbon $newEstimatedArrival = null): array
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = $newEstimatedArrival ?? Carbon::parse($shipment->estimated_arrival);

        // Jika estimasi tiba lebih lambat dari jadwal janji kontrak (expected)
        if ($estimated->greaterThan($expected)) {
            $delayMinutes = (int) $expected->diffInMinutes($estimated);
            $severity = $this->determineSeverity($delayMinutes);

            return [
                'delay_status'           => 'DELAYED',
                'delay_duration_minutes' => $delayMinutes,
                'delay_severity'         => $severity,
                'estimated_arrival'      => $estimated,
            ];
        }

        // Jika tepat waktu atau lebih cepat
        return [
            'delay_status'           => 'ON_TIME',
            'delay_duration_minutes' => 0,
            'delay_severity'         => 'NONE',
            'estimated_arrival'      => $estimated,
        ];
    }

    /**
     * Terapkan hasil perhitungan keterlambatan langsung ke kargo dan simpan ke database
     */
    public function applyDelay(Shipment $shipment, Carbon $newEstimatedArrival): Shipment
    {
        $metrics = $this->calculateDelay($shipment, $newEstimatedArrival);

        $shipment->update([
            'estimated_arrival'      => $metrics['estimated_arrival'],
            'delay_status'           => $metrics['delay_status'],
            'delay_duration_minutes' => $metrics['delay_duration_minutes'],
            'delay_severity'         => $metrics['delay_severity'],
        ]);

        return $shipment->fresh();
    }

    /**
     * Logika ambang batas tingkat keparahan keterlambatan
     */
    protected function determineSeverity(int $minutes): string
    {
        if ($minutes <= 1440) {       // <= 24 jam
            return 'LOW';
        }

        if ($minutes <= 4320) {       // <= 72 jam (3 hari)
            return 'MEDIUM';
        }

        return 'HIGH';                // > 3 hari
    }
}