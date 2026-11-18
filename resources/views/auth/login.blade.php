@extends('layouts.app')

@section('title', 'Masuk ke Portal - CargoTrace')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-3xl border border-slate-200 shadow-xl">
        
        <!-- Header Card -->
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 to-cyan-400 mx-auto flex items-center justify-center shadow-lg shadow-sky-500/30 mb-4">
                <svg class="w-7 h-7 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Portal Operasional</h2>
            <p class="text-xs text-slate-500 mt-1">Masuk untuk mengelola kargo, armada, dan analitik logistik.</p>
        </div>

        <!-- FORM LOGIN -->
        <form class="mt-8 space-y-5" action="{{ route('login.post') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Alamat Email</label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@cargotrace.com"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Kata Sandi</label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 mr-2">
                    Ingat sesi saya
                </label>
                <span class="text-slate-400">Default: password123</span>
            </div>

            <button 
                type="submit" 
                class="w-full py-3.5 px-4 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-slate-950 font-bold rounded-xl shadow-lg shadow-sky-500/25 transition duration-200 text-sm flex items-center justify-center space-x-2"
            >
                <span>Masuk ke Dashboard</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </form>

        <!-- AKUN DEMO KLIK-CEPAT (UNTUK UJIAN SIDANG DOSEN) -->
        <div class="pt-6 border-t border-slate-100">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">Akun Uji Coba Demo (Klik untuk Isi):</p>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <button type="button" onclick="fillLogin('admin@cargotrace.com')" class="p-2 text-center rounded-lg bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 transition">
                    <strong class="block text-[11px]">Admin</strong>
                    <span class="text-[10px] text-slate-400">Semua Akses</span>
                </button>
                <button type="button" onclick="fillLogin('operator@cargotrace.com')" class="p-2 text-center rounded-lg bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 transition">
                    <strong class="block text-[11px]">Operator</strong>
                    <span class="text-[10px] text-slate-400">Terminal JICT</span>
                </button>
                <button type="button" onclick="fillLogin('customer@cargotrace.com')" class="p-2 text-center rounded-lg bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 transition">
                    <strong class="block text-[11px]">Customer</strong>
                    <span class="text-[10px] text-slate-400">PT Nusantara</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function fillLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password123';
}
</script>
@endsection