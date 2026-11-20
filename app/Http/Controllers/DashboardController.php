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
     * Tampilkan Dashboard Internal (Slide 05, 10, & 14)
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

        // Hitung Metrik Statistik Sesuai Hak Akses (Slide 10)
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

        // Rata-rata persentase keterlambatan (Average Delay Rate)
        $avgDelayRate = $stats['total'] > 0 ? round(($stats['delayed'] / $stats['total']) * 100, 1) : 0;

        $carriers = Carrier::where('is_active', true)->get();

        // Data Donut Chart: Distribusi Keterlambatan per Carrier
        $carrierStats = [];
        foreach ($carriers as $carrier) {
            $carrierTotal = Shipment::where('carrier_id', $carrier->id)->count();
            $carrierDelayed = Shipment::where('carrier_id', $carrier->id)->where('delay_status', 'DELAYED')->count();
            if ($carrierTotal > 0) {
                $carrierStats[] = [
                    'name'    => $carrier->name,
                    'code'    => $carrier->code,
                    'total'   => $carrierTotal,
                    'delayed' => $carrierDelayed,
                    'pct'     => $carrierTotal > 0 ? round(($carrierDelayed / $carrierTotal) * 100) : 0,
                ];
            }
        }

        // Data Mini Peta Leaflet di Dashboard
        $mapShipments = Shipment::with(['origin', 'destination', 'trackingEvents'])
            ->whereIn('current_status', ['IN_TRANSIT', 'DELIVERED'])
            ->take(5)
            ->get();

        return view('dashboard.index', compact('shipments', 'stats', 'carriers', 'user', 'avgDelayRate', 'carrierStats', 'mapShipments'));
    }
}
