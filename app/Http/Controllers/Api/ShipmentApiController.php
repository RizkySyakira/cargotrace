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
     * Daftar seluruh kargo dengan filter status dan pembatasan hak akses (Role Scoping)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Shipment::with(['carrier', 'origin', 'destination']);

        // Jika Customer, batasi hanya data kargo miliknya saja
        if ($user && $user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        // Filter berdasarkan status jika diminta
        if ($request->filled('status')) {
            $query->where('current_status', strtoupper($request->status));
        }

        // Filter berdasarkan kondisi keterlambatan (ON_TIME / DELAYED)
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
     * Rincian lengkap kargo, rute, barang kontainer, dan timeline
     */
    public function show(string $tracking_number)
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

        return response()->json([
            'success' => true,
            'message' => 'Detail kargo berhasil diambil.',
            'data'    => $shipment,
        ], 200);
    }

    /**
     * GET /api/v1/public/track/{tracking_number}
     * Endpoint terbuka untuk pelacakan kargo tanpa perlu token
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

        // Ambil data cuaca di lokasi terakhir jika tersedia
        $latestEvent = $shipment->trackingEvents->last();
        $weather = null;
        if ($latestEvent && $latestEvent->latitude && $latestEvent->longitude) {
            $weather = $this->weatherService->getWeather((float) $latestEvent->latitude, (float) $latestEvent->longitude);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pelacakan kargo berhasil dimuat.',
            'data'    => [
                'shipment'        => $shipment,
                'latest_checkpoint' => $latestEvent,
                'current_weather'   => $weather,
            ],
        ], 200);
    }

    /**
     * POST /api/v1/shipments/{tracking_number}/checkpoints
     * Tambah pos kejadian/checkpoint baru oleh Operator Pelabuhan (Slide 07)
     */
    public function storeCheckpoint(Request $request, string $tracking_number)
    {
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

        $checkpoint = $this->trackingService->recordCheckpoint($shipment, $validated, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Pos perjalanan (checkpoint) berhasil dicatat.',
            'data'    => $checkpoint,
        ], 201);
    }

    /**
     * POST /api/v1/shipments/{tracking_number}/exceptions
     * Laporkan kendala kargo (Badai, Karantina, Kemacetan Pelabuhan)
     */
    public function storeException(Request $request, string $tracking_number)
    {
        $trackingNumber = strtoupper(trim($tracking_number));
        $shipment = Shipment::where('tracking_number', $trackingNumber)->firstOrFail();

        $validated = $request->validate([
            'exception_type'         => 'required|string',
            'severity'               => 'required|in:LOW,MEDIUM,HIGH',
            'description'            => 'required|string',
            'detected_at'            => 'nullable|date',
            'additional_delay_hours' => 'nullable|integer|min:1',
        ]);

        $exception = $this->exceptionService->reportException($shipment, $validated, $request->user()->id);

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