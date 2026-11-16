<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Carbon\Carbon;

class ShipmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected ShipmentService $shipmentService;
    protected Shipment $shipment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentService = app(ShipmentService::class);

        $customer = User::factory()->create(['role' => 'customer']);
        $origin = Location::create(['name' => 'Tanjung Priok', 'code' => 'IDTPP', 'city' => 'Jakarta', 'country' => 'ID', 'latitude' => -6.1, 'longitude' => 106.8]);
        $destination = Location::create(['name' => 'Singapore', 'code' => 'SGSIN', 'city' => 'Singapore', 'country' => 'SG', 'latitude' => 1.2, 'longitude' => 103.8]);
        $carrier = Carrier::create(['name' => 'Maersk', 'code' => 'MAEU']);

        $this->shipment = Shipment::create([
            'tracking_number'         => 'CRG-STATE-0001',
            'customer_id'             => $customer->id,
            'carrier_id'              => $carrier->id,
            'origin_location_id'      => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type'              => 'CONTAINER_20FT',
            'total_weight_kg'         => 18000,
            'current_status'          => Shipment::STATUS_CREATED,
            'expected_arrival'        => Carbon::now()->addDays(3),
            'estimated_arrival'       => Carbon::now()->addDays(3),
        ]);
    }

    /**
     * TEST 1: Transisi status yang sah berhasil berjalan (CREATED -> BOOKED)
     */
    public function test_valid_status_transition_succeeds(): void
    {
        $updated = $this->shipmentService->transitionStatus($this->shipment, Shipment::STATUS_BOOKED);

        $this->assertEquals(Shipment::STATUS_BOOKED, $updated->current_status);
        $this->assertDatabaseHas('shipments', [
            'id'             => $this->shipment->id,
            'current_status' => Shipment::STATUS_BOOKED,
        ]);
    }

    /**
     * TEST 2: Transisi status ilegal/melompat DITOLAK oleh State Machine (Slide 06)
     */
    public function test_invalid_status_transition_throws_exception(): void
    {
        // Status kargo saat ini CREATED, mencoba langsung meloncat ke DELIVERED (Ilegal!)
        $this->expectException(\DomainException::class);

        $this->shipmentService->transitionStatus($this->shipment, Shipment::STATUS_DELIVERED);
    }

    /**
     * TEST 3: Status Terminal (DELIVERED) tidak boleh diubah lagi
     */
    public function test_terminal_delivered_status_cannot_be_changed(): void
    {
        $this->shipment->update(['current_status' => Shipment::STATUS_DELIVERED]);

        $this->expectException(\DomainException::class);

        // Mencoba mengubah kargo yang sudah tiba kembali ke IN_TRANSIT (Ilegal!)
        $this->shipmentService->transitionStatus($this->shipment, Shipment::STATUS_IN_TRANSIT);
    }
}