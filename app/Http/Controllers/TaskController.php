<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Menampilkan semua task HANYA milik user yang sedang login
    public function index()
    {
        $tasks = Task::with('category')
            ->where('user_id', auth()->id()) // Filter berdasarkan user yang login
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ], 200);
    }

    // Menampilkan data kategori (pendukung form)
    public function create()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    // Menyimpan task baru dan otomatis dikaitkan ke user yang login
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:pending,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task = Task::create([
            'user_id' => auth()->id(), // Otomatis mengisi ID user yang sedang login
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil ditambahkan!',
            'data' => $task
        ], 201);
    }

    // Menampilkan detail task (dipastikan milik user yang login)
    public function show($id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('category')
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan atau bukan milik akun ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }

    // Mengambil data untuk form edit
    public function edit($id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan'
            ], 404);
        }

        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => [
                'task' => $task,
                'categories' => $categories
            ]
        ], 200);
    }

    // Mengupdate task (dipastikan milik user yang login)
    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan atau tidak bisa diubah'
            ], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'status' => 'required|in:pending,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diperbarui!',
            'data' => $task
        ], 200);
    }

    // Menghapus task (dipastikan milik user yang login)
    public function destroy($id)
    {
        $task = Task::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan'
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus!'
        ], 200);
    }
}