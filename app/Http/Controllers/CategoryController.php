<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan semua kategori HANYA milik user yang sedang login
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    // Menyimpan kategori baru dan otomatis dikaitkan ke user yang login
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'user_id' => auth()->id(), // Otomatis mengisi ID user yang sedang login
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'data' => $category
        ], 201);
    }

    // Menampilkan detail kategori (pastikan milik user yang login)
    public function show($id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ], 200);
    }

    // Mengupdate kategori (pastikan milik user yang login)
    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui!',
            'data' => $category
        ], 200);
    }

    // Menghapus kategori (pastikan milik user yang login)
    public function destroy($id)
    {
        $category = Category::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!'
        ], 200);
    }
}