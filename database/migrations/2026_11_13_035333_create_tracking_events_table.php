<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            
            $table->string('location_name'); // Nama lokasi checkpoint (misal: Selat Malaka, Port of Singapore)
            $table->string('event_type'); // CREATED, BOOKED, PICKED_UP, IN_TRANSIT, ARRIVED_HUB, DELIVERED
            $table->dateTime('event_time'); // Waktu pasti kejadian
            
            // Koordinat GPS untuk menggambar garis perjalanan di Leaflet Map:
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description'); // Penjelasan (misal: Kontainer dimuat ke kapal Maersk)
            
            // Integrasi External API Cuaca (Slide 09):
            $table->string('weather_condition')->nullable(); // Misal: Clear sky, Heavy Rain
            $table->decimal('weather_temp_c', 5, 2)->nullable(); // Suhu udara: 29.5°C
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            // Index agar urutan timeline kronologis cepat (Slide 07):
            $table->index(['shipment_id', 'event_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
