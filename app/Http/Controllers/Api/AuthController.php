<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login API & Penerbitan Bearer Token via Laravel Sanctum (Slide 03 & 13)
     */
    public function login(Request $request)
    {
        // 1. Validasi Input Request
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Cari Pengguna Berdasarkan Email
        $user = User::where('email', strtolower(trim($request->email)))->first();

        // 3. Verifikasi Password & Status Aktif Akun
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password yang Anda masukkan tidak valid.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan oleh Administrator.',
            ], 403);
        }

        // 4. Terbitkan Token Sanctum dengan Role Capability
        $token = $user->createToken('cargotrace_api_token', [$user->role])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Autentikasi berhasil. Token akses diterbitkan.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'role'         => $user->role,
                    'company_name' => $user->company_name,
                ],
            ],
        ], 200);
    }

    /**
     * Cek Profil Pengguna yang Sedang Login
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Profil pengguna berhasil diambil.',
            'data'    => $request->user(),
        ]);
    }

    /**
     * Logout API & Pencabutan Token (Revocation)
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang aktif digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token API berhasil dicabut (Logout berhasil).',
        ], 200);
    }
}