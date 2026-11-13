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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Port of Tanjung Priok, Port of Singapore
            $table->string('code', 10)->unique(); // Kode UN/LOCODE: IDTPP, SGSIN, NLRTM
            $table->string('type')->default('port'); // port, airport, hub, warehouse
            $table->string('city'); // Jakarta, Singapore, Rotterdam
            $table->string('country'); // Indonesia, Singapore, Netherlands
            
            // Kunci Peta Leaflet (Koordinat GPS presisi tinggi):
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
