<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use Carbon\Carbon;

class ShipmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer1;
    protected User $customer2;
    protected User $operator;
    protected Shipment $shipmentCust1;
    protected Shipment $shipmentCust2;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Siapkan Akun Pengguna Sesuai Role (Slide 03)
        $this->customer1 = User::factory()->create([
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $this->customer2 = User::factory()->create([
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $this->operator = User::factory()->create([
            'role'      => 'operator',
            'is_active' => true,
        ]);

        // 2. Siapkan Master Data Lokasi & Carrier
        $origin = Location::create([
            'name' => 'Tanjung Priok', 'code' => 'IDTPP', 'city' => 'Jakarta', 'country' => 'ID',
            'latitude' => -6.10, 'longitude' => 106.88
        ]);
        $destination = Location::create([
            'name' => 'Singapore', 'code' => 'SGSIN', 'city' => 'Singapore', 'country' => 'SG',
            'latitude' => 1.26, 'longitude' => 103.82
        ]);
        $carrier = Carrier::create([
            'name' => 'Samudera Indonesia', 'code' => 'SMDR'
        ]);

        // 3. Buat Kargo Milik Customer 1
        $this->shipmentCust1 = Shipment::create([
            'tracking_number'         => 'CRG-TEST-0001',
            'customer_id'             => $this->customer1->id,
            'carrier_id'              => $carrier->id,
            'origin_location_id'      => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type'              => 'CONTAINER_20FT',
            'total_weight_kg'         => 15000,
            'current_status'          => 'IN_TRANSIT',
            'expected_arrival'        => Carbon::now()->addDays(2),
            'estimated_arrival'       => Carbon::now()->addDays(2),
        ]);

        // 4. Buat Kargo Milik Customer 2
        $this->shipmentCust2 = Shipment::create([
            'tracking_number'         => 'CRG-TEST-0002',
            'customer_id'             => $this->customer2->id,
            'carrier_id'              => $carrier->id,
            'origin_location_id'      => $origin->id,
            'destination_location_id' => $destination->id,
            'cargo_type'              => 'CONTAINER_40FT',
            'total_weight_kg'         => 22000,
            'current_status'          => 'IN_TRANSIT',
            'expected_arrival'        => Carbon::now()->addDays(4),
            'estimated_arrival'       => Carbon::now()->addDays(4),
        ]);
    }

    /**
     * TEST 1 (Slide 13): Customer BISA melihat kargo miliknya sendiri (200 OK)
     */
    public function test_customer_can_view_own_shipment(): void
    {
        $token = $this->customer1->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$this->shipmentCust1->tracking_number}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.tracking_number', 'CRG-TEST-0001');
    }

    /**
     * TEST 2 (Slide 13): Customer DILARANG melihat kargo milik Customer lain (403 Forbidden)
     */
    public function test_customer_cannot_view_other_customer_shipment(): void
    {
        // Customer 1 mencoba mengakses kargo milik Customer 2
        $token = $this->customer1->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/v1/shipments/{$this->shipmentCust2->tracking_number}");

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /**
     * TEST 3 (Slide 13): Customer DILARANG menambahkan pos checkpoint kargo (403 Forbidden)
     */
    public function test_customer_cannot_record_checkpoint(): void
    {
        $token = $this->customer1->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$this->shipmentCust1->tracking_number}/checkpoints", [
                'location_name' => 'Selat Malaka',
                'event_type'    => 'IN_TRANSIT',
                'description'   => 'Mencoba memalsukan pos perjalanan',
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /**
     * TEST 4 (Slide 13): Operator RESMI berhasil menambahkan checkpoint kargo (201 Created)
     */
    public function test_operator_can_record_checkpoint(): void
    {
        $token = $this->operator->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/shipments/{$this->shipmentCust1->tracking_number}/checkpoints", [
                'location_name' => 'Bangka Strait',
                'event_type'    => 'IN_TRANSIT',
                'description'   => 'Kapal berlayar normal',
                'latitude'      => -2.45,
                'longitude'     => 105.70,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.location_name', 'Bangka Strait');
    }

    /**
     * TEST 5 (Slide 13): Publik bebas melacak kargo tanpa token (200 OK)
     */
    public function test_public_can_track_shipment_without_token(): void
    {
        $response = $this->getJson("/api/v1/public/track/{$this->shipmentCust1->tracking_number}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.shipment.tracking_number', 'CRG-TEST-0001');
    }
}