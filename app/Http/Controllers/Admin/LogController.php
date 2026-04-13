<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('admin.logs.index', compact('logs'));
    }
}
