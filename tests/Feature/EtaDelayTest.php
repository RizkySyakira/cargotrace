<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Shipment;
use App\Services\EtaService;
use Carbon\Carbon;

class EtaDelayTest extends TestCase
{
    protected EtaService $etaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->etaService = app(EtaService::class);
    }

    /**
     * TEST 1: Kargo tiba sesuai jadwal atau lebih cepat dihitung ON_TIME
     */
    public function test_on_time_shipment_calculation(): void
    {
        $now = Carbon::now();
        $shipment = new Shipment([
            'expected_arrival'  => $now->copy()->addDays(5),
            'estimated_arrival' => $now->copy()->addDays(5),
        ]);

        $result = $this->etaService->calculateDelay($shipment);

        $this->assertEquals('ON_TIME', $result['delay_status']);
        $this->assertEquals(0, $result['delay_duration_minutes']);
        $this->assertEquals('NONE', $result['delay_severity']);
    }

    /**
     * TEST 2: Keterlambatan 5 jam dihitung LOW severity
     */
    public function test_low_delay_severity_threshold(): void
    {
        $now = Carbon::now();
        $expected = $now->copy()->addDays(2);
        $newEta = $expected->copy()->addHours(5); // Terlambat 5 jam (300 menit)

        $shipment = new Shipment([
            'expected_arrival'  => $expected,
            'estimated_arrival' => $expected,
        ]);

        $result = $this->etaService->calculateDelay($shipment, $newEta);

        $this->assertEquals('DELAYED', $result['delay_status']);
        $this->assertEquals(300, $result['delay_duration_minutes']);
        $this->assertEquals('LOW', $result['delay_severity']);
    }

    /**
     * TEST 3: Keterlambatan 48 jam (badai di laut) dihitung MEDIUM severity
     */
    public function test_medium_delay_severity_threshold(): void
    {
        $now = Carbon::now();
        $expected = $now->copy()->addDays(2);
        $newEta = $expected->copy()->addHours(48); // Terlambat 48 jam (2880 menit)

        $shipment = new Shipment([
            'expected_arrival'  => $expected,
            'estimated_arrival' => $expected,
        ]);

        $result = $this->etaService->calculateDelay($shipment, $newEta);

        $this->assertEquals('DELAYED', $result['delay_status']);
        $this->assertEquals(2880, $result['delay_duration_minutes']);
        $this->assertEquals('MEDIUM', $result['delay_severity']);
    }

    /**
     * TEST 4: Keterlambatan 4 hari (>72 jam) dihitung HIGH severity
     */
    public function test_high_delay_severity_threshold(): void
    {
        $now = Carbon::now();
        $expected = $now->copy()->addDays(2);
        $newEta = $expected->copy()->addHours(96); // Terlambat 96 jam (4 hari)

        $shipment = new Shipment([
            'expected_arrival'  => $expected,
            'estimated_arrival' => $expected,
        ]);

        $result = $this->etaService->calculateDelay($shipment, $newEta);

        $this->assertEquals('DELAYED', $result['delay_status']);
        $this->assertEquals('HIGH', $result['delay_severity']);
    }
}