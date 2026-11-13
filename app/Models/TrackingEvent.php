<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    // Kolom-kolom titik checkpoint perjalanan
    protected $fillable = [
        'shipment_id',
        'location_id',
        'location_name',
        'event_type',
        'event_time',
        'latitude',
        'longitude',
        'description',
        'weather_condition',
        'weather_temp_c',
        'created_by',
    ];

    // Penerjemah tipe data:
    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'weather_temp_c' => 'float',
        ];
    }

    // RELASI: Catatan perjalanan ini MILIK kargo yang mana (belongsTo)
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    // RELASI: Titik ini berada di fasilitas pelabuhan yang mana
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // RELASI: Siapa petugas operator yang mencatat titik ini
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}