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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel shipments (jika kargo dihapus, rincian item ikut terhapus):
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            
            $table->string('item_name'); // Nama komoditas barang
            $table->string('package_type')->default('Box'); // Box, Pallet, Crate, Drum
            $table->integer('quantity')->default(1);
            $table->decimal('weight_kg', 10, 2);
            $table->decimal('volume_cbm', 10, 2)->nullable();
            
            // Kolom Logistik Profesional (Slide 11):
            $table->string('hs_code', 20)->nullable(); // Harmonized System Code untuk deklarasi Bea Cukai
            $table->boolean('is_dangerous_goods')->default(false); // Penanda barang berbahaya / mudah terbakar
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
