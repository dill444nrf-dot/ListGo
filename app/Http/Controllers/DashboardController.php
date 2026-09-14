<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();

        // 1. Statistik Kartu Dashboard
        $totalTasks = Task::where('user_id', $user->id)->count();
        $pendingTasks = Task::where('user_id', $user->id)->where('status', 'pending')->count();
        $completedTasks = Task::where('user_id', $user->id)->where('status', 'completed')->count();
        
        // Tugas yang akan deadline (status pending dan deadline dalam 2 hari ke depan)
        $upcomingDeadline = Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<=', $now->copy()->addDays(2))
            ->count();

        // 2. Daftar Tugas Terbaru (Recent Tasks) lengkap dengan kategori
        $recentTasks = Task::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil dimuat',
            'data' => [
                'user' => [
                    'name' => $user->name,
                ],
                'stats' => [
                    'total_tasks' => $totalTasks,
                    'pending_tasks' => $pendingTasks,
                    'completed_tasks' => $completedTasks,
                    'upcoming_deadline' => $upcomingDeadline,
                ],
                'recent_tasks' => $recentTasks
            ]
        ], 200);
    }
}