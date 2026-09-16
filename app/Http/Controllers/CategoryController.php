<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan semua kategori
    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'data' => $category
        ], 201);
    }

    // Menampilkan detail kategori
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category
        ], 200);
    }

    // Mengupdate kategori
    public function update(Request $request, Category $category)
    {
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

    // Menghapus kategori
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!'
        ], 200);
    }
}