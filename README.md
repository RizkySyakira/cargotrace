# 🚢 CargoTrace: Global Shipment Visibility & Tracking Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Automated Tests](https://img.shields.io/badge/Tests-14%20Passed%20(100%25)-success?style=for-the-badge&logo=githubactions&logoColor=white)]()
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

**CargoTrace** is an enterprise-grade maritime cargo tracking and visibility platform engineered for cross-border container shipping logistics. Designed to fulfill the Final Project (UAS) requirements for **Web Programming 2**, the system delivers real-time container journey visualization, automated delay detection (SLA engine), external meteorological satellite telemetry, and a secure RESTful API with token-based authorization.

---

## 🌟 Key Capabilities

1. **Interactive Maritime Route Visualization (Leaflet.js + OpenStreetMap)**
   - Zero-dependency on paid map billing (no Google Maps API fees or credit cards required).
   - Dynamic polyline route generation linking Port of Origin $\rightarrow$ Sea Transit Checkpoints $\rightarrow$ Port of Destination.
   - Interactive waypoint markers with coordinate popups and live marine weather conditions.

2. **Deterministic Delay & SLA Engine (Slide 08)**
   - Mathematical difference calculation between contractual Expected Arrival (SLA) and dynamic Estimated Arrival Time (ETA).
   - Configurable severity classifications:
     - $\le 24\text{ hours} \rightarrow \mathbf{LOW}$
     - $24\text{--}72\text{ hours} \rightarrow \mathbf{MEDIUM}$ (e.g. tropical storm rerouting)
     - $> 72\text{ hours} \rightarrow \mathbf{HIGH}$ (critical operational anomaly)

3. **Strict State Machine Lifecycle (Slide 06)**
   - Whitelist-enforced state transitions (`CREATED` $\rightarrow$ `BOOKED` $\rightarrow$ `PICKED_UP` $\rightarrow$ `IN_TRANSIT` $\rightarrow$ `DELIVERED`).
   - Prevents illegal status jumping and locks terminal states (`DELIVERED` & `CANCELLED`) permanently.

4. **Live Satellite Weather Telemetry (Slide 09)**
   - Real-time meteorological integration powered by the free Open-Meteo Satellite API.
   - Graceful offline fallback mechanism ensuring resilience against internet timeouts during demonstrations.

5. **RESTful API v1 with Sanctum & Strict Authorization (Slide 13)**
   - Public tracking endpoints for third-party consumers without credentials.
   - Protected endpoints guarded by Bearer tokens and role capabilities.
   - Strict tenant-level isolation: Customers are forbidden (403 Forbidden) from viewing or altering competitor shipments.

---

## 🏛️ Architecture & Clean Code Design

The system enforces a **Service Layer Architecture**, keeping controllers thin and isolating domain business logic into dedicated services:

```
[Browser / REST Client]
          │
          ▼
   [HTTP Routes] ────────► Web & API Sanctum Middleware
          │
          ▼
   [Controllers] ────────► Request Validation & JSON/Blade Response
          │
          ▼
  [Service Layer]
   ├── ShipmentService   (Tracking number generator & State Machine guard)
   ├── TrackingService   (Append-only chronological event logging)
   ├── EtaService        (Deterministic delay & severity calculator)
   ├── ExceptionService  (Incident workflow & automated delay adjustment)
   └── WeatherService    (Open-Meteo satellite integration with caching)
          │
          ▼
  [Eloquent Models] ─────► Relational constraints & Mass-assignment guards
          │
          ▼
   [MySQL Database]
```

---

## 👥 Pre-Configured Demo Accounts

All seeded accounts share the default password: **`password123`**

| Role | Email | Organization | Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | CargoTrace Global HQ | Full system oversight, audit trails, and configuration management |
| **Terminal Operator** | `operator@cargotrace.com` | JICT Tanjung Priok | Field checkpoint logging, exception reporting, and status updates |
| **Customer 1** | `customer@cargotrace.com` | PT Nusantara Agri Ekspor | Export shipment monitoring, manifest inspection, delay alerts |
| **Customer 2** | `customer2@cargotrace.com` | Asia Pacific Semiconductor Ltd | Independent account used to verify 403 authorization boundary tests |

