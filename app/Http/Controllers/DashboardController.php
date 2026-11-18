<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\Carrier;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Internal (Slide 05 & 10)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Shipment::with(['carrier', 'origin', 'destination', 'customer']);

        // OTORISASI SLIDE 13: Customer hanya melihat kargo miliknya sendiri
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('current_status', strtoupper($request->status));
        }

        // Filter carrier
        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        // Filter keterlambatan (ON_TIME / DELAYED)
        if ($request->filled('delay_status')) {
            $query->where('delay_status', strtoupper($request->delay_status));
        }

        // Pencarian resi
        if ($request->filled('search')) {
            $search = strtoupper(trim($request->search));
            $query->where('tracking_number', 'LIKE', "%{$search}%");
        }

        $shipments = $query->orderBy('id', 'desc')->paginate(10);

        // Hitung Metrik Statistik Sesuai Hak Akses
        $metricsBase = $user->isCustomer() ? Shipment::where('customer_id', $user->id) : Shipment::query();

        $stats = [
            'total'           => (clone $metricsBase)->count(),
            'in_transit'      => (clone $metricsBase)->where('current_status', 'IN_TRANSIT')->count(),
            'delivered'       => (clone $metricsBase)->where('current_status', 'DELIVERED')->count(),
            'delayed'         => (clone $metricsBase)->where('delay_status', 'DELAYED')->count(),
            'open_exceptions' => $user->isCustomer()
                ? ShipmentException::whereHas('shipment', fn($q) => $q->where('customer_id', $user->id))->where('status', '!=', 'RESOLVED')->count()
                : ShipmentException::where('status', '!=', 'RESOLVED')->count(),
        ];

        $carriers = Carrier::where('is_active', true)->get();

        return view('dashboard.index', compact('shipments', 'stats', 'carriers', 'user'));
    }
}