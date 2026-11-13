<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'city',
        'country',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function outboundShipments()
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    public function inboundShipments()
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }
}