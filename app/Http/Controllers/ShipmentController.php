<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Tampilkan form pembuatan kargo baru (Slide 05: Khusus Admin & Operator)
     */
    public function create()
    {
        $user = Auth::user();

        // Otorisasi: Customer tidak boleh mendaftarkan kargo sendiri
        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak. Hanya Admin atau Operator yang berhak mendaftarkan pengiriman kargo baru.');
        }

        $customers = User::where('role', 'customer')->where('is_active', true)->get();
        $carriers = Carrier::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->orderBy('city', 'asc')->get();

        return view('shipments.create', compact('customers', 'carriers', 'locations'));
    }

    /**
     * Simpan kargo baru ke database (Slide 14: DB Transaction)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi input data pengiriman & muatan
        $validated = $request->validate([
            'customer_id'             => 'required|exists:users,id',
            'carrier_id'              => 'required|exists:carriers,id',
            'origin_location_id'      => 'required|exists:locations,id|different:destination_location_id',
            'destination_location_id' => 'required|exists:locations,id',
            'cargo_type'              => 'required|string',
            'total_weight_kg'         => 'required|numeric|min:1',
            'total_volume_cbm'        => 'nullable|numeric|min:0.1',
            'declared_value'          => 'nullable|numeric|min:0',
            'currency'                => 'required|string|max:3',
            'expected_departure'      => 'nullable|date',
            'expected_arrival'        => 'required|date|after_or_equal:expected_departure',
            'notes'                   => 'nullable|string',

            // Rincian barang kontainer (Item Manifest)
            'item_name'               => 'required|string|max:255',
            'package_type'            => 'required|string',
            'quantity'                => 'required|integer|min:1',
            'hs_code'                 => 'nullable|string|max:20',
            'is_dangerous_goods'      => 'nullable|boolean',
        ]);

        $shipmentData = [
            'customer_id'             => $validated['customer_id'],
            'carrier_id'              => $validated['carrier_id'],
            'origin_location_id'      => $validated['origin_location_id'],
            'destination_location_id' => $validated['destination_location_id'],
            'cargo_type'              => $validated['cargo_type'],
            'total_weight_kg'         => $validated['total_weight_kg'],
            'total_volume_cbm'        => $validated['total_volume_cbm'] ?? null,
            'declared_value'          => $validated['declared_value'] ?? 0,
            'currency'                => $validated['currency'],
            'current_status'          => Shipment::STATUS_CREATED,
            'expected_departure'      => $validated['expected_departure'] ?? now(),
            'actual_departure'        => null,
            'expected_arrival'        => $validated['expected_arrival'],
            'estimated_arrival'       => $validated['expected_arrival'],
            'notes'                   => $validated['notes'] ?? null,
        ];

        $items = [
            [
                'item_name'          => $validated['item_name'],
                'package_type'       => $validated['package_type'],
                'quantity'           => $validated['quantity'],
                'weight_kg'          => $validated['total_weight_kg'],
                'volume_cbm'         => $validated['total_volume_cbm'] ?? null,
                'hs_code'            => $validated['hs_code'] ?? null,
                'is_dangerous_goods' => $request->boolean('is_dangerous_goods'),
            ]
        ];

        // Eksekusi pembuatan kargo via ShipmentService
        $shipment = $this->shipmentService->createShipment($shipmentData, $items, $user->id);

        return redirect()->route('tracking.show', $shipment->tracking_number)
            ->with('success', "Pengiriman kargo berhasil didaftarkan dengan nomor resi: {$shipment->tracking_number}");
    }

    /**
     * Update Status Kargo berdasarkan State Machine (Slide 06)
     */
    public function transitionStatus(Request $request, string $tracking_number)
    {
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Hanya Operator atau Admin yang boleh mengubah status kargo.');
        }

        $shipment = Shipment::where('tracking_number', $tracking_number)->firstOrFail();

        $request->validate([
            'status' => 'required|string',
            'notes'  => 'nullable|string',
        ]);

        try {
            $this->shipmentService->transitionStatus($shipment, $request->status, $user->id, $request->notes);

            return back()->with('success', "Status kargo {$shipment->tracking_number} berhasil diperbarui menjadi {$request->status}!");
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
