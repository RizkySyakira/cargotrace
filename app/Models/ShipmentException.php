<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentException extends Model
{
    use HasFactory;

    // 1. DAFTAR JENIS GANGGUAN RESMI (Slide 08)
    public const TYPE_CUSTOMS_HOLD = 'CUSTOMS_HOLD';             // Tertahan Bea Cukai
    public const TYPE_WEATHER_DELAY = 'WEATHER_DELAY';           // Badai / Cuaca Buruk
    public const TYPE_PORT_CONGESTION = 'PORT_CONGESTION';       // Pelabuhan Antre / Macet
    public const TYPE_DAMAGED = 'DAMAGED';                       // Muatan Rusak
    public const TYPE_MISROUTED = 'MISROUTED';                   // Salah Rute Jalur
    public const TYPE_DOCUMENTATION_ISSUE = 'DOCUMENTATION_ISSUE'; // Masalah Surat Dokumen

    // 2. SIKLUS PENYELESAIAN MASALAH (Workflow)
    public const STATUS_OPEN = 'OPEN';                           // Baru terjadi
    public const STATUS_INVESTIGATING = 'INVESTIGATING';         // Sedang ditangani petugas
    public const STATUS_RESOLVED = 'RESOLVED';                   // Sudah beres dan kapal jalan lagi

    protected $fillable = [
        'shipment_id',
        'exception_type',
        'severity',
        'status',
        'description',
        'detected_at',
        'resolved_at',
        'resolution_notes',
        'reported_by',
        'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // RELASI: Masalah ini terjadi pada kargo mana
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    // RELASI: Siapa petugas yang melaporkan masalah ini
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // RELASI: Siapa petugas yang menyelesaikan masalah ini
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}