<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER (Otomatis jadi admin jika user pertama ATAU email mengandung kata 'admin')
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Tentukan role secara dinamis
        $role = 'user'; // Default-nya user biasa

        // 1. Jika belum ada satupun user di database, pendaftar pertama ini otomatis jadi admin
        // 2. Atau jika emailnya mengandung kata 'admin'
        if (User::count() === 0 || str_contains(strtolower($request->email), 'admin')) {
            $role = 'admin';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role, // Otomatis terisi 'admin' atau 'user'
        ]);

        $token = $user->createToken('listgo-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Register berhasil!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // REGISTER KHUSUS ADMIN (Cadangan jika ingin mendaftarkan admin secara spesifik lewat endpoint khusus)
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        $token = $user->createToken('listgo-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi Admin berhasil!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah!',
            ], 401);
        }

        $token = $user->createToken('listgo-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'user' => $user, 
            'token' => $token,
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}