<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    // 1. DAFTAR NAMA STATUS (Slide 06 - State Machine)
    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    // 2. ATURAN ALUR STATUS YANG SAH (Whitelist Transisi)
    public const STATUS_FLOW = [
        self::STATUS_CREATED => [self::STATUS_BOOKED, self::STATUS_CANCELLED],
        self::STATUS_BOOKED => [self::STATUS_PICKED_UP, self::STATUS_CANCELLED],
        self::STATUS_PICKED_UP => [self::STATUS_IN_TRANSIT, self::STATUS_CANCELLED],
        self::STATUS_IN_TRANSIT => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED => [], // Terminal: sudah sampai, tidak boleh mundur lagi!
        self::STATUS_CANCELLED => [], // Terminal: kargo batal
    ];

    // Kolom-kolom yang boleh diisi
    protected $fillable = [
        'tracking_number',
        'customer_id',
        'carrier_id',
        'origin_location_id',
        'destination_location_id',
        'cargo_type',
        'total_weight_kg',
        'total_volume_cbm',
        'declared_value',
        'currency',
        'current_status',
        'expected_departure',
        'actual_departure',
        'expected_arrival',
        'estimated_arrival',
        'actual_arrival',
        'delay_status',
        'delay_duration_minutes',
        'delay_severity',
        'created_by',
        'notes',
    ];

    // Penerjemah tipe data tanggal dan angka
    protected function casts(): array
    {
        return [
            'total_weight_kg' => 'float',
            'total_volume_cbm' => 'float',
            'declared_value' => 'float',
            'expected_departure' => 'datetime',
            'actual_departure' => 'datetime',
            'expected_arrival' => 'datetime',
            'estimated_arrival' => 'datetime',
            'actual_arrival' => 'datetime',
            'delay_duration_minutes' => 'integer',
        ];
    }

    // RELASI-RELASI UTAMA (Slide 11):
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id'); // Kargo ini milik siapa?
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class); // Kapal apa yang bawa?
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_location_id'); // Pelabuhan asal mana?
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_location_id'); // Pelabuhan tujuan mana?
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class); // Barang apa saja di dalamnya?
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc'); // Riwayat perjalanannya
    }

    public function exceptions()
    {
        return $this->hasMany(ShipmentException::class)->orderBy('detected_at', 'desc'); // Kendala di jalan
    }

    public function openExceptions()
    {
        return $this->hasMany(ShipmentException::class)->where('status', '!=', 'RESOLVED'); // Kendala yang belum selesai
    }

    // Fungsi pembantu mengecek status kargo
    public function isDelivered(): bool
    {
        return $this->current_status === self::STATUS_DELIVERED;
    }

    public function isDelayed(): bool
    {
        return $this->delay_status === 'DELAYED';
    }
}