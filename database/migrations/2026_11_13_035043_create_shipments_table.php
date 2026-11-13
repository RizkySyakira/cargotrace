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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 30)->unique(); // Nomor resi kargo: CRG-2026-0001
            
            // Relasi Foreign Key (Slide 05 & 11)
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('carriers')->onDelete('restrict');
            $table->foreignId('origin_location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('destination_location_id')->constrained('locations')->onDelete('restrict');
            
            // Detail Muatan Kargo (Slide 06)
            $table->string('cargo_type'); // CONTAINER_20FT, CONTAINER_40FT, FCL, LCL, REEFER, BULK
            $table->decimal('total_weight_kg', 10, 2);
            $table->decimal('total_volume_cbm', 10, 2)->nullable();
            $table->decimal('declared_value', 12, 2)->nullable(); // Nilai pabean untuk asuransi/bea cukai
            $table->string('currency', 3)->default('USD'); // USD, IDR, dll
            // Lifecycle Status (Slide 06)
            $table->string('current_status')->default('CREATED');
            
            // Jadwal & ETA (Slide 08)
            $table->dateTime('expected_departure')->nullable();
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('expected_arrival'); // SLA / Janji Tiba Kontraktual
            $table->dateTime('estimated_arrival'); // ETA Dinamis (bisa mundur kalau macet/badai)
            $table->dateTime('actual_arrival')->nullable(); // Waktu riil saat kargo sampai
            // Mesin Hitung Keterlambatan / Delay Engine (Slide 08)
            $table->string('delay_status')->default('ON_TIME'); // ON_TIME, DELAYED
            $table->integer('delay_duration_minutes')->default(0);
            $table->string('delay_severity')->default('NONE'); // NONE, LOW, MEDIUM, HIGH
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            // Indexes agar pencarian & filter cepat (Slide 10)
            $table->index('current_status');
            $table->index('delay_status');
            $table->index('expected_arrival');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
