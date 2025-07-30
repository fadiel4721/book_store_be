<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Cari user berdasarkan email
            $user = User::where('email', $validated['email'])->first();

            // Periksa kredensial
            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data'    => [
                    'user'  => $user,
                    'token' => $token,
                ],
            ], 200);

        } catch (ValidationException $e) {
            // Response untuk validasi gagal
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            // Response untuk error tak terduga
            return response()->json([
                'success' => false,
                'message' => 'Gagal login: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Hapus semua token user saat ini
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout: ' . $e->getMessage(),
            ], 500);
        }
    }
}
