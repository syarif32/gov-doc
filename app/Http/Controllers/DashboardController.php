<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ---------------- ADMIN DASHBOARD DATA ----------------
        if ($user->role_level === 'admin') {
            $stats = [
                'total_users' => User::count(),
                'total_docs' => Document::count(),
                'total_depts' => Department::count(),
                'pending_users' => User::where('is_active', false)->count(),
                'recent_activities' => AuditLog::with('user')->latest()->take(10)->get(),
                'dept_distribution' => Department::withCount('users')->get(),
            ];
            return view('admin.dashboard', compact('stats'));
        }

        // ---------------- USER DASHBOARD DATA ----------------
        $stats = [
            'my_docs_count' => Document::where('owner_id', $user->id)->count(),
            'shared_with_me' => Document::whereHas('permissions', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'unread_messages' => Message::whereHas('conversation', function ($q) use ($user) {
                $q->whereHas('users', function ($u) use ($user) {
                    $u->where('users.id', $user->id);
                });
            })->where('sender_id', '!=', $user->id)->count(), // Simplified unread logic
            'dept_docs' => Document::whereHas('permissions', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->latest()->take(5)->get(),
            'my_recent_activities' => AuditLog::where('user_id', $user->id)->latest()->take(5)->get(),
        ];

        return view('user.dashboard', compact('stats'));
    }
}
