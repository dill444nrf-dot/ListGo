<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Memantau / melihat daftar seluruh user
    public function index(Request $request)
    {
        // Pastikan hanya admin yang bisa akses (pengamanan berlapis)
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Anda bukan admin.'
            ], 403);
        }

        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar seluruh user berhasil diambil',
            'data' => $users
        ], 200);
    }

    // 2. Menghapus user tertentu
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak! Anda bukan admin.'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Mencegah admin menghapus akunnya sendiri
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri!'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus oleh admin'
        ], 200);
    }
}