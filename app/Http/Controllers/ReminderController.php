<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Task;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    // Menampilkan reminder HANYA milik user yang sedang login
    public function index()
    {
        $reminders = Reminder::with('task.category')
            ->whereHas('task', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ], 200);
    }

    // Menyimpan reminder baru (pastikan task-nya milik user yang login)
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $taskExists = Task::where('id', $value)
                        ->where('user_id', auth()->id())
                        ->exists();

                    if (!$taskExists) {
                        $fail('Task tidak ditemukan atau bukan milik akun ini.');
                    }
                },
            ],
            'reminder_time' => 'required|date',
        ]);

        $reminder = Reminder::create([
            'task_id' => $request->task_id,
            'reminder_time' => $request->reminder_time,
            'is_sent' => false,
        ]);

        $reminder->load('task.category');

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil ditambahkan!',
            'data' => $reminder
        ], 201);
    }

    // Menampilkan detail reminder (pastikan milik user yang login)
    public function show($id)
    {
        $reminder = Reminder::with('task.category')
            ->whereHas('task', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('id', $id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reminder
        ], 200);
    }

    // Mengupdate reminder (pastikan milik user yang login)
    public function update(Request $request, $id)
    {
        $reminder = Reminder::whereHas('task', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('id', $id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'task_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $taskExists = Task::where('id', $value)
                        ->where('user_id', auth()->id())
                        ->exists();

                    if (!$taskExists) {
                        $fail('Task tidak ditemukan atau bukan milik akun ini.');
                    }
                },
            ],
            'reminder_time' => 'required|date',
        ]);

        $reminder->update([
            'task_id' => $request->task_id,
            'reminder_time' => $request->reminder_time,
        ]);

        $reminder->load('task.category');

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil diperbarui!',
            'data' => $reminder
        ], 200);
    }

    // Menghapus reminder (pastikan milik user yang login)
    public function destroy($id)
    {
        $reminder = Reminder::whereHas('task', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('id', $id)
            ->first();

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil dihapus!'
        ], 200);
    }
}