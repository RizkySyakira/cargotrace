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
         Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama armada: Maersk Line, Evergreen, dll
            $table->string('code', 10)->unique(); // Kode pelayaran resmi (misal: MAEU, EGLV)
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
