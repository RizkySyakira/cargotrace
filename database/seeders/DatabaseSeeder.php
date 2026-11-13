<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\ShipmentException;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic maritime logistics data.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. PENGGUNA SISTEM (USERS DENGAN 3 ROLE: ADMIN, OPERATOR, CUSTOMER)
        // =========================================================================
        $admin = User::create([
            'name'         => 'System Administrator',
            'email'        => 'admin@cargotrace.com',
            'password'     => Hash::make('password123'),
            'role'         => 'admin',
            'phone'        => '+628119876543',
            'company_name' => 'CargoTrace Global HQ',
            'is_active'    => true,
        ]);

        $operator = User::create([
            'name'         => 'Budi Santoso (Terminal Operator)',
            'email'        => 'operator@cargotrace.com',
            'password'     => Hash::make('password123'),
            'role'         => 'operator',
            'phone'        => '+628123456789',
            'company_name' => 'Jakarta International Container Terminal (JICT)',
            'is_active'    => true,
        ]);

        $customer1 = User::create([
            'name'         => 'Dewi Lestari (Supply Chain Manager)',
            'email'        => 'customer@cargotrace.com',
            'password'     => Hash::make('password123'),
            'role'         => 'customer',
            'phone'        => '+6281355551234',
            'company_name' => 'PT Nusantara Agri Ekspor',
            'is_active'    => true,
        ]);

        $customer2 = User::create([
            'name'         => 'Michael Chen (Logistics Director)',
            'email'        => 'customer2@cargotrace.com',
            'password'     => Hash::make('password123'),
            'role'         => 'customer',
            'phone'        => '+6591234567',
            'company_name' => 'Asia Pacific Semiconductor Ltd',
            'is_active'    => true,
        ]);

        // =========================================================================
        // 2. PERUSAHAAN PELAYARAN / CARRIERS ASLI DUNIA LOGISTIK
        // =========================================================================
        $carrierMaersk = Carrier::create([
            'name'          => 'Maersk Line',
            'code'          => 'MAEU',
            'contact_email' => 'operations@maersk.com',
            'contact_phone' => '+45 3363 3363',
            'website'       => 'https://www.maersk.com',
            'is_active'     => true,
        ]);

        $carrierEvergreen = Carrier::create([
            'name'          => 'Evergreen Marine Corp',
            'code'          => 'EGLV',
            'contact_email' => 'booking@evergreen-marine.com',
            'contact_phone' => '+886 2 2505 7766',
            'website'       => 'https://www.evergreen-marine.com',
            'is_active'     => true,
        ]);

        $carrierSamudera = Carrier::create([
            'name'          => 'Samudera Indonesia',
            'code'          => 'SMDR',
            'contact_email' => 'liner@samudera.id',
            'contact_phone' => '+62 21 548 0088',
            'website'       => 'https://www.samudera.id',
            'is_active'     => true,
        ]);

        $carrierMsc = Carrier::create([
            'name'          => 'Mediterranean Shipping Company (MSC)',
            'code'          => 'MSCU',
            'contact_email' => 'info@msc.com',
            'contact_phone' => '+41 22 703 8888',
            'website'       => 'https://www.msc.com',
            'is_active'     => true,
        ]);

        $carrierCma = Carrier::create([
            'name'          => 'CMA CGM Group',
            'code'          => 'CMAC',
            'contact_email' => 'customer@cma-cgm.com',
            'contact_phone' => '+33 4 88 91 90 00',
            'website'       => 'https://www.cma-cgm.com',
            'is_active'     => true,
        ]);

        // =========================================================================
        // 3. PELABUHAN & KOORDINAT GPS PRESISI (UNTUK LEAFLET MAP & OPEN-METEO)
        // =========================================================================
        $portPriok = Location::create([
            'name'      => 'Port of Tanjung Priok',
            'code'      => 'IDTPP',
            'type'      => 'port',
            'city'      => 'Jakarta',
            'country'   => 'Indonesia',
            'latitude'  => -6.1023000,
            'longitude' => 106.8837000,
            'is_active' => true,
        ]);

        $portSingapore = Location::create([
            'name'      => 'Port of Singapore (PSA)',
            'code'      => 'SGSIN',
            'type'      => 'port',
            'city'      => 'Singapore',
            'country'   => 'Singapore',
            'latitude'  => 1.2644000,
            'longitude' => 103.8228000,
            'is_active' => true,
        ]);

        $portRotterdam = Location::create([
            'name'      => 'Port of Rotterdam',
            'code'      => 'NLRTM',
            'type'      => 'port',
            'city'      => 'Rotterdam',
            'country'   => 'Netherlands',
            'latitude'  => 51.9540000,
            'longitude' => 4.1480000,
            'is_active' => true,
        ]);

        $portPerak = Location::create([
            'name'      => 'Port of Tanjung Perak',
            'code'      => 'IDSUB',
            'type'      => 'port',
            'city'      => 'Surabaya',
            'country'   => 'Indonesia',
            'latitude'  => -7.1994000,
            'longitude' => 112.7328000,
            'is_active' => true,
        ]);

        $portBelawan = Location::create([
            'name'      => 'Port of Belawan',
            'code'      => 'IDBLW',
            'type'      => 'port',
            'city'      => 'Medan',
            'country'   => 'Indonesia',
            'latitude'  => 3.7844000,
            'longitude' => 98.6922000,
            'is_active' => true,
        ]);

        $portShanghai = Location::create([
            'name'      => 'Port of Shanghai (Yangshan)',
            'code'      => 'CNSHG',
            'type'      => 'port',
            'city'      => 'Shanghai',
            'country'   => 'China',
            'latitude'  => 30.6277000,
            'longitude' => 122.0642000,
            'is_active' => true,
        ]);

        $portLosAngeles = Location::create([
            'name'      => 'Port of Los Angeles',
            'code'      => 'USLAX',
            'type'      => 'port',
            'city'      => 'Los Angeles',
            'country'   => 'United States',
            'latitude'  => 33.7432000,
            'longitude' => -118.2673000,
            'is_active' => true,
        ]);

        $now = Carbon::now();

        // =========================================================================
        // 4. SKENARIO 1: ON-TIME & IN-TRANSIT (EKSPOR KOPI GAYO KE SINGAPORE)
        // =========================================================================
        $shipment1 = Shipment::create([
            'tracking_number'         => 'CRG-2026-0001',
            'customer_id'             => $customer1->id,
            'carrier_id'              => $carrierSamudera->id,
            'origin_location_id'      => $portPriok->id,
            'destination_location_id' => $portSingapore->id,
            'cargo_type'              => 'CONTAINER_20FT',
            'total_weight_kg'         => 18500.00,
            'total_volume_cbm'        => 33.00,
            'declared_value'          => 85000.00,
            'currency'                => 'USD',
            'current_status'          => 'IN_TRANSIT',
            'expected_departure'      => $now->copy()->subDays(2),
            'actual_departure'        => $now->copy()->subDays(2),
            'expected_arrival'        => $now->copy()->addDay(),
            'estimated_arrival'       => $now->copy()->addDay(),
            'actual_arrival'          => null,
            'delay_status'            => 'ON_TIME',
            'delay_duration_minutes'  => 0,
            'delay_severity'          => 'NONE',
            'created_by'              => $admin->id,
            'notes'                   => 'Export of premium Gayo Arabica green beans for specialty coffee roasting.',
        ]);

        ShipmentItem::create([
            'shipment_id'        => $shipment1->id,
            'item_name'          => 'Organic Gayo Arabica Coffee Beans (Grade 1)',
            'package_type'       => 'Jute Bag',
            'quantity'           => 300,
            'weight_kg'          => 18500.00,
            'volume_cbm'         => 33.00,
            'hs_code'            => '0901.11', // HS Code resmi ekspor kopi mentah
            'is_dangerous_goods' => false,
        ]);

        // Timeline Checkpoint Kargo 1:
        TrackingEvent::create([
            'shipment_id'        => $shipment1->id,
            'location_id'        => $portPriok->id,
            'location_name'      => 'Port of Tanjung Priok, Jakarta',
            'event_type'         => 'CREATED',
            'event_time'         => $now->copy()->subDays(3)->setHour(9)->setMinute(0),
            'latitude'           => -6.1023000,
            'longitude'          => 106.8837000,
            'description'        => 'Electronic Shipping Order and customs export declaration registered.',
            'weather_condition'  => 'Sunny',
            'weather_temp_c'     => 30.5,
            'created_by'         => $customer1->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment1->id,
            'location_id'        => $portPriok->id,
            'location_name'      => 'JICT Terminal 1, Tanjung Priok',
            'event_type'         => 'PICKED_UP',
            'event_time'         => $now->copy()->subDays(2)->setHour(14)->setMinute(30),
            'latitude'           => -6.1023000,
            'longitude'          => 106.8837000,
            'description'        => 'Container gated-in, inspected, and loaded onto feeder vessel MV Samudera Mas.',
            'weather_condition'  => 'Clear Sky',
            'weather_temp_c'     => 31.0,
            'created_by'         => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment1->id,
            'location_id'        => null,
            'location_name'      => 'Bangka Strait (In Transit)',
            'event_type'         => 'IN_TRANSIT',
            'event_time'         => $now->copy()->subDay()->setHour(18)->setMinute(0),
            'latitude'           => -2.4500000,
            'longitude'          => 105.7000000,
            'description'        => 'Vessel cruising at 14 knots passing Bangka Strait on schedule.',
            'weather_condition'  => 'Partly Cloudy',
            'weather_temp_c'     => 28.5,
            'created_by'         => $operator->id,
        ]);

        // =========================================================================
        // 5. SKENARIO 2: DELAYED BY WEATHER (SEMIKONDUKTOR KE ROTTERDAM)
        // =========================================================================
        $shipment2 = Shipment::create([
            'tracking_number'         => 'CRG-2026-0002',
            'customer_id'             => $customer2->id,
            'carrier_id'              => $carrierMaersk->id,
            'origin_location_id'      => $portSingapore->id,
            'destination_location_id' => $portRotterdam->id,
            'cargo_type'              => 'CONTAINER_40FT',
            'total_weight_kg'         => 24800.00,
            'total_volume_cbm'        => 67.50,
            'declared_value'          => 420000.00,
            'currency'                => 'USD',
            'current_status'          => 'IN_TRANSIT',
            'expected_departure'      => $now->copy()->subDays(5),
            'actual_departure'        => $now->copy()->subDays(5),
            'expected_arrival'        => $now->copy()->addDays(15), // Janji awal SLA
            'estimated_arrival'       => $now->copy()->addDays(17), // Mundur 48 jam karena badai
            'actual_arrival'          => null,
            'delay_status'            => 'DELAYED',
            'delay_duration_minutes'  => 2880, // 48 jam = 2880 menit
            'delay_severity'          => 'HIGH',
            'created_by'              => $admin->id,
            'notes'                   => 'High value semiconductor integrated circuits under climate-monitored container.',
        ]);

        ShipmentItem::create([
            'shipment_id'        => $shipment2->id,
            'item_name'          => 'Microcontroller & Power Management ICs',
            'package_type'       => 'Crate',
            'quantity'           => 120,
            'weight_kg'          => 14200.00,
            'volume_cbm'         => 40.00,
            'hs_code'            => '8542.31', // HS Code prosesor / semikonduktor
            'is_dangerous_goods' => false,
        ]);

        ShipmentItem::create([
            'shipment_id'        => $shipment2->id,
            'item_name'          => 'Multilayer Ceramic Capacitors (MLCC)',
            'package_type'       => 'Box',
            'quantity'           => 80,
            'weight_kg'          => 10600.00,
            'volume_cbm'         => 27.50,
            'hs_code'            => '8532.24',
            'is_dangerous_goods' => false,
        ]);

        // Perekaman Insiden Gangguan (Shipment Exception):
        ShipmentException::create([
            'shipment_id'      => $shipment2->id,
            'exception_type'   => 'WEATHER_DELAY',
            'severity'         => 'HIGH',
            'status'           => 'INVESTIGATING',
            'description'      => 'Severe tropical cyclone in North Indian Ocean causing 6-meter swells. Captain throttled down speed to 7.5 knots for container safety.',
            'detected_at'      => $now->copy()->subDays(2),
            'resolved_at'      => null,
            'resolution_notes' => null,
            'reported_by'      => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment2->id,
            'location_id'        => $portSingapore->id,
            'location_name'      => 'Pasir Panjang Terminal, Singapore',
            'event_type'         => 'PICKED_UP',
            'event_time'         => $now->copy()->subDays(5)->setHour(10)->setMinute(0),
            'latitude'           => 1.2644000,
            'longitude'          => 103.8228000,
            'description'        => 'Loaded aboard vessel Madrid Maersk at Berth P24.',
            'weather_condition'  => 'Rain Shower',
            'weather_temp_c'     => 29.0,
            'created_by'         => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment2->id,
            'location_id'        => null,
            'location_name'      => 'Malacca Strait Crossing',
            'event_type'         => 'IN_TRANSIT',
            'event_time'         => $now->copy()->subDays(4)->setHour(6)->setMinute(0),
            'latitude'           => 3.0000000,
            'longitude'          => 100.5000000,
            'description'        => 'Navigating outbound Malacca traffic separation scheme.',
            'weather_condition'  => 'Moderate Rain',
            'weather_temp_c'     => 27.5,
            'created_by'         => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment2->id,
            'location_id'        => null,
            'location_name'      => 'Northern Indian Ocean (Weather Alert Zone)',
            'event_type'         => 'IN_TRANSIT',
            'event_time'         => $now->copy()->subDays(2)->setHour(21)->setMinute(15),
            'latitude'           => 5.8500000,
            'longitude'          => 95.1500000,
            'description'        => 'Speed reduced due to heavy cyclone swells. ETA revised by +48 hours.',
            'weather_condition'  => 'Heavy Thunderstorm',
            'weather_temp_c'     => 24.0,
            'created_by'         => $operator->id,
        ]);

        // =========================================================================
        // 6. SKENARIO 3: CUSTOMS HOLD (VANILI BEKU KE SHANGHAI)
        // =========================================================================
        $shipment3 = Shipment::create([
            'tracking_number'         => 'CRG-2026-0003',
            'customer_id'             => $customer1->id,
            'carrier_id'              => $carrierEvergreen->id,
            'origin_location_id'      => $portPerak->id,
            'destination_location_id' => $portShanghai->id,
            'cargo_type'              => 'REEFER',
            'total_weight_kg'         => 21000.00,
            'total_volume_cbm'        => 28.00,
            'declared_value'          => 65000.00,
            'currency'                => 'USD',
            'current_status'          => 'IN_TRANSIT',
            'expected_departure'      => $now->copy()->subDays(4),
            'actual_departure'        => $now->copy()->subDays(4),
            'expected_arrival'        => $now->copy()->addDays(3),
            'estimated_arrival'       => $now->copy()->addDays(4), // Mundur 24 jam karena karantina
            'actual_arrival'          => null,
            'delay_status'            => 'DELAYED',
            'delay_duration_minutes'  => 1440, // 24 jam
            'delay_severity'          => 'MEDIUM',
            'created_by'              => $admin->id,
            'notes'                   => 'Reefer container set at -18°C temperature for organic vanilla beans.',
        ]);

        ShipmentItem::create([
            'shipment_id'        => $shipment3->id,
            'item_name'          => 'Frozen Gourmet Bourbon Vanilla Beans',
            'package_type'       => 'Box',
            'quantity'           => 450,
            'weight_kg'          => 21000.00,
            'volume_cbm'         => 28.00,
            'hs_code'            => '0905.10',
            'is_dangerous_goods' => false,
        ]);

        ShipmentException::create([
            'shipment_id'      => $shipment3->id,
            'exception_type'   => 'CUSTOMS_HOLD',
            'severity'         => 'MEDIUM',
            'status'           => 'OPEN',
            'description'      => 'Customs quarantine verification hold: Phytosanitary digital certificate QR code re-validation requested.',
            'detected_at'      => $now->copy()->subDay(),
            'resolved_at'      => null,
            'resolution_notes' => null,
            'reported_by'      => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment3->id,
            'location_id'        => $portPerak->id,
            'location_name'      => 'Teluk Lamong Terminal, Surabaya',
            'event_type'         => 'PICKED_UP',
            'event_time'         => $now->copy()->subDays(4)->setHour(8)->setMinute(0),
            'latitude'           => -7.1994000,
            'longitude'          => 112.7328000,
            'description'        => 'Reefer power connected, cargo verified at -18.2°C.',
            'weather_condition'  => 'Sunny',
            'weather_temp_c'     => 32.0,
            'created_by'         => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment3->id,
            'location_id'        => null,
            'location_name'      => 'Karimata Strait Checkpoint',
            'event_type'         => 'IN_TRANSIT',
            'event_time'         => $now->copy()->subDay()->setHour(14)->setMinute(0),
            'latitude'           => -1.7500000,
            'longitude'          => 108.8500000,
            'description'        => 'Customs electronic flag received; shipment held pending doc audit.',
            'weather_condition'  => 'Overcast',
            'weather_temp_c'     => 29.0,
            'created_by'         => $operator->id,
        ]);

        // =========================================================================
        // 7. SKENARIO 4: DELIVERED (SOLAR PANEL SHANGHAI KE JAKARTA)
        // =========================================================================
        $shipment4 = Shipment::create([
            'tracking_number'         => 'CRG-2026-0004',
            'customer_id'             => $customer2->id,
            'carrier_id'              => $carrierMsc->id,
            'origin_location_id'      => $portShanghai->id,
            'destination_location_id' => $portPriok->id,
            'cargo_type'              => 'CONTAINER_40FT',
            'total_weight_kg'         => 19400.00,
            'total_volume_cbm'        => 58.00,
            'declared_value'          => 180000.00,
            'currency'                => 'USD',
            'current_status'          => 'DELIVERED',
            'expected_departure'      => $now->copy()->subDays(10),
            'actual_departure'        => $now->copy()->subDays(10),
            'expected_arrival'        => $now->copy()->subDay(),
            'estimated_arrival'       => $now->copy()->subDay(),
            'actual_arrival'          => $now->copy()->subDay()->setHour(16)->setMinute(30),
            'delay_status'            => 'ON_TIME',
            'delay_duration_minutes'  => 0,
            'delay_severity'          => 'NONE',
            'created_by'              => $admin->id,
            'notes'                   => 'Tier 1 Photovoltaic Solar Modules for industrial power plant project.',
        ]);

        ShipmentItem::create([
            'shipment_id'        => $shipment4->id,
            'item_name'          => 'Monocrystalline Solar PV Modules (550W)',
            'package_type'       => 'Pallet',
            'quantity'           => 220,
            'weight_kg'          => 19400.00,
            'volume_cbm'         => 58.00,
            'hs_code'            => '8541.43', // HS Code sel surya fotovoltaik
            'is_dangerous_goods' => false,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment4->id,
            'location_id'        => $portShanghai->id,
            'location_name'      => 'Yangshan Deepwater Port, Shanghai',
            'event_type'         => 'PICKED_UP',
            'event_time'         => $now->copy()->subDays(10)->setHour(11)->setMinute(0),
            'latitude'           => 30.6277000,
            'longitude'          => 122.0642000,
            'description'        => 'Export customs clearance sealed; container loaded on MSC Gulsun.',
            'weather_condition'  => 'Clear Sky',
            'weather_temp_c'     => 18.0,
            'created_by'         => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id'        => $shipment4->id,
            'location_id'        => $portPriok->id,
            'location_name'      => 'Port of Tanjung Priok Berth 102',
            'event_type'         => 'DELIVERED',
            'event_time'         => $now->copy()->subDay()->setHour(16)->setMinute(30),
            'latitude'           => -6.1023000,
            'longitude'          => 106.8837000,
            'description'        => 'Cargo discharged at Tanjung Priok, import duty cleared, and signed off by consignee.',
            'weather_condition'  => 'Sunny',
            'weather_temp_c'     => 31.5,
            'created_by'         => $operator->id,
        ]);
    }
}