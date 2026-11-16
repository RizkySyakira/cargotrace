<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use App\Services\ExceptionService;
use App\Services\WeatherService;

class ShipmentApiController extends Controller
{
    protected ShipmentService $shipmentService;
    protected TrackingService $trackingService;
    protected ExceptionService $exceptionService;
    protected WeatherService $weatherService;

    public function __construct(
        ShipmentService $shipmentService,
        TrackingService $trackingService,
        ExceptionService $exceptionService,
        WeatherService $weatherService
    ) {
        $this->shipmentService  = $shipmentService;
        $this->trackingService  = $trackingService;
        $this->exceptionService = $exceptionService;
        $this->weatherService   = $weatherService;
    }

    /**
     * GET /api/v1/shipments
     * Slide 13: Customer hanya dapat melihat shipment miliknya sendiri
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Shipment::with(['carrier', 'origin', 'destination']);

        // OTORISASI: Customer hanya melihat kargo miliknya sendiri (Data Isolation)
        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Filter status jika diminta
        if ($request->filled('status')) {
            $query->where('current_status', strtoupper($request->status));
        }

        // Filter delay_status (ON_TIME / DELAYED)
        if ($request->filled('delay_status')) {
            $query->where('delay_status', strtoupper($request->delay_status));
        }

        $shipments = $query->orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar data kargo berhasil diambil.',
            'data'    => $shipments,
        ], 200);
    }

    /**
     * GET /api/v1/shipments/{tracking_number}
     * Slide 13: Otorisasi ketat - Customer tidak boleh melihat kargo milik orang lain (403 Forbidden)
     */
    public function show(Request $request, string $tracking_number)
    {
        $trackingNumber = strtoupper(trim($tracking_number));

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->with([
                'carrier',
                'origin',
                'destination',
                'items',
                'trackingEvents.location',
                'exceptions',
            ])
            ->first();

        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => "Kargo dengan nomor resi '{$trackingNumber}' tidak ditemukan.",
            ], 404);
        }

        // OTORISASI SLIDE 13: Tolak jika Customer mencoba mengintip kargo milik Customer lain!
        $user = $request->user();
        if ($user && $user->isCustomer() && $shipment->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak (403 Forbidden). Anda tidak memiliki izin untuk mengakses data kargo milik perusahaan lain.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kargo berhasil diambil.',
            'data'    => $shipment,
        ], 200);
    }

    /**
     * GET /api/v1/public/track/{tracking_number}
     * Endpoint terbuka untuk pelacakan publik (tanpa data sensitif kepemilikan internal)
     */
    public function trackPublic(string $tracking_number)
    {
        $trackingNumber = strtoupper(trim($tracking_number));

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->with([
                'carrier:id,name,code',
                'origin:id,name,code,city,country,latitude,longitude',
                'destination:id,name,code,city,country,latitude,longitude',
                'items:id,shipment_id,item_name,hs_code,quantity,package_type,weight_kg',
                'trackingEvents',
                'exceptions',
            ])
            ->first();

        if (! $shipment) {
            return response()->json([
                'success' => false,
                'message' => "Resi kargo '{$trackingNumber}' tidak ditemukan.",
            ], 404);
        }

        $latestEvent = $shipment->trackingEvents->last();
        $weather = null;
        if ($latestEvent && $latestEvent->latitude && $latestEvent->longitude) {
            $weather = $this->weatherService->getWeather((float) $latestEvent->latitude, (float) $latestEvent->longitude);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pelacakan kargo berhasil dimuat.',
            'data'    => [
                'shipment'          => $shipment,
                'latest_checkpoint' => $latestEvent,
                'current_weather'   => $weather,
            ],
        ], 200);
    }

    /**
     * POST /api/v1/shipments/{tracking_number}/checkpoints
     * Slide 13: Hanya Operator atau Admin yang boleh menambah checkpoint pos kargo!
     */
    public function storeCheckpoint(Request $request, string $tracking_number)
    {
        $user = $request->user();

        // OTORISASI SLIDE 13: Customer DILARANG mengubah pos kargo!
        if (! $user->isOperator() && ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak (403 Forbidden). Hanya Operator Terminal Pelabuhan atau Admin yang berhak mencatat checkpoint kargo.',
            ], 403);
        }

        $trackingNumber = strtoupper(trim($tracking_number));
        $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();

        $validated = $request->validate([
            'location_name'     => 'required|string|max:255',
            'event_type'        => 'required|string',
            'description'       => 'required|string',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'event_time'        => 'nullable|date',
            'location_id'       => 'nullable|exists:locations,id',
            'weather_condition' => 'nullable|string',
            'weather_temp_c'    => 'nullable|numeric',
        ]);

        $checkpoint = $this->trackingService->recordCheckpoint($shipment, $validated, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Pos perjalanan (checkpoint) berhasil dicatat.',
            'data'    => $checkpoint,
        ], 201);
    }

    /**
     * POST /api/v1/shipments/{tracking_number}/exceptions
     * Slide 13: Hanya Operator atau Admin yang boleh melaporkan kendala kargo!
     */
    public function storeException(Request $request, string $tracking_number)
    {
        $user = $request->user();

        // OTORISASI SLIDE 13: Customer DILARANG memicu delay/gangguan kargo!
        if (! $user->isOperator() && ! $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak (403 Forbidden). Hanya Operator atau Admin yang berhak melaporkan anomali kargo.',
            ], 403);
        }

        $trackingNumber = strtoupper(trim($tracking_number));
        $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();

        $validated = $request->validate([
            'exception_type'         => 'required|string',
            'severity'               => 'required|in:LOW,MEDIUM,HIGH',
            'description'            => 'required|string',
            'detected_at'            => 'nullable|date',
            'additional_delay_hours' => 'nullable|integer|min:1',
        ]);

        $exception = $this->exceptionService->reportException($shipment, $validated, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Kendala kargo berhasil dilaporkan dan jadwal tiba (ETA) telah disesuaikan.',
            'data'    => [
                'exception' => $exception,
                'shipment'  => $shipment->fresh(),
            ],
        ], 201);
    }
}