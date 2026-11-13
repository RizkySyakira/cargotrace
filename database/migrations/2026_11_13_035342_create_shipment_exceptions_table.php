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
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            
            // Jenis Gangguan (Slide 08):
            $table->string('exception_type'); // CUSTOMS_HOLD, WEATHER_DELAY, PORT_CONGESTION, DAMAGED, MISROUTED, DOCUMENTATION_ISSUE
            $table->string('severity')->default('MEDIUM'); // Tingkat keparahan: LOW, MEDIUM, HIGH
            $table->string('status')->default('OPEN'); // Siklus masalah: OPEN, INVESTIGATING, RESOLVED
            
            $table->text('description'); // Penjelasan kendala yang terjadi
            $table->dateTime('detected_at'); // Waktu kendala terdeteksi
            $table->dateTime('resolved_at')->nullable(); // Waktu kendala berhasil diselesaikan
            $table->text('resolution_notes')->nullable(); // Catatan solusi penyelesaian
            
            // Aktor Petugas yang menangani:
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->index(['shipment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};
