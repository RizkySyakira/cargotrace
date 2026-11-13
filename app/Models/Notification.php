<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Kolom-kolom pesan notifikasi
    protected $fillable = [
        'user_id',
        'shipment_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
    ];

    // Penerjemah tipe data:
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    // RELASI: Notifikasi ini dikirim untuk user siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELASI: Notifikasi ini terkait pengiriman kargo yang mana
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}