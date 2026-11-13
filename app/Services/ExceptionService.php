<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\AuditLog;
use Carbon\Carbon;

class ExceptionService
{
    protected EtaService $etaService;

    public function __construct(EtaService $etaService)
    {
        $this->etaService = $etaService;
    }

    /**
     * Laporkan kendala kargo baru (Slide 08: Exception Handling)
     */
    public function reportException(Shipment $shipment, array $data, ?int $userId = null): ShipmentException
    {
        $exception = ShipmentException::create([
            'shipment_id'      => $shipment->id,
            'exception_type'   => $data['exception_type'],
            'severity'         => $data['severity'] ?? 'MEDIUM',
            'status'           => 'OPEN',
            'description'      => $data['description'],
            'detected_at'      => !empty($data['detected_at']) ? Carbon::parse($data['detected_at']) : Carbon::now(),
            'reported_by'      => $userId,
        ]);

        // Jika kendala memiliki estimasi penundaan waktu (dalam jam), update ETA otomatis
        if (!empty($data['additional_delay_hours'])) {
            $currentEta = Carbon::parse($shipment->estimated_arrival);
            $newEta = $currentEta->addHours((int) $data['additional_delay_hours']);
            $this->etaService->applyDelay($shipment, $newEta);
        }

        AuditLog::create([
            'user_id'        => $userId,
            'action'         => 'EXCEPTION_REPORTED',
            'auditable_type' => ShipmentException::class,
            'auditable_id'   => $exception->id,
            'old_values'     => null,
            'new_values'     => ['type' => $exception->exception_type, 'severity' => $exception->severity],
        ]);

        return $exception;
    }

    /**
     * Selesaikan kendala kargo (RESOLVED)
     */
    public function resolveException(ShipmentException $exception, string $notes, ?int $userId = null): ShipmentException
    {
        $exception->update([
            'status'           => 'RESOLVED',
            'resolved_at'      => Carbon::now(),
            'resolution_notes' => $notes,
            'resolved_by'      => $userId,
        ]);

        AuditLog::create([
            'user_id'        => $userId,
            'action'         => 'EXCEPTION_RESOLVED',
            'auditable_type' => ShipmentException::class,
            'auditable_id'   => $exception->id,
            'old_values'     => ['status' => 'OPEN/INVESTIGATING'],
            'new_values'     => ['status' => 'RESOLVED'],
        ]);

        return $exception->fresh();
    }
}
