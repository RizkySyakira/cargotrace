<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShipmentService
{
    /**
     * Otomatis generate nomor resi unik standar internasional (Slide 06)
     * Format: CRG-YYYYMM-XXXX (Contoh: CRG-202611-0005)
     */
    public function generateTrackingNumber(): string
    {
        $yearMonth = Carbon::now()->format('Ym');
        $prefix = "CRG-{$yearMonth}-";

        // Cari nomor urut terakhir pada bulan yang sama
        $lastShipment = Shipment::where('tracking_number', 'LIKE', "{$prefix}%")
            ->orderBy('tracking_number', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastShipment) {
            $lastSequence = (int) substr($lastShipment->tracking_number, -4);
            $nextNumber = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buat kargo baru beserta item muatan dalam satu transaksi aman (Slide 14: DB Transaction)
     */
    public function createShipment(array $data, array $items = [], ?int $userId = null): Shipment
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            // Generate nomor resi jika belum disediakan
            if (empty($data['tracking_number'])) {
                $data['tracking_number'] = $this->generateTrackingNumber();
            }

            // Tetapkan status awal dan pembuat
            $data['current_status'] = $data['current_status'] ?? Shipment::STATUS_CREATED;
            $data['created_by']     = $userId;
            $data['delay_status']   = 'ON_TIME';

            // Simpan data kargo utama
            $shipment = Shipment::create($data);

            // Simpan rincian barang/kontainer jika ada
            if (!empty($items)) {
                $shipment->items()->createMany($items);
            }

            // Catat jejak audit (Slide 10)
            AuditLog::create([
                'user_id'        => $userId,
                'action'         => 'SHIPMENT_CREATED',
                'auditable_type' => Shipment::class,
                'auditable_id'   => $shipment->id,
                'old_values'     => null,
                'new_values'     => ['tracking_number' => $shipment->tracking_number, 'status' => $shipment->current_status],
            ]);

            return $shipment->fresh(['items', 'origin', 'destination', 'carrier']);
        });
    }

    /**
     * Validasi apakah alur perpindahan status sah menurut State Machine (Slide 06)
     */
    public function canTransitionTo(Shipment $shipment, string $newStatus): bool
    {
        $allowedTransitions = Shipment::STATUS_FLOW[$shipment->current_status] ?? [];
        return in_array($newStatus, $allowedTransitions, true);
    }

    /**
     * Transisikan status kargo dengan pengawalan ketat State Machine
     */
    public function transitionStatus(Shipment $shipment, string $newStatus, ?int $userId = null, ?string $notes = null): Shipment
    {
        if (! $this->canTransitionTo($shipment, $newStatus)) {
            throw new \DomainException(
                "Transisi status dari '{$shipment->current_status}' ke '{$newStatus}' tidak sah (Ditolak oleh State Machine)."
            );
        }

        $oldStatus = $shipment->current_status;
        $updates = ['current_status' => $newStatus];

        // Jika mulai berangkat, catat actual_departure
        if ($newStatus === Shipment::STATUS_IN_TRANSIT && ! $shipment->actual_departure) {
            $updates['actual_departure'] = Carbon::now();
        }

        // Jika sampai di tujuan (DELIVERED), catat actual_arrival
        if ($newStatus === Shipment::STATUS_DELIVERED && ! $shipment->actual_arrival) {
            $updates['actual_arrival'] = Carbon::now();
        }

        if ($notes) {
            $updates['notes'] = $shipment->notes ? $shipment->notes . "\n" . $notes : $notes;
        }

        $shipment->update($updates);

        // Catat jejak audit forensik
        AuditLog::create([
            'user_id'        => $userId,
            'action'         => 'SHIPMENT_STATUS_TRANSITION',
            'auditable_type' => Shipment::class,
            'auditable_id'   => $shipment->id,
            'old_values'     => ['status' => $oldStatus],
            'new_values'     => ['status' => $newStatus],
        ]);

        return $shipment->fresh();
    }
}