---

## 🚢 Seeded Maritime Reference Scenarios

| Tracking Number | Carrier | Route | Commodity (HS Code) | Scenario / Operational State |
| :--- | :--- | :--- | :--- | :--- |
| **`CRG-2026-0001`** | Samudera Indonesia | Jakarta $\rightarrow$ Singapore | Gayo Arabica Coffee (`0901.11`) | **ON-TIME & IN-TRANSIT**: Navigating Bangka Strait on schedule. |
| **`CRG-2026-0002`** | Maersk Line | Singapore $\rightarrow$ Rotterdam | Microcontrollers (`8542.31`) | **DELAYED BY WEATHER (+48h)**: Cyclone in Indian Ocean; high severity alert. |
| **`CRG-2026-0003`** | Evergreen Marine | Surabaya $\rightarrow$ Shanghai | Frozen Vanilla Beans (`0905.10`) | **CUSTOMS HOLD (+24h)**: Reefer container undergoing phytosanitary re-check. |
| **`CRG-2026-0004`** | MSC Line | Shanghai $\rightarrow$ Jakarta | Solar PV Modules (`8541.43`) | **DELIVERED**: Successfully discharged at Tanjung Priok with signed proof. |

---

## 🚀 Quick Start Installation

### 1. Requirements
- PHP 8.2 or higher
- Composer
- MySQL 8.0 (XAMPP / native)

### 2. Setup Database & Seed Reference Data
```powershell
# Navigate to the repository
cd C:\xampp\htdocs\cargotrace

# Run migrations and seed realistic maritime data
php artisan migrate:fresh --seed
```

### 3. Launch Development Server
```powershell
php artisan serve
```
Open your browser at: **`http://127.0.0.1:8000`**

---

## 🧪 Automated Testing Suite

The application includes comprehensive automated feature tests covering all requirements from Slides 06, 08, 13, and 16.

```powershell
php artisan test
```

### Test Coverage Highlights:
- ✅ `ShipmentAuthorizationTest`: Customer isolation, 403 Forbidden enforcement, and operator privileges.
- ✅ `ShipmentLifecycleTest`: State Machine sequential progression and rejection of illegal status skips.
- ✅ `EtaDelayTest`: Mathematical accuracy of delay durations and threshold classifications (LOW, MEDIUM, HIGH).

---

## 📡 REST API v1 Endpoints (Slide 13)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Public | Authenticates credentials and returns a Bearer token |
| `GET` | `/api/v1/public/track/{tracking_number}` | Public | Public shipment telemetry and Open-Meteo satellite weather |
| `GET` | `/api/v1/auth/me` | Protected | Retrieves current authenticated profile |
| `POST` | `/api/v1/auth/logout` | Protected | Revokes active Sanctum API token |
| `GET` | `/api/v1/shipments` | Protected | Paginated list of shipments (strictly scoped by customer ownership) |
| `GET` | `/api/v1/shipments/{tracking_number}` | Protected | Complete container manifest, timeline, and exception audit logs |
| `POST` | `/api/v1/shipments/{tracking_number}/checkpoints` | Operator/Admin | Records a new journey checkpoint (auto-fetches satellite weather) |
| `POST` | `/api/v1/shipments/{tracking_number}/exceptions` | Operator/Admin | Logs operational disruption and recalculates ETA dynamically |

---

## ⚖️ Academic License & Compliance

Developed for the **Web Programming 2 Final Examination (UAS)**. Designed strictly under the sanctioned curriculum boundaries:
- Individual assignment compliance (group scenarios skipped).
- No paid cloud billing APIs used (Free OpenStreetMap and Open-Meteo utilized).
- Standardized PSR-12 and Clean Architecture compliance.
