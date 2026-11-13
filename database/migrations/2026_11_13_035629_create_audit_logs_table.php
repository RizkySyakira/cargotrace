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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Siapa pelakunya
            
            $table->string('action'); // CREATE_SHIPMENT, UPDATE_STATUS, ADD_TRACKING_EVENT, RESOLVE_EXCEPTION
            $table->string('auditable_type'); // Nama Model yang diubah
            $table->unsignedBigInteger('auditable_id'); // ID data yang diubah
            
            $table->json('old_values')->nullable(); // Data sebelum diedit
            $table->json('new_values')->nullable(); // Data sesudah diedit
            
            $table->string('ip_address', 45)->nullable(); // Alamat IP aktor
            $table->text('user_agent')->nullable(); // Browser / Device aktor
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
