<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Menampilkan semua task
    public function index()
    {
        $tasks = Task::with('category')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ], 200);
    }

    // Menampilkan form tambah task (Biasanya untuk API mengembalikan data pendukung)
    public function create()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    // Menyimpan task baru
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
            'user_id' => auth()->id(),
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

    // Menampilkan detail task
    public function show(Task $task)
    {
        $task->load('category');

        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }

    // Menampilkan form edit task
    public function edit(Task $task)
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'data' => [
                'task' => $task,
                'categories' => $categories
            ]
        ], 200);
    }

    // Mengupdate task
    public function update(Request $request, Task $task)
    {
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

    // Menghapus task
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus!'
        ], 200);
    }
}