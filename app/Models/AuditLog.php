<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Kolom catatan jejak audit forensik
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    // Penerjemah: Data diff perubahan disimpan sebagai array/JSON
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    // RELASI: Siapa orang/petugas yang melakukan aksi perubahan
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELASI POLIMORFIS: Jejak audit ini merekam model apa saja (Shipment, Exception, dll)
    public function auditable()
    {
        return $this->morphTo();
    }
}