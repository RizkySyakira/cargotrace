@extends('layouts.app')

@section('title', 'Pendaftaran Kargo Baru - CargoTrace')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Tombol Kembali -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-500 hover:text-sky-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        
        <!-- Header Form -->
        <div class="bg-gradient-to-r from-slate-900 to-sky-950 p-6 sm:p-8 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-400 border border-sky-400/30 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Pendaftaran Pengiriman Kargo Baru</h1>
                    <p class="text-xs text-sky-200/80 mt-0.5">Sistem akan otomatis menerbitkan nomor resi resmi dan menginisialisasi State Machine.</p>
                </div>
            </div>
        </div>

        <!-- FORM INPUT -->
        <form action="{{ route('shipments.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-medium space-y-1">
                    <strong class="block text-sm font-bold">Harap perbaiki kesalahan berikut:</strong>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- SEKSI 1: KEPEMILIKAN & ARMADA -->
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-sky-700 pb-2 border-b border-slate-100 mb-4 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>1. Pihak Pengirim & Armada Pelayaran</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Customer / Pemilik Kargo *</label>
                        <select name="customer_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <option value="">-- Pilih Akun Customer --</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->company_name ?? 'Individual' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Armada Pelayaran (Carrier) *</label>
                        <select name="carrier_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <option value="">-- Pilih Maskapai Pelayaran --</option>
                            @foreach ($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                    {{ $carrier->name }} ({{ $carrier->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- SEKSI 2: RUTE PELABUHAN & JADWAL -->
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-sky-700 pb-2 border-b border-slate-100 mb-4 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>2. Rute Pelabuhan & Komitmen Waktu (SLA)</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pelabuhan Muat Asal (Origin) *</label>
                        <select name="origin_location_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <option value="">-- Pilih Pelabuhan Asal --</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->code }}) - {{ $loc->country }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pelabuhan Bongkar Tujuan (Destination) *</label>
                        <select name="destination_location_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <option value="">-- Pilih Pelabuhan Tujuan --</option>
                            @foreach ($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->code }}) - {{ $loc->country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Perkiraan Keberangkatan (ETD)</label>
                        <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Janji Kontrak Tiba (Expected Arrival / SLA) *</label>
                        <input type="datetime-local" name="expected_arrival" required value="{{ old('expected_arrival', now()->addDays(5)->format('Y-m-d\TH:i')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- SEKSI 3: SPESIFIKASI KONTAINER & BARANG -->
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-sky-700 pb-2 border-b border-slate-100 mb-4 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>3. Manifest Peti Kemas & Komoditas Kargo</span>
                </h2>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Peti Kemas *</label>
                        <select name="cargo_type" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <option value="CONTAINER_20FT">CONTAINER 20FT</option>
                            <option value="CONTAINER_40FT">CONTAINER 40FT</option>
                            <option value="REEFER">REEFER (Pendingin)</option>
                            <option value="BULK">BULK (Curah)</option>
                            <option value="LCL">LCL (Less than Container)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Berat Total (kg) *</label>
                        <input type="number" step="0.01" name="total_weight_kg" required value="{{ old('total_weight_kg', 16000) }}" placeholder="Contoh: 15000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Volume Ruang (CBM)</label>
                        <input type="number" step="0.01" name="total_volume_cbm" value="{{ old('total_volume_cbm', 32.5) }}" placeholder="Contoh: 33.0" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Pabean (USD)</label>
                        <div class="flex">
                            <input type="number" step="0.01" name="declared_value" value="{{ old('declared_value', 75000) }}" placeholder="85000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-l-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">
                            <input type="hidden" name="currency" value="USD">
                            <span class="inline-flex items-center px-3 rounded-r-xl border border-l-0 border-slate-200 bg-slate-100 text-slate-500 text-xs font-bold">USD</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Komoditas Barang *</label>
                        <input type="text" name="item_name" required value="{{ old('item_name', 'Biji Kakao Fermentasi Ekspor Grade A') }}" placeholder="Contoh: Biji Kopi / Tekstil" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kemasan *</label>
                        <select name="package_type" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500">
                            <option value="Jute Bag">Jute Bag (Karung Goni)</option>
                            <option value="Box">Box (Karton)</option>
                            <option value="Pallet">Pallet</option>
                            <option value="Crate">Crate (Peti Kayu)</option>
                            <option value="Drum">Drum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Kemasan *</label>
                        <input type="number" name="quantity" required value="{{ old('quantity', 250) }}" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode HS Internasional (Harmonized System)</label>
                        <input type="text" name="hs_code" value="{{ old('hs_code', '1801.00') }}" placeholder="Contoh: 1801.00 atau 0901.11" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-mono focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 flex items-center pt-5">
                        <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="is_dangerous_goods" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span>Barang Berbahaya / Hazmat (Dangerous Goods)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SEKSI 4: CATATAN TAMBAHAN -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Instruksi Khusus / Catatan Pengiriman</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Simpan pada area kontainer terlindung cuaca langsung." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-sky-500 focus:bg-white">{{ old('notes') }}</textarea>
            </div>

            <!-- TOMBOL SUBMIT -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-slate-950 font-bold text-xs shadow-lg shadow-sky-500/25 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Daftarkan Pengiriman Kargo</span>
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
