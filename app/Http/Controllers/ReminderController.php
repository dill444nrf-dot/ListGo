<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // Menampilkan semua reminder (JSON)
    public function index()
    {
        $reminders = Reminder::with('task')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ], 200);
    }

    // Menyimpan reminder baru (JSON)
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'reminder_time' => 'required|date',
        ]);

        $reminder = Reminder::create([
            'task_id' => $request->task_id,
            'reminder_time' => $request->reminder_time,
            'is_sent' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil ditambahkan!',
            'data' => $reminder
        ], 201);
    }

    // Menampilkan detail reminder (JSON)
    public function show(Reminder $reminder)
    {
        $reminder->load('task');

        return response()->json([
            'success' => true,
            'data' => $reminder
        ], 200);
    }

    // Mengupdate reminder (JSON)
    public function update(Request $request, Reminder $reminder)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'reminder_time' => 'required|date',
        ]);

        $reminder->update([
            'task_id' => $request->task_id,
            'reminder_time' => $request->reminder_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil diperbarui!',
            'data' => $reminder
        ], 200);
    }

    // Menghapus reminder (JSON)
    public function destroy(Reminder $reminder)
    {
        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil dihapus!'
        ], 200);
    }
}
