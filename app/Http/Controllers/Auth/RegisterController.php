<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // We need departments so the user can pick which one they belong to
        $departments = Department::all();
        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'department_id' => 'required|exists:departments,id',
            'preferred_lang' => 'required|in:tk,ru,en',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'preferred_lang' => $request->preferred_lang,
            'role_level' => 'employee', // Default role
            'is_active' => false, // Keep them inactive until Admin approves! (Safety first)
        ]);

        // Audit Log
        $user->logAction('User Self-Registered');

        // Optional: Auto-login after register (or redirect to wait for approval)
        // Auth::login($user); 

        return redirect()->route('login')->with('success', 'Registration successful! Please wait for Admin approval.');
    }
}
