<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'item_name',
        'package_type',
        'quantity',
        'weight_kg',
        'volume_cbm',
        'hs_code',
        'is_dangerous_goods',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight_kg' => 'float',
            'volume_cbm' => 'float',
            'is_dangerous_goods' => 'boolean',
        ];
    }

    // RELASI: Rincian barang ini MILIK dari pengiriman kargo mana (belongsTo)
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}